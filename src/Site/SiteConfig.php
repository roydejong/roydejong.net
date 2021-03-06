<?php

namespace roydejong\dotnet\Site;

/**
 * Site configuration singleton.
 */
class SiteConfig
{
    /**
     * @var SiteConfig
     */
    protected static $_instance;

    /**
     * Gets the SiteConfig singleton instance.
     *
     * @return SiteConfig
     */
    public static function instance(): SiteConfig
    {
        if (!self::$_instance) {
            self::$_instance = new SiteConfig();
        }

        return self::$_instance;
    }

    public $siteUrl;
    public $adminPassword;

    public $instagramEnabled;
    public $instagramClientId;
    public $instagramClientSecret;
    public $instagramUsername;

    public $lastfmEnabled;
    public $lastfmApiKey;
    public $lastfmUsername;

    public $steamEnabled;
    public $steamApiKey;
    public $steamUserId;

    public $fitbitEnabled;
    public $fitbitClientId;
    public $fitbitClientSecret;
    public $fitbitUserId;

    /**
     * SiteConfig constructor.
     */
    protected function __construct()
    {
        // We're not publicly accessible, yay!
    }
}