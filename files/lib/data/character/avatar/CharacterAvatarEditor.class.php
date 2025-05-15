<?php

namespace rp\data\character\avatar;

use wcf\data\DatabaseObjectEditor;
use wcf\system\WCF;
use wcf\util\ImageUtil;

/**
 * Provides functions to edit avatars.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 *
 * @method static CharacterAvatar       create(array $parameters = [])
 * @method      CharacterAvatar     getDecoratedObject()
 * @mixin       CharacterAvatar
 */
class CharacterAvatarEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = CharacterAvatar::class;

    /**
     * Creates a WebP variant of the avatar, unless it is a GIF image. If the
     * character uploads a WebP image, this method will create a JPEG variant as a
     * fallback for ancient clients.
     *
     * Will return `true` if a variant has been created.
     */
    public function createAvatarVariant(): bool
    {
        if ($this->hasWebP) {
            return false;
        }

        if ($this->avatarExtension === "gif") {
            // We do not touch GIFs at all.
            return false;
        }

        $outputFilenameWithoutExtension = \preg_replace('~\.[a-z]+$~', '', $this->getLocation());
        $result = ImageUtil::createWebpVariant($this->getLocation(), $outputFilenameWithoutExtension);
        if ($result !== null) {
            $data = ['hasWebP' => 1];

            // A fallback jpeg image was just created.
            if ($result === false) {
                $data['avatarExtension'] = 'jpg';
            }

            $this->update($data);

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $sql = "DELETE FROM rp1_member_avatar
                WHERE       avatarID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$this->avatarID]);

        $this->deleteFiles();
    }

    /**
     * @inheritDoc
     */
    public static function deleteAll(array $objectIDs = []): int
    {
        $sql = "SELECT  *
                FROM    rp1_member_avatar
                WHERE   avatarID IN (" . \str_repeat('?,', \count($objectIDs) - 1) . "?)";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($objectIDs);
        while ($avatar = $statement->fetchObject(self::$baseClass)) {
            $editor = new self($avatar);
            $editor->deleteFiles();
        }

        return parent::deleteAll($objectIDs);
    }

    /**
     * Deletes avatar files.
     */
    public function deleteFiles(): void
    {
        // delete original size
        @\unlink($this->getLocation(null, false));

        if ($this->hasWebP) {
            @\unlink($this->getLocation(null, true));
        }
    }
}
