@extends('adminlte::page')

@section('title', 'Items')

@section('content_header')
    <h1>Items</h1>
@stop

@section('content')
    @include('partials.expiration.expire')
    <div style="height: 700px; overflow-y: auto;">
        <!-- Add Item Button -->
        @can('create-items')
            <a href="javascript:void(0)" class="btn btn-success" id="addItemBtn">Add Item</a>
        @endcan

        @can('export-items')
            <button id="apply-filter" class="btn btn-success">Export Result in Excel</button>
        @endcan
        @include('partials.filter-item', [
            'users' => $users,
            'categories' => $categories,
            'brands' => $brands,
            'units' => $units,
        ])
        <!-- DataTable for Items -->
        @can('read-items')
            <table class="table table-bordered" id="items-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th>Brand</th>
                        <th>Size</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Created By</th>
                        <th>Updated By</th>
                        <th>Action</th>
                    </tr>

                </thead>
            </table>
        @endcan
    </div>

    <!-- Modal for Add/Edit Item -->
    <div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemModalLabel">Add Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="itemForm">
                        @csrf
                        <input type="hidden" name="item_id" id="item-id">
                        <div class="mb-3">
                            <label for="item_code" class="form-label">Item Code</label>
                            <input type="text" class="form-control" id="item_code" name="item_code" maxlength="255"
                                placeholder="Enter item code">
                        </div>
                        <div class="mb-3">
                            <label for="item_name" class="form-label">Item Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="item_name" name="item_name" required
                                maxlength="255" placeholder="Enter item name">
                        </div>
                        <div class="mb-3">
                            <label for="item_category" class="form-label">Category <span
                                    class="text-danger">*</span></label>
                            <select class="form-control" id="item_category" name="item_category" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->categorie_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="item_unit" class="form-label">Unit <span class="text-danger">*</span></label>
                            <select class="form-control" id="item_unit" name="item_unit" required>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="item_brand" class="form-label">Brand <span class="text-danger">*</span></label>
                            <select class="form-control" id="item_brand" name="item_brand" required>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="item_size" class="form-label">Size</label>
                            <input type="text" class="form-control" id="item_size" name="item_size" maxlength="255"
                                placeholder="Enter item size">
                        </div>
                        <button type="submit" id="saveItemBtn" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Delete Confirmation -->
    <div class="modal fade" id="deleteItemModal" tabindex="-1" aria-labelledby="deleteItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteItemModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this item?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteItem">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toasts for Success/Error Messages -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
        <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">Item saved successfully!</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>

        <div id="errorToast" class="toast align-items-center text-white bg-danger border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="errorToastMessage">An error occurred!</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>
@stop

