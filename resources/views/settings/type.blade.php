@extends('adminlte::page')

@section('title', 'Stock Types')

@section('content_header')

    <h1>Stock Types</h1>
@stop

@section('content')
    <!-- Add Stock Type Button -->
    @can('create-stock-type')
        <a href="javascript:void(0)" class="btn btn-success" id="addStockTypeBtn">Add Stock Type</a>
    @endcan
    @can('export-stock-type')
        <button id="apply-filter" class="btn btn-success">Export Result in Excel</button>
    @endcan

    @include('partials.filter-type', ['users' => $users])
    <!-- DataTable for Stock Types -->
    @can('read-stock-type')
        <table class="table table-bordered" id="stock-types-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Stock Type Name</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Created By</th>
                    <th>Updated By</th>
                    <th>Action</th>
                </tr>

            </thead>
        </table>
    @endcan
    <!-- Modal for Add/Edit Stock Type -->
    <div class="modal fade" id="stockTypeModal" tabindex="-1" aria-labelledby="stockTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="stockTypeModalLabel">Add Stock Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="stockTypeForm">
                        @csrf
                        <input type="hidden" name="stock_type_id" id="stock-type-id">
                        <div class="mb-3 position-relative">
                            <label for="stock_type_name" class="form-label">Stock Type Name <span
                                    class="text-danger">*</span></label>

                            <!-- Input field with required attribute -->
                            <input type="text" class="form-control" id="stock_type_name" name="stock_type_name" required
                                maxlength="50" placeholder="Enter the stock type name">

                            <!-- Error message for stock type name -->
                            <div id="stock_type_name_error" class="text-danger"></div>
                        </div>
                        <button type="submit" id="saveStockTypeBtn" class="btn btn-primary" disabled>Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Delete Confirmation -->
    <div class="modal fade" id="deleteStockTypeModal" tabindex="-1" aria-labelledby="deleteStockTypeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteStockTypeModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this stock type?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteStockType">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toasts for Success/Error Messages -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
        <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">Stock type saved successfully!</div>
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
        var canEditStockType = @json($canEditStockType);
        var canDeleteStockType = @json($canDeleteStockType);
    </script>

    <script>
        $(function() {
            var table = $('#stock-types-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('type.index') }}",
                    data: function(d) {
                        d.stock_type_name = $('#filter-stock-type-name').val();
                        d.created_at = $('#filter-created-at').val();
                        d.updated_at = $('#filter-updated-at').val();
                        d.created_by = $('#filter-created-by').val();
                        d.updated_by = $('#filter-updated-by').val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'stock_type_name',
                        name: 'stock_type_name'
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
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'updated_by',
                        name: 'updated_by'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let actionButtons = '';

                            // Check if the user has permission to edit the stock type
                            if (canEditStockType) {
                                actionButtons +=
                                    `<button class="btn btn-primary edit-stock-type" data-id="${row.id}">Edit</button>`;
                            }

                            // Check if the user has permission to delete the stock type
                            if (canDeleteStockType) {
                                actionButtons +=
                                    `<button class="btn btn-danger delete-stock-type" data-id="${row.id}">Delete</button>`;
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
            new $.fn.dataTable.Responsive(table);

            // Add the buttons to the table
            table.buttons().container().appendTo('#assignedRoles-table_wrapper .col-md-6:eq(0)');



            // Filter functionality
            $('#filter-id, #filter-stock-type-name, #filter-created-at, #filter-updated-at, #filter-created-by, #filter-updated-by')
                .on('keyup change', function() {
                    table.draw();
                });

            // Add Stock Type button click
            $('#addStockTypeBtn').click(function() {
                $('#stockTypeForm')[0].reset();
                $('#stock-type-id').val('');
                $('#stockTypeModal').modal('show');
                $('#saveStockTypeBtn').attr('disabled', true); // Ensure Save button is disabled initially
                $('#stock_type_name_error').text(''); // Clear error messages
            });

            // Enable/Disable Save button based on stock_type_name input
            $('#stock_type_name').on('input', function() {
                var stockTypeNameValue = $(this).val().trim();
                if (stockTypeNameValue.length > 0) {
                    $('#saveStockTypeBtn').attr('disabled', false); // Enable button when input has value
                } else {
                    $('#saveStockTypeBtn').attr('disabled', true); // Disable button when input is empty
                }
            });

            // Edit Stock Type button click
            $('body').on('click', '.edit-stock-type', function() {
                var id = $(this).data('id');
                $.get('/type/' + id + '/edit', function(data) {
                    $('#stock-type-id').val(data.id);
                    $('#stock_type_name').val(data.stock_type_name);
                    $('#stockTypeModal').modal('show');
                    $('#saveStockTypeBtn').attr('disabled',
                        false); // Enable Save button during edit
                    $('#stock_type_name_error').text(''); // Clear error messages
                });
            });

            // Handle Form Submission for Add/Edit
            $('#stockTypeForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var method = $('#stock-type-id').val() ? 'PUT' : 'POST';
                var url = method === 'POST' ? "{{ route('type.store') }}" : '/type/' + $('#stock-type-id')
                    .val();

                $.ajax({
                    type: method,
                    url: url,
                    data: formData,
                    success: function(response) {
                        $('#stockTypeModal').modal('hide'); // Hide the modal
                        table.ajax.reload();

                        // Show success toast
                        var successToast = new bootstrap.Toast(document.getElementById(
                            'successToast'));
                        successToast.show();
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            if (errors.stock_type_name) {
                                $('#stock_type_name_error').text(errors.stock_type_name[
                                    0]); // Display error for stock type name
                            }
                        } else {
                            // General error message
                            $('#stock_type_name_error').text('An unexpected error occurred.');

                            // Show error toast with a general error message
                            var errorToast = new bootstrap.Toast(document.getElementById(
                                'errorToast'));
                            var errorMessage = xhr.responseJSON?.message ||
                                'An error occurred while processing your request.';
                            $('#errorToastMessage').text('Error: ' + errorMessage);
                            errorToast.show();
                        }
                    }
                });
            });

            // Trigger Delete Modal
            var stockTypeIdToDelete = null;
            $('body').on('click', '.delete-stock-type', function() {
                stockTypeIdToDelete = $(this).data('id');
                $('#deleteStockTypeModal').modal('show');
            });

            // Confirm Delete
            $('#confirmDeleteStockType').click(function() {
                $.ajax({
                    type: 'DELETE',
                    url: '/type/' + stockTypeIdToDelete,
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#deleteStockTypeModal').modal('hide');
                        table.ajax.reload();

                        // Show success toast
                        var successToast = new bootstrap.Toast(document.getElementById(
                            'successToast'));
                        successToast.show();
                    },
                    error: function(xhr) {
                        // General error handling
                        var errorToast = new bootstrap.Toast(document.getElementById(
                            'errorToast'));
                        var errorMessage = xhr.responseJSON?.message ||
                            'An error occurred while processing your request.';
                        $('#errorToastMessage').text('Error: ' + errorMessage);
                        errorToast.show();
                    }
                });
            });

            const filterButton = document.getElementById('apply-filter');

            // Select all the filter input elements
            const filters = {
                id: document.getElementById('filter-id'),
                stock_type_name: document.getElementById('filter-stock-type-name'), // Updated field
                created_at: document.getElementById('filter-created-at'),
                updated_at: document.getElementById('filter-updated-at'),
                created_by: document.getElementById('filter-created-by'),
                updated_by: document.getElementById('filter-updated-by'),
            };

            // Add event listener to the filter button
            filterButton.addEventListener('click', function() {
                // Build the query string from the filter inputs
                let queryString = '?';

                for (let key in filters) {
                    const value = filters[key].value;
                    if (value) {
                        queryString += `${key}=${value}&`;
                    }
                }

                // Redirect the page with the updated filters in the query string
                window.open('/export/type' + queryString.slice(0, -1), '_blank');
            });

        });
    </script>
@stop
