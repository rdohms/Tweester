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
        $results = Tweester_Twitter::getSearchResults(get_option('tweester_query'));
        
        if ($results != false) {
            //Process, store supporters
            foreach($results as $tweet){
                //Get Username
                $username = $tweet->from_user;
                
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