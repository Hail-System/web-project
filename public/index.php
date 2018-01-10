<?php
// Absolute path to the application base folder
defined('BASE_PATH') || define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

Hail\Framework::listen();