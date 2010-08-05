<?php
/**
 * Tasks Management Class
 *
 * This class wraps the plugin's tasks which are associated with cron jobs
 *
 * @package Tweester
 * @subpackage Tasks
 * @author Rafael Dohms
 */
class Tweester_Tasks
{
    /**
     * Central Core Manager
     * @var Tweester
     */
    private $coreManager;

    /**
     * Registers actions to the available cron hooks
     * @param Tweester $coreManager
     */
    public function __construct($coreManager)
    {
        //Set CoreManager
        $this->coreManager = $coreManager;
        
        //Tie our functions to our cron tasks
        add_action("tweester_searcher", array($this, 'updateAuthors'));
        
    }

    /**
     * Updates the Authors list executing a Twitter search for the parameters
     * configured
     */
    public function updateAuthors()
    {
        $results = Tweester_Twitter::getSearchResults($this->coreManager->getSettingsManager()->getOption('query')->getValue());

        //Get list of excludes
        $excludes = $this->coreManager->getSettingsManager()->getOption('excludes')->getValue();
        $excludedArray = explode(',', $excludes);
        $excludedArray = array_map('trim', $excludedArray);

        if ($results != false) {
            //Process, store supporters
            foreach($results as $tweet){
                //Get Username
                $username = $tweet->from_user;

                //Skip if in excluded
                if (in_array($username, $excludedArray)) {
                    continue;
                }

                //Check if already in base
                $data = $this->coreManager->getDbManager()->get_row("SELECT * FROM ".$this->coreManager->getDbManager()->getTableNameFor('authors')." WHERE twitter = '".$username."'");

                //Add to base if not
                if ($data == null){
                    $this->coreManager->getDbManager()->insert( $this->coreManager->getDbManager()->getTableNameFor('authors'), array( 'twitter' => $username, 'added_on' => date('Y-m-d H:i:s') ), array( '%s', '%s' ) );
                }
                
            }
        }
    }

    /**
     * Removes newly excluded authors from database upon filed update
     */
    public function removeExcludedAuthors()
    {
        //Get list of excludes
        $excludes = $this->coreManager->getSettingsManager()->getOption('excludes')->getValue();
        $excludedArray = explode(',', $excludes);
        $excludedArray = array_map('trim', $excludedArray);

        $query = "DELETE FROM ".$this->coreManager->getDbManager()->getTableNameFor('authors')." WHERE twitter IN ('".implode("','", $excludedArray)."')";

        $this->coreManager->getDbManager()->query($query);
    }

    /**
     * Registers cron job hooks to be used by the plugin
     */
    public static function addCronHooks()
    {
        //Call Scheduling
        if (!wp_next_scheduled('tweester_searcher')) {
            wp_schedule_event( time(),  'hourly', 'tweester_searcher' );
        }
    }
    
}

?>