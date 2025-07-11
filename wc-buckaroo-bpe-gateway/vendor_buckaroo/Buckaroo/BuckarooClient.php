<?php
/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * It is available through the world-wide-web at this URL:
 * https://tldrlegal.com/license/mit-license
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to support@buckaroo.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@buckaroo.nl for more information.
 *
 * @copyright Copyright (c) BuckarooDeps\Buckaroo B.V.
 * @license   https://tldrlegal.com/license/mit-license
 */

namespace BuckarooDeps\Buckaroo;

use BuckarooDeps\Buckaroo\Config\Config;
use BuckarooDeps\Buckaroo\Config\DefaultConfig;
use BuckarooDeps\Buckaroo\Exceptions\BuckarooException;
use BuckarooDeps\Buckaroo\Handlers\Credentials;
use BuckarooDeps\Buckaroo\Handlers\Logging\Observer as LoggingObserver;
use BuckarooDeps\Buckaroo\PaymentMethods\BatchTransactions;
use BuckarooDeps\Buckaroo\PaymentMethods\PaymentFacade;
use BuckarooDeps\Buckaroo\Services\ActiveSubscriptions;
use BuckarooDeps\Buckaroo\Services\TransactionService;
use BuckarooDeps\Buckaroo\Transaction\Client;

/**
 *
 */
class BuckarooClient
{
    /**
     * @var Client
     */
    private Client $client;
    /**
     * @var Config|null
     */
    private Config $config;

    /**
     * @param string|Config $websiteKey
     * @param string $secretKey
     * @param string|null $mode
     * @throws BuckarooException
     */
    public function __construct($websiteKey, ?string $secretKey = null, ?string $mode = null)
    {
        if ($websiteKey instanceof Config)
        {
            $this->config = $websiteKey;
        }

        if (is_string($websiteKey))
        {
            $this->config = $this->getConfig($websiteKey, $secretKey, $mode);
        }

        $this->client = new Client($this->config);
    }

    /**
     * @param string|null $method
     * @return PaymentFacade
     */
    public function method(?string $method = null): PaymentFacade
    {
        return new PaymentFacade($this->client, $method);
    }


    public function getActiveSubscriptions(): array
    {
        return (new ActiveSubscriptions($this->client))->get();
    }

    /**
     * @param array $transactions
     * @return BatchTransactions
     */
    public function batch(array $transactions): BatchTransactions
    {
        return new BatchTransactions($this->client, $transactions);
    }

    /**
     * @param string $transactionKey
     * @return TransactionService
     */
    public function transaction(string $transactionKey): TransactionService
    {
        return new TransactionService($this->client, $transactionKey);
    }

    /**
     * @return bool
     */
    public function confirmCredential(): bool
    {
        $credentialHandler = new Credentials($this->client, $this->config);

        return $credentialHandler->confirm();
    }

    /**
     * @param LoggingObserver $observer
     * @return $this
     */
    public function attachLogger(LoggingObserver $observer)
    {
        $this->config->getLogger()->attach($observer);

        return $this;
    }

    /**
     * @param Config $config
     * @return $this
     */
    public function setConfig(Config $config)
    {
        $this->client->config($config);

        return $this;
    }

    /**
     * @return Client
     */
    public function client()
    {
        return $this->client;
    }

    /**
     * @param string $websiteKey
     * @param string $secretKey
     * @param string|null $mode
     * @return Config|null
     * @throws BuckarooException
     */
    private function getConfig(string $websiteKey, string $secretKey, ?string $mode = null): ?Config
    {
        if ($websiteKey && $secretKey)
        {
            return new DefaultConfig($websiteKey, $secretKey, $mode);
        }

        throw new BuckarooException(null, "Config is missing.");
    }
}
