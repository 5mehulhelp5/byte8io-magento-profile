<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Model\ResourceModel\Config;

/**
 * @inheritDoc
 */
class GetConfigDataCache implements GetConfigDataInterface
{
    /**
     * @var array
     */
    private array $cache = [];

    /**
     * @param GetConfigData $resource
     */
    public function __construct(
        private readonly GetConfigData $resource
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute(int $parentId): array
    {
        if (!isset($this->cache[$parentId])) {
            $this->cache[$parentId] = $this->resource->execute($parentId);
        }

        return $this->cache[$parentId];
    }
}
