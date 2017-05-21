<?php

namespace roydejong\dotnet\Generation;

use roydejong\dotnet\Integration\Fitbit;
use roydejong\dotnet\Integration\GeoDecoder;
use roydejong\dotnet\Integration\Instagram;
use roydejong\dotnet\Integration\Lastfm;
use roydejong\dotnet\Integration\Steam;
use roydejong\dotnet\Site\SiteConfig;

/**
 * Page generator for the admin tools page.
 */
class AdminToolsGenerator extends PageGenerator
{
    /**
     * @@inheritdoc
     */
    public function getTemplateName(): string
    {
        return "admin_tools.twig";
    }

    /**
     * @inheritdoc
     */
    public function needsGeneration(): bool
    {
        // This page does not normally change unless config changes
        return false;
    }

    /**
     * @inheritdoc
     */
    public function generate(): string
    {
        $config = SiteConfig::instance();

        if ($config->instagramEnabled) {
            $instagramClient = new Instagram($config);

            $this->setValue("instagram_enabled", true);
            $this->setValue("instagram_auth_url", $instagramClient->getAuthUrl());
        }

        if ($config->fitbitEnabled) {
            $fitbitClient = new Fitbit($config);

            $this->setValue("fitbit_enabled", true);
            $this->setValue("fitbit_auth_url", $fitbitClient->getAuthUrl());
        }

        return parent::generate();
    }
}