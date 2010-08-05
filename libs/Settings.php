<?php
include('Settings/Option.php');
include('Settings/Option/Query.php');
include('Settings/Option/Excludes.php');

/**
 * Settings Management Class
 *
 * This class handles the plugin's custom Settings page and its properties,
 * creating menu links and pages as well as registering fields
 *
 * @package Tweester
 * @subpackage Settings
 * @author Rafael Dohms
 */
class Tweester_Settings
{
    const SECTION_SEARCH = 'tweester_section_search';
    const SECTION_TAG = 'tweester_section_tag';
    const SECTION_CRON = 'tweester_section_cron';

    /**
     * Central Core Manager
     * @var Tweester
     */
    private $coreManager;

    /**
     * @var array
     */
    private $configFields = array();

    /**
     * Registers init/menu hooks
     * 
     * @param <type> $coreManager
     */
    public function __construct($coreManager)
    {
        $this->coreManager = $coreManager;
        
        add_action('admin_init', array($this, 'registerSections'));
        add_action('admin_menu', array($this, 'registerMenuEntries'));
        add_action('admin_init', array($this, 'addFields'));

    }

    /**
     * Create plugin menu entry
     */
    public function registerMenuEntries()
    {
        add_options_page('Tweester - Configuration', 'Tweester', 'administrator', TWEESTER_MAINFILE, array($this, 'settingsPage'));
    }

    /**
     * Register sections available in the settings page
     */
    public function registerSections()
    {
        add_settings_section(self::SECTION_SEARCH, 'Search and List', array($this, 'renderSearchSection'), TWEESTER_MAINFILE);
        add_settings_section(self::SECTION_TAG, 'Inserting Tag', array($this, 'renderTagSection'), TWEESTER_MAINFILE);
        add_settings_section(self::SECTION_CRON, 'Updating Author list', array($this, 'renderCronSection'), TWEESTER_MAINFILE);
    }

    /**
     * Instantiate our configuration fields
     */
    public function addFields()
    {
        $this->configFields['query'] = new Tweester_Settings_Option_Query(self::SECTION_SEARCH, $this->coreManager);
        $this->configFields['excludes'] = new Tweester_Settings_Option_Excludes(self::SECTION_SEARCH, $this->coreManager);
    }

    /**
     * Render the HTML for the Search section
     */
    public function renderSearchSection()
    {
        echo '<p>Options for finding users who will be listed</p>';
    }

    /**
     * Render the HTML for the tag section
     */
    public function renderTagSection()
    {
        echo '<p>To show the list of users on a post or page, just insert the code below into it</p>';
        echo '<p>[tweester_list]</p>';
    }

    /**
     * Render the HTML for the Cron Section
     */
    public function renderCronSection()
    {
        global $pagenow, $plugin_page;
        $forceUrl = $pagenow . "?page=" . $plugin_page . "&exec_action=run_update";

        echo '<p>Tweester schedules itself to be executed every hour, using WordPress\' built-in scheduling. If needed you can for a DB update using the button below.</p>';
        echo '<p><a href="'.$forceUrl.'">Force update</a></p>';
    }

    /**
     * Render Settings page and execute tasks
     */
    function settingsPage()
    {
        //Run attached actions
        $alert = $this->executeTasks();

        //Render HTML
        Tweester_HTMLRenderer::renderSettingsPage($alert);

    }

    /**
     * Detects actions that need to be executed by the Settings page
     *
     * @todo find better way of accessing $_GET properties
     * @return string Message to be displayed
     */
    private function executeTasks()
    {
        //Read from $_GET
        if (isset($_GET['exec_action'])) {
            $action = $_GET['exec_action'];
        } else {
            $action = null;
        }
        
        switch($action){
            case 'run_update':
                $this->coreManager->getTaskManager()->updateAuthors();
                $msg = "Authors Updated";
            break;
            default:
                $msg = null;
        }

        return $msg;
    }

    /**
     * Gets a Option object
     * @param string $name
     * @return Tweester_Settings_Option
     */
    public function getOption($name)
    {
        if (array_key_exists($name, $this->configFields)){
            return $this->configFields[$name];
        } else {
            return false;
        }
    }
}

?>