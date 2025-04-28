<?php

namespace BuckarooDeps\Buckaroo\Transaction\Request\HttpClient;

use BuckarooDeps\Buckaroo\Config\Config;
use BuckarooDeps\Buckaroo\Exceptions\BuckarooException;
use BuckarooDeps\Buckaroo\Exceptions\TransferException;
use BuckarooDeps\Buckaroo\Handlers\Logging\Subject;
use BuckarooDeps\GuzzleHttp\Client;
use BuckarooDeps\GuzzleHttp\ClientInterface;
use BuckarooDeps\GuzzleHttp\Exception\GuzzleException;
use BuckarooDeps\GuzzleHttp\Exception\RequestException;

class GuzzleHttpClientV5 extends HttpClientAbstract
{
    /**
     * @var Subject
     */
    protected Subject $logger;
    protected ClientInterface $httpClient;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $logger = $config->getLogger();
        parent::__construct($logger);
        $this->logger = $logger;

        $this->httpClient = new Client([
            'timeout' => (int)($config->getTimeout() ?? self::TIMEOUT),
            'connect_timeout' => (int)($config->getConnectTimeout() ?? self::CONNECT_TIMEOUT),
        ]);
    }

    /**
     * @param string $url
     * @param array $headers
     * @param string $method
     * @param string|null $data
     * @return array|mixed
     * @throws TransferException
     * @throws BuckarooException|GuzzleException
     */

    public function call(string $url, array $headers, string $method, string $data = null)
    {
        $headers = $this->convertHeadersFormat($headers);

        $request = $this->httpClient->createRequest($method, $url, [
            'headers' => $headers,
            'body' => $data,
        ]);

        try
        {
            $response = $this->httpClient->send($request);

            $result = (string) $response->getBody();

            $this->logger->info('RESPONSE HEADERS: ' . json_encode($response->getHeaders()));
            $this->logger->info('RESPONSE BODY: ' . $response->getBody());
        } catch (RequestException $e) {
            throw new TransferException($this->logger, "Transfer failed", 0, $e);
        }

        $result = $this->getDecodedResult($response, $result);

        return [
            $response,
            $result,
        ];
    }
}
