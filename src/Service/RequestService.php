<?php
namespace BT\Service;

class RequestService
{

    public static $isSingleton = TRUE;
    private static $instance;

    private static $requestedUrl = null;
    private static $getParameters = null;
    private static $postParameters = null;


    private function __construct()
    {
        // SINGLETON OBJECT
    }

    private function __clone()
    {
        // SINGLETON OBJECT
    }

    public static function getInstance()
    {

        if (!isset(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class;

            self::$requestedUrl = $_SERVER['REQUEST_URI'];
            self::readParameters();
        }

        return self::$instance;
    }


    private static function readParameters()
    {

        self::$getParameters = $_GET;
        self::$postParameters = $_POST;

    }


    public function getRequestedUrl()
    {
        return self::$requestedUrl;
    }


    public function issetGetParameter($parameterName)
    {
        return isset(self::$getParameters[$parameterName]);
    }


    public function issetPostParameter($parameterName)
    {
        return isset(self::$postParameters[$parameterName]);
    }


    public function getGetParameterNames()
    {
        return array_keys(self::$getParameters);
    }


    public function getPostParameterNames()
    {
        return array_keys(self::$postParameters);
    }


    public function getGetParameter($parameterName)
    {
        if (isset(self::$getParameters[$parameterName])) {
            return self::$getParameters[$parameterName];
        }
        return null;
    }


    public function getPostParameter($parameterName)
    {
        if (isset(self::$postParameters[$parameterName])) {
            return self::$postParameters[$parameterName];
        }
        return null;
    }

    public function getHeader($headerName)
    {

        $headerName = 'HTTP_' . strtoupper(str_replace('-', '_', $headerName));
        if (isset($_SERVER[$headerName])) {
            return $_SERVER[$headerName];
        }
        return null;
    }

#****f* _libs/requestHandler/isAjaxRequest()
# FUNCTION
# Pr�ft, ob die aktuelle Anfrage per Ajax kam und gibt einen Boolean-Wert zur�ck.
# Dabei wird nur einmal gepr�ft und das Ergebnis im ControllerObject gecached.
#
# RESULT
# * [Bool] false, wenn Request nicht per Ajax kam.
# * [Bool] true, wenn Request nicht per Ajax kam.
#***
    public function isAjaxRequest()
    {

        if (self::getHeader('X_REQUESTED_WITH') == 'xmlhttprequest') {
            return true;
        }

        return false;
    }

}
