<?php

namespace Webkul\CustomerVerification\Http\Controllers\Customer;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Webkul\Core\Repositories\SubscribersListRepository;
use Webkul\Customer\Repositories\CustomerGroupRepository;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\CustomerVerification\Http\Requests\Customer\RegistrationWithDocumentsRequest;
use Webkul\CustomerVerification\Repositories\CustomerVerificationDocumentRepository;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Shop\Mail\Customer\EmailVerificationNotification;
use Webkul\Shop\Mail\Customer\RegistrationNotification;

class RegistrationController extends Controller
{
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected CustomerGroupRepository $customerGroupRepository,
        protected SubscribersListRepository $subscriptionRepository,
        protected CustomerVerificationDocumentRepository $documentRepository
    ) {}

    public function index(): View
    {
        return view('shop::customers.sign-up');
    }

    public function store(RegistrationWithDocumentsRequest $registrationRequest): Response
    {
        $customerGroup = core()->getConfigData('customer.settings.create_new_account_options.default_group');

        $subscription = $this->subscriptionRepository->findOneWhere(['email' => $registrationRequest->input('email')]);

        $data = array_merge($registrationRequest->only([
            'first_name',
            'last_name',
            'email',
            'password_confirmation',
            'is_subscribed',
        ]), [
            'password' => bcrypt($registrationRequest->input('password')),
            'api_token' => Str::random(80),
            'is_verified' => ! core()->getConfigData('customer.settings.email.verification'),
            'customer_group_id' => $this->customerGroupRepository->findOneWhere(['code' => $customerGroup])->id,
            'channel_id' => core()->getCurrentChannel()->id,
            'token' => md5(uniqid(rand(), true)),
            'subscribed_to_news_letter' => (bool) ($registrationRequest->input('is_subscribed') ?? $subscription?->is_subscribed),
        ]);

        Event::dispatch('customer.registration.before');

        $customer = $this->customerRepository->create($data);

        $this->storeDocuments($customer->id, $registrationRequest);

        if ($subscription) {
            $this->subscriptionRepository->update([
                'customer_id' => $customer->id,
            ], $subscription->id);
        }

        if (
            ! empty($data['is_subscribed'])
            && ! $subscription
        ) {
            Event::dispatch('customer.subscription.before');

            $subscription = $this->subscriptionRepository->create([
                'email' => $data['email'],
                'customer_id' => $customer->id,
                'channel_id' => core()->getCurrentChannel()->id,
                'is_subscribed' => 1,
                'token' => uniqid(),
            ]);

            Event::dispatch('customer.subscription.after', $subscription);
        }

        Event::dispatch('customer.create.after', $customer);

        Event::dispatch('customer.registration.after', $customer);

        if (core()->getConfigData('customer.settings.email.verification')) {
            session()->flash('success', trans('shop::app.customers.signup-form.success-verify'));
        } else {
            session()->flash('success', trans('shop::app.customers.signup-form.success'));
        }

        return redirect()->route('shop.customer.session.index');
    }

    protected function storeDocuments(int $customerId, RegistrationWithDocumentsRequest $request): void
    {
        $documentMap = [
            'id_document' => 'id_document',
            'driver_license' => 'driver_license',
            'address_proof' => 'address_proof',
        ];

        $hasAllDocuments = true;

        foreach ($documentMap as $field => $type) {
            if (! $request->hasFile($field)) {
                $hasAllDocuments = false;
                continue;
            }

            $file = $request->file($field);
            $extension = strtolower($file->getClientOriginalExtension());
            $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeName = Str::slug($baseName) ?: 'document';
            $fileName = $safeName . '-' . now()->timestamp . '.' . $extension;
            $directory = 'customer-documents/' . $customerId . '/' . $type;

            $path = Storage::disk('public')->putFileAs($directory, $file, $fileName);

            $this->documentRepository->create([
                'customer_id' => $customerId,
                'type' => $type,
                'path' => $path,
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'status' => 'pending',
                'original_name' => $file->getClientOriginalName(),
            ]);
        }

        DB::table('customers')
            ->where('id', $customerId)
            ->update([
                'verification_status' => $hasAllDocuments ? 'pending' : 'incomplete',
            ]);
    }
}
