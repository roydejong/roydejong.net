<?php

namespace roydejong\dotnet\Generation;
use roydejong\dotnet\Integration\Instagram;
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
                $this->setValue("stalker_location_enabled", true);
                $this->setValue("stalker_location_coordinates", $lastCoords);
            }
        }

        return parent::generate();
    }
}