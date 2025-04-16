<?php

namespace Modules\SqlGenerator\App\Http\Controllers; // Updated namespace

use App\Http\Controllers\Controller; // Keep using the base Controller
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Modules\SqlGenerator\App\Models\SqlGeneratorSetting; // Import the model

class SqlGeneratorController extends Controller
{

    public function __construct()
    {
        // Assuming the permission name remains the same for now
        $this->middleware('permission:view sqlgenerator', ['only' => ['index', 'generate']]); // Added generate to middleware
    }

    // Removed getApiUrl() and getApiKey() methods

    public function index()
    {
        $tableStructure = $this->getTableStructure();
        $settings = SqlGeneratorSetting::first(); // Get settings from DB
        // Use module view: sqlgenerator::index
        return view('sqlgenerator::index', compact('tableStructure', 'settings')); // Pass settings to view
    }

    public function generate(Request $request)
    {
        $naturalLanguage = $request->input('naturalLanguage');
        $apiUrl = $request->input('sql_generator_api_url'); // Get URL from request
        $apiKey = $request->input('sql_generator_api_key'); // Get Key from request
        $tableStructure = $this->getTableStructure();

        // Save/Update settings in the database
        $settings = SqlGeneratorSetting::updateOrCreate(
            ['id' => 1], // Assuming only one settings record
            ['api_url' => $apiUrl, 'api_key' => $apiKey]
        );

        // Use the saved/updated settings for the API call
        $response = $this->sendApiRequest($naturalLanguage, $tableStructure, $settings->api_url, $settings->api_key);

        // Check for errors returned directly from sendApiRequest (e.g., missing credentials)
        if (isset($response['error'])) {
             // Pass settings back to the view on error
             return view('sqlgenerator::index', compact('tableStructure', 'naturalLanguage', 'settings'))
                ->withError($response['error']);
        }

        if (isset($response['choices'][0]['message']['content'])) {
            $sqlQuery = trim($response['choices'][0]['message']['content']);

            $queryResult = null;
            $error = null;

            $sqlQuery = preg_replace('/^```sql\s*|```$/', '', $sqlQuery);
            $sqlQuery = trim($sqlQuery);


            if (preg_match('/^(SELECT|SHOW|DESCRIBE|EXPLAIN)\\s/i', $sqlQuery)) {
                try {
                    $queryResult = DB::select($sqlQuery);
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }
            } else {
                // Allow non-SELECT queries but show a message instead of executing
                 $error = 'Only SELECT, SHOW, DESCRIBE, or EXPLAIN queries can be executed directly. The generated query was: ' . $sqlQuery;
                 $sqlQuery = $sqlQuery; // Keep the generated query for display
                 $queryResult = []; // Set empty result
             }

             // Pass settings back to the view on success
             return view('sqlgenerator::index', compact('tableStructure', 'naturalLanguage', 'sqlQuery', 'queryResult', 'error', 'settings'));
         }

         // Pass settings back to the view on API processing error
         return view('sqlgenerator::index', compact('tableStructure', 'naturalLanguage', 'settings'))
             ->withError('Error processing API response');
     }

    private function getTableStructure()
    {
        $tables = [];
        // Get table names based on the current database connection
        $databaseName = DB::connection()->getDatabaseName();
        $tableNames = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = ?", [$databaseName]);

        foreach ($tableNames as $table) {
            $tableName = reset($table); // Revert to using reset() to get the table name
            // Use information_schema for broader compatibility
            $columns = DB::select("SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_KEY, COLUMN_DEFAULT, EXTRA FROM information_schema.columns WHERE table_schema = ? AND table_name = ?", [$databaseName, $tableName]);
            $tables[$tableName] = $columns;
        }

        return $tables;
    }

    // Accept API URL and Key as parameters
    private function sendApiRequest($naturalLanguage, $tableStructure, $apiUrl, $apiKey)
    {
        // Use the provided URL and Key directly
        if (empty($apiUrl) || empty($apiKey)) {
             // Return an error structure compatible with the view
            return ['error' => 'API URL or API Key must be provided.'];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post($apiUrl, [
            "model" => "gpt-3.5-turbo", // Consider making this configurable
            "messages" => [
                [
                    "role" => "system",
                    "content" => "You are an assistant that generates valid SQL queries based on the following database structure: " . json_encode($tableStructure) . ". Return only the SQL query, without explanations or additional text."
                ],
                [
                    "role" => "user",
                    "content" => $naturalLanguage
                ]
            ],
            "temperature" => 0.7 // Consider making this configurable
        ]);

        // Handle potential HTTP errors
        if ($response->failed()) {
            return ['error' => 'API request failed with status: ' . $response->status() . ' - ' . $response->body()];
        }

        $responseData = $response->json();

        // Handle cases where the expected structure isn't returned
        if (!isset($responseData['choices'][0]['message']['content'])) {
             return ['error' => 'Unexpected API response format: ' . json_encode($responseData)];
        }


        return $responseData;
    }
}
