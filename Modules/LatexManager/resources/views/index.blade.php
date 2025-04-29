@extends('adminlte::page')

@section('title', 'Latex Items')

@section('plugins.Datatables', true)

@section('content_header')
    <h1>Latex Items</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List of Latex Items</h3>
            <div class="card-tools">
                <a href="{{ route('admin.latex-manager.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Create New Item
                </a>
            </div>
        </div>
        <div class="card-body"> {{-- Removed p-0 for better DataTable spacing --}}
            <table id="latexItemsTable" class="table table-bordered table-striped"> {{-- Added ID and bordered class --}}
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Title</th>
                        <th style="width: 150px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latexItems as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->title }}</td>
                            <td>
                                <a href="{{ route('admin.latex-manager.show', $item->id) }}" class="btn btn-info btn-xs">View</a>
                                <a href="{{ route('admin.latex-manager.edit', $item->id) }}" class="btn btn-warning btn-xs">Edit</a>
                                <form action="{{ route('admin.latex-manager.destroy', $item->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No Latex items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    {{-- Add custom CSS here --}}
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#latexItemsTable').DataTable({
                "responsive": true, // Optional: Add responsiveness
                "autoWidth": false, // Optional: Disable auto width calculation
            });
        });
    </script>
@stop
