<?php

namespace Vediansoft\LaravelDynamicForms;

use Illuminate\Support\Collection;
use Vediansoft\LaravelDynamicForms\DynamicFormBuilder;
use Illuminate\Support\Facades\DB;

trait DynamicForms
{
    use DynamicFormBuilder;

    /**
     * This method provides output based on rules
     * which are set wihtin the intializer of this package.
     * This method will try to render the variable set needed
     * in order to generate the form.
     * 
     * This method is currently under revision. 
     * In the future we will use pluck and specific relation
     * values to get columns via that route.
     * 
     * @return  collection  all schema columns collection 
     */
    public function form()
    {
        // Create the modifier macros
        $this->createMacro();
        // Collect the column data
        return collect($this->getColumns());
    }

    /**
     * Setup connection
     * ---
     * This gets the current schema columns from
     * the model we're currently residing
     * 
     * @return mixed 
     */
    protected function connection()
    {
        // Setup schema builder
        $conn = DB::connection();
        // Get model table name
        $table = $this->getTable();
        // Get schema manager
        $schema = $conn->getDoctrineSchemaManager();
        // Get column listing
        $table_details = $schema->listTableDetails($table);
        // return table columns
        return $table_details->getColumns();
    }

    /**
     * This will check whether set value of column
     * is allowed to be edited. 
     * 
     * Momentarily this is under review.
     * 
     * @param array $value
     * @param string $key 
     * @return mixed 
     */
    private function getEditable(array $value, string $key)
    {
        /// Return the remapped editable columns
        return $value['value'] = $this->model->$key;
    }

    /**
     * Gets the basename of the current model
     * ---
     * It's necessary to call from self::class. Otherwise
     * it is very difficult to get the related model class
     * while inhereting from within this trait
     * 
     * @return string 
     */
    private function getModelBase()
    {
        // Get app base namespace
        $base = explode('\\', self::class)[0];
        // Get classname from this model
        $model_name = class_basename($this);
        // Return Namespace\Model
        return "$base\\{$model_name}";
    }

    /**
     * Creates certain macros to be executed within the model chain
     * ---
     * This method is currently under review
     * 
     * @return void 
     */
    private function createMacro()
    {
        // Necessary for the editing field
        $model = $this;

        // Inject editable values within the form based on what stage of the controller we are at
        Collection::macro('edit', function ($foreign = false) use ($model) {

            // Map collection array as key -> value use model and foreign
            return $this->map(function ($value, $key) use ($model, $foreign) {
                // Check whether current value is a foreign relation
                if(!$foreign) {
                    // If not just get the current model property
                    $value['value'] = $model->$key;
                } else {
                    // Otherwise find the related model based on this current model
                    $value['value'] = $value['model']::find($model->$foreign)->$key;                    
                }
                return $value;
            });

        });

        /**
         * Create a relation macro for current model this is helpful for 
         * generation of dropdown/select boxes that are linked within 
         * a set of chained relations
         */
        Collection::macro('relation', function ($relations, $pluck) {

            // Get the full modelset and convert to array
            $models = $this->toArray();
            // Loop through given relations as columnName->Entity
            foreach ($relations as $column_name => $entity) {
                // Get the name of the current entity
                $model_name = $entity->getModelBase();
                // Fetch everything within the current model
                $current_model = $model_name::all();
                // Pluck whatever column we wish to pluck                
                $pluck = $pluck[$column_name];
                // Set selected value to current entity plucked value
                $models[$column_name]['selected']   = $entity->{$pluck[0]};
                // Generate the <options></options> field attributes
                $models[$column_name]['options']    = $current_model->pluck($pluck[1], $pluck[0]);
                // Loop through all current entity columns as index -> column
                foreach ($entity->getColumns() as $index => $column) {
                    // Set current models to column index them by their intial index
                    $models[$index] = $column;
                }
            }
            // Finally collect the relatable models and return
            return collect($models);

        });

        // Remove everything we want to hide from the collection
        Collection::macro('hide', function ($hide) {
            // Make sure $hide is an array
            $hide = is_array($hide) ? $hide : [$hide];
            // Get current model values
            $values = $this;
            // Loop through hidden fields
            foreach($hide as $h) {
                // For each field that is hidden, unset it from the array
                unset($values[$h]);

            }
            // Finally collect and return visible values
            return collect($values);

        });

        // Sometimes we want to just disable the fields instead of hiding
        Collection::macro('disable', function ($disable) {
            // Make sure $disable is an array
            $disable = is_array($disable) ? $disable : [$disable];
            // Convert current schema values to array
            $values = $this->toArray();
            // Iterate through all fields that need the disabled attribute
            foreach($disable as $h) {
                // Set the disabled attribute for corresponding fields
                $values[$h]['disabled'] = 'disabled';
            }
            // Finally collect and return schema values
            return collect($values);
        });
    }
}
