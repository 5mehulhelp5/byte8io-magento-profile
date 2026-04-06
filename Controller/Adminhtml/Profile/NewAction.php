<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Controller\Adminhtml\Profile;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Byte8\Profile\Controller\Adminhtml\Profile as ProfileController;

/**
 * @inheritDoc
 */
class NewAction extends ProfileController
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
