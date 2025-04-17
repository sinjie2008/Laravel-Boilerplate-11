@extends('adminlte::page') {{-- Assuming you are using adminlte layout --}}

@section('title', 'Sidebar Items')

@section('content_header')
    <h1>Sidebar Items</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manage Sidebar Menu Items</h3>
            <div class="card-tools">
                <a href="{{ route('admin.sidebar.items.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Item
                </a>
            </div>
        </div>
        <div class="card-body">
            {{-- Display Success/Error Messages --}}
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

            <p>Drag and drop items to reorder them.</p>

            <div id="sidebar-item-list-container">
                 @include('sidebarmanager::items._item_list', ['items' => $items])
            </div>

            <div class="mt-3">
                <button id="save-order-btn" class="btn btn-success" style="display: none;">Save New Order</button>
            </div>

        </div>
    </div>
@stop

@section('css')
    {{-- Add any custom CSS here --}}
    <style>
        .sortable-ghost {
            background-color: #f0f0f0;
            opacity: 0.7;
        }
        .item-details {
            border: 1px solid #eee;
            padding: 10px;
            margin-bottom: 0; /* Removed margin as list-group-item has its own */
            background-color: #f9f9f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .item-actions a, .item-actions button {
            margin-left: 5px;
        }
        .handle {
            cursor: move;
            margin-right: 10px;
            color: #aaa;
        }
        .list-group-item {
            border-radius: 0; /* Optional: Adjust styling */
            margin-bottom: 5px;
        }
        .nested-sortable {
             margin-top: 5px; /* Space between parent and nested list */
        }
    </style>
@stop

@section('js')
    {{-- Include SortableJS library --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const listContainer = document.getElementById('sidebar-item-list-container');
            const saveButton = document.getElementById('save-order-btn');
            let sortableInstances = [];

            function initializeSortable(element) {
                let groupElement = element.querySelector('.sortable-group');
                if (groupElement) {
                    let sortable = new Sortable(groupElement, {
                        group: 'nested',
                        animation: 150,
                        fallbackOnBody: true,
                        swapThreshold: 0.65,
                        handle: '.handle',
                        ghostClass: 'sortable-ghost',
                        onEnd: function(evt) {
                            saveButton.style.display = 'inline-block'; // Show save button on change
                        }
                    });
                    sortableInstances.push(sortable);

                    // Recursively initialize for nested groups
                    let nestedContainers = groupElement.querySelectorAll('.nested-sortable');
                    nestedContainers.forEach(container => {
                        initializeSortable(container);
                    });
                }
            }

            // Initial setup
            initializeSortable(listContainer);

            // Serialize nested structure
            function serialize(sortableElement) {
                let serialized = [];
                let children = sortableElement.children;
                for (let i = 0; i < children.length; i++) {
                    let item = children[i];
                    let nested = item.querySelector('.nested-sortable .sortable-group');
                    let data = {
                        id: item.dataset.id
                    };
                    if (nested && nested.children.length > 0) {
                        data.children = serialize(nested);
                    }
                    serialized.push(data);
                }
                return serialized;
            }

            // Save order via AJAX
            saveButton.addEventListener('click', function () {
                let topLevelGroup = listContainer.querySelector('.sortable-group');
                if (!topLevelGroup) return;

                let nestedData = serialize(topLevelGroup);
                console.log('Serialized Data:', JSON.stringify(nestedData));

                // Disable button during save
                saveButton.disabled = true;
                saveButton.textContent = 'Saving...';

                fetch('{{ route("admin.sidebar.items.updateOrder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ orderData: nestedData })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Save response:', data);
                    if (data.status === 'success') {
                         saveButton.style.display = 'none'; // Hide button on success
                         // Optionally show a success message (e.g., using Toastr or appending to page)
                         alert('Order saved successfully!');
                    } else {
                         alert('Error saving order: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    alert('An error occurred while saving the order.');
                })
                .finally(() => {
                     saveButton.disabled = false;
                     saveButton.textContent = 'Save New Order';
                });
            });
        });
    </script>
@stop 