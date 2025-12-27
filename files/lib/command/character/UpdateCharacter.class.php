<?php

namespace rp\command\character;

use rp\data\character\Character;
use rp\data\character\CharacterEditor;
use rp\event\character\CharacterUpdated;
use wcf\system\event\EventHandler;

/**
 * Updates an existing character.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class UpdateCharacter
{
    /**
     * @param array<string,mixed> $data
     * @param mixed[] $formData
     */
    public function __construct(
        private readonly array $data,
        private readonly array $formData,
        private readonly Character $character
    ) {}

    public function __invoke(): void
    {
        $data = $this->data;

        $data['lastUpdateTime'] = \TIME_NOW;

        if ($data['userID'] === null) {
            $data['isDisabled'] = 1;
        }

        $editor = new CharacterEditor($this->character);
        $editor->update($data);

        EventHandler::getInstance()->fire(
            new CharacterUpdated(
                new Character($this->character->getObjectID()),
                $this->formData
            )
        );
    }
}
