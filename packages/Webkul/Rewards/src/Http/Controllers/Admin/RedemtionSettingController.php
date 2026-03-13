<?php

namespace Webkul\Rewards\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Webkul\Rewards\Repositories\RedemptionSettingRepository;

class RedemtionSettingController extends Controller
{
    /**
     * Create a new datagrid instance.
     * 
     * @return void
     */
    public function __construct(protected RedemptionSettingRepository $redemptionSettingRepository)
    {
    }

    /** 
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $data = $this->redemptionSettingRepository->getData();

        return view('rewards::admin.rewards.redemption.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function store()
    {
        $pointsInput = trim((string) request()->input('points', ''));

        $conversionRateInput = trim((string) request()->input('conversion_rate', ''));

        $validationInput = [
            'points'          => $pointsInput === '' ? null : str_replace(',', '.', $pointsInput),
            'conversion_rate' => $conversionRateInput === '' ? null : str_replace(',', '.', $conversionRateInput),
        ];

        $validated = validator($validationInput, [
            'points'          => ['nullable', 'numeric', 'min:0'],
            'conversion_rate' => ['nullable', 'numeric', 'min:0'],
        ])->validate();

        $payload = [
            'redemp_over_subtotal' => request()->boolean('redemp_over_subtotal') ? 1 : 0,
            'apply_points_checkout' => request()->boolean('apply_points_checkout') ? 1 : 0,
            'points'                => array_key_exists('points', $validated) && $validated['points'] !== null
                ? (int) $validated['points']
                : null,
            'conversion_rate'       => array_key_exists('conversion_rate', $validated) && $validated['conversion_rate'] !== null
                ? (float) $validated['conversion_rate']
                : null,
        ];

        $data = $this->redemptionSettingRepository->getData();

        if ($data) {
            $data->update($payload);
        } else {
            $this->redemptionSettingRepository->create($payload);
        }
        
        session()->flash('success', trans('rewards::app.admin.rewards.redemption.index.update-success'));

        return redirect()->route('admin.reward.redemption.index');
    }
}