<?php

namespace Webkul\CustomerVerification\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\CustomerVerification\DataGrids\VerificationDataGrid;
use Webkul\CustomerVerification\Mail\ReminderNotification;
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

    public function sendReminder(int $customerId): JsonResponse
    {
        $customer = $this->customerRepository->find($customerId);

        if (! $customer) {
            abort(404);
        }

        Mail::to($customer->email)->queue(new ReminderNotification($customer));

        return response()->json(['message' => trans('customer-verification::app.common.reminder-sent')]);
    }

    public function uploadDocument(Request $request, int $customerId, string $docType): RedirectResponse
    {
        $customer = $this->customerRepository->find($customerId);

        if (! $customer) {
            abort(404);
        }

        $validTypes = \Webkul\CustomerVerification\Support\Verification::REQUIRED_DOCUMENT_TYPES;

        if (! in_array($docType, $validTypes, true)) {
            abort(422);
        }

        $request->validate([
            'document' => ['required', 'file', 'mimes:png,jpg,jpeg,webp,pdf', 'max:5120'],
        ]);

        $this->documentService->storeCustomerDocument($customerId, $docType, $request->file('document'));

        session()->flash('success', trans('customer-verification::app.common.document_uploaded'));

        return redirect()->route('admin.verification.show', $customerId);
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
