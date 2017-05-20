<?php

namespace roydejong\dotnet\Generation;
use roydejong\dotnet\Integration\GeoDecoder;
use roydejong\dotnet\Integration\Instagram;
use roydejong\dotnet\Integration\Lastfm;
use roydejong\dotnet\Integration\Steam;
use roydejong\dotnet\Site\SiteConfig;

/**
 * Page generator for the homepage / landing / root page.
 */
class HomepageGenerator extends PageGenerator
{
    /**
     * @@inheritdoc
     */
    public function getTemplateName(): string
    {
        return "homepage.twig";
    }

    /**
     * @inheritdoc
     */
    public function needsGeneration(): bool
    {
        // The homepage is automatically regenerated periodically
        return false;
    }

    /**
     * @inheritdoc
     */
    public function generate(): string
    {
        $config = SiteConfig::instance();

        // Instagram integration for last known location
        if ($config->instagramEnabled) {
            $instagramClient = new Instagram($config);

            $lastCoords = $instagramClient->getLastSeenCoordinates();

            if ($lastCoords) {
                $decoded = GeoDecoder::fromAddress($lastCoords);

                if ($decoded) {
                    $this->setValue("stalker_location_enabled", true);
                    $this->setValue("stalker_location_coordinates", $lastCoords);
                    $this->setValue("stalker_location_text", "{$decoded->locality}, {$decoded->country}");
                }
            }
        }

        // Lastfm integration for last song
        if ($config->lastfmEnabled) {
            $lastfmClient = new Lastfm($config);

            $lastTrack = $lastfmClient->getLastPlayedSong();

            if ($lastTrack) {
                $this->setValue("stalker_music_enabled", true);
                $this->setValue("stalker_music_track", $lastTrack);
            }
        }

        // Steam integration for last game
        if ($config->steamEnabled) {
            $steamClient = new Steam($config);

            $lastGame = $steamClient->getLastPlayedGame();

            if ($lastGame) {
                $this->setValue("stalker_game_enabled", true);
                $this->setValue("stalker_game_info", $lastGame);
            }
        }

        return parent::generate();
    }
}