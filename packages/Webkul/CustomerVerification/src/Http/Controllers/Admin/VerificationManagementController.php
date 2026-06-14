<?php

namespace Webkul\CustomerVerification\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\CustomerVerification\DataGrids\VerificationDataGrid;
use Webkul\CustomerVerification\Repositories\CustomerVerificationDocumentRepository;
use Webkul\CustomerVerification\Services\AdminVerificationActionService;
use Webkul\CustomerVerification\Services\CustomerVerificationDocumentService;
use Webkul\CustomerVerification\Support\Verification;

class VerificationManagementController extends Controller
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private CustomerVerificationDocumentRepository $documentRepository,
        private AdminVerificationActionService $verificationService,
        private CustomerVerificationDocumentService $documentService
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datagrid(VerificationDataGrid::class)->process();
        }

        return view('customer-verification::admin.verifications.index');
    }

    public function show(int $customerId): View
    {
        $customer = $this->customerRepository->find($customerId);

        if (!$customer) {
            abort(404);
        }

        $documents = $this->documentRepository->where('customer_id', $customerId)->get();

        return view('customer-verification::admin.verifications.show', [
            'customer' => $customer,
            'documents' => $documents,
            'statuses' => Verification::STATUSES,
        ]);
    }

    public function destroyDocument(int $customerId, int $documentId): RedirectResponse
    {
        $customer = $this->customerRepository->find($customerId);

        if (! $customer) {
            abort(404);
        }

        if ($customer->verification_status === Verification::STATUS_APPROVED) {
            session()->flash('error', trans('customer-verification::app.common.cannot_delete_doc_approved'));

            return redirect()->route('admin.verification.show', $customerId);
        }

        $this->documentService->deleteDocument($documentId, $customerId);

        session()->flash('success', trans('customer-verification::app.common.document_deleted'));

        return redirect()->route('admin.verification.show', $customerId);
    }

    public function approve(int $customerId): RedirectResponse
    {
        $customer = $this->customerRepository->find($customerId);

        if (!$customer) {
            abort(404);
        }

        $adminId = auth('admin')->id();

        $this->verificationService->approve($customerId, $adminId);

        session()->flash('success', trans('customer-verification::app.common.customer_approved'));

        return redirect()->route('admin.verification.index');
    }

    public function reject(int $customerId, Request $request): RedirectResponse
    {
        $customer = $this->customerRepository->find($customerId);

        if (!$customer) {
            abort(404);
        }

        $reason = $request->input('rejection_reason');

        if (!$reason) {
            return redirect()->back()->withErrors(['rejection_reason' => trans('customer-verification::app.common.rejection_reason_required')]);
        }

        $adminId = auth('admin')->id();

        $this->verificationService->reject($customerId, $adminId, $reason);

        session()->flash('success', trans('customer-verification::app.common.customer_rejected'));

        return redirect()->route('admin.verification.index');
    }
}
