<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Renames legacy softcommerce_profile_* tables to byte8_profile_*.
 *
 * Uses a Schema Patch (not Data Patch) because RENAME TABLE and TRUNCATE
 * are DDL statements that cannot run inside transactions.
 *
 * Safe on fresh installs — all operations check for table existence first.
 */
class MigrateTablesFromSoftCommerce implements SchemaPatchInterface
{
    private const TABLE_RENAMES = [
        'softcommerce_profile_entity' => 'byte8_profile_entity',
        'softcommerce_profile_config' => 'byte8_profile_config',
        'softcommerce_profile_history' => 'byte8_profile_history',
        'softcommerce_profile_queue' => 'byte8_profile_queue',
        'softcommerce_profile_schedule' => 'byte8_profile_schedule',
    ];

    /**
     * Transient tables to truncate before renaming.
     * These can be very large on production and have no business value.
     */
    private const TABLES_TO_TRUNCATE = [
        'softcommerce_profile_history',
        'softcommerce_profile_queue',
    ];

    public function __construct(
        private SchemaSetupInterface $schemaSetup
    ) {
    }

    /**
     * @inheritDoc
     */
    public function apply(): self
    {
        $this->schemaSetup->startSetup();
        $connection = $this->schemaSetup->getConnection();

        // Truncate transient tables first
        $connection->query('SET FOREIGN_KEY_CHECKS = 0');
        foreach (self::TABLES_TO_TRUNCATE as $table) {
            $tableName = $this->schemaSetup->getTable($table);
            if ($connection->isTableExists($tableName)) {
                $connection->truncateTable($tableName);
            }
        }
        $connection->query('SET FOREIGN_KEY_CHECKS = 1');

        // Rename tables
        foreach (self::TABLE_RENAMES as $oldTable => $newTable) {
            $oldTableName = $this->schemaSetup->getTable($oldTable);
            $newTableName = $this->schemaSetup->getTable($newTable);

            if (!$connection->isTableExists($oldTableName)) {
                continue;
            }

            if ($connection->isTableExists($newTableName)) {
                $count = (int) $connection->fetchOne(
                    $connection->select()->from($newTableName, ['COUNT(*)'])
                );

                if ($count > 0) {
                    continue;
                }

                $connection->query('SET FOREIGN_KEY_CHECKS = 0');
                $connection->dropTable($newTableName);
                $connection->query(sprintf(
                    'RENAME TABLE %s TO %s',
                    $connection->quoteIdentifier($oldTableName),
                    $connection->quoteIdentifier($newTableName)
                ));
                $connection->query('SET FOREIGN_KEY_CHECKS = 1');
            } else {
                $connection->query('SET FOREIGN_KEY_CHECKS = 0');
                $connection->query(sprintf(
                    'RENAME TABLE %s TO %s',
                    $connection->quoteIdentifier($oldTableName),
                    $connection->quoteIdentifier($newTableName)
                ));
                $connection->query('SET FOREIGN_KEY_CHECKS = 1');
            }
        }

        $this->schemaSetup->endSetup();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
