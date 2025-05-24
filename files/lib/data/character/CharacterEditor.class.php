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
 * @method static   Character   create(array $parameters = [])
 * @method  Character   getDecoratedObject()
 * @mixin   Character
 */
class CharacterEditor extends DatabaseObjectEditor
{
    protected static $baseClass = Character::class;
}
