@extends('adminlte::page')

@section('title', __('Media Manager'))

{{-- Include DataTables CSS --}}
@section('plugins.Datatables', true)

@section('content_header')
    <h1 class="m-0 text-dark">{{ __('Media Manager') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('mediamanager.create') }}" class="btn btn-primary btn-sm float-right">
                        <i class="fas fa-plus mr-1"></i>
                        {{ __('Upload New Media') }}
                    </a>
                </div>
                <div class="card-body">
                    {{-- Display Session Messages --}}
                    {{-- @include('partials.alert') --}} {{-- Removed this line as the partial doesn't exist --}}
                    {{-- Or manually display them: --}}
                     @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if (session('error'))
                         <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif 

                    <table id="mediaTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Thumbnail') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Size') }}</th>
                                <th>{{ __('Date Added') }}</th>
                                <th>{{ __('Attached To') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mediaItems as $item)
                                <tr>
                                    <td>
                                        @if (Str::startsWith($item->mime_type, 'image/'))
                                            <a href="{{ $item->getFullUrl() }}" target="_blank" rel="noopener noreferrer">
                                                <img src="{{ $item->getFullUrl('thumb') }}" alt="{{ $item->name }}" style="max-height: 50px; max-width: 70px; object-fit: cover;">
                                            </a>
                                        @else
                                            <span class="text-muted"><i class="fas fa-file fa-2x"></i></span>
                                        @endif
                                    </td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->mime_type }}</td>
                                    <td>{{ $item->human_readable_size }}</td>
                                    <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if($item->model)
                                            {{ class_basename($item->model_type) }} #{{ $item->model_id }}
                                        @else
                                            <span class="text-muted">{{ __('N/A') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Edit Button (links to edit route) --}}
                                        <a href="{{ route('mediamanager.edit', $item) }}" class="btn btn-xs btn-info mr-1" title="{{ __('Edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Download Button (links to show route) --}}
                                        <a href="{{ route('mediamanager.show', $item) }}" class="btn btn-xs btn-success mr-1" title="{{ __('Download') }}">
                                            <i class="fas fa-download"></i>
                                        </a>

                                        {{-- Delete Button (uses a form) --}}
                                        <form action="{{ route('mediamanager.destroy', $item) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger" title="{{ __('Delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">{{ __('No media items found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
<script>
    $(document).ready(function() {
        // Initialize DataTables
        $('#mediaTable').DataTable({
            // Add any specific DataTables options here
            // Example: disable sorting on actions column
            "columnDefs": [
                { "orderable": false, "targets": [0, 6] }, // Disable sorting for Thumbnail and Actions
                { "searchable": false, "targets": [0, 6] } // Disable searching for Thumbnail and Actions
            ],
            "language": { // Optional: Add localization
                 "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/English.json" // Change to your language
            }
        });

        // Delete Confirmation
        $('.delete-form').on('submit', function(e) {
            if (!confirm('{{ __("Are you sure you want to delete this media item?") }}')) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush

{{-- Reminder about Thumbnails and Conversions --}}
{{-- Ensure you have run `php artisan media-library:regenerate` if you added the 'thumb' conversion later. --}}
{{-- Ensure the 'thumb' conversion is defined in config/media-library.php or the relevant model. --}}
