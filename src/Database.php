<?php

namespace Josh\Database;

use Josh\Database\Exceptions\NotFoundException;

class Database extends BaseDatabase
{

    /**
     * Set where closure
     *
     * @var array
     */
    private $where = [];

    /**
     * Set table name
     *
     * @var string
     */
    private $table = null;

    /**
     * Database constructor.
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     * @param  null $databasePath
     */
    public function __construct($databasePath = null)
    {
        if ($databasePath !== null) {
            return parent::__construct($databasePath);
        }

        throw new Exceptions\NotFoundException('Database notfound.');
    }

    /**
     * Set table name
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     * @param  $name
     * @return $this
     */
    public function table($name)
    {
        $this->table = $name;

        return $this;
    }

    /**
     * Set where closure
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     * @param  $column
     * @param  $condition
     * @param  $value
     * @return $this
     */
    public function where($column, $condition, $value)
    {
        $this->where[] = [
            'column' => $column, 'condition' => $condition, 'value' => $value
        ];

        return $this;
    }

    /**
     * Insert to table
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     * @param  array $datas
     */
    public function insert(array $datas)
    {
        if ($this->isTable($this->table) instanceof NotFoundException) {
            $this->createTable($this->table);
        }

        $this->insertDatasToTable($this->table, $datas);
    }

    /**
     * Get all datas
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     */
    public function all()
    {
        $datas = $this->parseDatas([
            'table' => $this->table, 'where' => $this->where
        ]);

        if ($this->typeOfResponse === 'json') {
            $this->responseJson($datas);
        }

        return $datas;
    }

    /**
     * Get first record
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  18 Nov 2016
     */
    public function first()
    {
        $datas = $this->getRawFromDatas($this->parseDatas([
            'table' => $this->table, 'where' => $this->where
        ]));

        if ($this->typeOfResponse === 'json') {
            $this->responseJson($datas[0]);
        }

        return $datas[0];
    }

    /**
     * Get latest record
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  13 Dec 2016
     */
    public function latest()
    {
        $datas = $this->getRawFromDatas($this->parseDatas([
            'table' => $this->table, 'where' => $this->where
        ]));

        $count = count($datas) - 1;

        if ($this->typeOfResponse === 'json') {
            $this->responseJson($datas[$count]);
        }

        return $datas[$count];
    }

    /**
     * delete records
     *
     * @author Nonkr <nonkr@hotmail.com>
     * @since  10 Jun 2017
     */
    public function delete()
    {
        $metadata = $this->getMetadataFromDatas($this->parseDatas([
            'table' => $this->table, 'where' => $this->where
        ]));

        $this->deleteDataFromTable($this->table, $metadata);
    }

    /**
     * update records
     *
     * @author Nonkr <nonkr@hotmail.com>
     * @since  10 Jun 2017
     * @param  array $newData
     */
    public function update(array $newData)
    {
        $metadata = $this->getMetadataFromDatas($this->parseDatas([
            'table' => $this->table, 'where' => $this->where
        ]));

        $this->updateDatasFromTable($this->table, $metadata, $newData);
    }
}
