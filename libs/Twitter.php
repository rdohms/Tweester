<?php

class Tweester_Twitter
{
	
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