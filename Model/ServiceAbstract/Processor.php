<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Model\ServiceAbstract;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Byte8\Core\Framework\DataStorageInterfaceFactory;
use Byte8\Core\Framework\MessageCollectorInterfaceFactory;

/**
 * @inheritDoc
 */
class Processor extends Service implements ServiceInterface
{
    /**
     * @var ProcessorInterface[]
     */
    private array $processors;

    /**
     * @param DataStorageInterfaceFactory $dataStorageFactory
     * @param MessageCollectorInterfaceFactory $messageCollectorFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     * @param array $processors
     */
    public function __construct(
        DataStorageInterfaceFactory $dataStorageFactory,
        MessageCollectorInterfaceFactory $messageCollectorFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = [],
        array $processors = []
    ) {
        $this->processors = $this->initServices($processors);
        parent::__construct(
            $dataStorageFactory,
            $messageCollectorFactory,
            $searchCriteriaBuilder,
            $data
        );
    }

    /**
     * @inheritDoc
     */
    public function execute(): void
    {
        $this->initialize();

        foreach ($this->processors as $processor) {
            $processor->execute();
        }

        $this->finalize();
    }

    /**
     * @return Processor
     */
    public function initialize(): static
    {
        $this->initTypeInstances($this->context, $this->processors);
        return parent::initialize();
    }
}
