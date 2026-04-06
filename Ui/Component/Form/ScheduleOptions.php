<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\ProfileSchedule\Ui\Component\Form;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Byte8\ProfileSchedule\Api\Data\ScheduleInterface;
use Byte8\ProfileSchedule\Model\GetScheduleDataInterface;

/**
 * @inheritDoc
 */
class ScheduleOptions implements OptionSourceInterface
{
    /**
     * @var array|null
     */
    private ?array $options = null;

    /**
     * @param GetScheduleDataInterface $getScheduleData
     * @param RequestInterface $request
     */
    public function __construct(
        private readonly GetScheduleDataInterface $getScheduleData,
        private readonly RequestInterface $request
    ) {
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        if (null === $this->options) {
            $this->options = [];
            $scheduleData = $this->getScheduleData->execute();
            if ($typeId = $this->request->getParam(ScheduleInterface::TYPE_ID)) {
                $scheduleData = $this->getScheduleData->applySearchCriteria(ScheduleInterface::TYPE_ID, $typeId);
            }

            foreach ($scheduleData as $item) {
                $this->options[] = [
                    'value' => $item[ScheduleInterface::ENTITY_ID],
                    'label' => $item[ScheduleInterface::NAME]
                ];
            }
        }

        return $this->options;
    }
}
