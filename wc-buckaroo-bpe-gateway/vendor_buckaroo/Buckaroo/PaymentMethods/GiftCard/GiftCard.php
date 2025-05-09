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

namespace BuckarooDeps\Buckaroo\PaymentMethods\GiftCard;

use BuckarooDeps\Buckaroo\Models\Model;
use BuckarooDeps\Buckaroo\PaymentMethods\GiftCard\Models\Pay;
use BuckarooDeps\Buckaroo\PaymentMethods\GiftCard\Models\PayPayload;
use BuckarooDeps\Buckaroo\PaymentMethods\GiftCard\Models\Refund;
use BuckarooDeps\Buckaroo\PaymentMethods\PayablePaymentMethod;
use BuckarooDeps\Buckaroo\Transaction\Response\TransactionResponse;

class GiftCard extends PayablePaymentMethod
{
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
    public function refund(?Model $model = null): TransactionResponse
    {
        return parent::refund($model ?? new Refund($this->payload));
    }

    /**
     * @return TransactionResponse
     */
    public function payRedirect(): TransactionResponse
    {
        $this->payModel = PayPayload::class;

        $pay = new PayPayload($this->payload);

        $this->setPayPayload();

        return $this->postRequest();
    }

    /**
     * @param Model|null $model
     * @return PayablePaymentMethod|mixed
     */
    public function payRemainder(?Model $model = null)
    {
        return parent::payRemainder(new Pay($this->payload));
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function paymentName(): string
    {
        if (isset($this->payload['name']))
        {
            return $this->payload['name'];
        }

        return 'giftcard';
    }
}
