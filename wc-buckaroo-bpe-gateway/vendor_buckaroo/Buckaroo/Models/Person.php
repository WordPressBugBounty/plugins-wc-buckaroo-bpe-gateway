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

declare(strict_types=1);

namespace BuckarooDeps\Buckaroo\Models;

use BuckarooDeps\Buckaroo\Models\Interfaces\Recipient;

class Person extends Model implements Recipient
{
    protected string $category;
    protected string $gender;
    protected string $culture;
    protected string $careOf;
    protected string $title;
    protected ?string $initials;
    protected string $name;
    protected string $firstName;
    protected string $lastNamePrefix;
    protected string $lastName;
    protected ?string $birthDate;
    protected string $placeOfBirth;
}
