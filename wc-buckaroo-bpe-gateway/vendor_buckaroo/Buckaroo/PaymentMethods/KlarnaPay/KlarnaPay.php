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

namespace BuckarooDeps\Buckaroo\PaymentMethods\KlarnaPay;

use BuckarooDeps\Buckaroo\Models\Model;
use BuckarooDeps\Buckaroo\PaymentMethods\KlarnaPay\Models\Pay;
use BuckarooDeps\Buckaroo\PaymentMethods\KlarnaPay\Models\PayPayload;
use BuckarooDeps\Buckaroo\PaymentMethods\PayablePaymentMethod;
use BuckarooDeps\Buckaroo\Transaction\Response\TransactionResponse;

class KlarnaPay extends PayablePaymentMethod
{
    /**
     * @var string
     */
    protected string $paymentName = 'klarna';
    /**
     * @var string
     */
    protected string $payModel = PayPayload::class;

    /**
     * @param Model|null $model
     * @return TransactionResponse
     */
    public function pay(?Model $model = null): TransactionResponse
    {
        return parent::pay($model ?? new Pay($this->payload));
    }

    /**
     * @return KlarnaPay|mixed
     */
    public function payInInstallments(): TransactionResponse
    {
        $pay = new Pay($this->payload);

        $this->setPayPayload();

        $this->setServiceList('PayInInstallments', $pay);

        return $this->postRequest();
    }
    
     /**
     * @param Model|null $model
     * @return TransactionResponse
     */
    public function payRemainder(?Model $model = null): TransactionResponse
    {
        return parent::payRemainder($model ?? new Pay($this->payload));
    }
}
