<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\ProfileSchedule\Model\Schedule;

use Magento\Framework\Exception\CronException;

/**
 * Interface CronExpressionValidatorInterface
 * used to validate cron expression
 */
interface CronExpressionValidatorInterface
{
    /**
     * @param string $cronExpression
     * @return bool
     * @throws CronException
     */
    public function isValid(string $cronExpression): bool;
}
