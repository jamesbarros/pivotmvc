<?php

// Set the base path 
defined('PIVOTMVC_BASE') || define('PIVOTMVC_BASE', realpath(__DIR__ . '/../'));

// Set the environment from, well, the environment
defined('PIVOTMVC_ENV') || define('PIVOTMVC_ENV', getenv('PIVOTMVC_BASE') ?: 'production');

// Autoloading
require PIVOTMVC_BASE . '/vendor/autoload.php';
