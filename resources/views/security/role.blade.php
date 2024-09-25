@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')

    <h1>Roles</h1>

@stop

@section('content')

    <!-- Add Role Button -->
    @can('create-role')
        <a href="javascript:void(0)" class="btn btn-success" id="addRoleBtn">Add Role</a>
    @endcan
    @can('export-role')
        <button id="apply-filter" class="btn btn-success">Export Result in Excel</button>
    @endcan
    @include('partials.filter-role', ['users' => $users])
    <!-- DataTable for Roles -->
    @can('read-role')
        <div class="container-fluid">
            <table class="table table-bordered dt-responsive nowrap" id="roles-table" style="width: 100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Role Name</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Created By</th>
                        <th>Updated By</th>
                        <th>Action</th>
                    </tr>

                </thead>

            </table>
        </div>
    @endcan
    <!-- Modal for Add/Edit Role -->
    <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">Add Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="roleForm">
                        @csrf
                        <input type="hidden" name="role_id" id="role-id">
                        <div class="mb-3 position-relative">
                            <label for="role_name" class="form-label">Role Name <span class="text-danger">*</span></label>

                            <!-- Input field with required attribute -->
                            <input type="text" class="form-control" id="role_name" name="role_name" required
                                maxlength="50" placeholder="Enter the role name">

                            <!-- Error message for role name -->
                            <div id="role_name_error" class="text-danger"></div>
                        </div>
                        <button type="submit" id="saveRoleBtn" class="btn btn-primary" disabled>Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Delete Confirmation -->
    <div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteRoleModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this role?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteRole">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toasts for Success/Error Messages -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
        <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">Role saved successfully!</div>
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
        var canEditRole = @json($canEditRole);
        var canDeleteRole = @json($canDeleteRole);
    </script>
    <script>
        $(function() {

            var table = $('#roles-table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('roles.index') }}",
                    data: function(d) {
                        d.role_name = $('#filter-role-name').val();
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
                        data: 'role_name',
                        name: 'role_name'
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

                            if (canEditRole) {
                                actionButtons +=
                                    `<button class="btn btn-primary edit-role" data-id="${row.id}">Edit</button>`;
                            }

                            if (canDeleteRole) {
                                actionButtons +=
                                    `<button class="btn btn-danger delete-role" data-id="${row.id}">Delete</button>`;
                            }

                            return actionButtons;
                        }
                    }
                ]
            });


            // Filter functionality
            $('#filter-id, #filter-role-name, #filter-created-at, #filter-updated-at, #filter-created-by, #filter-updated-by')
                .on('keyup change', function() {
                    table.draw();
                });

            // Add Role button click
            $('#addRoleBtn').click(function() {
                $('#roleForm')[0].reset();
                $('#role-id').val('');
                $('#roleModal').modal('show');
                $('#saveRoleBtn').attr('disabled', true); // Ensure Save button is disabled initially
                $('#role_name_error').text(''); // Clear error messages
            });

            // Enable/Disable Save button based on role_name input
            $('#role_name').on('input', function() {
                var roleNameValue = $(this).val().trim();
                $('#saveRoleBtn').attr('disabled', roleNameValue.length === 0);
            });

            // Edit Role button click
            $('body').on('click', '.edit-role', function() {
                var id = $(this).data('id');
                $.get('/roles/' + id + '/edit', function(data) {
                    $('#role-id').val(data.id);
                    $('#role_name').val(data.role_name);
                    $('#roleModal').modal('show');
                    $('#saveRoleBtn').attr('disabled', false); // Enable Save button during edit
                    $('#role_name_error').text(''); // Clear error messages
                });
            });

            // Handle Form Submission for Add/Edit
            $('#roleForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var method = $('#role-id').val() ? 'PUT' : 'POST';
                var url = method === 'POST' ? "{{ route('roles.store') }}" : '/roles/' + $('#role-id')
                    .val();

                $.ajax({
                    type: method,
                    url: url,
                    data: formData,
                    success: function(response) {
                        $('#roleModal').modal('hide'); // Hide the modal
                        table.ajax.reload();

                        // Show success toast
                        var successToast = new bootstrap.Toast(document.getElementById(
                            'successToast'));
                        successToast.show();
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            if (errors.role_name) {
                                $('#role_name_error').text(errors.role_name[
                                    0]); // Display error for role name
                            }
                        } else {
                            // General error message
                            $('#role_name_error').text('An unexpected error occurred.');
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
            var roleIdToDelete = null;
            $('body').on('click', '.delete-role', function() {
                roleIdToDelete = $(this).data('id');
                $('#deleteRoleModal').modal('show');
            });

            // Confirm Delete
            $('#confirmDeleteRole').click(function() {
                $.ajax({
                    type: 'DELETE',
                    url: '/roles/' + roleIdToDelete,
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#deleteRoleModal').modal('hide');
                        table.ajax.reload();

                        // Show success toast
                        var successToast = new bootstrap.Toast(document.getElementById(
                            'successToast'));
                        successToast.show();
                    },
                    error: function(xhr) {
                        var errorToast = new bootstrap.Toast(document.getElementById(
                            'errorToast'));
                        var errorMessage = xhr.responseJSON?.message ||
                            'An error occurred while processing your request.';
                        $('#errorToastMessage').text('Error: ' + errorMessage);
                        errorToast.show();
                    }
                });
            });
            // Select the filter button
            const filterButton = document.getElementById('apply-filter');

            // Select all the filter input elements
            const filters = {
                id: document.getElementById('filter-id'),
                role_name: document.getElementById('filter-role-name'),
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
                window.open('/export/roles' + queryString.slice(0, -1),
                    '_blank'); // Update the URL to your export route
            });

        });
    </script>

@stop
