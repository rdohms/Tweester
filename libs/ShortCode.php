<?php
/**
 * ShortCode Management Class
 *
 * This class wraps and handles all the shortcode tags used by the plugin using
 * the shortcode API in wordpress (http://codex.wordpress.org/Shortcode_API)
 *
 * @package Tweester
 * @subpackage ShortCode
 * @link http://codex.wordpress.org/Shortcode_API
 * @author Rafael Dohms
 */
class Tweester_ShortCode
{

    /**
     * @var Tweester_DB
     */
    private $dbManager;
    
    /**
     * Central Core Manager
     * @var Tweester
     */
    private $coreManager;

    /**
     * Registers shorcode tags
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

    /**
     * Renders a list of authors.
     * Usage:
     *   add [tweester_list] to post or page
     *
     * @param array $atts
     * @return string
     */
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

    /**
     * Retrieves User Data.
     *
     * Thie method first checks the local cache database and if needed calls
     * twitter for more information
     *
     * @param string $username
     * @return stdClass
     */
    private function getUserData($username)
    {
        $reCacheCutoff = new DateTime();
        $reCacheCutoff->modify("-15 days");

        //Check cache
        $data = $this->dbManager->get_row("SELECT *, DATE_FORMAT(cached_on, '%Y%m%d%h%i%s') AS cacheint FROM ".$this->dbManager->getTableNameFor('user_data_cache')." WHERE twitter = '".$username."'");

        if ($data === null || $data->cacheint < $reCacheCutoff->format('YmdHis')){
            //Get From Twitter
            $twData = Tweester_Twitter::getUserData($username);

            //Clear previous cache
            $this->dbManager->query("DELETE FROM ".$this->coreManager->getDbManager()->getTableNameFor('user_data_cache'). " WHERE twitter = '".$username."'");
            
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