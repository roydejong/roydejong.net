<?php

use Enlighten\Enlighten;
use Enlighten\Http\Request;

use Enlighten\Http\Response;
use Enlighten\Http\ResponseCode;
use roydejong\dotnet\Generation\AdminToolsGenerator;
use roydejong\dotnet\Generation\HomepageGenerator;
use roydejong\dotnet\Integration\Fitbit;
use roydejong\dotnet\Integration\Instagram;
use roydejong\dotnet\Site\SiteConfig;
use roydejong\dotnet\Site\SiteEngine;

require_once "../vendor/autoload.php";

define('PATH_PUBLIC', __DIR__);
define('PATH_PROJECT', realpath(__DIR__ . "/../"));
define('PATH_SRC', PATH_PROJECT . "/src");
define('PATH_COMPILATION', PATH_PROJECT . "/compiled");
define('PATH_TEMPLATES', PATH_PROJECT . "/templates");

function getEnableDebugMode(): bool {
    return ($_SERVER['SERVER_NAME'] === 'dev.roydejong.net');
}

define('DEBUG_ENABLED', getEnableDebugMode());

require_once PATH_PROJECT . "/config/config.php";

$app = new Enlighten();

// Route: Homepage
$app->get('/', function (Request $request): Response {
    return SiteEngine::fire(new HomepageGenerator(), $request);
});

// Route: Admin tools
$app->get('/admin', function (Request $request): Response {
    return SiteEngine::fire(new AdminToolsGenerator(), $request);
});

// Route: Callback for Instagram OAuth
$app->get('/external/ig_callback', function (Request $request, Response $response) {
    $ig = new Instagram(SiteConfig::instance());

    if ($ig->handleOAuthCode($request->getQueryParam('code', ''))) {
        $response->setResponseCode(ResponseCode::HTTP_OK);
        $response->setBody('OK');
    } else {
        $response->setResponseCode(ResponseCode::HTTP_BAD_REQUEST);
        $response->setBody('NOT_OK');
    }
});

// Route: Callback for Instagram OAuth
$app->get('/external/fitbit_callback', function (Request $request, Response $response) {
    $fitbit = new Fitbit(SiteConfig::instance());

    if ($fitbit->handleOAuthCode($request->getQueryParam('code', ''))) {
        $response->setResponseCode(ResponseCode::HTTP_OK);
        $response->setBody('OK');
    } else {
        $response->setResponseCode(ResponseCode::HTTP_BAD_REQUEST);
        $response->setBody('NOT_OK');
    }
});

$app->start();