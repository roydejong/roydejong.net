<?php

namespace roydejong\dotnet\Integration\Structs;

/**
 * Data struct that represents a Last.fm track that is being played, or was recently played.
 */
class LastfmTrack
{
    /**
     * Is track playing now?
     *
     * @var bool
     */
    public $isPlaying;

    /**
     * Name of the track's artist.
     *
     * @var string
     */
    public $artistName;

    /**
     * Full name of the track.
     *
     * @var string
     */
    public $name;

    /**
     * Link to the track info on Last.fm.
     *
     * @var string
     */
    public $url;

    /**
     * The date/time on which this track was played.
     *
     * @var \DateTime
     */
    public $date;
}