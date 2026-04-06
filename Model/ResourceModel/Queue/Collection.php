<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Model\ResourceModel\Queue;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;
use Byte8\Profile\Api\Data\QueueInterface;
use Byte8\Profile\Model\Queue;
use Byte8\Profile\Model\ResourceModel;

/**
 * @inheritDoc
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = QueueInterface::ENTITY_ID;

    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->_init(Queue::class, ResourceModel\Queue::class);
    }

    /**
     * @param SerializerInterface $serializer
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        SerializerInterface $serializer,
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        ?AdapterInterface $connection = null,
        ?AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * @inheritDoc
     */
    protected function _afterLoad(): Collection|AbstractCollection|static
    {
        parent::_afterLoad();
        // $this->addMetadataColumns();
        return $this;
    }

    public function addMetadataColumns(): void
    {
        foreach ($this->getItems() as $item) {
        }
    }
}
