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

namespace BuckarooDeps\Buckaroo\PaymentMethods\CreditCard;

use BuckarooDeps\Buckaroo\PaymentMethods\CreditCard\Models\CardData;
use BuckarooDeps\Buckaroo\PaymentMethods\CreditCard\Models\SecurityCode;
use BuckarooDeps\Buckaroo\PaymentMethods\CreditCard\Models\SessionData;
use BuckarooDeps\Buckaroo\PaymentMethods\Interfaces\Combinable;
use BuckarooDeps\Buckaroo\PaymentMethods\PayablePaymentMethod;
use BuckarooDeps\Buckaroo\Transaction\Response\TransactionResponse;

class CreditCard extends PayablePaymentMethod implements Combinable
{
    /**
     * @return TransactionResponse
     */
    public function payEncrypted(): TransactionResponse
    {
        $cardData = new CardData($this->payload);

        $this->setPayPayload();

        $this->setServiceList('PayEncrypted', $cardData);

        return $this->postRequest();
    }

    /**
     * @return TransactionResponse
     */
    public function authorizeWithToken(): TransactionResponse
    {
        $cardData = new SessionData($this->payload);

        $this->setPayPayload();

        $this->setServiceList('AuthorizeWithToken', $cardData);

        return $this->postRequest();
    }

    /**
     * @return TransactionResponse
     */
    public function payWithToken(): TransactionResponse
    {
        $cardData = new SessionData($this->payload);

        $this->setPayPayload();

        $this->setServiceList('PayWithToken', $cardData);

        return $this->postRequest();
    }

    /**
     * @return TransactionResponse
     */
    public function authorizeEncrypted(): TransactionResponse
    {
        $cardData = new CardData($this->payload);

        $this->setPayPayload();

        $this->setServiceList('AuthorizeEncrypted', $cardData);

        return $this->postRequest();
    }

    /**
     * @return TransactionResponse
     */
    public function payWithSecurityCode(): TransactionResponse
    {
        $securityCode = new SecurityCode($this->payload);

        $this->setPayPayload();

        $this->setServiceList('PayWithSecurityCode', $securityCode);

        return $this->postRequest();
    }

    /**
     * @return TransactionResponse
     */
    public function authorizeWithSecurityCode(): TransactionResponse
    {
        $securityCode = new SecurityCode($this->payload);

        $this->setPayPayload();

        $this->setServiceList('AuthorizeWithSecurityCode', $securityCode);

        return $this->postRequest();
    }

    /**
     * @return TransactionResponse
     */
    public function authorize(): TransactionResponse
    {
        $this->setPayPayload();

        $this->setServiceList('Authorize');

        return $this->postRequest();
    }

    /**
     * @return TransactionResponse
     */
    public function capture(): TransactionResponse
    {
        $this->setPayPayload();

        $this->setServiceList('Capture');

        return $this->postRequest();
    }

    /**
     * @return TransactionResponse
     */
    public function payRecurrent(): TransactionResponse
    {
        $this->setPayPayload();

        $this->setServiceList('PayRecurrent');

        return $this->postRequest();
    }

    /**
     * @return TransactionResponse
     */
    public function payRemainderEncrypted(): TransactionResponse
    {
        $cardData = new CardData($this->payload);

        $this->setPayPayload();

        $this->setServiceList('PayRemainderEncrypted', $cardData);

        return $this->postRequest();
    }

    /**
     * @return TransactionResponse
     */
    public function cancelAuthorize(): TransactionResponse
    {
        $this->setRefundPayload();

        $this->setServiceList('CancelAuthorize');

        return $this->postRequest();
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

        throw new \Exception('Missing creditcard name');
    }
}
