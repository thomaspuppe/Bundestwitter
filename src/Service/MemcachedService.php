<?php
namespace BT\Service;

class MemcachedService
{

    public static $isSingleton = true;
    private static $instance;
    private static $memcached = null;

    private function __construct()
    {
        // SINGLETON OBJECT
    }

    private function __clone()
    {
        // SINGLETON OBJECT
    }

    # TODO: cooler machen, von einem Interface abgehen
    # (von dem sich auch der DatabaseCache und der FileCache bedienen)
    # interface CacheableInterface
    # {
    #    public function set($key, $data);
    #    public function get($key);
    #    public function delete($key);
    #    public function exists($key);
    # }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class;

            self::$memcached = new \Memcached();
            self::$memcached->addServer('127.0.0.1', 11211);
        }
        return self::$instance;
    }

    public function set($key, $data, $cachetime = 3600)
    {
        return $memcached->set($key, $data, $cachetime);
    }

    public function get($key)
    {
        return self::$memcached->get($key);
    }
}
