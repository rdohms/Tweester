<?php
/**
 * Table Class
 *
 * This is an abstract class for representing custom plugin tables
 *
 * @abstract
 * @package Tweester
 * @subpackage DB
 * @author Rafael Dohms
 */
abstract class Tweester_DB_Table
{
    /**
     * @var Tweester_DB
     */
    protected $db;

    /**
     * Internal plugin tablename prefix
     * @var string
     */
    protected $pluginPrefix = 'tweester_';

    /**
     * Table name (defines in extending objects)
     * @var string
     */
    protected $tableName;

    /**
     * Generic constructor, receives a DB Manager instance
     * @param <type> $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Combines Wordpress prefix, internal plugin prefix and table name
     * returning a fully qualified table name
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->db->prefix . $this->pluginPrefix . $this->tableName;
    }

    /**
     * Checks if table exists in the database
     * @return boolean
     */
    public function exists()
    {
        return !($this->db->get_var("SHOW TABLES LIKE '".$this->getTableName()."'") != $this->getTableName());
    }

    /**
     * Executes Delta for database, creating/updating it
     */
    public function create()
    {
        dbDelta($this->getSQL());
    }

    /**
     * Returns Table SQL, implemented by extending classes
     * @abstract
     */
    abstract function getSQL();
    
}

?>