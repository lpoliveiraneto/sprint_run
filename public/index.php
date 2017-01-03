<?php

define('REQUEST_MICROTIME', microtime(true));

error_reporting(E_ALL | E_STRICT);

require_once __DIR__.'/../vendor/autoload.php';

// All system's routes
require_once(__DIR__ . '/../app/routes.php');
