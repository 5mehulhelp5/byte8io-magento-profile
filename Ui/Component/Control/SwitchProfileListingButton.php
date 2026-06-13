<?php
/**
 * Copyright © Byte8 Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Byte8\Profile\Ui\Component\Control;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Byte8\Core\Ui\Component\Control\FontAwesomeButton;

/**
 * @inheritDoc
 */
class SwitchProfileListingButton implements ButtonProviderInterface
{
    private const EXPORT = 'export';
    private const IMPORT = 'import';

    /**
     * @param RequestInterface $request
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        protected readonly RequestInterface $request,
        protected readonly UrlInterface $urlBuilder
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        $baseUrl = "{$this->urlBuilder->getBaseUrl()}admin";
        $key = $this->request->getParam('key');
        $currentUrl = $this->urlBuilder->getCurrentUrl();
        $currentUrl = str_replace([$baseUrl, 'key', $key, 'index'], '', $currentUrl);
        $urlParts = explode('/', $currentUrl);
        $urlParts = array_filter($urlParts);
        $profileEntity = (string) current($urlParts);

        if (!$typeId = $this->getTypeId((string) next($urlParts))) {
            return [];
        }

        $entityName = explode('_', $profileEntity);
        $entityName = end($entityName);
        $title = $entityName . ' ' . key($typeId);
        $typeId = current($typeId);

        return [
            'title' => __('Switch To %1', ucwords($title)),
            'on_click' => sprintf(
                "location.href = '%s';",
                $this->urlBuilder->getUrl("$profileEntity/$typeId/index")
            ),
            'class_name' => FontAwesomeButton::class,
            FontAwesomeButton::FONT_NAME => 'fa-solid fa-shuffle',
            'class' => 'secondary',
            'sort_order' => 10
        ];
    }

    /**
     * @param string $typeId
     * @return array
     */
    private function getTypeId(string $typeId): array
    {
        switch ($typeId) {
            case self::EXPORT:
                return [self::EXPORT => self::EXPORT];
            case self::IMPORT:
                return [self::IMPORT => self::IMPORT];
        }

        $result = [];
        if (strpos($typeId, self::EXPORT) !== false) {
            $result[self::IMPORT] = str_replace(self::EXPORT, self::IMPORT, $typeId);
        } elseif (strpos($typeId, self::IMPORT) !== false) {
            $result[self::EXPORT] = str_replace(self::IMPORT, self::EXPORT, $typeId);
        }

        return $result;
    }
}
