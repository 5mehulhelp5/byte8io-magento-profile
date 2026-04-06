<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\ProfileSchedule\Model\ScheduleProcessor;

use Magento\Framework\Exception\LocalizedException;
use Byte8\Core\Framework\MessageCollectorInterface;

/**
 * Interface QueueProcessorInterface used
 * to process scheduled tasks
 */
interface QueueProcessorInterface
{
    public const IS_SCHEDULED_EVENT = 'isScheduledEvent';

    /**
     * @param int $profileId
     * @return MessageCollectorInterface
     * @throws LocalizedException
     */
    public function execute(int $profileId): MessageCollectorInterface;
}
