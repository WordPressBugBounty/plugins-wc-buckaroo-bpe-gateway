<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace BuckarooDeps\Ramsey\Uuid\Generator;

use BuckarooDeps\Ramsey\Uuid\Converter\TimeConverterInterface;
use BuckarooDeps\Ramsey\Uuid\Exception\InvalidArgumentException;
use BuckarooDeps\Ramsey\Uuid\Exception\RandomSourceException;
use BuckarooDeps\Ramsey\Uuid\Exception\TimeSourceException;
use BuckarooDeps\Ramsey\Uuid\Provider\NodeProviderInterface;
use BuckarooDeps\Ramsey\Uuid\Provider\TimeProviderInterface;
use BuckarooDeps\Ramsey\Uuid\Type\Hexadecimal;
use Throwable;

use function ctype_xdigit;
use function dechex;
use function hex2bin;
use function is_int;
use function pack;
use function sprintf;
use function str_pad;
use function strlen;

use const STR_PAD_LEFT;

/**
 * DefaultTimeGenerator generates strings of binary data based on a node ID,
 * clock sequence, and the current time
 */
class DefaultTimeGenerator implements TimeGeneratorInterface
{
    /**
     * @var NodeProviderInterface
     */
    private $nodeProvider;

    /**
     * @var TimeConverterInterface
     */
    private $timeConverter;

    /**
     * @var TimeProviderInterface
     */
    private $timeProvider;

    public function __construct(
        NodeProviderInterface $nodeProvider,
        TimeConverterInterface $timeConverter,
        TimeProviderInterface $timeProvider
    ) {
        $this->nodeProvider = $nodeProvider;
        $this->timeConverter = $timeConverter;
        $this->timeProvider = $timeProvider;
    }

    /**
     * @throws InvalidArgumentException if the parameters contain invalid values
     * @throws RandomSourceException if random_int() throws an exception/error
     *
     * @inheritDoc
     */
    public function generate($node = null, ?int $clockSeq = null): string
    {
        if ($node instanceof Hexadecimal) {
            $node = $node->toString();
        }

        $node = $this->getValidNode($node);

        if ($clockSeq === null) {
            try {
                // This does not use "stable storage"; see RFC 4122, Section 4.2.1.1.
                $clockSeq = random_int(0, 0x3fff);
            } catch (Throwable $exception) {
                throw new RandomSourceException(
                    $exception->getMessage(),
                    (int) $exception->getCode(),
                    $exception
                );
            }
        }

        $time = $this->timeProvider->getTime();

        $uuidTime = $this->timeConverter->calculateTime(
            $time->getSeconds()->toString(),
            $time->getMicroseconds()->toString()
        );

        $timeHex = str_pad($uuidTime->toString(), 16, '0', STR_PAD_LEFT);

        if (strlen($timeHex) !== 16) {
            throw new TimeSourceException(sprintf(
                'The generated time of \'%s\' is larger than expected',
                $timeHex
            ));
        }

        $timeBytes = (string) hex2bin($timeHex);

        return $timeBytes[4] . $timeBytes[5] . $timeBytes[6] . $timeBytes[7]
            . $timeBytes[2] . $timeBytes[3]
            . $timeBytes[0] . $timeBytes[1]
            . pack('n*', $clockSeq)
            . $node;
    }

    /**
     * Uses the node provider given when constructing this instance to get
     * the node ID (usually a MAC address)
     *
     * @param string|int|null $node A node value that may be used to override the node provider
     *
     * @return string 6-byte binary string representation of the node
     *
     * @throws InvalidArgumentException
     */
    private function getValidNode($node): string
    {
        if ($node === null) {
            $node = $this->nodeProvider->getNode();
        }

        // Convert the node to hex, if it is still an integer.
        if (is_int($node)) {
            $node = dechex($node);
        }

        if (!ctype_xdigit((string) $node) || strlen((string) $node) > 12) {
            throw new InvalidArgumentException('Invalid node value');
        }

        return (string) hex2bin(str_pad((string) $node, 12, '0', STR_PAD_LEFT));
    }
}
