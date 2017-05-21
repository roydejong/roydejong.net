<?php

use roydejong\dotnet\Site\SiteConfig;

$config = SiteConfig::instance();

// TODO Fill me in and save me as "config.php"

// Site basics
$config->siteUrl = 'http://dev.roydejong.net';
$config->adminPassword = '';

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
