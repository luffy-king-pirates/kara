@extends('adminlte::page')

@section('title', 'User Assigned Role')

@section('content_header')
    <!-- Bootstrap CSS -->

    <h1>User Assigned Role</h1>
@stop

@section('content')

    <!-- Add User Assigned Unit Button -->
    @can('create-user-assigned-role')
        <a href="javascript:void(0)" class="btn btn-success" id="addUnitBtn">Add User Assigned Unit</a>
    @endcan
    @can('export-user-assigned-role')
        <button id="apply-filter" class="btn btn-success">Export Result in Excel</button>
    @endcan

    <!-- DataTable for User Assigned assignedRoles -->
    @can('read-user-assigned-role')
        <table class="table table-bordered" id="assignedRoles-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Action</th>
                </tr>

            </thead>
        </table>
    @endcan
    <!-- Modal for Add/Edit User Assigned Unit -->
    <div class="modal fade" id="unitModal" tabindex="-1" aria-labelledby="unitModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unitModalLabel">Add User Assigned Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="unitForm">
                        @csrf
                        <input type="hidden" name="unit_id" id="unit-id">

                        <div class="mb-3 position-relative">
                            <label for="user_id" class="form-label">User <span class="text-danger">*</span></label>
                            <select class="form-control" id="user_id" name="user_id" required>
                                <option value="">Select User</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <div id="user_id_error" class="text-danger"></div>
                        </div>

                        <div class="mb-3 position-relative">
                            <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-control" id="role_id" name="role_id" required>
                                <option value="">Select Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                @endforeach
                            </select>
                            <div id="role_id_error" class="text-danger"></div>
                        </div>

                        <button type="submit" id="saveUnitBtn" class="btn btn-primary" disabled>Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Delete Confirmation -->
    <div class="modal fade" id="deleteUnitModal" tabindex="-1" aria-labelledby="deleteUnitModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUnitModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user assigned unit?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteUnit">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toasts for Success/Error Messages -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
        <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">User Assigned Unit saved successfully!</div>
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
        var canEditUserAssignedRole = @json($canEditUserAssignedRole);
        var canDeleteUserAssignedRole = @json($canDeleteUserAssignedRole);
    </script>

    <script>
        $(function() {
            var table = $('#assignedRoles-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('assignedRoles.index') }}",
                    data: function(d) {
                        d.user_id = $('#filter-user').val();
                        d.role_id = $('#filter-role').val();
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
                        data: 'user',
                        name: 'user.name'
                    }, // Assuming 'user' relation in your Unit model
                    {
                        data: 'role',
                        name: 'role.role_name'
                    }, // Assuming 'role' relation in your Unit model
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

                            if (canEditUserAssignedRole) {
                                actionButtons +=
                                    `<button class="btn btn-primary edit-unit" data-id="${row.id}">Edit</button>`;
                            }

                            if (canDeleteUserAssignedRole) {
                                actionButtons +=
                                    `<button class="btn btn-danger delete-unit" data-id="${row.id}">Delete</button>`;
                            }

                            return actionButtons;
                        }
                    }
                ],

            });


            // Add the buttons to the table
            table.buttons().container().appendTo('#assignedRoles-table_wrapper .col-md-6:eq(0)');

            // Filter functionality
            $('#filter-id, #filter-user, #filter-role, #filter-created-at, #filter-updated-at, #filter-created-by, #filter-updated-by')
                .on('keyup change', function() {
                    table.draw();
                });

            // Add User Assigned Unit button click
            $('#addUnitBtn').click(function() {
                $('#unitForm')[0].reset();
                $('#unit-id').val('');
                $('#unitModal').modal('show');
                $('#saveUnitBtn').attr('disabled', true); // Ensure Save button is disabled initially
                $('#user_id_error').text(''); // Clear error messages
                $('#role_id_error').text(''); // Clear error messages
            });

            // Enable/Disable Save button based on user_id and role_id inputs
            $('#user_id, #role_id').on('change', function() {
                var userIdValue = $('#user_id').val();
                var roleIdValue = $('#role_id').val();
                if (userIdValue && roleIdValue) {
                    $('#saveUnitBtn').attr('disabled',
                        false); // Enable button when both dropdowns have values
                } else {
                    $('#saveUnitBtn').attr('disabled', true); // Disable button if either is empty
                }
            });

            // Edit User Assigned Unit button click
            $('body').on('click', '.edit-unit', function() {
                var id = $(this).data('id');
                $.get('/assignedRoles/' + id + '/edit', function(data) {
                    $('#unit-id').val(data.id);
                    $('#user_id').val(data.user_id);
                    $('#role_id').val(data.role_id);
                    $('#unitModal').modal('show');
                    $('#saveUnitBtn').attr('disabled', false); // Enable Save button during edit
                    $('#user_id_error').text(''); // Clear error messages
                    $('#role_id_error').text(''); // Clear error messages
                });
            });

            // Handle Form Submission for Add/Edit
            $('#unitForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var method = $('#unit-id').val() ? 'PUT' : 'POST';
                var url = method === 'POST' ? "{{ route('assignedRoles.store') }}" : '/assignedRoles/' + $(
                    '#unit-id').val();

                $.ajax({
                    type: method,
                    url: url,
                    data: formData,
                    success: function(response) {
                        $('#unitModal').modal('hide'); // Hide the modal
                        table.ajax.reload();

                        // Show success toast
                        var successToast = new bootstrap.Toast(document.getElementById(
                            'successToast'));
                        successToast.show();
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            if (errors.user_id) {
                                $('#user_id_error').text(errors.user_id[
                                    0]); // Display error for user selection
                            }
                            if (errors.role_id) {
                                $('#role_id_error').text(errors.role_id[
                                    0]); // Display error for role selection
                            }
                        } else {
                            $('#user_id_error').text('An unexpected error occurred.');
                            $('#role_id_error').text('An unexpected error occurred.');
                        }
                    }
                });
            });

            // Trigger Delete Modal
            var unitIdToDelete = null;
            $('body').on('click', '.delete-unit', function() {
                unitIdToDelete = $(this).data('id');
                $('#deleteUnitModal').modal('show');
            });

            // Confirm Delete
            $('#confirmDeleteUnit').click(function() {
                $.ajax({
                    type: 'DELETE',
                    url: '/assignedRoles/' + unitIdToDelete,
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#deleteUnitModal').modal('hide');
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

            // Select the submit button
            const submitButton = document.getElementById('apply-filter'); // Replace with your actual button ID

            // Select all the user and role input elements
            const inputs = {
                user_id: document.getElementById('user_id'),
                role_id: document.getElementById('role_id'),
            };

            // Add event listener to the submit button
            submitButton?.addEventListener('click', function() {
                // Build the query string from the input values
                let queryString = '?';

                for (let key in inputs) {
                    const value = inputs[key].value;
                    if (value) {
                        queryString +=
                            `${key}=${encodeURIComponent(value)}&`; // encodeURIComponent to handle special characters
                    } else {
                        // Display an error message if any input is empty
                        document.getElementById(`${key}_error`).innerText =
                            `Please select a ${key.replace('_', ' ')}.`;
                    }
                }


                // Redirect the page with the updated filters in the query string (or perform AJAX request)
                window.open('/export/assignedRoles', '_blank'); // Update the URL to your export route

            });


        });
    </script>
@stop
