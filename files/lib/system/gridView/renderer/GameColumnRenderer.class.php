<?php

namespace rp\system\gridView\renderer;

use rp\system\game\GameHandler;
use wcf\data\DatabaseObject;
use wcf\system\gridView\renderer\DefaultColumnRenderer;
use wcf\util\StringUtil;

/**
 * Formats the content of a column as a game. The value of the column must be a game id.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
class GameColumnRenderer extends DefaultColumnRenderer
{
    public function __construct(
        public readonly string $fallbackValue = 'title'
    ) {}

    #[\Override]
    public function render(mixed $value, DatabaseObject $row): string
    {
        if (!$value) {
            if ($this->fallbackValue) {
                return StringUtil::encodeHTML($row->{$this->fallbackValue} ?? '');
            }

            return '';
        }

        $game = GameHandler::getInstance()->getGameByIdentifier($value);
        if ($game === null) {
            return '';
        }

        return StringUtil::encodeHTML($game->getTitle());
    }
}
