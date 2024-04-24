<?php

namespace Vediansoft\LaravelDynamicForms;

/**
 * Dynamic form builder trait.
 * ---
 * This trait is meant for decomposing models and 
 * generate a corresponding editable form out of it
 * 
 * @author Tom van Tilburg
 */
trait DynamicFormBuilder
{

    /**
     * Fetching current model columns
     * ---
     * Shorthand method to extract columns from the current connection
     * 
     * @return  array   columns
     */
    public function getColumns()
    {
        // Get current table columns
        return $this->extractColumns( $this->connection() );
    }

    /**
     * Extracting the current model usable columns
     * ---
     * This method will extract the columns from the model
     * of the current connection. It will try to automatically
     * exclude columns from the model which we don't want to use
     * within the controller->view. When excluded types are found
     * the iteration will skip. 
     * 
     * If the iteration isn't skip, it will get the field types of 
     * column and name
     * 
     * @param   mixed     $columns 
     * @return  array 
     */
    private function extractColumns($columns) {
        // Loop through fields
        foreach ($columns as $name => $column) {
            // Check for fields we need to exclude
            if ($this->excludeFields($name)) {
                continue;
            }
            // Setup the fields array
            $fields[$name] = $this->getFieldTypes($column, $name);
        }
        return $fields;
    }

    /**
     * Get field types from model
     * ---
     * This method will generate the field attributes
     * according to what datatype is set within the database
     * structure. It will try to render the attributes all
     * automatically. 
     * 
     * In dire need of customisation, it's best to add the 
     * customDynamic() method to your model and apply 
     * the specific needs for that particular column within the model.
     * 
     * @param   object  $column     The fetched column from connection
     * @param   string  $name       The name of the actual field
     * @return  array               Returns set array of field attributes
     */
    public function getFieldTypes(object $column, string $name)
    {
        // Get the actual datatype of the column
        $datatype = $column->getType()->getName();
        
        // Set config to the corrosponding datatype found in column
        $config = "dynamicforms.generate.{$datatype}";

        // Check whether custom dynanmic is not false and if the 
        $type        = $this->setupCustomDynamic($name, 'type');
        $class       = $this->setupCustomDynamic($name, 'class');
        $disabled    = $this->setupCustomDynamic($name, 'disabled');

        // Return field types array
        return [
            'model'     => $this->getModelBase(),
            'id'        => $name,
            'name'      => $name,
            'type'      => !$type   ?  config("{$config}.type")   :  $type,
            'class'     => !$class  ?  config("{$config}.class")  :  $class,
            'disabled'  => $disabled,                   // will be ignored if false
            'length'    => $column->getLength(),        // Get maximum set length of column in schema
            'value'     => ''                           // Needs to be empty for initial instantiation
        ];
    }

    /**
     * Setup custom dynamic model configuration
     * ---
     * Checks if $key is set within the custom dynamic array
     * if not then just returns false
     * 
     * @param   string  $name   Actual name of the column
     * @param   string  $key    Key of field attribute within the configuration
     * @return mixed|false 
     */
    private function setupCustomDynamic(string $name, string $key) {
        // First check whether function is actually active
        return isset($this->checkForCustomDynamic($name)[$key]) ? $this->checkForCustomDynamic($name)[$key] : false;
    }

    /**
     * Get custom dynamic model configuration
     * ---
     * Sometimes it's necessary to overwrite the current 
     * configuration file within the program. You can achieve
     * this by either overwriting the configuration file or 
     * just appending the method "customDynamic()" to your model
     * 
     * This method needs to be configured as following: 
     * 
     * function customDynamic() {
     *     
     *      return [
     *          "columnName" => [
     *              "type"  => "text",
     *              "class" => "form-control custom"
     *          ]
     *      ];
     * }
     * 
     * @param string $name 
     * @return mixed|false 
     */
    private function checkForCustomDynamic(string $name) {
        // Check if method is available within the current model and if it is set
        if( method_exists($this, 'customDynamic') && isset($this->customDynamic[$name])) {
            // If so return the custom dynamic configuration
            return $this->customDynamic[$name];
        } 
        // Assume nothing happened return false
        return false;
    }

    /**
     * Excluding specific fields
     * ---
     * Some fields we don't want to include within our form generation
     * this could be due to security issues or if fields may not 
     * be updated within the schema. If they can't be updated
     * within the schema, they must be specified to be excluded.
     * In some cases, you'd already set the guarded method
     * for fields like username or password. 
     * Those (unless specified otherwise) will by default be excluded
     * from the generation of the fields.
     * 
     * @param   string  $field  Name of the current field within the model
     * @return  bool            Returns either true or false
     */
    private function excludeFields(string $field)
    {
        // In Model->$guarded
        if ($this->isGuarded($field) && !$this->getGuarded(['*'])) {
            return true;
        }
        // In Model->$hidden
        if (in_array($field, $this->getHidden())) {
            return true;
        }
        // Not in Model->$fillable
        if (!in_array($field, $this->getFillable())) {
            return true;
        }
        return false;
    }
}
