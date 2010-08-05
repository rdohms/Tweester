<?php
/**
 * User Data Cache Table Class
 *
 * Wraps User Data Cache table in database
 *
 * @package Tweester
 * @subpackage DB
 * @author Rafael Dohms
 */
class Tweester_DB_Table_UserDataCache extends Tweester_DB_Table
{
    /**
     * @var string
     */
    protected $tableName = 'userdatacache';

    /**
     * Returns Table SQL
     * @return string
     */
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