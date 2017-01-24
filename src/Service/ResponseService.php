<?php
namespace BT\Service;

class ResponseService
{

    public static $isSingleton = TRUE;
    private static $instance;

    private static $status = '200 OK';
    private static $headers = array();
    private static $body = null;

    public static $metaTitle = '';
    public static $metaDescription = '';

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
            self::setDefaultMetaAttributes();
        }

        return self::$instance;
    }

    public function setStatus($status)
    {
        self::$status = $status;
        return self::$instance;
    }

    public function addHeader($name, $value)
    {
        self::$headers[$name] = $value;
        return self::$instance;
    }

    public function write($data)
    {
        self::$body .= $data;
        return self::$instance;
    }

    public function flush()
    {
        header('HTTP/1.0 ' . self::$status);
        foreach (self::$headers as $name => $value) {
            header($name . ': ' . $value);
        }


        self::$body = str_replace('{{META_TITLE}}', self::$metaTitle, self::$body);
        self::$body = str_replace('{{META_DESCRIPTION}}', self::$metaDescription, self::$body);
        self::$body = str_replace('{{META_SITENAME}}', $GLOBALS['CONFIG']['CONTENT']['meta_sitename'], self::$body);
        self::$body = str_replace('{{LOGOTITLE}}', $GLOBALS['CONFIG']['CONTENT']['logotitle'], self::$body);


        // TODO: wenn kein ajax Request, dann Header hier reingeben!?
        // Oder steuert das besser der Controller?

        echo self::$body;
    }

    public function redirect($location)
    {
        header('HTTP/1.0 ' . self::$status);
        header('Location: ' . $location);
        die();
    }

    private static function setDefaultMetaAttributes() {
        self::$metaTitle = $GLOBALS['CONFIG']['CONTENT']['default_meta_title'];
        self::$metaDescription = $GLOBALS['CONFIG']['CONTENT']['default_meta_description'];
    }

    public function setMetaTitle ($title) {
        self::$metaTitle = $title . ' | ' . $GLOBALS['CONFIG']['CONTENT']['logotitle'];
    }

    public function setMetaDescription ($description) {
        self::$metaDescription = $description;
    }

}
