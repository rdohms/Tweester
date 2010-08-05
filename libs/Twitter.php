<?php
/**
 * Twitter Class
 *
 * This class wraps all of the communication with twitter effectively executing
 * all the API calls
 *
 * @package Tweester
 * @subpackage Twitter
 * @author Rafael Dohms
 */
class Tweester_Twitter
{
	/**
         * Retrieves data about selected user
         *
         * @param string $username
         * @return stdClass
         */
	public static function getUserData($username)
	{
		$url = "http://twitter.com/users/show.json?screen_name=".$username;
		
		$callRes = wp_remote_get($url);

		if (array_key_exists('body', $callRes)) {
			
			$data = json_decode($callRes['body']);
			
			if ($data !== null){
				return $data;
			}
			
		}
		
		return false;
	}

        /**
         * Executes a search agains the Twitter API
         *
         * @param string $query
         * @return stdClass|false
         */
	public static function getSearchResults($query)
	{
		$url = "http://search.twitter.com/search.json?q=".urlencode($query);
		
		$searchRes = wp_remote_get($url);

		$body = json_decode($searchRes['body']);
		if ($body != null) {
			return $body->results;
		} else {
			return false;
		}
				
	}
}

?>