@extends('adminlte::page')

@section('title', 'SQL Generator')

@section('content_header')
    <h1>SQL Generator</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form method="post" action="{{ route('admin.sql-generator.generate') }}"> {{-- Corrected route name --}}
            @csrf
            <div class="form-group">
                <label for="naturalLanguage">Enter your request:</label>
                <textarea id="naturalLanguage" name="naturalLanguage" class="form-control" rows="4" required>{{ $naturalLanguage ?? '' }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Generate SQL</button>
        </form>

        @if(isset($sqlQuery))
            <div class="mt-4">
                <h5>Generated SQL Query:</h5>
                <pre class="bg-light p-3 rounded">{{ $sqlQuery }}</pre>
            </div>
        @endif

        {{-- Display API or Query Execution Errors --}}
        @if(isset($error))
            <div class="alert alert-danger mt-4">
                Error: {{ $error }}
            </div>
        @endif

        @if(isset($queryResult))
            <div class="mt-4">
                <h5>Query Results:</h5>
                @if(is_array($queryResult) && count($queryResult) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    @foreach((array)$queryResult[0] as $column => $value)
                                        <th>{{ $column }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($queryResult as $row)
                                    <tr>
                                        @foreach((array)$row as $value)
                                            <td>{{ $value }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif(is_array($queryResult)) {{-- Check if it's an array but empty --}}
                    <p>Query executed successfully, but returned no results.</p>
                @else {{-- Handle cases where $queryResult might not be an array (e.g., error occurred before query) --}}
                    <p>No results to display.</p>
                @endif
            </div>
        @endif

         {{-- Display Table Structure --}}
        @if(isset($tableStructure) && is_array($tableStructure) && count($tableStructure) > 0)
            <div class="mt-5">
                <h4>Database Structure Used for Generation:</h4>
                <div style="max-height: 400px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; background-color: #f8f9fa;">
                    @foreach($tableStructure as $tableName => $columns)
                        <h5>Table: <code>{{ $tableName }}</code></h5>
                        <ul>
                            @foreach($columns as $column)
                                <li>
                                    <code>{{ $column->COLUMN_NAME }}</code>
                                    (<small>{{ $column->DATA_TYPE }}{{ $column->IS_NULLABLE === 'YES' ? ', Nullable' : '' }}{{ !empty($column->COLUMN_KEY) ? ', Key: ' . $column->COLUMN_KEY : '' }}</small>)
                                </li>
                            @endforeach
                        </ul>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@stop
