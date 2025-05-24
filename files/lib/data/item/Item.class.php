<?php

namespace rp\data\item;

use wcf\data\DatabaseObject;
use wcf\data\ILinkableObject;
use wcf\system\language\LanguageFactory;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a item.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @property-read   int $itemID     unique id of the item
 * @property-read   int $time       timestamp of the item created
 * @property-read   string  $searchItemID       id of the item from the database
 * @property-read   array   $additionalData     array with additional data of the item
 */
final class Item extends DatabaseObject implements IRouteController, ILinkableObject
{
    /**
     * item icon expire time (days)
     * @var int
     */
    const ITEM_ICON_CACHE_EXPIRE = 7;

    /**
     * item icon local cache location
     * @var string
     */
    const ITEM_ICON_CACHE_LOCATION = 'images/item/icons/%s.%s';

    /**
     * urls of this item icon
     */
    protected string $url = '';

    /**
     * Returns the html code to display the icon.
     */
    public function getIcon(?int $size = null): string
    {
        return \sprintf(
            '<img src="%s" style="width: %dpx; height: %dpx;" alt="" class="itemIcon"',
            StringUtil::encodeHTML($this->getIconPath()),
            $size,
            $size
        );
    }

    /**
     * Returns full path to icon.
     */
    public function getIconPath(): string
    {
        if (!$this->icon) {
            return WCF::getPath() . 'images/placeholderTiny.png';
        }

        if (empty($this->url)) {
            $cachedFilename = \sprintf(
                self::ITEM_ICON_CACHE_LOCATION,
                \md5(\mb_strtolower($this->icon)),
                $this->itemIconFileExtension ?: $this->iconExtension
            );

            $cachedFilePath = RP_DIR . $cachedFilename;

            if (
                \file_exists($cachedFilePath) &&
                \filemtime($cachedFilePath) > (TIME_NOW - self::ITEM_ICON_CACHE_EXPIRE * 86400)
            ) {
                $this->url = WCF::getPath('rp') . $cachedFilename;
            } else {
                $this->url = LinkHandler::getInstance()->getLink('ItemIconDownload', [
                    'application' => 'rp',
                    'forceFrontend' => true,
                ], 'itemID=' . $this->itemID);
            }
        }

        return $this->url;
    }

    [\Override]
    public function getLink(): string
    {
        return LinkHandler::getInstance()->getLink('Item', [
            'application' => 'rp',
            'forceFrontend' => true,
            'object' => $this,
        ]);
    }

    [\Override]
    public function getTitle(): string
    {
        $itemName = $this->additionalData[WCF::getLanguage()->languageCode]['name'] ?? null;

        if ($itemName === null) {
            foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
                $itemName = $this->additionalData[$language->languageCode]['name'] ?? null;
                if ($itemName !== null) {
                    return $itemName;
                }
            }
        }

        return $itemName ?? 'Unknown';
    }

    public function getTooltip(): string
    {
        $tooltip = $this->additionalData[WCF::getLanguage()->languageCode]['tooltip'] ?? null;

        if ($tooltip === null) {
            foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
                $tooltip = $this->additionalData[$language->languageCode]['tooltip'] ?? null;
                if ($tooltip !== null) {
                    return $tooltip;
                }
            }
        }

        return $tooltip ?? '';
    }

    [\Override]
    protected function handleData($data): void
    {
        parent::handleData($data);

        $this->data['additionalData'] = $this->parseAdditionalData($data['additionalData'] ?? '');
    }

    /**
     * Parses and returns additional data from serialized input.
     */
    private function parseAdditionalData(string $serializedData): array
    {
        $parsedData = @unserialize($serializedData);

        // Check if unserialized data is an array, otherwise return an empty array
        if (\is_array($parsedData)) {
            return $parsedData;
        }

        return [];
    }

    [\Override]
    public function __get($name): mixed
    {
        $value = parent::__get($name);

        if ($value === null && \array_key_exists($name, $this->data['additionalData'])) {
            $value = $this->data['additionalData'][$name];
        }

        return $value;
    }
}
