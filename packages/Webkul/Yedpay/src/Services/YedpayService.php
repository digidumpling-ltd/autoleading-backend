<?php

namespace Webkul\Yedpay\Services;

use Exception;
use Yedpay\Client;
use Yedpay\Library;
use Yedpay\Response\Error;
use Yedpay\Response\Success;

class YedpayService
{
    public function __construct(
        protected string $apiKey,
        protected string $signingKey,
        protected bool $sandbox = true,
    ) {}

    /**
     * Create an online payment and return the Yedpay hosted payment URL.
     *
     * @throws Exception
     */
    public function createPayment(float $amount, string $customId, string $returnUrl, string $notifyUrl): string
    {
        $environment = $this->sandbox ? Library::STAGING : Library::PRODUCTION;

        $client = new Client($environment, $this->apiKey, false);
        $client->setReturnUrl($returnUrl)->setNotifyUrl($notifyUrl);

        $response = $client->onlinePayment($customId, $amount);

        if ($response instanceof Error) {
            throw new Exception('Yedpay payment creation failed: ' . $response->getMessage());
        }

        /** @var Success $response */
        $data = $response->getData();

        $parsed = is_string($data) ? json_decode($data, true) : (array) $data;

        $url = $parsed['checkout_url'] ?? $parsed['payment_url'] ?? $parsed['url'] ?? null;

        if (! $url) {
            throw new Exception('Yedpay did not return a payment URL. Response: ' . json_encode($parsed));
        }

        return $url;
    }

    /**
     * Verify the signature on a Yedpay callback/return request.
     */
    public function verifyCallback(array $data): bool
    {
        $environment = $this->sandbox ? Library::STAGING : Library::PRODUCTION;

        $client = new Client($environment, $this->apiKey, false);

        return (bool) $client->verifySign($data, $this->signingKey);
    }

    /**
     * Return true only when the callback data indicates a completed payment.
     */
    public function isPaymentPaid(array $data): bool
    {
        return ($data['status'] ?? '') === 'paid';
    }

    /**
     * Query the status of an online payment by custom ID.
     *
     * @throws Exception
     */
    public function queryPayment(string $customId): array
    {
        $environment = $this->sandbox ? Library::STAGING : Library::PRODUCTION;

        $client = new Client($environment, $this->apiKey, false);

        $response = $client->queryOnlinePayment($customId);

        if ($response instanceof Error) {
            throw new Exception('Yedpay query failed: ' . $response->getMessage());
        }

        $data = $response->getData();

        return is_string($data) ? json_decode($data, true) : (array) $data;
    }
}
