<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Model\ScheduleProcessor;

use Byte8\Core\Framework\MessageCollectorInterface;
use Byte8\Core\Framework\MessageCollectorInterfaceFactory;

/**
 * @inheritDoc
 */
abstract class QueueProcessor implements QueueProcessorInterface
{
    /**
     * New message collector for structured message handling
     * Replaces MessageStorage with format-agnostic collection
     *
     * @var MessageCollectorInterface
     */
    protected MessageCollectorInterface $messageCollector;

    /**
     * @param MessageCollectorInterfaceFactory $messageCollectorFactory
     */
    public function __construct(protected readonly MessageCollectorInterfaceFactory $messageCollectorFactory)
    {
        $this->messageCollector = $this->messageCollectorFactory->create();
    }

    /**
     * @return void
     */
    protected function init(): void
    {
        $this->messageCollector->reset();
    }
}
