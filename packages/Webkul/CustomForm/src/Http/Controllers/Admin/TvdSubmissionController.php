<?php

namespace Webkul\CustomForm\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Webkul\CustomForm\Repositories\TvdSubmissionRepository;

class TvdSubmissionController extends Controller
{
    public function __construct(
        protected TvdSubmissionRepository $tvdSubmissionRepository
    ) {}

    public function index(): View
    {
        $submissions = $this->tvdSubmissionRepository
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('customform::admin.tvd-submissions.index', compact('submissions'));
    }

    public function show(int $id): View
    {
        $submission = $this->tvdSubmissionRepository->findOrFail($id);

        return view('customform::admin.tvd-submissions.show', compact('submission'));
    }
}
