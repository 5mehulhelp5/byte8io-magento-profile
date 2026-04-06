<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Model;

use Magento\Store\Api\Data\StoreInterface;
use Byte8\Profile\Api\Data\ProfileInterface;

/**
 * Interface RegistryLocatorInterface
 */
interface RegistryLocatorInterface
{
    const CURRENT_PROFILE = 'byte8_profile';

    /**
     * @return ProfileInterface|Profile
     */
    public function getProfile();

    /**
     * @return StoreInterface
     */
    public function getStore();
}
