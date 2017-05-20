<?php

use Enlighten\Enlighten;
use Enlighten\Http\Request;

use Enlighten\Http\Response;
use roydejong\dotnet\Generation\HomepageGenerator;
use roydejong\dotnet\Site\SiteEngine;

require_once "../vendor/autoload.php";

define('PATH_PUBLIC', __DIR__);
define('PATH_PROJECT', realpath(__DIR__ . "/../"));
define('PATH_SRC', PATH_PROJECT . "/src");
define('PATH_COMPILATION', PATH_PROJECT . "/compiled");
define('PATH_TEMPLATES', PATH_PROJECT . "/templates");

$app = new Enlighten();

$app->get('/', function (Request $request): Response {
    return SiteEngine::fire(new HomepageGenerator(), $request);
});

$app->start();