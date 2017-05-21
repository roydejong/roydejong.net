<?php

use roydejong\dotnet\Site\SiteConfig;

$config = SiteConfig::instance();

// Site basics
$config->siteUrl = 'http://dev.roydejong.net';

// Instagram integration
$config->instagramEnabled = false;
$config->instagramClientId = '';
$config->instagramClientSecret = '';

// Lastfm integration
$config->lastfmEnabled = false
$config->lastfmApiKey = '';
$config->lastfmUsername = '';

// Steam integration
$config->steamEnabled = false;
$config->steamApiKey = '';
$config->steamUserId = '';

// Fitbit integration
$config->fitbitEnabled = false;
$config->fitbitClientId = '';
$config->fitbitClientSecret = '';
$config->fitbitUserId = '';


