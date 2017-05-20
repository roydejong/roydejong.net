<?php

namespace roydejong\dotnet\Integration;

use GuzzleHttp\Client;
use roydejong\dotnet\Integration\Structs\LastfmTrack;
use roydejong\dotnet\Site\SiteConfig;

class Lastfm
{
    /**
     * @var SiteConfig
     */
    protected $config;

    /**
     * Lastfm constructor.
     *
     * @param SiteConfig $config
     */
    public function __construct(SiteConfig $config)
    {
        $this->config = $config;

        $this->client = new \Chrismou\LastFm\LastFm(
            new Client(),
            $this->config->lastfmApiKey,
            null
        );
    }

    /**
     * @return null|LastfmTrack
     */
    public function getLastPlayedSong(): ?LastfmTrack
    {
        $mostRecentTrack =  $this->client->get('user.getRecentTracks', [
            'limit' => 1,
            'user' => $this->config->lastfmUsername
        ]);

        if ($mostRecentTrack && isset($mostRecentTrack->recenttracks) && isset($mostRecentTrack->recenttracks->track)) {
            $apiTrackData = $mostRecentTrack->recenttracks->track;

            if (is_array($apiTrackData)) {
                $apiTrackData = array_shift($apiTrackData);
            }

            $_varNameHashText = '#text';
            $_varNameAtAttributes = '@attr';

            $trackInfo = new LastfmTrack();
            $trackInfo->artistName = $apiTrackData->artist->$_varNameHashText;
            $trackInfo->name = $apiTrackData->name;
            $trackInfo->url = $apiTrackData->url;
            $trackInfo->date = new \DateTime();

            if (isset($apiTrackData->$_varNameAtAttributes) && isset($apiTrackData->$_varNameAtAttributes->nowplaying)
                && $apiTrackData->$_varNameAtAttributes->nowplaying === 'true') {
                $trackInfo->isPlaying = true;
            } else {
                $trackInfo->isPlaying = false;
                $trackInfo->date->setTimestamp($apiTrackData->date->uts);
            }

            return $trackInfo;
        }

        return null;
    }
}