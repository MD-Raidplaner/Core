<?php

namespace rp\data\character;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit characters.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 * 
 * @mixin   Character
 * @extends DatabaseObjectEditor<Character>
 */
final class CharacterEditor extends DatabaseObjectEditor
{
    protected static string $baseClass = Character::class;
}
