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

namespace BuckarooDeps\Buckaroo\PaymentMethods\Paypal;

use BuckarooDeps\Buckaroo\Models\Model;
use BuckarooDeps\Buckaroo\PaymentMethods\Interfaces\Combinable;
use BuckarooDeps\Buckaroo\PaymentMethods\PayablePaymentMethod;
use BuckarooDeps\Buckaroo\PaymentMethods\Paypal\Models\ExtraInfo;
use BuckarooDeps\Buckaroo\PaymentMethods\Paypal\Models\Pay;
use BuckarooDeps\Buckaroo\Transaction\Response\TransactionResponse;

class Paypal extends PayablePaymentMethod implements Combinable
{
    /**
     * @var string
     */
    protected string $paymentName = 'paypal';

    /**
     * @param Model|null $model
     * @return TransactionResponse
     */
    public function pay(?Model $model = null): TransactionResponse
    {
        return parent::pay($model ?? new Pay($this->payload));
    }

    /**
     * @return TransactionResponse
     */
    public function payRecurrent(): TransactionResponse
    {
        $pay = new Pay($this->payload);

        $this->setPayPayload();

        $this->setServiceList('PayRecurrent', $pay);

        return $this->postRequest();
    }

    /**
     * @return Paypal|mixed
     */
    public function extraInfo()
    {
        $extraInfo = new ExtraInfo($this->payload);

        $this->setPayPayload();

        $this->setServiceList('Pay,ExtraInfo', $extraInfo);

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
