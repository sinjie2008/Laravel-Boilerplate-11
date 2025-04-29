<?php

namespace Modules\ExcelManager\App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Nwidart\Modules\Facades\Module;
use Illuminate\Support\Facades\Log; // Added for logging errors
use Illuminate\Database\Eloquent\Model; // Added for type hinting

class GenericModelImport implements ToModel, WithHeadingRow, WithValidation
{
    protected string $moduleName;
    protected string $modelName;
    protected string $modelNamespace;
    protected array $fillable = []; // Store fillable attributes for validation

    /**
     * @param string $moduleName
     * @param string $modelName
     */
    public function __construct(string $moduleName, string $modelName)
    {
        $this->moduleName = $moduleName;
        $this->modelName = $modelName;
        $this->resolveModelNamespace();
        $this->resolveFillableAttributes();
    }

    /**
     * Resolve the fully qualified namespace for the target model.
     */
    protected function resolveModelNamespace(): void
    {
        if ($this->moduleName === 'App') {
            $appNamespace = app()->getNamespace();
            $this->modelNamespace = $appNamespace . 'Models\\' . $this->modelName;
        } else {
            $module = Module::find($this->moduleName);
            if ($module && $module->isEnabled()) {
                $this->modelNamespace = "Modules\\{$module->getName()}\\App\\Models\\{$this->modelName}";
            } else {
                $this->modelNamespace = "";
            }
        }

        if (empty($this->modelNamespace) || !class_exists($this->modelNamespace)) {
            $message = "Model class '" . $this->modelNamespace . "' not found or invalid for " . $this->moduleName . "::" . $this->modelName . ".";
            Log::error($message);
            throw new \InvalidArgumentException($message);
        }
         // Ensure it's actually an Eloquent model
        if (!is_subclass_of($this->modelNamespace, Model::class)) {
             $message = "Class '{$this->modelNamespace}' is not an Eloquent Model.";
            Log::error($message);
            throw new \InvalidArgumentException($message);
        }
    }

     /**
     * Resolve and store the fillable attributes of the target model.
     */
    protected function resolveFillableAttributes(): void
    {
        try {
            $modelInstance = new $this->modelNamespace();
            $this->fillable = $modelInstance->getFillable();
            if (empty($this->fillable)) {
                 Log::warning("Model {$this->modelNamespace} has no \$fillable attributes defined. Import might fail or be incomplete.");
                // Consider falling back to DB columns if needed, but $fillable is safer for mass assignment.
            }
        } catch (\Throwable $e) {
            Log::error("Could not instantiate model {$this->modelNamespace} to get fillable attributes: " . $e->getMessage());
             // Decide if you want to throw an exception or allow proceeding without fillable info
             throw new \RuntimeException("Could not determine fillable attributes for model {$this->modelName}.", 0, $e);
        }
    }


    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row): ?Model
    {
        // Ensure model namespace was resolved
        if (empty($this->modelNamespace) || empty($this->fillable)) {
             Log::error("Skipping row due to unresolved model or missing fillable attributes for {$this->moduleName}::{$this->modelName}. Row Data: " . json_encode($row));
            return null; // Skip row if model or fillable couldn't be determined
        }

        // Filter row data to only include fillable attributes
        $fillableData = [];
        foreach ($this->fillable as $attribute) {
             // Use array_key_exists in case the value is null
            if (array_key_exists($attribute, $row)) {
                 // Basic sanitization/type casting could be added here if needed
                $fillableData[$attribute] = $row[$attribute];
            } else {
                // Log if an expected fillable column is missing in the row
                Log::warning("Fillable attribute '{$attribute}' missing in row for {$this->modelNamespace}. Row Data: " . json_encode($row));
                // Optionally set to null or skip the row depending on requirements
                 // $fillableData[$attribute] = null;
            }
        }

        // Check if there's any data to actually insert/update
        if (empty($fillableData)) {
            Log::info("Skipping row with no fillable data for {$this->modelNamespace}. Row Data: " . json_encode($row));
            return null;
        }


        try {
            // Use new static() if your model uses static methods for creation,
            // otherwise instantiate and save. Using create() handles mass assignment safely.
            return $this->modelNamespace::create($fillableData);
        } catch (\Illuminate\Database\QueryException $e) {
             // Log database errors (e.g., unique constraint violations)
            Log::error("Database error importing row for {$this->modelNamespace}: " . $e->getMessage() . ". Row Data: " . json_encode($fillableData));
            // Decide how to handle DB errors: skip row, stop import, etc.
            return null; // Skip row on DB error
        } catch (\Throwable $e) {
            // Log any other unexpected errors during model creation
            Log::error("Unexpected error importing row for {$this->modelNamespace}: " . $e->getMessage() . ". Row Data: " . json_encode($fillableData));
            return null; // Skip row on other errors
        }
    }

    /**
     * Define validation rules. Column names MUST match the Excel header row.
     *
     * @return array
     */
    public function rules(): array
    {
        // Dynamically create basic 'required' rules for fillable attributes.
        // You might need more sophisticated logic based on model properties or DB schema.
        $rules = [];
        foreach ($this->fillable as $attribute) {
            // Basic validation: assume all fillable fields from Excel are required.
            // Adjust this logic based on your actual needs (e.g., check model rules or DB schema nullability)
            $rules[$attribute] = 'required';
        }
        // Example: Add specific rule if needed
        // if ($this->modelName === 'User') {
        //     $rules['email'] = 'required|email|unique:users,email';
        // }
        return $rules;
    }

     /**
     * Custom validation messages.
     *
     * @return array
     */
    public function customValidationMessages(): array
    {
        // Provide generic messages or customize per attribute/rule
        $messages = [];
        foreach ($this->fillable as $attribute) {
            $messages["{$attribute}.required"] = "The '{$attribute}' column is required in the Excel file.";
        }
        // Example: Add specific message
        // if ($this->modelName === 'User') {
        //      $messages['email.unique'] = 'The email :input has already been taken.';
        // }
        return $messages;
    }

} 