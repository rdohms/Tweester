<?php
/**
 * Query Field Class
 *
 * Manages the cron_run_time field in configuration
 *
 * @package Tweester
 * @subpackage Settings
 * @author Rafael Dohms
 */
class Tweester_Settings_Option_CronRunTime extends Tweester_Settings_Option
{
    /**
     * @var string
     */
    protected $fieldName = 'tweester_cron_run_time';

    /**
     * @var string
     */
    protected $label = '';

    /**
     * Renders form input and description
     */
    public function render() 
    {
    }

    /**
     * Executes needed actions when option is updated
     */
    public function onUpdate()
    {
        
    }
}


?>