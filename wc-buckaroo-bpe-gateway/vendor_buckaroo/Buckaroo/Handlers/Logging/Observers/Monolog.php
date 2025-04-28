<?php

namespace BuckarooDeps\Buckaroo\Handlers\Logging\Observers;

use BuckarooDeps\Buckaroo\Handlers\Logging\Observer;
use BuckarooDeps\Monolog\Handler\StreamHandler;
use BuckarooDeps\Monolog\Logger;
use BuckarooDeps\Psr\Log\LoggerInterface;

class Monolog implements Observer
{
    protected LoggerInterface $log;

    public function __construct()
    {
        $this->log = new Logger('BuckarooDeps\Buckaroo log');
        $this->log->pushHandler(new StreamHandler('php://stdout', Logger::INFO));
    }

    public function handle(string $method, string $message, array $context = [])
    {
        $this->log->$method($message, $context);
    }
}
