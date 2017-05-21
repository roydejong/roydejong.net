<?php

global $app;

// Route: Homepage
use Enlighten\Http\Request;
use Enlighten\Http\Response;
use Enlighten\Http\ResponseCode;
use roydejong\dotnet\Generation\AdminToolsGenerator;
use roydejong\dotnet\Generation\HomepageGenerator;
use roydejong\dotnet\Integration\Fitbit;
use roydejong\dotnet\Integration\Instagram;
use roydejong\dotnet\Site\SiteConfig;
use roydejong\dotnet\Site\SiteEngine;

$app->get('/', function (Request $request): Response {
    return SiteEngine::fire(new HomepageGenerator(), $request);
});

// Route: Admin tools
$app->route('/admin', function (Request $request): Response {
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