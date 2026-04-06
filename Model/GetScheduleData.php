<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8_Profile\Model;

use Byte8_Profile\Api\Data\ScheduleInterface;
use Byte8_Profile\Model\ResourceModel;

/**
 * @inheritDoc
 */
class GetScheduleData implements GetScheduleDataInterface
{
    /**
     * @var array|null
     */
    private ?array $data = null;

    /**
     * @param ResourceModel\Schedule $resource
     */
    public function __construct(
        private readonly ResourceModel\Schedule $resource
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute(): array
    {
        if (null === $this->data) {
            $this->data = $this->getData();
        }

        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function applySearchCriteria(string $searchKey, $searchValue): array
    {
        return array_filter($this->data ?: [], function ($item) use ($searchKey, $searchValue) {
            return is_array($searchValue)
                ? isset($item[$searchKey]) && in_array($item[$searchKey], $searchValue)
                : isset($item[$searchKey]) && $item[$searchKey] == $searchValue;
        });
    }

    /**
     * @return array
     */
    private function getData(): array
    {
        $connection = $this->resource->getConnection();
        $select = $connection->select()
            ->from($connection->getTableName(ScheduleInterface::DB_TABLE_NAME));
        return $connection->fetchAll($select);
    }
}
