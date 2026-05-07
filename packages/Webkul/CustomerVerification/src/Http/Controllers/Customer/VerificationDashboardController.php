<?php

namespace Webkul\CustomerVerification\Http\Controllers\Customer;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Throwable;
use Webkul\CustomerVerification\Http\Requests\Customer\CustomerVerificationUploadRequest;
use Webkul\CustomerVerification\Services\CustomerVerificationDocumentService;
use Webkul\CustomerVerification\Services\CustomerVerificationStatusService;
use Webkul\CustomerVerification\Support\Verification;
use Webkul\Shop\Http\Controllers\Controller;

class VerificationDashboardController extends Controller
{
    public function __construct(
        protected CustomerVerificationDocumentService $documentService,
        protected CustomerVerificationStatusService $statusService
    ) {}

    public function index(): View
    {
        $customer = auth()->guard('customer')->user();

        abort_if(! $customer, 403);

        $documentMeta = $this->documentService->getDocumentMeta();

        $documents = $this->documentService->getCustomerDocumentsByType((int) $customer->id);

        $missingDocumentTypes = array_values(array_filter(
            array_keys($documentMeta),
            fn (string $documentType): bool => ! $documents->has($documentType)
        ));

        return view('customer-verification::shop.customers.account.verification-dashboard', [
            'customer' => $customer,
            'documents' => $documents,
            'documentMeta' => $documentMeta,
            'missingDocumentTypes' => $missingDocumentTypes,
            'rejectionReason' => $this->getRejectionReason((int) $customer->id),
        ]);
    }

    public function upload(CustomerVerificationUploadRequest $request): RedirectResponse
    {
        $customer = auth()->guard('customer')->user();

        abort_if(! $customer, 403);

        $requestedCustomerId = (int) $request->input('customer_id', $customer->id);

        abort_if($requestedCustomerId !== (int) $customer->id, 403);

        try {
            DB::beginTransaction();

            foreach (Verification::REQUIRED_DOCUMENT_TYPES as $documentType) {
                if (! $request->hasFile($documentType)) {
                    continue;
                }

                $this->documentService->storeCustomerDocument(
                    (int) $customer->id,
                    $documentType,
                    $request->file($documentType)
                );
            }

            $movedToPending = $this->statusService->moveToPendingWhenEligible($customer);

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            report($exception);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'documents' => trans('customer-verification::app.common.verification_upload_failed'),
                ]);
        }

        $flashMessageKey = $movedToPending
            ? 'verification_all_docs_uploaded'
            : 'verification_docs_complete';

        return redirect()
            ->route('shop.customer.verification.index')
            ->with('success', trans('customer-verification::app.common.'.$flashMessageKey));
    }

    protected function getRejectionReason(int $customerId): ?string
    {
        if (! Schema::hasTable('customer_verifications')) {
            return null;
        }

        $rejectionReason = DB::table('customer_verifications')
            ->where('customer_id', $customerId)
            ->value('rejection_reason');

        if (! is_string($rejectionReason)) {
            return null;
        }

        $rejectionReason = trim($rejectionReason);

        return $rejectionReason === ''
            ? null
            : $rejectionReason;
    }
}
