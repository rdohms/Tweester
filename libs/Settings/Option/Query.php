<?php
/**
 * Query Field Class
 *
 * Manages the query field in configuration
 *
 * @package Tweester
 * @subpackage Settings
 * @author Rafael Dohms
 */
class Tweester_Settings_Option_Query extends Tweester_Settings_Option
{
    /**
     * @var string
     */
    protected $fieldName = 'tweester_query';

    /**
     * @var string
     */
    protected $label = 'Query to find supporters';

    /**
     * Renders form input and description
     */
    public function render() 
    {
        echo "<input name='".$this->fieldName."' id='".$this->fieldName."' type='text' value='".get_option($this->fieldName)."' class='regular-text' />";
        echo '<span class="description">Use Twitter search params like: #php (<a href="http://search.twitter.com/operators">or these operators</a>)</span>';
    }

    /**
     * Executes needed actions when option is updated
     */
    public function onUpdate()
    {
        
    }
}


?>