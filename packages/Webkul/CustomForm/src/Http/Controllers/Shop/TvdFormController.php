<?php

namespace Webkul\CustomForm\Http\Controllers\Shop;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Webkul\CustomForm\Http\Requests\TvdFormRequest;
use Webkul\CustomForm\Mail\TvdSubmissionMail;
use Webkul\CustomForm\Repositories\TvdSubmissionRepository;

class TvdFormController extends Controller
{
    public function __construct(
        protected TvdSubmissionRepository $tvdSubmissionRepository
    ) {}

    public function index(): View
    {
        return view('customform::shop.tvd-refund');
    }

    public function submit(TvdFormRequest $request): RedirectResponse
    {
        try {
            $submission = $this->tvdSubmissionRepository->create($request->only([
                'chinese_name',
                'english_name',
                'rental_model',
                'return_date',
                'contact_number',
                'email',
                'refund_type',
                'local_bank_info',
                'overseas_bank_info',
            ]));

            Mail::queue(new TvdSubmissionMail($submission));

            session()->flash('success', trans('customform::app.success'));
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());

            report($e);
        }

        return back();
    }
}
