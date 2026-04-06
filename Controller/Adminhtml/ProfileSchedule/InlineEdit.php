<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Controller\Adminhtml\ProfileSchedule;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Byte8\Profile\Api\Data\ScheduleInterface;
use Byte8\Profile\Model\ResourceModel\Schedule;

/**
 * @inheritDoc
 */
class InlineEdit extends Action implements HttpPostActionInterface
{
    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Byte8_Profile::manage';

    /**
     * @param Schedule $resource
     * @param Context $context
     */
    public function __construct(
        private readonly Schedule $resource,
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute(): ResultInterface
    {
        $errors = [];

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        if (!$this->canExecute() || !$request = $this->getRequest()->getParam('items', [])) {
            $resultJson->setData([
                'messages' => __('Could not process inline save'),
                'error' => true,
            ]);
            return $resultJson;
        }

        foreach ($request as $item) {
            if (!$entityId = $item[ScheduleInterface::ENTITY_ID] ?? null) {
                continue;
            }

            try {
                $this->resource->insertOnDuplicate([$item]);
            } catch (\Exception $e) {
                $errors[] = __(
                    '[ID: %value] %message',
                    [
                        'value' => $entityId,
                        'message' => $e->getMessage()
                    ]
                );
            }
        }

        $resultJson->setData([
            'messages' => $errors,
            'error' => count($errors),
        ]);

        return $resultJson;
    }

    /**
     * @return bool
     */
    private function canExecute(): bool
    {
        return $this->getRequest()->isXmlHttpRequest() && $this->getRequest()->isPost();
    }
}
