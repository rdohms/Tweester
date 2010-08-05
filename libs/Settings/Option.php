<?php
/**
 * Option Class
 *
 * This is an abstract class for wrapping and representing an option stored in
 * the WP options table
 *
 * @abstract
 * @package Tweester
 * @subpackage Settings
 * @author Rafael Dohms
 */
abstract class Tweester_Settings_Option
{
    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @var string
     */
    protected $label;

    /**
     * Registers and builds the option and in which section it should be
     * @param string $section
     */
    public function __construct($section)
    {
        //Register field Callback and Label
        add_settings_field($this->fieldName, $this->label, array($this, 'render'), TWEESTER_MAINFILE, $section);
        
        //Register field for POST processing
        register_setting(TWEESTER_MAINFILE, $this->fieldName);
    }

    /**
     * Renders form input
     *
     * @abstract
     */
    abstract function render();
    
}


?>