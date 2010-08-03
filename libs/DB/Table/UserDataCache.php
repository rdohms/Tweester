<?php

class Tweester_DB_Table_UserDataCache extends Tweester_DB_Table
{
    protected $tableName = 'userdatacache';
    
    public function getSQL()
    {
        $sql = "CREATE TABLE  ".$this->getTableName()." (
                  twitter varchar(70) NOT NULL ,
                  data TEXT NULL ,
                  cached_on DATETIME NULL ,
                PRIMARY KEY  (twitter) )";
        
        return $sql;
    }
}

?>