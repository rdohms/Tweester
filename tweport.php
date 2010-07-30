<?php
/*
Plugin Name: TwePort
Plugin URI: --
Description: This plugin allows you to show a page of people who supported your cause on twitter.
Version: 0.1
Author: Rafael Dohms
Author URI: http://www.rafaeldohms.com.br
License: CC
*/

register_activation_hook( __FILE__, array('TwePort', 'activateSelf') );
add_filter('init', array('TwePort','init'));

class TwePort
{
	private $db;
	
	private $suppTableName;
	private $dataCacheTableName;
	
	private $pluginPath;
	
	public function __construct()
	{
		global $wpdb;
		
		$this->db = $wpdb;
		
		$this->suppTableName = $this->db->prefix . "tweport_supporters";
		$this->dataCacheTableName = $this->db->prefix . "tweport_data_cache";
		
		$this->pluginPath = get_option('siteurl')."/wp-content/plugins/".dirname(plugin_basename(__FILE__));
	}
	
    public static function init()
    {
		$tw = new TwePort();

        add_shortcode('tweport_list', array($tw, 'shortCodeList'));
        add_action('admin_init', array($tw, 'initPluginSettings'));
		add_action('admin_menu', array($tw, 'initPluginSettingsMenu'));
		
		add_action("wp_head",array($tw,'renderStyleSheets'));
		add_action("tweport_searcher", array($tw, 'executeSearch'));
		
    }

    public static function activateSelf()
    {
		$tw = new TwePort();
		$tw->initTables();
		
		if (!wp_next_scheduled('tweport_searcher')) {
			wp_schedule_event( time(),  'hourly', 'tweport_searcher' );
		}

    }

    // [tweport_list foo="foo-value"]
    public function shortCodeList($atts) 
    {
        //extract(shortcode_atts(array('foo' => 'something','bar' => 'something else'), $atts));

		//Get Supporters
		$data = $this->db->get_results("SELECT * FROM $this->suppTableName");
		
		$list = array();
		foreach($data as $user){
			
			$list[] = $this->getUserData($user->twitter);
			
		}

		$html = $this->renderList($list);

        return $html;
    }

    function initPluginSettingsMenu() 
	{
		add_options_page('Tweport - Options', 'TwePort', 'administrator', __FILE__, array($this, 'renderSettingsPage'));
	}
	
    function initPluginSettings() 
	{
        add_settings_section('tweport_config', 'Search', array($this, 'settingsSectionInit'), __FILE__);
        add_settings_field('tweport_query', 'Query to find supporters', array($this, 'settingsQueryFieldCallback'), __FILE__, 'tweport_config');
        register_setting(__FILE__,'tweport_query');
    }



	function initTables()
	{
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		$supTableName = $this->suppTableName;
		$dataTableName = $this->dataCacheTableName;
		
		if($this->db->get_var("SHOW TABLES LIKE '$supTableName'") != $supTableName) {
			$sql = "CREATE TABLE  ".$supTableName." (
			  		  id INT NOT NULL AUTO_INCREMENT ,
			  		  twitter VARCHAR(70) NULL ,
			  		  added_on DATETIME NULL ,
			        PRIMARY KEY  (id) ,
			        UNIQUE KEY twitter_UNIQUE (twitter) )";
			
			dbDelta($sql);
		}
		
		if($this->db->get_var("SHOW TABLES LIKE '$dataTableName'") != $dataTableName) {
			$sql = "CREATE TABLE ".$dataTableName." (
					  twitter varchar(70) NOT NULL ,
					  data TEXT NULL ,
					  cached_on DATETIME NULL ,
				    PRIMARY KEY  (twitter) )";
				
			dbDelta($sql);
		}
	}

    function settingsSectionInit() 
    {
        echo '<p>Options for detecting supporters</p>';
    }

    function settingsQueryFieldCallback() 
    {
        echo "<input name='tweport_query' id='gv_thumbnails_insert_into_excerpt' type='text' value='".get_option('tweport_query')."' class='code' />";
     } 

	public function renderStyleSheets()
	{
		echo '<link rel="stylesheet" href="'.$this->pluginPath.'/tweport.css'.'" type="text/css" media="screen" />';
	}

	function renderSettingsPage()
	{
		
		echo '<div>';
		echo '<h2>TwePort</h2>';
		echo 'Options relating to the Custom Plugin.';
		echo '<form action="options.php" method="post">';
		
		settings_fields(__FILE__);
		do_settings_sections(__FILE__);
		
		echo '<p class="submit"><input type="submit" name="submit" class="button-primary" value="Save Changes" /></p>';
		echo '</form></div>';

	}
	
	public function renderList($list)
	{
		
		$html = "";
		foreach($list as $user){

			$html .= "<div class='tweport_div'>";
			$html .= "<img src='".$user->profile_image_url."' class='tweport_img'>";
			$html .= "<p class='tweport_name'>".$user->name."</p>";
			$html .= "<p class='tweport_bio'>".$user->description."</p>";
			$html .= "<p class='tweport_url'><a href='".$user->url."'>Site</a> | <a href='http://twitter.com/".$user->screen_name."'>Twitter: @".$user->screen_name."</a></p>";
			$html .= "</div>";
			
		}
		
		return $html;
	}
	
	public function executeSearch()
	{
		$url = "http://search.twitter.com/search.json?q=".urlencode(get_option('tweport_query'));
		
		$searchRes = wp_remote_get($url);

		$body = json_decode($searchRes['body']);
		if ($body != null) {
			//Process, store supporters
			foreach($body->results as $tweet){
				//Get Username
				$username = $tweet->from_user;
				
				//Check if already in base
				$data = $this->db->get_row("SELECT * FROM $this->suppTableName WHERE twitter = '".$username."'");

				//Add to base if not
				if ($data == null){
					$this->db->insert( $this->suppTableName, array( 'twitter' => $username, 'added_on' => date('Y-m-d H:i:s') ), array( '%s', '%s' ) );
				}
				
			}
		}
		
		file_put_contents('temp.txt', time());
	}
	
	public function getUserData($username)
	{
		//Check cache
		$data = $this->db->get_row("SELECT * FROM $this->dataCacheTableName WHERE twitter = '".$username."'");

		if ($data === null){
			//Get From Twitter
			$twData = $this->getUserDataFromTwitter($username);

			//Cache
			$this->db->insert( $this->dataCacheTableName, array( 'twitter' => $username, 'data' => json_encode($twData), 'cached_on' => date('Y-m-d H:i:s') ), array( '%s', '%s', '%s' ) );
			
			$userData = $twData;
		} else {
			$userData = json_decode($data->data);
		}
		
		return $userData;
		
	}
	
	public function getUserDataFromTwitter($username)
	{
		$url = "http://twitter.com/users/show.json?screen_name=".$username;
		
		$callRes = wp_remote_get($url);

		$data = json_decode($callRes['body']);

		return $data;
	}
}

?>