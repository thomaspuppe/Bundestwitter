<?php
namespace BT;

// Autoload

class Kernel
{
    public static function classLoader($class)
    {

        $class = str_replace('BT\\', '', $class);
        $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

        if (strpos($class, 'Service') !== false ||
            strpos($class, 'Controller') !== false ||
            strpos($class, 'Model') !== false ||
            strpos($class, 'Repository') !== false
        ) {
            if (file_exists(ROOT . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $class . '.php')) {
                require_once(ROOT . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $class . '.php');
                return true;
            }
        }
        return false;
    }
}

spl_autoload_register('\BT\Kernel::classLoader');

// Default Settings
date_default_timezone_set('Europe/Berlin');

// Error Tracking
if (ENVIRONMENT != 'DEV') {
    if (!defined('CURLOPT_CONNECTTIMEOUT_MS')) {
        define('CURLOPT_CONNECTTIMEOUT_MS', 156);
    }
    if (!defined('CURLOPT_TIMEOUT_MS')) {
        define('CURLOPT_TIMEOUT_MS', 156);
    }

    require(ROOT . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Service' . DIRECTORY_SEPARATOR . 'Raven' . DIRECTORY_SEPARATOR . 'Autoloader.php');
    \Raven_Autoloader::register();

    $GLOBALS['ERRORHANDLER'] = new \Raven_Client($GLOBALS['CONFIG']['GETSENTRY']['client']);

    // Install error handlers and shutdown function to catch fatal errors
    $error_handler = new \Raven_ErrorHandler($GLOBALS['ERRORHANDLER']);
    $error_handler->registerExceptionHandler();
    $error_handler->registerErrorHandler();
    $error_handler->registerShutdownFunction();
} else {
    error_reporting(E_ALL);
}
