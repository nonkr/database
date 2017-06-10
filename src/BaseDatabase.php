<?php

namespace Josh\Database;

use Josh\Database\Exceptions\NotFoundException;
use Josh\Database\Helper\Conditions;
use Josh\Database\Helper\Response;

class BaseDatabase
{
    use Response, Conditions;

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

        if (!file_exists($databasePath)) {
            touch($databasePath);
            chmod($databasePath, 0777);
        }

        $this->databaseDatas = json_decode(file_get_contents($this->dbPath), true);

        if (!empty($this->databaseDatas)) {
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

        if (!empty($this->databaseDatas)) {
            foreach ($this->tables as $table) {
                if ($tableName === $table) {
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
        if ($this->isTable($table)) {
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
     * @param  array $where
     * @return bool|mixed
     */
    protected function getDataFromTable($table, array $where)
    {
        if ($this->isTable($table)) {

            $datas = $this->databaseDatas[$table];

            $returnDatas = [];

            if (!empty($where)) {

                foreach ($where as $whereClosure) {

                    foreach ($datas as $index => $dbdata) {

                        $condition = $whereClosure['condition'];

                        $value1 = $dbdata[$whereClosure['column']];

                        $value2 = $whereClosure['value'];

                        if ($this->doCondition($condition, $value1, $value2) !== false) {
                            $returnDatas[] = [
                                "_metadata" => $index,
                                "raw" => $dbdata
                            ];
                        }

                    }
                }

            } else {
                $returnDatas = array_merge($returnDatas, $datas);
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
        if (!$this->hasIdKey($datas)) {
            $datas = array_merge(['_id' => $this->getNextId($table)], $datas);
        }

        if ($this->isTable($table)) {
            array_push($this->databaseDatas[$table], $datas);

            file_put_contents($this->dbPath, json_encode($this->databaseDatas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * Delete datas from table
     *
     * @author Nonkr <nonkr@hotmail.com>
     * @since  10 Jun 2017
     * @param  $table
     * @param  array $metadata
     */
    protected function deleteDataFromTable($table, array $metadata)
    {
        if (!empty($metadata) && $this->isTable($table)) {
            $loop = 0;
            foreach ($metadata as $metadatum) {
                array_splice($this->databaseDatas[$table], $metadatum - ($loop++), 1);
            }

            file_put_contents($this->dbPath, json_encode($this->databaseDatas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * Update datas to table
     *
     * @author Nonkr <nonkr@hotmail.com>
     * @since  10 Jun 2017
     * @param  $table
     * @param  array $metadata
     * @param  array $newData
     */
    protected function updateDatasFromTable($table, array $metadata, array $newData)
    {
        if (!empty($metadata) && !empty($newData) && $this->isTable($table)) {
            foreach ($metadata as $metadatum) {
                $this->databaseDatas[$table][$metadatum] = array_merge($this->databaseDatas[$table][$metadatum], $newData);
            }

            file_put_contents($this->dbPath, json_encode($this->databaseDatas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * Get next _id record
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     * @param  $table
     * @return int
     */
    protected function getNextId($table)
    {
        $datas = $this->table($table)->toArray()->all();

        if (!empty($datas)) {
            return $datas[count($datas) - 1]['_id'] + 1;
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

        foreach (array_keys($datas) as $data) {
            if ($data === "_id") {
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
     * @return array
     */
    protected function parseDatas(array $datas)
    {
        if ($this->isTable($datas['table'])) {
            return $this->getDataFromTable($datas['table'], $datas['where']);
        }

        return [];
    }

    /**
     * gete metadata from Datas
     *
     * @author Nonkr <nonkr@hotmail.com>
     * @since  10 Jun 2017
     * @param  array $datas
     * @return array
     */
    protected function getMetadataFromDatas(array $datas)
    {
        $metadatas = [];
        foreach ($datas as $data) {
            array_push($metadatas, $data['_metadata']);
        }
        return $metadatas;
    }

    /**
     * gete raw from Datas
     *
     * @author Nonkr <nonkr@hotmail.com>
     * @since  10 Jun 2017
     * @param  array $datas
     * @return array
     */
    protected function getRawFromDatas(array $datas)
    {
        $raw = [];
        foreach ($datas as $data) {
            array_push($raw, $data['raw']);
        }
        return $raw;
    }
}
