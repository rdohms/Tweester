<?php
/*
Plugin Name: Tweester
Plugin URI: http://github.com/rdohms/Tweester
Description: This plugin allows you to show a page of people who supported your cause on twitter.
Version: 0.2.1
Author: Rafael Dohms
Author URI: http://www.rafaeldohms.com.br
License: CC
*/

//Include plugin files
require_once('libs/Settings.php');
require_once('libs/DB.php');
require_once('libs/Tasks.php');
require_once('libs/ShortCode.php');
require_once('libs/HTMLRenderer.php');
require_once('libs/Twitter.php');

//Define constants for use
define('TWEESTER_MAINFILE', __FILE__);

//Register initialization and activation hooks
register_activation_hook( TWEESTER_MAINFILE, array('Tweester', 'activateSelf') );
add_filter('init', array('Tweester','init'));

/**
 * Core Management Class
 *
 * Responsible for calling everything else and providing access to
 * all auxiliary classes
 *
 * @package Tweester
 * @author Rafael Dohms
 */
class Tweester
{
    /**
     * @var String
     */
    private $pluginPath;

    /**
     * @var Tweester_Settings
     */
    private $settingsManager;

    /**
     * @var Tweester_Tasks
     */
    private $taskManager;

    /**
     * @var Tweester_DB
     */
    private $dbManager;

    /**
     * @var Tweester_ShortCode
     */
    private $shortCodeManager;

    /**
     * Constructor
     * Instantiates all dependent classes
     */
    public function __construct()
    {
        //Define var for global use
        $this->pluginPath = plugins_url()."/".dirname(plugin_basename(TWEESTER_MAINFILE));
        
        //Call dependent Objects
        $this->settingsManager = new Tweester_Settings($this);
        $this->dbManager = new Tweester_DB($this);
        $this->taskManager = new Tweester_Tasks($this);
        $this->shortCodeManager = new Tweester_ShortCode($this);
        
    }

    /**
     * Initializes plugin upon WP initilization process
     *
     * Creates the working instance and inits registrations
     */
    public static function init()
    {
        //Instantiate Class
        $tw = new Tweester();
        
        //Tie in Stylesheet rendering
        add_action("wp_head",array('Tweester_HTMLRenderer','renderCSSTag'));
        
    }

    /**
     * Activation Function
     * Executed when the plugin is activeted in the dashboard. It takes care of
     * managing the plugin tables and cron hooks.
     */
    public static function activateSelf()
    {
        //Instantiate class
        $tw = new Tweester();
        
        //Call DB Manager
        $tw->dbManager->createTables();
        
        //Get our cron entries
        Tweester_Tasks::addCronHooks();

    }

    /**
     * @return Tweester_Settings
     */
    public function getSettingsManager() {
        return $this->settingsManager;
    }

    /**
     * @return Tweester_DB
     */
    public function getDbManager() {
        return $this->dbManager;
    }

    /**
     * @return Tweester_ShortCode
     */
    public function getShortCodeManager() {
        return $this->shortCodeManager;
    }

    /**
     * @return Tweester_Tasks
     */
    public function getTaskManager()
    {
        return $this->taskManager;
    }
}

?>