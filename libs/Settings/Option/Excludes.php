<?php

class Tweester_Settings_Option_Excludes extends Tweester_Settings_Option
{
    
    protected $fieldName = 'tweester_excludes';
    protected $label = 'Users to be ignored';
    
    public function render() 
    {
        echo "<textarea name='".$this->fieldName."'  id='".$this->fieldName."' class='large-text code'>".get_option($this->fieldName)."</textarea>";
        echo '<span class="description">These users will not show up in the list, use a comma separated list, for example: johndoe, jane, tarzan</span>';
    }
    
}


?>