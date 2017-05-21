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
     * The laziest admin auth scheme ever.
     * Handles admin session management, handles password submissions and verifies the password.
     *
     * @return bool
     */
    protected function isAuthenticated(): bool
    {
        $cfg = SiteConfig::instance();

        if (!session_status() === PHP_SESSION_ACTIVE) {
            session_name('admin');
            session_start();
        }

        if (!$cfg->adminPassword) {
            // Not set in config
            return false;
        }

        if ($this->request->getPost('password')) {
            $_SESSION['admin_pass'] = $this->request->getPost('password');
        }

        return $cfg->adminPassword === $_SESSION['admin_pass'];
    }

    /**
     * @@inheritdoc
     */
    public function getTemplateName(): string
    {
        return $this->isAuthenticated() ? "admin_tools.twig" : "admin_login.twig";
    }

    /**
     * @inheritdoc
     */
    public function needsGeneration(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isDynamic(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function generate(): string
    {
        $config = SiteConfig::instance();

        if ($this->isAuthenticated()) {
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
        }

        return parent::generate();
    }
}