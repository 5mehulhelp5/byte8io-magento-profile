<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Ui\DataProvider\Profile\Config\Form;

use Magento\Framework\Exception\LocalizedException;

/**
 * Interface ConfigDataScopeStorageInterface
 */
interface ConfigDataScopeStorageInterface
{
    /**
     * @param array $request
     * @return array
     * @throws LocalizedException
     */
    public function saveFormData(array $request): array;
}
