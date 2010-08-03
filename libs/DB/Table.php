<?php

abstract class Tweester_DB_Table
{
    protected $db;
    protected $pluginPrefix = 'tweester_';
    protected $tableName;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getTableName()
    {
        return $this->db->prefix . $this->pluginPrefix . $this->tableName;
    }

    public function exists()
    {
        return !($this->db->get_var("SHOW TABLES LIKE '".$this->getTableName()."'") != $this->getTableName());
    }

    public function create()
    {
        dbDelta($this->getSQL());
    }

    abstract function getSQL();
    
}

?>