<?php
//Include upgrade file for dbDelta function
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

//Include Table classes
require_once('DB/Table.php');
require_once('DB/Table/Authors.php');
require_once('DB/Table/UserDataCache.php');

/**
 * Database Management Class
 *
 * This class wraps all DB actions for custom plugin tables. It wraps the WPDB
 * object forwarding functions to it while also handling table name and related
 * operations custom to the plugin.
 *
 * @package Tweester
 * @subpackage DB
 * @author Rafael Dohms
 */
class Tweester_DB
{
    /**
     * @var wpdb
     */
    private $wpdb;

    /**
     * @var array
     */
    private $tables = array();

    /**
     * Central Core Manager
     * @var Tweester
     */
    private $coreManager;

    /**
     * Store reference to Core Manager and retrieve WP's wpdb object. Also holds
     * list of custom plugin tables
     *
     * @global wpdb $wpdb
     * @param Tweester $coreManager
     */
    public function __construct($coreManager)
    {
        //Get WP's DB object
        global $wpdb;
        $this->wpdb = $wpdb;

        //Set CoreManager
        $this->coreManager = $coreManager;

        //Declare Plugin Tables
        $this->tables['authors'] = new Tweester_DB_Table_Authors($this);
        $this->tables['user_data_cache'] = new Tweester_DB_Table_UserDataCache($this);
    }

    /**
     * Runs through custom plugin tables checking if they exists, and creating
     * them
     */
    public function createTables()
    {
        //Create Plugin Tables
        foreach($this->tables as $table) {
            if(!$table->exists()) {
                $table->create();
            }
        }
    }

    /**
     * Retrieve full name for a custom plugin table, this get the table object
     * instance and retrieves the db based on its friendly name
     *
     * @param string $table friendly table name
     * @return string
     */
    public function getTableNameFor($table)
    {

        if (!array_key_exists($table, $this->tables)) {
            return false;
        }
        
        return $this->tables[$table]->getTableName();
    }

    /**
     * Magic method.
     * Forwards calls to any undefined method to the WPDB object
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments) 
    {
        return call_user_func_array( array($this->wpdb, $name), $arguments );
    }

    /**
     * Magic Method
     * Return value of undeclared properties forwarding the call to the WPDB
     * object
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->wpdb->$name;
    }
}

?>