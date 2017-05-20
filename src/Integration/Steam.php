<?php

namespace roydejong\dotnet\Integration;

use GuzzleHttp\Client;
use roydejong\dotnet\Integration\Structs\SteamGame;
use roydejong\dotnet\Site\SiteConfig;
use Steam\Configuration;
use Steam\Runner\DecodeJsonStringRunner;
use Steam\Runner\GuzzleRunner;
use Steam\Utility\GuzzleUrlBuilder;

/**
 * Steam integration helper for game history.
 */
class Steam
{
    /**
     * @var SiteConfig
     */
    protected $config;

    /**
     * Steam constructor.
     *
     * @param SiteConfig $config
     */
    public function __construct(SiteConfig $config)
    {
        $this->config = $config;

        $this->client = new \Steam\Steam(new Configuration([
            Configuration::STEAM_KEY => $config->steamApiKey
        ]));

        $this->client->addRunner(new GuzzleRunner(new Client(), new GuzzleUrlBuilder()));
        $this->client->addRunner(new DecodeJsonStringRunner());
    }

    /**
     * Attempts to determine currently playing or last played game on Steam.
     *
     * @return null|SteamGame
     */
    public function getLastPlayedGame(): ?SteamGame
    {
        // Check if we have a current game via user profile
        $currentGame = $this->getCurrentGameFromProfile();

        if ($currentGame) {
            return $currentGame;
        }

        // No current game, use history
        return $this->getLastGameFromHistory();
    }

    /**
     * Fetches the current playing game via the profile status.
     *
     * @return null|SteamGame
     */
    protected function getCurrentGameFromProfile(): ?SteamGame
    {
        $result = $this->client->run(new \Steam\Command\User\GetPlayerSummaries([$this->config->steamUserId]));

        if (!isset($result['response']) || !isset($result['response']['players'])) {
            return null;
        }

        $playerInfo = array_shift($result['response']['players']);

        if (!$playerInfo) {
            return null;
        }

        if ($playerInfo['gameextrainfo']) {
            $game = new SteamGame();
            $game->playingNow = true;
            $game->name = $playerInfo['gameextrainfo'];
            return $game;
        }

        return null;
    }

    /**
     * Fetches the last played game via the profile game history.
     *
     * @return null|SteamGame
     */
    protected function getLastGameFromHistory(): ?SteamGame
    {
        $result = $this->client->run(new \Steam\Command\PlayerService\GetRecentlyPlayedGames($this->config->steamUserId, 1));

        if (!isset($result['response']) || !isset($result['response']['games'])) {
            return null;
        }

        $gameInfo = array_shift($result['response']['games']);

        if (!$gameInfo) {
            return null;
        }

        if ($gameInfo['name']) {
            $game = new SteamGame();
            $game->playingNow = false;
            $game->name = $gameInfo['name'];
            return $game;
        }

        return null;
    }
}