<?php

namespace roydejong\dotnet\Integration;

/**
 * Static integration helper for google maps geocoder.
 */
class GeoDecoder
{
    const API_URL = "http://maps.google.com/maps/api/geocode";

    /**
     * Resulting locality name.
     *
     * @var string
     */
    public $locality;

    /**
     * Resulting country name.
     *
     * @var string
     */
    public $country;

    /**
     * GeoDecoder result constructor.
     *
     * @param string $locality
     * @param string $countryName
     */
    protected function __construct(string $locality, string $countryName)
    {
        $this->locality = $locality;
        $this->country = $countryName;
    }

    /**
     * Performs a geocode operation based on a given $inputString.
     *
     * @param string $addressString
     * @return GeoDecoder|null
     */
    public static function fromAddress(string $addressString): ?GeoDecoder
    {
        $url = self::API_URL . "/json?address={$addressString}&sensor=false";

        $get = file_get_contents($url);
        $geoData = json_decode($get);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        $data = null;

        if (isset($geoData->results[0])) {
            $localityName = null;
            $countryName = null;

            foreach ($geoData->results[0]->address_components as $addressComponent) {
                if (in_array('locality', $addressComponent->types)) {
                    $localityName = $addressComponent->long_name;
                }

                if (in_array('country', $addressComponent->types)) {
                    $countryName = $addressComponent->long_name;
                }
            }

            if (!empty($localityName) || !empty($countryName)) {
                return new GeoDecoder($localityName, $countryName);
            }
        }

        return $data;
    }
}