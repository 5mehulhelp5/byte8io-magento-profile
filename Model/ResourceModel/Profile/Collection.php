<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Byte8\Profile\Model\ResourceModel\Profile;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Byte8\Profile\Model\Profile;
use Byte8\Profile\Model\ResourceModel;

/**
 * @inheritDoc
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'byte8_profile_entity_collection';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(Profile::class, ResourceModel\Profile::class);
    }
}
