@extends('adminlte::page')

@section('title', 'SQL Generator')

@section('content_header')
    <h1>SQL Generator</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form method="post" action="{{ route('sql.generate') }}">
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

        @if(isset($error))
            <div class="alert alert-danger mt-4">
                Error: {{ $error }}
            </div>
        @endif

        @if(isset($queryResult))
            <div class="mt-4">
                <h5>Query Results:</h5>
                @if(count($queryResult) > 0)
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
                @else
                    <p>No results found.</p>
                @endif
            </div>
        @endif
    </div>
</div>
@stop 