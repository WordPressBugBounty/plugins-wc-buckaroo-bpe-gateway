<?php

namespace BuckarooDeps\Buckaroo\Transaction\Request\HttpClient;

use BuckarooDeps\Buckaroo\Config\Config;
use BuckarooDeps\GuzzleHttp\Client;
use BuckarooDeps\GuzzleHttp\ClientInterface;
use function defined;

class HttpClientFactory
{
    public static function createClient(Config $config)
    {
        if (defined(ClientInterface::class . '::MAJOR_VERSION')) {
            $major = ClientInterface::MAJOR_VERSION;
        } elseif (defined(ClientInterface::class . '::VERSION')) {
            $major = (int)explode('.', ClientInterface::VERSION)[0];
        } elseif (defined(Client::class . '::VERSION')) {
            $major = (int)explode('.', Client::VERSION)[0];
        } else {
            $major = 7;
        }

        return $major === 5
            ? new GuzzleHttpClientV5($config)
            : new GuzzleHttpClientV7($config);
    }
}
