<?php

namespace rp\system\cache\eager\data;

use rp\data\game\Game;

/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class GameCacheData
{
    public function __construct(
        /** @var array<int, Game> */
        public readonly array $games,
        /** @var array<string, int> */
        public readonly array $identifiers,
    ) {}

    /**
     * Returns the current selected game.
     */
    public function getCurrentGame(): Game
    {
        $game = $this->getGame(RP_CURRENT_GAME_ID);

        if ($game === null) {
            // fallback to default game
            $game = new Game(null, [
                'identifier' => 'de.md-raidplaner.rp.game.default',
            ]);
        }

        return $game;
    }

    /**
     * Returns the game with the given game id or `null` if no such game exists.
     */
    public function getGame(int $gameID): ?Game
    {
        return $this->games[$gameID] ?? null;
    }

    /**
     * Returns the game with the given game identifier or `null` if no such game exists.
     */
    public function getGameByIdentifier(string $identifier): ?Game
    {
        if (!isset($this->identifiers[$identifier])) return null;
        return $this->getGame($this->identifiers[$identifier]);
    }

    /**
     * Returns the game with the given game ids.
     * 
     * @return array<int, Game>
     */
    public function getGames(array $gameIDs): array
    {
        return \array_filter(
            \array_map(fn($gameID) => $this->getGame($gameID), $gameIDs),
            fn($game) => $game !== null
        );
    }
}
