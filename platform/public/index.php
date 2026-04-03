<?php

/**
 * Touristik Platform — Front Controller
 *
 * All requests are routed through this file.
 */

define('BASE_PATH', dirname(__DIR__));

// Load the core App
require_once BASE_PATH . '/core/App.php';

// Boot and run
$app = new \Core\App();
$app->run();
