<?php
/**
 * Authors Table Class
 *
 * Wraps Authors table in database
 *
 * @package Tweester
 * @subpackage DB
 * @author Rafael Dohms
 */
class Tweester_DB_Table_Authors extends Tweester_DB_Table
{
    /**
     * @var string
     */
    protected $tableName = 'authors';

    /**
     * Returns table SQL
     * @return string 
     */
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