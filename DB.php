<?php


use Josh\Database\Database;

class DB {

    /**
     * base path of database
     *
     * @var string
     */
    protected static $basePath;

    /**
     * Set database basePath
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  19 Nov 2016
     * @param $path
     */
    public static function setBasePath($path)
    {
        self::$basePath = $path;
    }

    /**
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  19 Nov 2016
     * @param $method
     * @param $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = new Database(self::$basePath);

        return $instance->$method(...$args);
    }

}