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

namespace BuckarooDeps\Buckaroo\PaymentMethods\iDealQR\Models;

use BuckarooDeps\Buckaroo\Models\Model;

class Generate extends Model
{
    protected float $amount;
    protected bool $amountIsChangeable;
    protected string $purchaseId;
    protected string $description;
    protected bool $isOneOff;
    protected string $expiration;
    protected bool $isProcessing;
    protected float $minAmount;
    protected float $maxAmount;
    protected int $imageSize;
}
