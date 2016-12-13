<?php

namespace Josh\Database;

use Josh\Database\Helper\Response;
use Josh\Database\Helper\Conditions;
use Josh\Database\Exceptions\NotFoundException;

class BaseDatabase
{
    use Response , Conditions;

    /**
     * Database datas
     *
     * @var array
     */
    private $databaseDatas = [];

    /**
     * database path
     *
     * @var string
     */
    private $dbPath = null;

    /**
     * Tables of databse
     *
     * @var array
     */
    private $tables = [];

    /**
     * Set type of response
     *
     * @var string
     */
    public $typeOfResponse = 'json';

    /**
     * BaseDatabase constructor.
     *
     * @param  null $databasePath
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     */
    public function __construct($databasePath = null)
    {
        $this->dbPath = $databasePath;

        if(! file_exists($databasePath)) {
            touch($databasePath);
            chmod($databasePath, 0777);
        }

        $this->databaseDatas = json_decode(file_get_contents($this->dbPath), true);

        if(! empty($this->databaseDatas)) {
            $this->tables = array_keys($this->databaseDatas);
        }
    }

    /**
     * Check table in database
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     * @param  $tableName
     * @return bool|NotFoundException
     */
    protected function isTable($tableName)
    {

        if(! empty($this->databaseDatas)) {
            foreach ($this->tables as $table){
                if($tableName === $table) {
                    return true;
                    break;
                }
            }
        }

        return new NotFoundException("Table { $tableName } notfound");
    }

    /**
     * Create table
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     * @param  $table
     */
    protected function createTable($table)
    {
        if($this->isTable($table)) {
            $this->databaseDatas[$table] = [];

            file_put_contents($this->dbPath, json_encode($this->databaseDatas));
        }
    }

    /**
     * Get datas from table
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     * @param  $table
     * @return bool|mixed
     */
    protected function getDataFromTable($table,array $where)
    {
        if($this->isTable($table)) {

            $datas = $this->databaseDatas[$table];

            $returnDatas = [];

            if(! empty($where)) {

                foreach ($where as $whereClosure){

                    foreach ($datas as $dbdata){

                        $condition = $whereClosure['condition'];

                        $value1 = $dbdata[$whereClosure['column']];

                        $value2 = $whereClosure['value'];

                        if($this->doCondition($condition,$value1,$value2) !== false) {
                            $returnDatas[] = $dbdata;
                        }

                    }
                }

            } else {
                $returnDatas = array_merge($returnDatas,$datas);
            }

            return $returnDatas;
        }

        return false;
    }

    /**
     * Insert datas to table
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     * @param  $table
     * @param  array $datas
     */
    protected function insertDatasToTable($table, array $datas)
    {
        if(! $this->hasIdKey($datas)) {
            $datas = array_merge([ 'id' => $this->getNextId($table) ], $datas);
        }

        if($this->isTable($table)) {
            array_push($this->databaseDatas[$table], $datas);

            file_put_contents($this->dbPath, json_encode($this->databaseDatas));
        }
    }

    /**
     * Get next id record
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     * @param  $table
     * @return int
     */
    protected function getNextId($table)
    {
        $datas = $this->table($table)->toArray()->all();

        if(! empty($datas)) {
            return $datas[count($datas) - 1]['id'] + 1;
        }

        return 1;
    }

    /**
     * Check has key
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     * @param  $table
     * @return int
     */
    protected function hasIdKey($datas)
    {

        foreach (array_keys($datas) as $data){
            if($data === "id") {
                return true;
            }
        }

        return false;
    }

    /**
     * Set json response
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     * @return $this
     */
    public function toJson()
    {
        $this->typeOfResponse = 'json';

        return $this;
    }

    /**
     * Set array response
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     * @return $this
     */
    public function toArray()
    {
        $this->typeOfResponse = 'array';

        return $this;
    }

    /**
     * Parse datas of table
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     * @param  array $datas
     * @return array|bool
     */
    protected function parseDatas(array $datas)
    {
        if($this->isTable($datas['table'])) {
            return $this->getDataFromTable($datas['table'], $datas['where']);
        }

        return false;
    }

}