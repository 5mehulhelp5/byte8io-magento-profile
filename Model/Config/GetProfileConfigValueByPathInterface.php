<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Model\Config;

use Magento\Framework\Exception\LocalizedException;

/**
 * Interface GetProfileConfigValueByPathInterface used to
 * obtain profile config value by XML path.
 */
interface GetProfileConfigValueByPathInterface
{
    /**
     * @param string $path
     * @return array
     * @throws LocalizedException
     */
    public function execute(string $path): array;
}