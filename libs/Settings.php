<?php
include('Settings/Option.php');
include('Settings/Option/Query.php');
include('Settings/Option/Excludes.php');

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
    private $configFields = array();
    
    public function __construct($coreManager)
    {
        $this->coreManager = $coreManager;
        
        add_action('admin_init', array($this, 'registerSections'));
        add_action('admin_menu', array($this, 'registerMenuEntries'));
        add_action('admin_init', array($this, 'addFields'));

    }
    
    public function registerMenuEntries()
    {
        add_options_page('Tweester - Configuration', 'Tweester', 'administrator', TWEESTER_MAINFILE, array($this, 'renderSettingsPage'));
    }
    
    public function registerSections()
    {
        add_settings_section(self::SECTION_SEARCH, 'Search and List', array($this, 'renderSearchSection'), TWEESTER_MAINFILE);
        add_settings_section(self::SECTION_TAG, 'Inserting Tag', array($this, 'renderTagSection'), TWEESTER_MAINFILE);
        add_settings_section(self::SECTION_CRON, 'Updating Author list', array($this, 'renderCronSection'), TWEESTER_MAINFILE);
    }
    
    public function addFields()
    {
        $this->configFields[] = new Tweester_Settings_Option_Query(self::SECTION_SEARCH);
        $this->configFields[] = new Tweester_Settings_Option_Excludes(self::SECTION_SEARCH);
    }
    
    public function renderSearchSection()
    {
        echo '<p>Options for finding users who will be listed</p>';
    }
    
    public function renderTagSection()
    {
        echo '<p>To show the list of users on a post or page, just insert the code below into it</p>';
        echo '<p>[tweester_list]</p>';
    }
    
    public function renderCronSection()
    {
        echo '<p>Tweester schedules itself to be executed every hour, using WordPress\' built-in scheduling. If needed you can for a DB update using the button below.</p>';
        echo '<p><a href="http://wp.macdohms/wp-admin/options-general.php?page=tweester/tweester.php&exec_action=run_update">Force update</a></p>';
    }
    
    function renderSettingsPage()
    {
        //Run attached actions
        $alert = $this->executeTasks();
        echo $alert;
        //Render HTML
        echo '<div>';
        echo '<h2>Tweester</h2>';
        echo 'Configuration for the Tweester plugin';
        echo '<form action="options.php" method="post">';
        
        settings_fields(TWEESTER_MAINFILE);
        do_settings_sections(TWEESTER_MAINFILE);
        
        echo '<p class="submit"><input type="submit" name="submit" class="button-primary" value="Save Changes" /></p>';
        echo '</form></div>';

    }

    /**
     *
     * @return <type>
     *
     * @todo find better way of accessing $_GET properties
     */
    private function executeTasks()
    {
        //Read from $_GET
        if (array_key_exists('exec_action', $_GET)) {
            $action = $_GET['exec_action'];
        
            switch($action){
                case 'run_update':
                    $this->coreManager->getTaskManager()->updateAuthors();
                    $msg = "Authors Updated";
                break;
            }
        
            return $msg;
        } else {
            return;
        }
    }
}

?>