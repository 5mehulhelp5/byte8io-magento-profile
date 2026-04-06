<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Model\ResourceModel;

use Byte8\Core\Model\ResourceModel\AbstractResource;
use Byte8\Profile\Api\Data\QueueInterface;

/**
 * @inheritDoc
 */
class Queue extends AbstractResource
{
    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->_init(QueueInterface::DB_TABLE_NAME, QueueInterface::ENTITY_ID);
    }
}
