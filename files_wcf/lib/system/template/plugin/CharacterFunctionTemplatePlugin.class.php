<?php

namespace wcf\system\template\plugin;

use rp\data\character\CharacterProfile;
use wcf\system\template\TemplateEngine;
use wcf\util\ClassUtil;
use wcf\util\StringUtil;

/**
 * Template function plugin which generates links to characters.
 * 
 * Attributes:
 * - `object` (required) has to be a (decorated) `Character` object.
 * - `type` (optional) supports the following values:
 *      - `default` (default value) generates a link with the character title with popover support.
 *      - `formated` generates a link with the formatted character title with popover support.
 *      - `avatarXY` generates a link with the character's avatar in size `XY`.
 *      - `plain` generates a link link without character title formatting and popover support
 * - `append` (optional) is appended to the character link.
 * 
 * All other additional parameter values are added as attributes to the `a` element. Parameter names
 * in camel case are changed to kebab case (`fooBar` becomes `foo-bar`).
 *
 * Usage:
 *      {character object=$character}
 *      {character object=$character type='plain'}
 *      {character object=$character type='avatar48'}
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterFunctionTemplatePlugin implements IFunctionTemplatePlugin
{
    /**
     * @inheritDoc
     */
    public function execute($tagArgs, TemplateEngine $tplObj): string
    {
        if (!isset($tagArgs['object'])) {
            throw new \InvalidArgumentException("Missing 'object' attribute.");
        }

        $object = $tagArgs['object'];
        unset($tagArgs['object']);
        if (!($object instanceof CharacterProfile) && !ClassUtil::isDecoratedInstanceOf($object, CharacterProfile::class)) {
            $type = \gettype($object);
            if (\is_object($object)) {
                $type = "'" . \get_class($object) . "' object";
            }

            throw new \InvalidArgumentException(
                "'object' attribute is no '" . CharacterProfile::class . "' object, instead {$type} given."
            );
        }

        $additionalParameters = '';
        $content = '';
        if (isset($tagArgs['type'])) {
            $type = $tagArgs['type'];
            unset($tagArgs['type']);

            if ($type === 'plain') {
                $content = StringUtil::encodeHTML($object->getTitle());
            } else if (\preg_match('~^avatar(\d+)$~', $type, $matches)) {
                $content = $object->getAvatar()->getImageTag($matches[1]);
            } else if ($type !== 'default') {
                throw new \InvalidArgumentException("Unknown 'type' value '{$type}'.");
            }
        }

        // default case
        if ($content === '') {
            $content = $object->getTitle();

            if ($object->getObjectID()) {
                $additionalParameters = ' data-object-id="' . $object->getObjectID() . '"';
                if (isset($tagArgs['class'])) {
                    $tagArgs['class'] = 'rpCharacterLink ' . $tagArgs['class'];
                } else {
                    $tagArgs['class'] = 'rpCharacterLink';
                }
            }
        }

        if (isset($tagArgs['href'])) {
            throw new \InvalidArgumentException("'href' attribute is not allowed.");
        }

        $append = '';
        if (isset($tagArgs['append'])) {
            $append = $tagArgs['append'];
            unset($tagArgs['append']);
        }

        foreach ($tagArgs as $name => $value) {
            if (!\preg_match('~^[a-z]+([A-z]+)+$~', $name)) {
                throw new \InvalidArgumentException("Invalid additional argument name '{$name}'.");
            }

            $additionalParameters .= ' ' . \strtolower(\preg_replace('~([A-Z])~', '-$1', $name))
                . '="' . StringUtil::encodeHTML($value) . '"';
        }

        if (!$object->getObjectID()) {
            return '<span' . $additionalParameters . '>' . $content . '</span>';
        }

        return '<a href="' . StringUtil::encodeHTML($object->getLink() . $append) . '"' . $additionalParameters . '>' . $content . '</a>';
    }
}
