<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\ProfileSchedule\Model\ResourceModel\Schedule;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Byte8\ProfileSchedule\Api\Data\ScheduleInterface;
use Byte8\ProfileSchedule\Model\Schedule;
use Byte8\ProfileSchedule\Model\ResourceModel;

/**
 * @inheritDoc
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected $_idFieldName = ScheduleInterface::ENTITY_ID;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(Schedule::class, ResourceModel\Schedule::class);
    }

    /**
     * @param $status
     * @return $this
     */
    public function addStatusFilter($status)
    {
        $this->addFieldToFilter(ScheduleInterface::STATUS, ['eq' => $status]);
        return $this;
    }
}
