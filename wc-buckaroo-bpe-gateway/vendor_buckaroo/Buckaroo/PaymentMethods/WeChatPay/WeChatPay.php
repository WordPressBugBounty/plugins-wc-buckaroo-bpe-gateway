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

namespace BuckarooDeps\Buckaroo\PaymentMethods\WeChatPay;

use BuckarooDeps\Buckaroo\Models\Model;
use BuckarooDeps\Buckaroo\PaymentMethods\PayablePaymentMethod;
use BuckarooDeps\Buckaroo\PaymentMethods\WeChatPay\Models\Pay;
use BuckarooDeps\Buckaroo\Transaction\Response\TransactionResponse;

class WeChatPay extends PayablePaymentMethod
{
    /**
     * @var string
     */
    protected string $paymentName = 'WeChatPay';

    /**
     * @param Model|null $model
     * @return TransactionResponse
     */
    public function pay(?Model $model = null): TransactionResponse
    {
        return parent::pay($model ?? new Pay($this->payload));
    }
}
