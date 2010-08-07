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


    public function getMaxSearchResults($query, $maxPages = 15){

        $p = 1;
        $results = array();

        while ($p < $maxPages) {
            $pResults = Tweester_Twitter::getSearchResults($query, $p);
            $results = array_merge($results, $pResults);
            $p++;
        }

        return $results;

    }

    /**
     * Executes a search agains the Twitter API
     *
     * @param string $query
     * @return stdClass|false
     */
	public static function getSearchResults($query, $page = 1)
	{
		$url = "http://search.twitter.com/search.json?";
        
        $urlData = array(
            'q' => urlencode($query),
            'rpp' => '100',
            'page' => $page
        );

		$searchRes = wp_remote_get($url . http_build_query($urlData));

		$body = json_decode($searchRes['body']);
        
		if ($body != null) {
			return $body->results;
		} else {
			return false;
		}
				
	}
}

?>