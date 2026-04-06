<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\ProfileSchedule\Model;

/**
 * Interface IsActiveScheduleInterface used to
 * provide status of scheduler.
 */
interface IsActiveScheduleInterface
{
    /**
     * @param string $typeId
     * @return bool
     */
    public function execute(string $typeId): bool;
}
