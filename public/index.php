<?php

use Zend\Mvc\Application;
use Zend\Stdlib\ArrayUtils;

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));

/*
 * ログを出力する
 * ・すべての引数のvar_dump結果をファイルに記録する
 * ・出力先ディレクトリは、data/logs
 * ・第一引数が xxx.log という文字列だった場合、そのファイル名で出力。それ以外の場合は app.debug に出力する
 */
function _applog()
{
    $args = func_get_args();

    $logFileName = 'app.debug';
    if (is_string($args[0]) && preg_match('/^(.+)\.log$/', $args[0])) {
        $logFileName = array_shift($args);
    }

    $logDir = __DIR__ . "/../data/logs/";
    $logFile = $logDir . $logFileName;

    ob_start();
    foreach ($args as $arg) {
        var_dump($arg);
    }

    $logHead = date('[Y-m-d H:i:s]');
    $logHead .= array_key_exists('REMOTE_ADDR', $_SERVER) ? sprintf(" [remote_address: %s]", $_SERVER["REMOTE_ADDR"]) : '';

    $logText = $logHead . "\n" . ob_get_contents() . "\n";
    ob_end_clean();
    file_put_contents($logFile, $logText, FILE_APPEND);
}
/**
 * エラーログを出力する
 */
function _appError()
{
    _applog('app_error.log', func_get_args());
}

// Composer autoloading
include __DIR__ . '/../vendor/autoload.php';

if (! class_exists(Application::class)) {
    throw new RuntimeException(
        "Unable to load application.\n"
        . "- Type `composer install` if you are developing locally.\n"
        . "- Type `vagrant ssh -c 'composer install'` if you are using Vagrant.\n"
        . "- Type `docker-compose run zf composer install` if you are using Docker.\n"
    );
}

// Retrieve configuration
$appConfig = require __DIR__ . '/../config/application.config.php';
if (file_exists(__DIR__ . '/../config/development.config.php')) {
    $appConfig = ArrayUtils::merge($appConfig, require __DIR__ . '/../config/development.config.php');
}

// Run the application!
Application::init($appConfig)->run();
