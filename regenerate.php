<?php

// This file is usually triggered by cron or a similar source.
// It regenerates certain pages.

use Enlighten\Enlighten;
use Enlighten\Http\Request;
use Enlighten\Http\RequestMethod;
use roydejong\dotnet\Generation\HomepageGenerator;
use roydejong\dotnet\Generation\PageGenerator;

require_once "./vendor/autoload.php";

global $app;
$app = new Enlighten();

require_once "./config/globals.php";
require_once "./config/config.php";

/**
 * @var $generators PageGenerator[]
 */
$generators = [
    new HomepageGenerator()
];

$genericRequest = new Request();
$genericRequest->setMethod(RequestMethod::GET);
$genericRequest->setRequestUri('/forced-generation-dummy');

foreach ($generators as $generator) {
    $generator->setRequest($genericRequest);
    $generator->generate();
}