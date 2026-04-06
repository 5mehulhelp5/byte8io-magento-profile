<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\ProfileSchedule\Controller\Adminhtml\ProfileSchedule;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Data\Collection;
use Byte8\Profile\Model\TypeInstanceOptionsInterface;
use Byte8\ProfileSchedule\Api\Data\ScheduleInterface;
use Byte8\ProfileSchedule\Api\ScheduleRepositoryInterface;
use Byte8\ProfileSchedule\Model\ResourceModel;

/**
 * @inheritDoc
 */
class MassDelete extends AbstractMassAction
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Byte8_Profile::manage';

    /**
     * @param WriterInterface $configWriter
     * @param ScheduleRepositoryInterface $scheduleRepository
     * @param TypeInstanceOptionsInterface $typeInstanceOptions
     * @param ResourceModel\Schedule\CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param Context $context
     */
    public function __construct(
        private readonly WriterInterface $configWriter,
        private readonly ScheduleRepositoryInterface $scheduleRepository,
        private readonly TypeInstanceOptionsInterface $typeInstanceOptions,
        ResourceModel\Schedule\CollectionFactory $collectionFactory,
        Filter $filter,
        Context $context
    ) {
        parent::__construct($collectionFactory, $filter, $context);
    }

    /**
     * @inheritDoc
     */
    protected function massAction(Collection $collection): void
    {
        $result = [];
        /** @var ScheduleInterface $item */
        foreach ($collection->getItems() as $item) {
            try {
                $this->processCrontabDelete($item);
                $this->scheduleRepository->delete($item);
                $result[] = $item->getEntityId();
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        $this->messageManager->addSuccessMessage(
            __(
                'Selected schedules have been deleted. Effected IDs: %1',
                implode(', ', $result)
            )
        );
    }

    /**
     * @param ScheduleInterface $model
     * @return void
     */
    private function processCrontabDelete(ScheduleInterface $model): void
    {
        $typeId = $model->getTypeId();
        if (!$typeId || !$cronGroup = $this->typeInstanceOptions->getCronGroupByTypeId($typeId)) {
            return;
        }

        $this->configWriter->delete(
            sprintf(ScheduleInterface::CRON_SCHEDULE_PATH, $cronGroup, $typeId)
        );
    }
}
