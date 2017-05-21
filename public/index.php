<?php

use Enlighten\Enlighten;

require_once "../vendor/autoload.php";

global $app;
$app = new Enlighten();

require_once "../config/globals.php";
require_once "../config/config.php";
require_once "../config/routes.php";

$app->start();