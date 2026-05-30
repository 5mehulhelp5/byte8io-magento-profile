<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Tidies up residual state left by the SoftCommerce → Byte8 migration:
 *   1. Deduplicates authorization_rule rows that share the same
 *      (role_id, resource_id) — created when several legacy
 *      SoftCommerce_Profile* modules were collapsed to a single
 *      Byte8_Profile resource namespace by MigrateFromSoftCommerce.
 *   2. Removes orphaned patch_list entries pointing at SoftCommerce\Profile*
 *      classes that no longer exist in this codebase.
 *   3. Removes cron_schedule rows targeting legacy softcommerce_profile_*
 *      job codes; the live jobs run under byte8_profile_* now.
 */
class CleanupAfterSoftCommerceMigration implements DataPatchInterface
{
    /**
     * Legacy PHP namespace prefixes whose Setup patches were folded
     * into Byte8_Profile and whose patch_list rows are now orphans.
     */
    private const LEGACY_NAMESPACES = [
        'SoftCommerce\\Profile',
        'SoftCommerce\\ProfileHistory',
        'SoftCommerce\\ProfileQueue',
        'SoftCommerce\\ProfileSchedule',
        'SoftCommerce\\ProfileConfig',
    ];

    private const LEGACY_CRON_JOB_PREFIX = 'softcommerce_profile';

    public function __construct(
        private ModuleDataSetupInterface $moduleDataSetup
    ) {
    }

    /**
     * @inheritDoc
     */
    public function apply(): self
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $this->deduplicateAuthorizationRules();
        $this->cleanupOrphanedPatchList();
        $this->cleanupStaleCronSchedule();

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    /**
     * Drop duplicate (role_id, resource_id) rows, keeping the one with the lowest rule_id.
     */
    private function deduplicateAuthorizationRules(): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $tableName = $this->moduleDataSetup->getTable('authorization_rule');

        if (!$connection->isTableExists($tableName)) {
            return;
        }

        $quoted = $connection->quoteIdentifier($tableName);
        $connection->query(sprintf(
            'DELETE r1 FROM %1$s AS r1'
            . ' INNER JOIN %1$s AS r2'
            . ' ON r1.role_id = r2.role_id'
            . ' AND r1.resource_id = r2.resource_id'
            . ' AND r1.rule_id > r2.rule_id',
            $quoted
        ));
    }

    /**
     * Delete patch_list rows whose PHP class lives under a legacy SoftCommerce_Profile* namespace.
     */
    private function cleanupOrphanedPatchList(): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $tableName = $this->moduleDataSetup->getTable('patch_list');

        if (!$connection->isTableExists($tableName)) {
            return;
        }

        foreach (self::LEGACY_NAMESPACES as $namespace) {
            // MySQL LIKE treats '\' as escape, so each literal backslash in the
            // class name has to be doubled in the pattern.
            $pattern = str_replace('\\', '\\\\', $namespace) . '\\\\%';
            $connection->delete($tableName, ['patch_name LIKE ?' => $pattern]);
        }
    }

    /**
     * Delete cron_schedule rows whose job_code targets a legacy softcommerce_profile_* job.
     */
    private function cleanupStaleCronSchedule(): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $tableName = $this->moduleDataSetup->getTable('cron_schedule');

        if (!$connection->isTableExists($tableName)) {
            return;
        }

        $connection->delete(
            $tableName,
            ['job_code LIKE ?' => self::LEGACY_CRON_JOB_PREFIX . '%']
        );
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [
            MigrateFromSoftCommerce::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
