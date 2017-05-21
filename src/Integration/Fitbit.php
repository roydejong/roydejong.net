<?php

namespace roydejong\dotnet\Integration;

use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\RequestInterface;
use roydejong\dotnet\Data\StupidDb;
use roydejong\dotnet\Integration\Structs\SleepSummary;
use roydejong\dotnet\Site\SiteConfig;

class Fitbit
{
    const DB_KEY_TOKEN = "firbit.token";
    const DB_KEY_TOKEN_REFRESH = "firbit.token.refresh";
    const DB_KEY_TOKEN_EXPIRES = "firbit.token.expires";
    const DB_KEY_USERNAME = "fitbit.username";

    /**
     * @var \djchen\OAuth2\Client\Provider\Fitbit
     */
    protected $client;

    /**
     * @var SiteConfig
     */
    protected $config;

    /**
     * Fitbit constructor.
     *
     * @param SiteConfig $config
     */
    public function __construct(SiteConfig $config)
    {
        $this->config = $config;

        $this->client = new \djchen\OAuth2\Client\Provider\Fitbit([
            'clientId' => $config->fitbitClientId,
            'clientSecret' => $config->fitbitClientSecret,
            'redirectUri' => "{$config->siteUrl}/external/fitbit_callback"
        ]);
    }

    /**
     * Gets the OAuth url.
     *
     * @return string
     */
    public function getAuthUrl(): string
    {
        return $this->client->getAuthorizationUrl();
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

        $token = $this->client->getAccessToken('authorization_code', [
            'code' => $oauthCode
        ]);

        // Check that we obtained a token, and that it matches our expected user
        // NB: User matching helps us prevent the unlikely scenario of CSRF attacks where people authorize THEIR accounts to my site instead...
        return $this->storeAccessToken($token);
    }

    /**
     * Verifies a given AccessToken and then stores it if it looks okay.
     *
     * @param AccessToken|null $token
     * @return bool
     */
    protected function storeAccessToken(?AccessToken $token): bool
    {
        if ($token && $token->getToken() && $token->getValues()['user_id'] === $this->config->fitbitUserId) {
            StupidDb::setString(self::DB_KEY_TOKEN, $token->getToken());
            StupidDb::setString(self::DB_KEY_TOKEN_REFRESH, $token->getRefreshToken());
            StupidDb::setInt(self::DB_KEY_TOKEN_EXPIRES, $token->getExpires());
            StupidDb::commit();
            return true;
        }

        return false;
    }

    /**
     * @return null|string
     */
    protected function getAccessToken(): ?string
    {
        $accessToken = StupidDb::getString(self::DB_KEY_TOKEN);

        if (!$accessToken) {
            // Ain't got no token to begin with
            return null;
        }

        $tsNow = time();
        $expiryTs = StupidDb::getString(self::DB_KEY_TOKEN_EXPIRES);

        if ($tsNow >= $expiryTs) {
            // Expired, try to refresh
            $refreshToken = StupidDb::getString(self::DB_KEY_TOKEN_EXPIRES);

            $newAccessToken = $this->client->getAccessToken('refresh_token', [
                'refresh_token' => $refreshToken
            ]);

            if ($this->storeAccessToken($newAccessToken)) {
                // Refreshed OK, all good
                return $newAccessToken->getToken();
            } else {
                // Something horrible happened, idk
                return null;
            }
        } else {
            // Not expired, all good
            return $accessToken;
        }
    }

    /**
     * @param string $targetApi
     * @return \Psr\Http\Message\RequestInterface
     */
    protected function prepareAuthenticatedRequest(string $targetApi): RequestInterface
    {
        return $this->client->getAuthenticatedRequest(
            \djchen\OAuth2\Client\Provider\Fitbit::METHOD_GET,
            \djchen\OAuth2\Client\Provider\Fitbit::BASE_FITBIT_API_URL . $targetApi,
            $this->getAccessToken(), [
            'headers' => [\djchen\OAuth2\Client\Provider\Fitbit::HEADER_ACCEPT_LANG => 'en_US'], [\djchen\OAuth2\Client\Provider\Fitbit::HEADER_ACCEPT_LOCALE => 'en_US']
        ]);
    }

    /**
     * @return null|SleepSummary
     */
    public function getLastSleep(): ?SleepSummary
    {
        $tomorrow = new \DateTime();
        $tomorrow->modify('+1 day');

        $beforeDateStr = $tomorrow->format('Y-m-d');

        $request = $this->prepareAuthenticatedRequest("/1.2/user/-/sleep/list.json?sort=desc&limit=1&offset=0&beforeDate={$beforeDateStr}");
        $response = $this->client->getParsedResponse($request);

        if (!$response || !is_array($response) || !isset($response['sleep'])) {
            return null;
        }

        $lastSleepData = array_shift($response['sleep']);

        $sleepStruct = new SleepSummary();
        $sleepStruct->dateOfSleep = $lastSleepData['dateOfSleep'];
        $sleepStruct->durationSecs = intval($lastSleepData['duration']) / 1000;
        $sleepStruct->startDateTime = new \DateTime();
        $sleepStruct->startDateTime->setTimestamp(strtotime($lastSleepData['startTime']));
        $sleepStruct->endDateTime = new \DateTime();
        $sleepStruct->endDateTime->setTimestamp(strtotime($lastSleepData['endTime']));
        $sleepStruct->minutesAsleep = intval($lastSleepData['minutesAsleep']);
        $sleepStruct->minutesInBed = intval($lastSleepData['timeInBed']);
        $sleepStruct->efficiency = intval($lastSleepData['efficiency']);
        return $sleepStruct;
    }
}