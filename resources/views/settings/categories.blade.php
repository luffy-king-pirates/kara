@extends('adminlte::page')

@section('title', 'Item Categories')

@section('content_header')
    <h1>Item Categories</h1>
@stop

@section('content')
    <!-- Add Category Button -->
    <a href="javascript:void(0)" class="btn btn-success" id="addCategoryBtn">Add Category</a>
    <button id="apply-filter" class="btn btn-success">Export Result in Excel</button>
    @include('partials.filter-categories', ['users' => $users])
    <!-- DataTable for Categories -->
    <table class="table table-bordered" id="categories-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th>Action</th>
            </tr>

        </thead>
    </table>

    <!-- Modal for Add/Edit Category -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm">
                        @csrf
                        <input type="hidden" name="categorie_id" id="categorie-id">
                        <div class="mb-3 position-relative">
                            <label for="categorie_name" class="form-label">Category Name <span
                                    class="text-danger">*</span></label>

                            <!-- Input field with required attribute -->
                            <input type="text" class="form-control" id="categorie_name" name="categorie_name" required
                                maxlength="50" placeholder="Enter the category name">

                            <!-- Error message for category name -->
                            <div id="categorie_name_error" class="text-danger"></div>
                        </div>
                        <button type="submit" id="saveCategoryBtn" class="btn btn-primary" disabled>Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Delete Confirmation -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCategoryModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this category?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteCategory">Delete</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Toasts for Success/Error Messages -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
        <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">Category saved successfully!</div>
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
        $(function() {
            var table = $('#categories-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('categories.index') }}",
                    data: function(d) {
                        d.categorie_name = $('#filter-categorie-name').val();
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
                        data: 'categorie_name',
                        name: 'categorie_name'
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
                            return `
                            <button class="btn btn-primary edit-category" data-id="${row.id}">Edit</button>
                            <button class="btn btn-danger delete-category" data-id="${row.id}">Delete</button>
                        `;
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
            $('#filter-id, #filter-categorie-name, #filter-created-at, #filter-updated-at, #filter-created-by, #filter-updated-by')
                .on('keyup change', function() {
                    table.draw();
                });

            // Add Category button click
            $('#addCategoryBtn').click(function() {
                $('#categoryForm')[0].reset();
                $('#categorie-id').val('');
                $('#categoryModal').modal('show');
                $('#saveCategoryBtn').attr('disabled', true); // Ensure Save button is disabled initially
                $('#categorie_name_error').text(''); // Clear error messages
            });

            // Enable/Disable Save button based on categorie_name input
            $('#categorie_name').on('input', function() {
                var categorieNameValue = $(this).val().trim();
                if (categorieNameValue.length > 0) {
                    $('#saveCategoryBtn').attr('disabled', false); // Enable button when input has value
                } else {
                    $('#saveCategoryBtn').attr('disabled', true); // Disable button when input is empty
                }
            });

            // Edit Category button click
            $('body').on('click', '.edit-category', function() {
                var id = $(this).data('id');
                $.get('/categories/' + id + '/edit', function(data) {
                    $('#categorie-id').val(data.id);
                    $('#categorie_name').val(data.categorie_name);
                    $('#categoryModal').modal('show');
                    $('#saveCategoryBtn').attr('disabled', false); // Enable Save button during edit
                    $('#categorie_name_error').text(''); // Clear error messages
                });
            });

            // Handle Form Submission for Add/Edit
            $('#categoryForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var method = $('#categorie-id').val() ? 'PUT' : 'POST';
                var url = method === 'POST' ? "{{ route('categories.store') }}" : '/categories/' + $(
                    '#categorie-id').val();

                $.ajax({
                    type: method,
                    url: url,
                    data: formData,
                    success: function(response) {
                        $('#categoryModal').modal('hide'); // Hide the modal
                        table.ajax.reload();

                        // Show success toast
                        var successToast = new bootstrap.Toast(document.getElementById(
                            'successToast'));
                        successToast.show();
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            if (errors.categorie_name) {
                                $('#categorie_name_error').text(errors.categorie_name[
                                    0]); // Display error for category name
                            }
                        } else {
                            // General error message
                            $('#categorie_name_error').text('An unexpected error occurred.');

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
            var categorieIdToDelete = null;
            $('body').on('click', '.delete-category', function() {
                categorieIdToDelete = $(this).data('id');
                $('#deleteCategoryModal').modal('show');
            });

            // Confirm Delete
            $('#confirmDeleteCategory').click(function() {
                $.ajax({
                    type: 'DELETE',
                    url: '/categories/' + categorieIdToDelete,
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#deleteCategoryModal').modal('hide');
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
                categorie_name: document.getElementById('filter-categorie-name'), // Updated to 'categorie_name'
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

                // Redirect the page with the updated filters in the query string
                window.open('/export/categories' + queryString.slice(0, -1),
                    '_blank'); // Update the URL to '/export/categories'
            });
        });
    </script>

@stop
