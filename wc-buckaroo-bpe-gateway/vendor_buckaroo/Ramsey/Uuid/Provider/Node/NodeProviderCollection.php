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

namespace BuckarooDeps\Ramsey\Uuid\Provider\Node;

use BuckarooDeps\Ramsey\Collection\AbstractCollection;
use BuckarooDeps\Ramsey\Uuid\Provider\NodeProviderInterface;
use BuckarooDeps\Ramsey\Uuid\Type\Hexadecimal;

/**
 * A collection of NodeProviderInterface objects
 *
 * @extends AbstractCollection<NodeProviderInterface>
 */
class NodeProviderCollection extends AbstractCollection
{
    public function getType(): string
    {
        return NodeProviderInterface::class;
    }

    /**
     * Re-constructs the object from its serialized form
     *
     * @param string $serialized The serialized PHP string to unserialize into
     *     a UuidInterface instance
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     * @psalm-suppress RedundantConditionGivenDocblockType
     */
    public function unserialize($serialized): void
    {
        /** @var array<array-key, NodeProviderInterface> $data */
        $data = unserialize($serialized, [
            'allowed_classes' => [
                Hexadecimal::class,
                RandomNodeProvider::class,
                StaticNodeProvider::class,
                SystemNodeProvider::class,
            ],
        ]);

        $this->data = array_filter(
            $data,
            function ($unserialized): bool {
                return $unserialized instanceof NodeProviderInterface;
            }
        );
    }
}
