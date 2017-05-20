<?php

namespace roydejong\dotnet\Integration;

use roydejong\dotnet\Data\StupidDb;
use roydejong\dotnet\Site\SiteConfig;

/**
 * Instagram integration.
 */
class Instagram
{
    const DB_KEY_TOKEN = "instagram.token";
    const DB_KEY_USERNAME = "instagram.username";

    /**
     * @var \MetzWeb\Instagram\Instagram
     */
    protected $client;

    /**
     * Instagram integration constructor.
     *
     * @param SiteConfig $config
     */
    public function __construct(SiteConfig $config)
    {
        $this->client = new \MetzWeb\Instagram\Instagram([
            'apiKey' => $config->instagramClientId,
            'apiSecret' => $config->instagramClientSecret,
            'apiCallback' => "{$config->siteUrl}/external/ig_callback"
        ]);

        $storedToken = StupidDb::getString(self::DB_KEY_TOKEN);

        if ($storedToken) {
            $this->client->setAccessToken($storedToken);
        }
    }

    /**
     * Gets the OAuth url.
     *
     * @return string
     */
    public function getAuthUrl(): string
    {
        return $this->client->getLoginUrl();
    }

    /**
     * Deals with an incoming OAuth code, trying to extract and store the token from it.
     *
     * @param string $oauthCode
     * @return bool
     */
    public function handleOAuthCode(string $oauthCode): bool
    {
        if (!$oauthCode) {
            return false;
        }

        $token = $this->client->getOAuthToken($oauthCode);

        if ($token && isset($token->access_token) && isset($token->user) && isset($token->user->username)) {
            StupidDb::setString(self::DB_KEY_TOKEN, $token->access_token);
            StupidDb::setString(self::DB_KEY_USERNAME, $token->user->username);
            StupidDb::commit();
            return true;
        }

        return false;
    }

    /**
     * Gets "last seen" coordinates in a string format (<"lat>,<long>" format).
     *
     * @return string|null
     */
    public function getLastSeenCoordinates(): ?string
    {
        $userMedia = $this->client->getUserMedia();

        if (!$userMedia || !isset($userMedia->data)) {
            return null;
        }

        $userMediaData = $userMedia->data;

        foreach ($userMediaData as $userMediaDatum) {
            if (!isset($userMediaDatum->location)) {
                continue;
            }

            $lastLocationObj = $userMediaDatum->location;

            $formattedGeoAddress = sprintf('%s,%s', floatval($lastLocationObj->latitude),
                floatval($lastLocationObj->longitude));

            return $formattedGeoAddress;
        }

        return null;
    }
}