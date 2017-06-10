<?php

/**
 * Database UnitTest
 *
 * @author Alireza Josheghani <a.josheghani@anetwork.ir>
 * @since 19 Nov 2016
 */

class DatabaseUnitTest extends PHPUnit_Framework_TestCase {

    /**
     * Database path
     *
     * @var string
     */
    protected $dbPath = null;

    /**
     * DatabaseUnitTest constructor.
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  19 Nov 2016
     */
    public function __construct()
    {
        parent::__construct();

        $this->dbPath = __DIR__ . '/../database.json';
        DB::setBasePath($this->dbPath);
    }

    /**
     * Insert datas to database
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  19 Nov 2016
     */
    public function testInsertDatas()
    {
        DB::table('users')->insert([
            'firstname' => 'Alireza',
            'lastname' => 'Josheghani',
            'age' => 20,
            'email' => 'josheghani.dev@gmail.com'
        ]);

        $results = DB::table('users')->where('_id','=',1)->toArray()->first();

        $this->assertEquals($results['_id'],1);

        $this->assertEquals($results['firstname'],'Alireza');

        $this->assertEquals($results['lastname'],'Josheghani');

        $this->assertEquals($results['age'],20);

        $this->deleteDatabase();
    }

    /**
     * Delete database file
     *
     * @author Alireza Josheghani <josheghani.dev@gmail.com>
     * @since  19 Nov 2016
     */
    public function deleteDatabase()
    {
        unlink($this->dbPath);
    }

}
