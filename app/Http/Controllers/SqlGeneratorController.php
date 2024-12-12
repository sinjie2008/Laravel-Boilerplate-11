<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class SqlGeneratorController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view sqlgenerator', ['only' => ['index']]);
    }

    private function getApiUrl()
    {
        return config('sql-generator.api_url');
    }

    private function getApiKey()
    {
        return config('sql-generator.api_key');
    }

    public function index()
    {
        $tableStructure = $this->getTableStructure();
        return view('sql-generator.index', compact('tableStructure'));
    }

    public function generate(Request $request)
    {
        $naturalLanguage = $request->input('naturalLanguage');
        $tableStructure = $this->getTableStructure();

        $response = $this->sendApiRequest($naturalLanguage, $tableStructure);
        
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
                $error = 'Invalid SQL query returned.';
            }

            return view('sql-generator.index', compact('tableStructure', 'naturalLanguage', 'sqlQuery', 'queryResult', 'error'));
        }

        return view('sql-generator.index', compact('tableStructure', 'naturalLanguage'))
            ->withError('Error processing API response');
    }

    private function getTableStructure()
    {
        $tables = [];
        $tableNames = DB::select('SHOW TABLES');
        
        foreach ($tableNames as $table) {
            $tableName = reset($table);
            $columns = DB::select("DESCRIBE $tableName");
            $tables[$tableName] = $columns;
        }
        
        return $tables;
    }

    private function sendApiRequest($naturalLanguage, $tableStructure)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getApiKey(),
            'Content-Type' => 'application/json',
        ])->post($this->getApiUrl(), [
            "model" => "gpt-3.5-turbo",
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
            "temperature" => 0.7
        ]);

        return $response->json();
    }
} 