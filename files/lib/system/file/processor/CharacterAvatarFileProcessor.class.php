<?php

namespace rp\system\file\processor;

use rp\data\character\CharacterProfile;
use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use rp\system\character\command\SetAvatar;
use wcf\data\file\File;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exception\UserInputException;
use wcf\system\file\processor\AbstractFileProcessor;
use wcf\system\file\processor\FileProcessorPreflightResult;
use wcf\system\file\processor\ImageCropperConfiguration;
use wcf\system\file\processor\ImageCropSize;
use wcf\system\file\processor\ThumbnailFormat;
use wcf\system\WCF;
use wcf\util\FileUtil;

/**
 * Processes character avatar files.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterAvatarFileProcessor extends AbstractFileProcessor
{
    private const SESSION_VARIABLE = 'rp_character_avatar_processor_%d';

    public const AVATAR_SIZE = 128;

    /**
     * Size of HiDPI version
     */
    public const AVATAR_SIZE_2X = 256;

    #[\Override]
    public function acceptUpload(string $filename, int $fileSize, array $context): FileProcessorPreflightResult
    {
        $character = $this->getCharacter($context);

        if ($character === null) {
            return FileProcessorPreflightResult::InvalidContext;
        }

        if (!$character->canEditAvatar()) {
            return FileProcessorPreflightResult::InsufficientPermissions;
        }

        if ($fileSize > $this->getMaximumSize($context)) {
            return FileProcessorPreflightResult::FileSizeTooLarge;
        }

        if (!FileUtil::endsWithAllowedExtension($filename, $this->getAllowedFileExtensions($context))) {
            return FileProcessorPreflightResult::FileExtensionNotPermitted;
        }

        return FileProcessorPreflightResult::Passed;
    }

    #[\Override]
    public function adopt(File $file, array $context): void
    {
        $character = $this->getCharacter($context);
        if ($character === null) {
            return;
        }

        if ($character->avatarFileID !== null) {
            WCF::getSession()->register(\sprintf(self::SESSION_VARIABLE, $character->avatarFileID), \TIME_NOW);
            WCF::getSession()->update();
        }

        (new SetAvatar($character->getDecoratedObject(), $file))();
    }

    #[\Override]
    public function canAdopt(File $file, array $context): bool
    {
        $characterFromContext = $this->getCharacter($context);
        $characterFromCoreFile = $this->getCharacterByFile($file);

        if ($characterFromCoreFile === null) {
            return true;
        }

        if ($characterFromContext->characterID === $characterFromCoreFile->characterID) {
            return true;
        }

        return false;
    }

    #[\Override]
    public function canDelete(File $file): bool
    {
        $character = $this->getCharacterByFile($file);
        if ($character === null) {
            return WCF::getSession()->getVar(
                \sprintf(self::SESSION_VARIABLE, $file->getObjectID())
            ) !== null;
        }

        return $character->canEditAvatar();
    }

    #[\Override]
    public function canDownload(File $file): bool
    {
        $character = $this->getCharacterByFile($file);
        if ($character === null) {
            return false;
        }

        return true;
    }

    #[\Override]
    public function countExistingFiles(array $context): ?int
    {
        $character = $this->getCharacter($context);
        if ($character === null) {
            return null;
        }

        return $character->avatarFileID !== null ? 1 : 0;
    }

    #[\Override]
    public function delete(array $fileIDs, array $thumbnailIDs): void
    {
        \array_map(
            static fn(int $fileID) => WCF::getSession()->unregister(
                \sprintf(self::SESSION_VARIABLE, $fileID)
            ),
            $fileIDs
        );

        $conditionBuilder = new PreparedStatementConditionBuilder();
        $conditionBuilder->add('avatarFileID IN (?)', $fileIDs);

        $sql = "UPDATE  rp1_member
                SET     avatarFileID = ?
                " . $conditionBuilder;
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([null, ...$conditionBuilder->getParameters()]);
    }

    #[\Override]
    public function getAllowedFileExtensions(array $context): array
    {
        return \explode("\n", WCF::getSession()->getPermission('user.rp.characterAvatarAllowedFileExtensions'));
    }

    /**
     * @param array<string, mixed> $context
     */
    private function getCharacter(array $context): ?CharacterProfile
    {
        $characterID = $context['characterID'] ?? null;
        if ($characterID === null) {
            return null;
        }

        return CharacterProfileRuntimeCache::getInstance()->getObject($characterID);
    }

    private function getCharacterByFile(File $file): ?CharacterProfile
    {
        $sql = "SELECT  characterID
                FROM    rp1_member
                WHERE   avatarFileID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$file->getObjectID()]);
        $characterID = $statement->fetchSingleColumn();

        if ($characterID === null) {
            return null;
        }

        return CharacterProfileRuntimeCache::getInstance()->getObject($characterID);
    }

    #[\Override]
    public function getImageCropperConfiguration(): ?ImageCropperConfiguration
    {
        return ImageCropperConfiguration::forExact(
            new ImageCropSize(
                self::AVATAR_SIZE,
                self::AVATAR_SIZE
            ),
            new ImageCropSize(
                self::AVATAR_SIZE_2X,
                self::AVATAR_SIZE_2X
            )
        );
    }

    #[\Override]
    public function getMaximumSize(array $context): int
    {
        /**
         * Reject the file if it is larger than 750 KiB after resizing. A worst-case
         * completely-random 128x128 PNG is around 35 kB and JPEG is around 50 kB.
         * 
         * Animated GIFs can be much larger depending on the length of animation,
         * 750 kB seems to be a reasonable upper bound for anything that can be
         * considered reasonable with regard to "distration" and mobile data
         * volume.
         */
        return 750_000;
    }

    #[\Override]
    public function getObjectTypeName(): string
    {
        return 'de.md-raidplaner.rp.character.avatar';
    }

    #[\Override]
    public function getThumbnailFormats(): array
    {
        return [
            new ThumbnailFormat(
                '128',
                self::AVATAR_SIZE,
                self::AVATAR_SIZE,
                false
            ),
            new ThumbnailFormat(
                '256',
                self::AVATAR_SIZE_2X,
                self::AVATAR_SIZE_2X,
                false
            ),
        ];
    }

    #[\Override]
    public function validateUpload(File $file): void
    {
        $imageData = @\getimagesize($file->getPathname());
        if ($imageData === false) {
            throw new UserInputException('file', 'noImage');
        }

        if ($imageData[0] !== $imageData[1]) {
            throw new UserInputException('file', 'notSquare');
        }

        if ($imageData[0] !== self::AVATAR_SIZE && $imageData[0] !== self::AVATAR_SIZE_2X) {
            throw new UserInputException('file', 'wrongSize');
        }
    }
}
