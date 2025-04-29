@extends('adminlte::page')

@section('title', 'Upload Excel Data')

@section('content_header')
    <h1 class="excel-manager-header">
        <i class="fas fa-file-upload text-success excel-icon"></i> 
        Upload Excel Data - {{ $modelName }}
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card excel-card">
        <div class="card-header bg-primary">
            <h3 class="card-title"><i class="fas fa-file-excel mr-1"></i> Excel Data Import</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <!-- Display validation errors if any -->
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <h5><i class="icon fas fa-ban"></i> Validation Error</h5>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Display success message if any -->
                    @if(session('success'))
                        <div class="alert alert-success">
                            <h5><i class="icon fas fa-check"></i> Success</h5>
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Upload Form -->
                    <form action="{{ route('excelmanager.upload.handle', ['moduleName' => $moduleName, 'modelName' => $modelName]) }}" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          class="mb-3">
                        @csrf
                        <div class="form-group">
                            <label for="excel_file">Excel File (XLSX, XLS, CSV)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="excel_file" id="excel_file" class="custom-file-input" required>
                                    <label class="custom-file-label" for="excel_file">Choose file</label>
                                </div>
                            </div>
                            <div class="form-text text-muted mt-2">
                                <small>
                                    Only Excel files with proper headers will be imported. The first row must contain column names that match the model's fields.
                                </small>
                            </div>
                        </div>

                        <div class="mt-3 excel-actions">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-upload mr-1"></i> Upload & Import
                            </button>
                            
                            <!-- Template download link -->
                            <a href="{{ route('excelmanager.template.download', ['moduleName' => $moduleName, 'modelName' => $modelName]) }}" 
                               class="btn btn-info">
                                <i class="fas fa-file-download mr-1"></i> Download Template
                            </a>
                        </div>
                    </form>

                    <!-- Return button -->
                    <div class="mt-4">
                        <a href="{{ route('excelmanager.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Excel Manager
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('modules/excelmanager/css/excel-manager.css') }}">
@stop

@section('js')
<script>
$(document).ready(function () {
    // Try initializing bs-custom-file-input if available
    if (typeof bsCustomFileInput !== 'undefined') {
        bsCustomFileInput.init();
    }
    
    // Add a direct event handler for the file input change
    $('#excel_file').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });
});
</script>
@stop 