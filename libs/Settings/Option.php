<?php

abstract class Tweester_Settings_Option
{
    
    protected $fieldName;
    protected $label;
    
    public function __construct($section)
    {
        //Register field Callback and Label
        add_settings_field($this->fieldName, $this->label, array($this, 'render'), TWEESTER_MAINFILE, $section);
        
        //Register field for POST processing
        register_setting(TWEESTER_MAINFILE, $this->fieldName);
    }
    
    abstract function render();
    
}


?>