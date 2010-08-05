<?php
/**
 * Excludes Field Class
 *
 * Manages the Excludes field in configuration
 *
 * @package Tweester
 * @subpackage Settings
 * @author Rafael Dohms
 */
class Tweester_Settings_Option_Excludes extends Tweester_Settings_Option
{

    /**
     * @var string
     */
    protected $fieldName = 'tweester_excludes';

    /**
     * @var string
     */
    protected $label = 'Users to be ignored';

    /**
     * Renders form input and description
     */
    public function render() 
    {
        echo "<textarea name='".$this->fieldName."'  id='".$this->fieldName."' class='large-text code'>".get_option($this->fieldName)."</textarea>";
        echo '<span class="description">These users will not show up in the list, use a comma separated list, for example: johndoe, jane, tarzan</span>';
    }

    /**
     * Executes needed actions when option is updated
     */
    public function onUpdate()
    {
        $this->coreManager->getTaskManager()->removeExcludedAuthors();
    }
}


?>