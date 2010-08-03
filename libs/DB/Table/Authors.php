<?php

class Tweester_DB_Table_Authors extends Tweester_DB_Table
{
    protected $tableName = 'authors';
    
    public function getSQL()
    {
        $sql = "CREATE TABLE  ".$this->getTableName()." (
                  id INT NOT NULL AUTO_INCREMENT ,
                  twitter VARCHAR(70) NULL ,
                  added_on DATETIME NULL ,
                PRIMARY KEY  (id) ,
                UNIQUE KEY twitter_UNIQUE (twitter) )";
        
        return $sql;
    }
}

?>