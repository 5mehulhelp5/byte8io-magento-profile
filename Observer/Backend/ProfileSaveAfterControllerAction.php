<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Observer\Backend;

use Byte8\Profile\Model\Config\ScheduleConfigInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @inheritDoc
 */
class ProfileSaveAfterControllerAction implements ObserverInterface
{
    /**
     * @param CacheInterface $cache
     */
    public function __construct(
        private CacheInterface $cache
    ) {
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        /** @var RequestInterface $request */
        $request = $observer->getEvent()->getRequest();

        if ($profileId = $request->getParam('id')) {
            $this->cache->clean([ScheduleConfigInterface::CACHE_KEY_ONETIME_PROCESS . '_' . (int) $profileId]);
        }
    }
}
