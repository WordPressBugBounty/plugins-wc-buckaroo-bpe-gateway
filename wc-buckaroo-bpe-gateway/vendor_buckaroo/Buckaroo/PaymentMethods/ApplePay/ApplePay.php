<?php

namespace BuckarooDeps\Buckaroo\PaymentMethods\ApplePay;

use BuckarooDeps\Buckaroo\Models\Model;
use BuckarooDeps\Buckaroo\PaymentMethods\ApplePay\Models\Pay;
use BuckarooDeps\Buckaroo\PaymentMethods\ApplePay\Models\PayPayload;
use BuckarooDeps\Buckaroo\PaymentMethods\PayablePaymentMethod;
use BuckarooDeps\Buckaroo\Transaction\Response\TransactionResponse;

class ApplePay extends PayablePaymentMethod
{
    /**
     * @var string
     */
    protected string $paymentName = 'applepay';

    /**
     * @param Model|null $model
     * @return TransactionResponse
     */
    public function pay(?Model $model = null): TransactionResponse
    {
        return parent::pay($model ?? new Pay($this->payload));
    }

    public function payRedirect(): TransactionResponse
    {
        $this->payModel = PayPayload::class;

        $pay = new PayPayload($this->payload);

        $this->setPayPayload();
        
        return $this->postRequest();
    }
}
