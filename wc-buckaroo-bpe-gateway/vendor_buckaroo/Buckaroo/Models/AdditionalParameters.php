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

namespace BuckarooDeps\Buckaroo\Models;

/**
 *
 */
class AdditionalParameters extends Model
{
    /**
     * @var array
     */
    protected ?array $AdditionalParameter = [];

    /**
     * @var array
     */
    protected ?array $List = [];

    /**
     * @var bool
     */
    private bool $isDataRequest;

    public function __construct(?array $values = null, $isDataRequest = false)
    {
        $this->isDataRequest = $isDataRequest;

        parent::__construct($values);
    }

    /**
     * @param array|null $data
     * @return AdditionalParameters
     */
    public function setProperties(?array $data)
    {
        foreach ($data ?? [] as $name => $value)
        {
            if($this->isDataRequest)
            {
                $this->List[] = [
                    'Value' => $value,
                    'Name' => $name,
                ];

                continue;
            }

            $this->AdditionalParameter[] = [
                'Value' => $value,
                'Name' => $name,
            ];
        }

        return parent::setProperties($data);
    }
}
