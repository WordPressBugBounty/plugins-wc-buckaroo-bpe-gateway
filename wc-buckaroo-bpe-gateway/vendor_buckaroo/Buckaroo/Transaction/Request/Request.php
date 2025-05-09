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

namespace BuckarooDeps\Buckaroo\Transaction\Request;

use ArrayAccess;
use BuckarooDeps\Buckaroo\Resources\Arrayable;
use Exception;
use JsonSerializable;

class Request implements JsonSerializable, ArrayAccess, Arrayable
{
    /**
     * @var array
     */
    protected array $data = [];
    /**
     * @var array
     */
    protected array $headers = [];

    /**
     * @param $offset
     * @param $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset))
        {
            $this->data[] = $value;
        } else
        {
            $this->data[$offset] = $value;
        }
    }

    /** Implement ArrayAccess */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    /** Implement ArrayAccess */
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    /** Implement ArrayAccess */
    public function offsetGet($offset): mixed
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    /** Implement JsonSerializable */
    public function jsonSerialize(): mixed
    {
        return $this->data;
    }

    /** Implement Arrayable */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function setHeader($name, $value)
    {
        $this->headers[strtolower($name)] = $value;
    }

    /**
     * @param  string $name
     * @return string
     */
    public function getHeader($name)
    {
        if (isset($this->headers[strtolower($name)]))
        {
            return $this->headers[strtolower($name)];
        }

        return null;
    }

    /**
     * @return array [ string ]
     */
    public function getHeaders(): array
    {
        return array_map(function ($value, $key) {
            return $key . ': ' . $value;
        }, $this->headers);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
