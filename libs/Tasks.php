<?php

class Tweester_Tasks
{
    /**
     * Central Core Manager
     * @var Tweester
     */
    private $coreManager;

    public function __construct($coreManager)
    {
        //Set CoreManager
        $this->coreManager = $coreManager;
        
        //Tie our functions to our cron tasks
        add_action("tweester_searcher", array($this, 'updateAuthors'));
        
    }
    
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

    public static function addCronHooks()
    {
        //Call Scheduling
        if (!wp_next_scheduled('tweester_searcher')) {
            wp_schedule_event( time(),  'hourly', 'tweester_searcher' );
        }
    }
    
}

?>