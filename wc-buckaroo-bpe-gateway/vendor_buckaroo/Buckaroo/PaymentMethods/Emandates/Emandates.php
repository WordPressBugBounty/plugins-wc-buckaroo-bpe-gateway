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

namespace BuckarooDeps\Buckaroo\PaymentMethods\Emandates;

use BuckarooDeps\Buckaroo\Models\Payload\DataRequestPayload;
use BuckarooDeps\Buckaroo\PaymentMethods\Emandates\Models\Mandate;
use BuckarooDeps\Buckaroo\PaymentMethods\Interfaces\Combinable;
use BuckarooDeps\Buckaroo\PaymentMethods\PaymentMethod;

class Emandates extends PaymentMethod implements Combinable
{
    /**
     * @var string
     */
    protected string $paymentName = 'emandate';
    /**
     * @var array|string[]
     */
    protected array $requiredConfigFields = ['currency'];

    /**
     * @return Emandates|mixed
     */
    public function issuerList()
    {
        $this->setServiceList('GetIssuerList');

        return $this->dataRequest();
    }

    /**
     * @return Emandates|mixed
     */
    public function createMandate()
    {
        $mandate = new Mandate($this->payload);

        $this->setServiceList('CreateMandate', $mandate);

        $this->request->setPayload(new DataRequestPayload($this->payload));

        return $this->dataRequest();
    }

    /**
     * @return Emandates|mixed
     */
    public function status()
    {
        $mandate = new Mandate($this->payload);

        $this->setServiceList('GetStatus', $mandate);

        $this->request->setPayload(new DataRequestPayload($this->payload));

        return $this->dataRequest();
    }

    /**
     * @return Emandates|mixed
     */
    public function modifyMandate()
    {
        $mandate = new Mandate($this->payload);

        $this->setServiceList('ModifyMandate', $mandate);

        $this->request->setPayload(new DataRequestPayload($this->payload));

        return $this->dataRequest();
    }

    /**
     * @return Emandates|mixed
     */
    public function cancelMandate()
    {
        $mandate = new Mandate($this->payload);

        $this->setServiceList('CancelMandate', $mandate);

        $this->request->setPayload(new DataRequestPayload($this->payload));

        return $this->dataRequest();
    }
}
