<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Ui\DataProvider\Schedule\Modifier\Form;

use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Byte8\Profile\Ui\DataProvider\Modifier\Form\MetadataPoolInterface;

/**
 * @inheritDoc
 */
class HistoryListModifier implements ModifierInterface
{
    private const DATA_COMPONENT = 'profile_history_listing';
    private const DATA_SOURCE = 'history_list';
    private const FORM_NAME = 'byte8_profile_schedule_form';

    /**
     * @param MetadataPoolInterface $metadataPool
     * @param RequestInterface $request
     */
    public function __construct(
        private readonly MetadataPoolInterface $metadataPool,
        private readonly RequestInterface $request
    ) {
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data): array
    {
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta): array
    {
        // Only touch $meta if the target fieldset is already defined there.
        // This modifier is scoped to the schedule form; running it on a form
        // without this fieldset would produce a partial meta entry with no
        // componentType and crash UiComponentFactory::mergeMetadataItem().
        if (!isset($meta[self::DATA_SOURCE])) {
            return $meta;
        }

        if (!$this->canIncludeListing()) {
            return $meta;
        }

        $metaData = $this->metadataPool->get(self::FORM_NAME);
        if (!isset($metaData['children'][self::DATA_SOURCE]['children'][self::DATA_COMPONENT])) {
            return $meta;
        }

        $meta[self::DATA_SOURCE]['arguments']['data']['config']['visible'] = true;
        $meta[self::DATA_SOURCE]['children'][self::DATA_COMPONENT]['arguments']['data']['config']['autoRender'] = true;

        return $meta;
    }

    /**
     * @return bool
     */
    private function canIncludeListing(): bool
    {
        return $this->request->getParam('id')
            && !$this->request->getParam('isModal', false)
            && !$this->request->getParam('popup', false);
    }
}
