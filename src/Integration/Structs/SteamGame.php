<?php

namespace roydejong\dotnet\Integration\Structs;

/**
 * Data struct that represents a Steam game that is being played, or was recently played.
 */
class SteamGame
{
    /**
     * Name of the game.
     *
     * @var string
     */
    public $name;

    /**
     * Flag whether game is being played right now.
     *
     * @var bool
     */
    public $playingNow;
}