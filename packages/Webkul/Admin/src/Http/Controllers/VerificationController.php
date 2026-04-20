<?php

namespace Webkul\Admin\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Webkul\Admin\DataGrids\VerificationDataGrid;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Core\Repositories\ChannelRepository;

class VerificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected ChannelRepository $channelRepository,
    ) {}

    /**
     * Show the form for listing all verified customers.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin::customers.verifications.index');
    }

    /**
     * Get DataGrid for verifications list.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVerifications()
    {
        return datagrid(VerificationDataGrid::class)->process();
    }

    /**
     * Show customer verification details.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $customer = $this->customerRepository->findOrFail($id);
        
        $documents = DB::table('customer_verification_documents')
            ->where('customer_id', $id)
            ->get();

        return view('admin::customers.verifications.view', compact('customer', 'documents'));
    }

    /**
     * Update customer verification status.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id)
    {
        $customer = $this->customerRepository->findOrFail($id);
        
        $status = request()->input('verification_status');
        $rejectionReason = request()->input('rejection_reason', null);

        if (! in_array($status, ['pending', 'approved', 'rejected', 'incomplete'])) {
            return response()->json(['message' => 'Invalid status'], 400);
        }

        $customer->update([
            'verification_status' => $status,
        ]);

        if ($status === 'rejected' && $rejectionReason) {
            DB::table('customer_verifications')
                ->updateOrCreate(
                    ['customer_id' => $id],
                    ['rejection_reason' => $rejectionReason, 'reviewed_at' => now()]
                );
        }

        return response()->json([
            'message' => trans('admin::app.customers.verifications.update-success'),
            'customer' => $customer,
        ]);
    }

    /**
     * Download verification document.
     *
     * @param int $documentId
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadDocument($documentId)
    {
        $document = DB::table('customer_verification_documents')
            ->where('id', $documentId)
            ->firstOrFail();

        return response()->download(storage_path('app/' . $document->path), $document->original_name);
    }
}
