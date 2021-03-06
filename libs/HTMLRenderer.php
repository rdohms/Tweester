<?php
/**
 * HTML Renderer Class
 *
 * This class provides method to output most of the plugins HTML outputs
 *
 * @package Tweester
 * @subpackage HTML
 * @author Rafael Dohms
 */
class Tweester_HTMLRenderer
{
    /**
     * Render a list of users into HTML
     *
     * @param array $list
     * @return string
     */
    public static function renderAuthorList($list)
    {
        $html = '<div id="tweester_list">';

        if (count($list) > 0) {
            
            foreach($list as $user){

                $html .= "<div class='user'>";
                $html .= "<img src='".$user->profile_image_url."' class='avatar' width=48 height=48>";
                $html .= "<p class='name'>".$user->name."</p>";
                $html .= "<p class='bio'>".$user->description."</p>";
                $html .= "<p class='url'><a href='".$user->url."'>Site</a> | <a href='http://twitter.com/".$user->screen_name."'>Twitter: @".$user->screen_name."</a></p>";
                $html .= "</div>";
            
            }
        } else {
            $html .= '<div class="none">No supporters yet!</div>';
        }
        
        $html .= "</div>";
        return $html;
    }

    /**
     * Outputs CSS Tag to template
     */
    public static function renderCSSTag()
    {
        echo '<link rel="stylesheet" href="'.get_option('siteurl')."/wp-content/plugins/".dirname(plugin_basename(TWEESTER_MAINFILE)).'/tweester.css'.'" type="text/css" media="screen" />';
    }

    /**
     * Outputs the HTML for the Settings Page
     *
     * @param string $alert
     */
    public static function renderSettingsPage($alert = null)
    {

        if ($alert != null){
            echo '<div class="updated"><p><strong>'.$alert.'</strong></p></div>';
        }

        //Render HTML
        echo '<div>';
        echo '<h2>Tweester</h2>';
        echo 'Configuration for the Tweester plugin';
        echo '<form action="options.php" method="post">';

        settings_fields(Tweester_Settings::SETTINGS_GROUP);
        do_settings_sections(TWEESTER_MAINFILE);

        echo '<p class="submit"><input type="submit" name="submit" class="button-primary" value="Save Changes" /></p>';
        echo '</form></div>';

    }
}


?>