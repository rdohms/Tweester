<?php
/*
Plugin Name: Tweester
Plugin URI: --
Description: This plugin allows you to show a page of people who supported your cause on twitter.
Version: 0.1
Author: Rafael Dohms
Author URI: http://www.rafaeldohms.com.br
License: CC
*/

include('libs/Settings.php');
include('libs/DB.php');
include('libs/Tasks.php');
include('libs/ShortCode.php');
include('libs/HTMLRenderer.php');
include('libs/Twitter.php');



//Define constants for use
define('TWEESTER_MAINFILE', __FILE__);

register_activation_hook( TWEESTER_MAINFILE, array('Tweester', 'activateSelf') );
add_filter('init', array('Tweester','init'));

class Tweester
{
    private $db;
    
    private $pluginPath;
    private $settingsManager;
    private $taskManager;
    private $dbManager;
    private $shortCodeManager;
        
    public function __construct()
    {
        //Define var for global use
        $this->pluginPath = get_option('siteurl')."/wp-content/plugins/".dirname(plugin_basename(TWEESTER_MAINFILE));
        
        //Call dependent Objects
        $this->settingsManager = new Tweester_Settings($this);
        $this->dbManager = new Tweester_DB($this);
        $this->taskManager = new Tweester_Tasks($this);
        $this->shortCodeManager = new Tweester_ShortCode($this);
        
    }
    
    public static function init()
    {
        //Instantiate Class
        $tw = new Tweester();
        
        //Tie in Stylesheet rendering
        add_action("wp_head",array('Tweester_HTMLRenderer','renderCSSTag'));
        
    }

    public static function activateSelf()
    {
        //Instantiate class
        $tw = new Tweester();
        
        //Call DB Manager
        $tw->dbManager->createTables();
        
        //Get our cron entries
        Tweester_Tasks::addCronHooks();

    }

    public function getSettingsManager() {
        return $this->settingsManager;
    }

    public function getDbManager() {
        return $this->dbManager;
    }

    public function getShortCodeManager() {
        return $this->shortCodeManager;
    }
    
    public function getTaskManager()
    {
        return $this->taskManager;
    }
}

?>