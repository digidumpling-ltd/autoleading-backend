<?php

namespace Webkul\CustomerVerification\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\CustomerVerification\Repositories\CustomerVerificationDocumentRepository;
use Webkul\CustomerVerification\Services\AdminVerificationActionService;
use Webkul\CustomerVerification\Support\Verification;

class VerificationManagementController extends Controller
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private CustomerVerificationDocumentRepository $documentRepository,
        private AdminVerificationActionService $verificationService
    ) {}

    public function index(Request $request): View
    {
        $filter = $request->input('status', 'all');
        $search = $request->input('search', '');

        $query = $this->customerRepository->getModel();

        // Apply status filter
        if ($filter !== 'all') {
            $query = $query->where('verification_status', $filter);
        }

        // Apply search filter for reference number, email, name, or phone
        if (!empty($search)) {
            $query = $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('customer-verification::admin.verifications.index', [
            'customers' => $customers,
            'filter' => $filter,
            'search' => $search,
            'statuses' => Verification::STATUSES,
        ]);
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

    public function approve(int $customerId): RedirectResponse
    {
        $customer = $this->customerRepository->find($customerId);

        if (!$customer) {
            abort(404);
        }

        $adminId = auth('admin')->id();

        $this->verificationService->approve($customerId, $adminId);

        session()->flash('success', trans('shop::app.customers.verification.customer-approved'));

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
            return redirect()->back()->withErrors(['rejection_reason' => trans('shop::app.customers.verification.rejection-reason-required')]);
        }

        $adminId = auth('admin')->id();

        $this->verificationService->reject($customerId, $adminId, $reason);

        session()->flash('success', trans('shop::app.customers.verification.customer-rejected'));

        return redirect()->route('admin.verification.index');
    }
}
