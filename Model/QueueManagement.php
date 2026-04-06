<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Serialize\SerializerInterface;
use Byte8\Core\Model\Trait\ConnectionTrait;
use Byte8\Profile\Api\Data\QueueInterface;
use Byte8\Profile\Api\QueueManagementInterface;
use Byte8\Profile\Model\ResourceModel\Queue as ResourceModel;
/**
 * @inhertidoc
 */
class QueueManagement implements QueueManagementInterface
{
    use ConnectionTrait;

    /**
     * @param ResourceConnection $resourceConnection
     * @param ResourceModel $resourceModel
     * @param SerializerInterface $serializer
     */
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
        private readonly ResourceModel $resourceModel,
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addToQueue(
        int|string $subjectId,
        string $subjectTypeId,
        array $metadata = [],
        array $message = []
    ): int
    {
        $request = [
            QueueInterface::SUBJECT_ENTITY_ID => $subjectId,
            QueueInterface::SUBJECT_TYPE_ID => $subjectTypeId,
            QueueInterface::METADATA => $this->serializer->serialize($metadata),
            QueueInterface::MESSAGE => $this->serializer->serialize($message)
        ];

        return (int) $this->getConnection()->insertOnDuplicate(
            $this->getConnection()->getTableName(QueueInterface::DB_TABLE_NAME),
            $request
        );
    }

    /**
     * @inheritDoc
     */
    public function removeFromQueueByEntityId(array $entityIds): int
    {
        return (int) $this->getConnection()->delete(
            $this->getConnection()->getTableName(QueueInterface::DB_TABLE_NAME),
            [QueueInterface::ENTITY_ID . ' IN (?)' => $entityIds]
        );
    }

    /**
     * @inheritDoc
     */
    public function removeFromQueue(array|int|string $subjectEntityId, string $subjectTypeId): int
    {
        return (int) $this->getConnection()->delete(
            $this->getConnection()->getTableName(QueueInterface::DB_TABLE_NAME),
            [
                QueueInterface::SUBJECT_TYPE_ID . ' IN (?)' => is_array($subjectEntityId)
                    ? $subjectEntityId
                    : [$subjectEntityId],
                QueueInterface::SUBJECT_TYPE_ID . ' = ?' => $subjectTypeId
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function removeFromQueueBySubjectTypeId(string $subjectTypeId): int
    {
        return (int) $this->getConnection()->delete(
            $this->getConnection()->getTableName(QueueInterface::DB_TABLE_NAME),
            [QueueInterface::SUBJECT_TYPE_ID . ' = ?' => $subjectTypeId]
        );
    }

    /**
     * @inheritDoc
     */
    public function clearQueue(): void
    {
        $this->getConnection()->truncateTable(
            $this->getConnection()->getTableName(QueueInterface::DB_TABLE_NAME)
        );
    }

    /**
     * @inheritDoc
     */
    public function updateQueueStatusByEntityId(array $entityIds, string $status): int
    {
        return (int) $this->getConnection()->update(
            $this->getConnection()->getTableName(QueueInterface::DB_TABLE_NAME),
            [QueueInterface::STATUS => $status],
            [QueueInterface::ENTITY_ID . ' IN (?)' => $entityIds]
        );
    }

    /**
     * @inheritDoc
     */
    public function updateQueueStatusBySubjectEntityId(
        int|string $subjectEntityId,
        string $subjectTypeId,
        string $status
    ): int
    {
        return (int) $this->getConnection()->update(
            $this->getConnection()->getTableName(QueueInterface::DB_TABLE_NAME),
            [QueueInterface::STATUS => $status],
            [
                QueueInterface::SUBJECT_ENTITY_ID . ' = ?' => $subjectEntityId,
                QueueInterface::SUBJECT_TYPE_ID . ' = ?' => $subjectTypeId
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function updateQueueData(array $bindData, array|string $where): int
    {
        return (int) $this->getConnection()->update(
            $this->getConnection()->getTableName(QueueInterface::DB_TABLE_NAME),
            $bindData,
            $where
        );
    }

    /**
     * @inheritDoc
     */
    public function saveMultipleOnDuplicate(array $data, array $fields = []): int
    {
        return (int) $this->getConnection()->insertOnDuplicate(
            $this->getConnection()->getTableName(QueueInterface::DB_TABLE_NAME),
            $this->resourceModel->buildBatchDataForSave($data),
            $fields
        );
    }
}
