<?php

namespace BuckarooDeps\Buckaroo\Services;

use BuckarooDeps\Buckaroo\Transaction\Client;
use BuckarooDeps\Buckaroo\Transaction\Response\Response;
use BuckarooDeps\Buckaroo\Transaction\Response\TransactionResponse;

class TransactionService
{
    /**
     * @var
     */
    private $transaction;
    /**
     * @var string
     */
    private string $transactionKey;
    /**
     * @var Client
     */
    private Client $client;

    /**
     * @param Client $client
     * @param string $transactionKey
     */
    public function __construct(Client $client, string $transactionKey)
    {
        $this->transactionKey = $transactionKey;
        $this->client = $client;
    }

    /**
     * @return TransactionResponse
     * @throws \BuckarooDeps\Buckaroo\Exceptions\BuckarooException
     */
    public function status(): TransactionResponse
    {
        return $this->client->get(
            TransactionResponse::class,
            $this->client->getEndpoint('json/Transaction/Status/' . $this->transactionKey)
        );
    }

    /**
     * @return Response
     * @throws \BuckarooDeps\Buckaroo\Exceptions\BuckarooException
     */
    public function refundInfo(): Response
    {
        return $this->client->get(
            Response::class,
            $this->client->getEndpoint('json/Transaction/RefundInfo/' . $this->transactionKey)
        );
    }

    /**
     * @return Response
     * @throws \BuckarooDeps\Buckaroo\Exceptions\BuckarooException
     */
    public function cancelInfo(): Response
    {
        return $this->client->get(
            Response::class,
            $this->client->getEndpoint('json/Transaction/Cancel/' . $this->transactionKey)
        );
    }
}
