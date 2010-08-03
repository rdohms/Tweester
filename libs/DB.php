<?php

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

require_once('DB/Table.php');
require_once('DB/Table/Authors.php');
require_once('DB/Table/UserDataCache.php');

class Tweester_DB
{
    
    private $wpdb;
    private $tables = array();

    /**
     * Central Core Manager
     * @var Tweester
     */
    private $coreManager;
    
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
    
    public function createTables()
    {
        //Create Plugin Tables
        foreach($this->tables as $table) {
            if(!$table->exists()) {
                $table->create();
            }
        }
    }
    
    public function getTableNameFor($table)
    {

        if (!array_key_exists($table, $this->tables)) {
            return false;
        }
        
        return $this->tables[$table]->getTableName();
    }
    
    public function __call($name, $arguments) 
    {
        return call_user_func_array( array($this->wpdb, $name), $arguments );
    }

    public function __get($name)
    {
        return $this->wpdb->$name;
    }
}

?>