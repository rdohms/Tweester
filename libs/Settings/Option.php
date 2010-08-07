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
     * @var Tweester
     */
    protected $coreManager;

    /**
     * Registers and builds the option and in which section it should be
     * @param string $section
     */
    public function __construct($section, $coreManager)
    {

        //Get Core Manager
        $this->coreManager = $coreManager;

        //Register field Callback and Label
        add_settings_field($this->fieldName, $this->label, array($this, 'render'), TWEESTER_MAINFILE, $section);
        
        //Register field for POST processing
        register_setting(Tweester_Settings::SETTINGS_GROUP, $this->fieldName);

        //Register on update Hook
        add_action('update_option_'.$this->fieldName, array($this, 'onUpdate'));
    }

    /**
     * Get valu from DB
     * @return mixed
     */
    public function getValue()
    {
        return get_option($this->fieldName, null);
    }

    /**
     * Save value into DB
     * @param mixed $value
     */
    public function setValue($value)
    {
        update_option($this->fieldName, $value);
    }

    /**
     * Renders form input
     *
     * @abstract
     */
    abstract function render();

    /**
     * Executes needed actions when option is updated
     */
    abstract function onUpdate();
}


?>