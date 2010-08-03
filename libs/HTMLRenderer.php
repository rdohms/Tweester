<?php

class Tweester_HTMLRenderer
{
    
    public static function renderAuthorList($list)
    {
        
        if (count($list) > 0) {
            $html = "";
            foreach($list as $user){

                $html .= "<div class='tweester_div'>";
                $html .= "<img src='".$user->profile_image_url."' class='tweester_img'>";
                $html .= "<p class='tweester_name'>".$user->name."</p>";
                $html .= "<p class='tweester_bio'>".$user->description."</p>";
                $html .= "<p class='tweester_url'><a href='".$user->url."'>Site</a> | <a href='http://twitter.com/".$user->screen_name."'>Twitter: @".$user->screen_name."</a></p>";
                $html .= "</div>";
            
            }
        } else {
            $html = '<div class="tweester_none">No supporters yet!</div>';
        }
        
        return $html;
    }
    
    public static function renderCSSTag()
    {
        echo '<link rel="stylesheet" href="'.get_option('siteurl')."/wp-content/plugins/".dirname(plugin_basename(TWEESTER_MAINFILE)).'/tweester.css'.'" type="text/css" media="screen" />';
    }
}

?>