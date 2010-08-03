<?php

class Tweester_ShortCode
{

    private $dbManager;
    
    /**
     * Central Core Manager
     * @var Tweester
     */
    private $coreManager;

    /**
     *
     * @param Tweester $coreManager
     */
    public function __construct($coreManager)
    {
        //Set CoreManager
        $this->coreManager = $coreManager;

        $this->dbManager = $this->coreManager->getDbManager();
        
        //Register our shortcode tags
        add_shortcode('tweester_list', array($this, 'authorList'));
        
    }
    // [tweester_list foo="foo-value"]
    public function authorList($atts)
    {
        //extract(shortcode_atts(array('foo' => 'something','bar' => 'something else'), $atts));

        //Get Supporters
        $data = $this->dbManager->get_results("SELECT * FROM ".$this->dbManager->getTableNameFor('authors'));
        $data = ($data === null)? array() : $data ;
        
        $list = array();
        foreach($data as $user){
        
            $list[] = $this->getUserData($user->twitter);
        
        }

        $html = Tweester_HTMLRenderer::renderAuthorList($list);

        return $html;

    }
    
    private function getUserData($username)
    {
        //Check cache
        $data = $this->dbManager->get_row("SELECT * FROM ".$this->dbManager->getTableNameFor('user_data_cache')." WHERE twitter = '".$username."'");

        if ($data === null){
            //Get From Twitter
            $twData = Tweester_Twitter::getUserData($username);

            //Cache
            $this->dbManager->insert( $this->dbManager->getTableNameFor('user_data_cache'), array( 'twitter' => $username, 'data' => json_encode($twData), 'cached_on' => date('Y-m-d H:i:s') ), array( '%s', '%s', '%s' ) );
            
            $userData = $twData;
        } else {
            $userData = json_decode($data->data);
        }
        
        return $userData;
        
    }
    
}

?>