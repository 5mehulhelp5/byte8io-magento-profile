<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Controller\Adminhtml\ProfileSchedule;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * @inheritDoc
 */
class Edit extends Action implements HttpGetActionInterface
{
    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Byte8_Profile::manage';

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($this->getRequest()->getParam('popup')) {
            $resultPage->addHandle(['popup', 'Byte8_Profile_new_popup']);
            $pageConfig = $resultPage->getConfig();
            $pageConfig->addBodyClass('profile-popup');
        } else {
            $resultPage
                ->setActiveMenu('Byte8_Profile::profile_schedule')
                ->addBreadcrumb(__('Profile Schedule'), __('Profile Schedule'))
                ->addBreadcrumb(
                    $id ? __('Manage Profile Schedule') : __('New Profile Schedule'),
                    $id ? __('Manage Profile Schedule') : __('New Profile Schedule')
                );
        }

        $resultPage->getConfig()->getTitle()->prepend(
            $id ? __('Manage Profile Schedule') : __('New Profile Schedule')
        );

        return $resultPage;
    }
}
