<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Model\ResourceModel;

use Byte8\Core\Model\ResourceModel\AbstractResource;
use Byte8\Profile\Api\Data\ProfileInterface;

/**
 * @inheritDoc
 */
class Profile extends AbstractResource
{
    /**
     * @var string
     */
    protected $_useIsObjectNew = true;

    /**
     * @var string
     */
    protected string $_eventPrefix = 'byte8_profile_entity_resource_model';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ProfileInterface::DB_TABLE_NAME, ProfileInterface::ENTITY_ID);
    }
}
