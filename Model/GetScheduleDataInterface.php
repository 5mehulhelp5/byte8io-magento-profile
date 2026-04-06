<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Model;

/**
 * Interface GetScheduleDataInterface used to
 * provide schedule data in array format.
 */
interface GetScheduleDataInterface
{
    /**
     * @return array
     */
    public function execute(): array;

    /**
     * @param string $searchKey
     * @param string|array|mixed $searchValue
     * @return array
     */
    public function applySearchCriteria(string $searchKey, $searchValue): array;
}
