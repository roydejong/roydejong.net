<?php

define('PATH_PUBLIC', __DIR__);
define('PATH_PROJECT', realpath(__DIR__ . "/../"));
define('PATH_SRC', PATH_PROJECT . "/src");
define('PATH_COMPILATION', PATH_PROJECT . "/compiled");
define('PATH_TEMPLATES', PATH_PROJECT . "/templates");

function getEnableDebugMode(): bool
{
    return (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] === 'dev.roydejong.net');
}

define('DEBUG_ENABLED', getEnableDebugMode());
