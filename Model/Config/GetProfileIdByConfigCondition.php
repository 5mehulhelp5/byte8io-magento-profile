<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Model\Config;

use Byte8\Profile\Api\Data\ConfigInterface;
use Byte8\Profile\Model\ResourceModel\Config;

/**
 * @inheritDoc
 */
class GetProfileIdByConfigCondition implements GetProfileIdByConfigConditionInterface
{
    /**
     * @var array
     */
    private array $data = [];

    /**
     * @param Config $resource
     */
    public function __construct(
        private readonly Config $resource
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute(string $path, $value = null): array
    {
        if (isset($this->data[$path])) {
            return $this->data[$path] ?? [];
        }

        $data = $this->resource->getDataByPath($path, [ConfigInterface::PARENT_ID, ConfigInterface::VALUE]);
        if (null !== $value) {
            $data = array_filter($data, function ($item) use ($value) {
                return isset($item[ConfigInterface::VALUE]) && $item[ConfigInterface::VALUE] == $value;
            });
        }
        $this->data[$path] = array_column($data, ConfigInterface::PARENT_ID);

        return $this->data[$path];
    }
}