@section('js')
    @include('partials.import-cdn')
    <script>
        var canEditItem = @json($canEditItem);
        var canDeleteItem = @json($canDeleteItem);
    </script>

    <script>
        $(function() {
            // Initialize DataTable
            console.log("canEditItem = ", canEditItem)
            var table = $('#items-table')?.DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('items.index') }}",
                    data: function(d) {
                        d.item_code = $('#filter-item-code').val();
                        d.item_name = $('#filter-item-name').val();
                        d.item_category = $('#filter-category').val();
                        d.item_unit = $('#filter-unit').val();
                        d.item_brand = $('#filter-brand').val();
                        d.item_size = $('#filter-item-size').val();
                        d.created_by = $('#filter-created-by').val();
                        d.updated_by = $('#filter-updated-by').val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'item_code',
                        name: 'item_code'
                    },
                    {
                        data: 'item_name',
                        name: 'item_name'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'brand',
                        name: 'brand'
                    },
                    {
                        data: 'item_size',
                        name: 'item_size'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'updated_by',
                        name: 'updated_by'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let actionButtons = '';

                            if (canEditItem) {
                                actionButtons +=
                                    `<button class="btn btn-primary edit-btn" data-id="${row.id}">Edit</button>`;
                            }

                            if (canDeleteItem) {
                                actionButtons +=
                                    `<button class="btn btn-danger delete-btn" data-id="${row.id}">Delete</button>`;
                            }

                            return actionButtons;
                        }
                    }

                ],
                colReorder: true, // Enable column reordering
                buttons: [{
                        extend: 'colvis', // Enable column visibility button
                        text: 'Show/Hide Columns',
                        titleAttr: 'Show/Hide Columns'
                    },
                    'copy', 'excel', 'pdf', 'print' // Add other export buttons as needed
                ],
                dom: 'Bfrtip', // Position the buttons
            });
            if (table) {
                new $.fn.dataTable.Responsive(table);

                // Add the buttons to the table
                table?.buttons()?.container()?.appendTo('#items-table_wrapper .col-md-6:eq(0)');

            }

            // Filter event
            $('#filter-id, #filter-item-code, #filter-item-name, #filter-category,#filter-unit, #filter-brand, #filter-item-size, #filter-created-by, #filter-updated-by')
                .on('keyup change', function() {
                    table.draw();
                });

            // Open modal for adding/editing an item
            $('#addItemBtn').click(function() {
                $('#itemForm')[0].reset();
                $('#itemModalLabel').text('Add Item');
                $('#itemModal').modal('show');
            });

            // Save item
            $('#itemForm').submit(function(e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('items.store') }}",
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#itemModal').modal('hide');
                        $('#successToast').toast('show');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        $('#errorToastMessage').text(xhr.responseJSON.message ||
                            'An error occurred');
                        $('#errorToast').toast('show');
                    }
                });
            });

            $('body').on('click', '.edit-btn', function() {
                var id = $(this).data('id'); // Get the ID from the button's data attribute
                $.get('/items/' + id + '/edit', function(data) {
                    // Populate modal fields with data from the server
                    $('#item-id').val(data.id);
                    $('#item_code').val(data.item_code);
                    $('#item_name').val(data.item_name);
                    $('#item_size').val(data.item_size);
                    $('#item_category').val(data
                        .item_category); // Assuming you have a dropdown for categories
                    $('#item_unit').val(data
                        .item_unit);

                    $('#item_brand').val(data
                        .item_brand); // Assuming you have a dropdown for brands
                    $('#itemModal').modal('show'); // Show the modal

                    // Enable the save button
                    $('#saveItemBtn').prop('disabled', false);

                    // Clear error messages
                    $('#item_name_error').text('');
                    $('#item_code_error').text('');
                    $('#item_size_error').text('');
                    // Add more fields as necessary
                });
            });
            // Open delete confirmation modal
            $('#items-table').on('click', '.delete-btn', function() {
                var itemId = $(this).data('id');
                $('#confirmDeleteItem').data('id', itemId);
                $('#deleteItemModal').modal('show');
            });

            // Confirm delete
            $('#confirmDeleteItem').click(function() {
                var itemId = $(this).data('id');

                $.ajax({
                    url: "/items/" + itemId,
                    method: 'DELETE',
                    success: function() {
                        $('#deleteItemModal').modal('hide');
                        $('#successToast').toast('show');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        $('#errorToastMessage').text(xhr.responseJSON.message ||
                            'An error occurred');
                        $('#errorToast').toast('show');
                    }
                });
            });


            // Select the filter button
            const filterButton = document.getElementById('apply-filter');

            // Select all the filter input elements
            const filters = {
                id: document.getElementById('filter-id'),
                item_code: document.getElementById('filter-item-code'),
                item_name: document.getElementById('filter-item-name'),
                item_size: document.getElementById('filter-item-size'),
                category_id: document.getElementById('filter-category'),
                brand_id: document.getElementById('filter-brand'),
                created_at: document.getElementById('filter-created-at'),
                updated_at: document.getElementById('filter-updated-at'),
                created_by: document.getElementById('filter-created-by'),
                updated_by: document.getElementById('filter-updated-by'),
            };

            // Add event listener to the filter button
            filterButton?.addEventListener('click', function() {
                // Build the query string from the filter inputs
                let queryString = '?';

                for (let key in filters) {
                    const value = filters[key].value;
                    if (value) {
                        queryString +=
                            `${key}=${encodeURIComponent(value)}&`; // encodeURIComponent to handle special characters
                    }
                }

                // Redirect the page with the updated filters in the query string (or perform AJAX request)
                window.open('/export/items' + queryString.slice(0, -1),
                    '_blank'); // Update the URL to your export route
            });

        });
    </script>
@stop
