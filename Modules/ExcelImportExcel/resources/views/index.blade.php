@extends('adminlte::page') {{-- Assuming you use the jeroennoten/laravel-adminlte package --}}
{{-- Or use your module's master layout: @extends('excelimportexcel::layouts.master') --}}

@section('title', 'Excel Template Management')

@section('content_header')
    <h1>Excel Template Management</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Models & Available Templates</h3>
        </div>
        <div class="card-body">
            @if(empty($modelsWithTemplates))
                <p>No models found in the application or active modules.</p>
            @else
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Model Name</th>
                            <th>Source</th>
                            <th>Template Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modelsWithTemplates as $key => $modelData)
                            <tr>
                                <td>{{ $modelData['name'] }}</td>
                                <td>{{ $modelData['module'] }}</td>
                                <td>
                                    {{-- Indicate if a specific template exists, but always allow download --}}
                                    @if($modelData['has_template'])
                                        <span class="badge badge-success" title="Uses specific Export class">Custom Template</span>
                                    @else
                                        <span class="badge badge-info" title="Uses generic template based on columns/fillable">Auto-Generated</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Always provide download link, controller handles logic --}}
                                    <a href="{{ route('excelimportexcel.template.download', ['moduleName' => $modelData['module'], 'modelName' => $modelData['name']]) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-download"></i> Download Template
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <hr>
                <p class="text-muted small">
                    Templates are generated automatically based on model properties ($fillable or database columns).
                    To customize a template (e.g., specific columns, formatting), create a specific Export class named <code>[ModelName]Export.php</code>
                    inside the <code>Modules/ExcelImportExcel/App/Exports/</code> directory.
                </p>
            @endif
        </div>
    </div>
@stop

{{-- Optional: Add custom CSS or JS if needed --}}
{{-- @section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop --}}

{{-- @section('js')
    <script> console.log('Hi!'); </script>
@stop --}}
