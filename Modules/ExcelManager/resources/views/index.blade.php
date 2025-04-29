@extends('adminlte::page')

@section('title', 'Excel Template Management')

@section('content_header')
    <h1 class="excel-manager-header"><i class="fas fa-file-excel text-success excel-icon"></i> Excel Template Management</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card excel-card">
            <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fas fa-table mr-1"></i> Excel Manager</h3>
            </div>
            <div class="card-body">
                @if(count($modelsWithTemplates) > 0)
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info-circle"></i> About Excel Manager</h5>
                                <p class="mb-0">This tool allows you to manage Excel exports and imports for your models:</p>
                                <ul class="mb-0 mt-1">
                                    <li><strong>Download Template</strong> - Get an empty Excel file with the model's field names as headers</li>
                                    <li><strong>Download Data</strong> - Export all records from the database</li>
                                    <li><strong>Upload Excel</strong> - Import data from an Excel file to the database</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="excel-manager-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Module</th>
                                    <th>Model</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($modelsWithTemplates as $modelKey => $modelData)
                                    <tr>
                                        <td>{{ $modelData['module'] }}</td>
                                        <td>{{ $modelData['name'] }}</td>
                                        <td class="text-center excel-actions">
                                            <!-- Download Template button -->
                                            <a href="{{ route('excelmanager.template.download', ['moduleName' => $modelData['module'], 'modelName' => $modelData['name']]) }}" 
                                               class="btn btn-sm btn-primary" 
                                               title="Download Template">
                                                <i class="fas fa-file-download"></i> Template
                                            </a>
                                            
                                            <!-- Download Data button -->
                                            <a href="{{ route('excelmanager.data.download', ['moduleName' => $modelData['module'], 'modelName' => $modelData['name']]) }}" 
                                               class="btn btn-sm btn-danger" 
                                               title="Download Data">
                                                <i class="fas fa-database"></i> Data
                                            </a>
                                            
                                            <!-- Upload Excel button -->
                                            <a href="{{ route('excelmanager.upload.form', ['moduleName' => $modelData['module'], 'modelName' => $modelData['name']]) }}" 
                                               class="btn btn-sm btn-success" 
                                               title="Upload Excel">
                                                <i class="fas fa-file-upload"></i> Upload
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> No Models Available</h5>
                        <p>No models were found for Excel export/import. To add Excel support to a model:</p>
                        <ol>
                            <li>Ensure your model has a <code>$fillable</code> array defined with the fields you want to export/import.</li>
                            <li>For custom export formats, create a model-specific export class (e.g. <code>UserExport</code>)
                            inside the <code>Modules/ExcelManager/App/Exports/</code> directory.</li>
                        </ol>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('modules/excelmanager/css/excel-manager.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}">
@stop

@section('js')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#excel-manager-table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true
            });
        });
    </script>
@stop
