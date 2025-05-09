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

namespace BuckarooDeps\Buckaroo\PaymentMethods\BuckarooVoucher;

use BuckarooDeps\Buckaroo\Models\Model;
use BuckarooDeps\Buckaroo\Models\Payload\DataRequestPayload;
use BuckarooDeps\Buckaroo\PaymentMethods\BuckarooVoucher\Models\Create;
use BuckarooDeps\Buckaroo\PaymentMethods\BuckarooVoucher\Models\Deactivate;
use BuckarooDeps\Buckaroo\PaymentMethods\BuckarooVoucher\Models\GetBalance;
use BuckarooDeps\Buckaroo\PaymentMethods\BuckarooVoucher\Models\Pay;
use BuckarooDeps\Buckaroo\PaymentMethods\PayablePaymentMethod;
use BuckarooDeps\Buckaroo\Transaction\Response\TransactionResponse;

class BuckarooVoucher extends PayablePaymentMethod
{
    /**
     * @var string
     */
    protected string $paymentName = 'buckaroovoucher';

    /**
     * @param Model|null $model
     * @return TransactionResponse
     */
    public function pay(?Model $model = null): TransactionResponse
    {
        $this->setPayPayload();

        $pay = new Pay($this->payload);

        return parent::pay($model ?? $pay);
    }


    /**
     * @param Model|null $model
     * @return TransactionResponse
     */
    public function payRemainder(?Model $model = null): TransactionResponse
    {
        $this->setPayPayload();

        $pay = new Pay($this->payload);

        return parent::payRemainder($model ?? $pay);
    }

    /**
     * @return TransactionResponse
     */
    public function getBalance(): TransactionResponse
    {
        $this->payModel = DataRequestPayload::class;

        $data = new GetBalance($this->payload);

        $this->setPayPayload();

        $this->setServiceList('GetBalance', $data);

        return $this->dataRequest();
    }
    /**
     * @return TransactionResponse
     */
    public function create(): TransactionResponse
    {
        $this->payModel = DataRequestPayload::class;

        $data = new Create($this->payload);

        $this->setPayPayload();

        $this->setServiceList('CreateApplication', $data);

        return $this->dataRequest();
    }

    /**
     * @return TransactionResponse
     */
    public function deactivate(): TransactionResponse
    {
        $this->payModel = DataRequestPayload::class;
        
        $data = new Deactivate($this->payload);

        $this->setPayPayload();

        $this->setServiceList('DeactivateVoucher', $data);

        return $this->dataRequest();
    }
}
