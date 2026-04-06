<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Api\Service;

/**
 * Interface PurgeProfileDataInterface
 * Service for purging profile-related data from database tables
 */
interface PurgeProfileDataInterface
{
    /**
     * Purge all data from byte8_* tables except byte8_profile_entity
     *
     * @param int|null $profileId Optional profile ID to purge data for specific profile
     * @return void
     * @throws \Exception
     */
    public function execute(?int $profileId = null): void;

    /**
     * Get list of tables that will be purged
     *
     * @return string[]
     */
    public function getTablesToPurge(): array;

    /**
     * Check if purge operation is allowed
     *
     * @return bool
     */
    public function canPurge(): bool;
}