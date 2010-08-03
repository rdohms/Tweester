<?php

class Tweester_Settings_Option_Query extends Tweester_Settings_Option
{
    
    protected $fieldName = 'tweester_query';
    protected $label = 'Query to find supporters';
    
    public function render() 
    {
        echo "<input name='".$this->fieldName."' id='".$this->fieldName."' type='text' value='".get_option($this->fieldName)."' class='regular-text' />";
        echo '<span class="description">Use Twitter search params like: #php (<a href="http://search.twitter.com/operators">or these operators</a>)</span>';
    }
    
}


?>