<?php
//
//use App\Kernel;
//
//require_once dirname(__DIR__).'/vendor/autoload_runtime.php';
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//ini_set('log_errors', 1);
//ini_set('error_log', dirname(__DIR__) . '/var/log/php-errors.log');
//
//// Check if essential directories exist and are writable
//$varDir = dirname(__DIR__) . '/var';
//$logDir = $varDir . '/log';
//$cacheDir = $varDir . '/cache';
//
//if (!is_dir($logDir)) {
//    mkdir($logDir, 0755, true);
//}
//if (!is_dir($cacheDir)) {
//    mkdir($cacheDir, 0755, true);
//}
//
//return function (array $context) {
//    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
//};


use App\Kernel;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool)$context['APP_DEBUG']);
};
