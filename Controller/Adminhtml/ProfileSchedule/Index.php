<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8_Profile\Controller\Adminhtml\ProfileSchedule;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * @inheritDoc
 */
class Index extends Action
{
    /**
     * @inheritDoc
     */
    public const ADMIN_RESOURCE = 'Byte8_Profile::manage';

    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Action\Context $context,
        private readonly PageFactory $resultPageFactory,
        private readonly ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('grid');
            return $resultForward;
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Byte8_Profile::profile_schedule');
        $resultPage->getConfig()->getTitle()->prepend(__('Profile Schedules'));
        $resultPage->addBreadcrumb(__('Profile Schedules'), __('Profile Schedules'));
        $resultPage->addBreadcrumb(__('Manage Profile Schedules'), __('Manage Profile Schedules'));

        return $resultPage;
    }
}
