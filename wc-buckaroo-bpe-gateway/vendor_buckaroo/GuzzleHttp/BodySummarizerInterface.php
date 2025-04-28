<?php

namespace BuckarooDeps\GuzzleHttp;

use BuckarooDeps\Psr\Http\Message\MessageInterface;

interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message): ?string;
}
