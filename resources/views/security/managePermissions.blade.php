@extends('adminlte::page')

@section('title', 'Manage Permissions')

@section('content_header')
    <h1>Manage Permissions</h1>
@stop

@section('content')
    @include('partials.filter-manage-permissions')
    <table id="permissions-table" class="table">
        <thead>
            <tr>
                <th>Role Name</th>
                <th>Page</th>
                <th>Actions</th>
                <th>Manage</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <!-- Toasts for Success/Error Messages -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
        <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">Permission Added successfully!</div>
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

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    @include('partials.import-cdn')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#permissions-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('managePermissions.index') }}",
                    data: function(d) {
                        d.role_name = $('#filter-role-name').val();
                        d.page = $('#filter-page').val();
                        d.action = $('#filter-action').val();
                    }
                },

                columns: [{
                        data: 'role_name',
                        name: 'role_name'
                    },
                    {
                        data: 'page',
                        name: 'page'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            // Generate the checkbox HTML with checked state based on permissions
                            return `
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="create" id="create-${row.id}-${row.page}" ${row.permissions.includes('create') ? 'checked' : ''}>
                                    <label class="form-check-label" for="create-${row.id}-${row.page}">Create</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="read" id="read-${row.id}-${row.page}" ${row.permissions.includes('read') ? 'checked' : ''}>
                                    <label class="form-check-label" for="read-${row.id}-${row.page}">Read</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="update" id="update-${row.id}-${row.page}" ${row.permissions.includes('update') ? 'checked' : ''}>
                                    <label class="form-check-label" for="update-${row.id}-${row.page}">Update</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="delete" id="delete-${row.id}-${row.page}" ${row.permissions.includes('delete') ? 'checked' : ''}>
                                    <label class="form-check-label" for="delete-${row.id}-${row.page}">Delete</label>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'manage',
                        name: 'manage',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<button class="btn btn-primary save-permissions" data-role-id="${row.id}">Save</button>`;
                        }
                    }
                ]

            });
            new $.fn.dataTable.Responsive(table);

            $('#reset-filters').on('click', function() {
                $('#filter-role-name').val('');
                $('#filter-page').val('');
                $('#filter-action').val('');
                table.draw(); // Redraw the table with cleared filters
            });

              $('#filter-action, #filter-role-name, #filter-page')
                .on('keyup change', function() {
                    table.draw();
                });

            // Save permissions when button is clicked
            $('#permissions-table').on('click', '.save-permissions', function() {
                var roleId = $(this).data('role-id');
                var permissions = {};

                // Collect permissions for the specific role
                $(this).closest('tr').find('input[type=checkbox]').each(function() {
                    var page = $(this).closest('tr').find('td:nth-child(2)').text().trim();
                    var action = $(this).val();
                    if ($(this).is(':checked')) {
                        if (!permissions[page]) {
                            permissions[page] = [];
                        }
                        permissions[page].push(action);
                    }
                });

                // Send AJAX request to save permissions
                $.ajax({
                    url: "{{ route('managePermissions.save') }}", // Update to correct route
                    method: 'POST',
                    data: {
                        role_id: roleId,
                        permissions: permissions,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        var successToast = new bootstrap.Toast(document.getElementById(
                            'successToast'));
                        successToast.show();
                        table.ajax.reload(); // Reload DataTable to reflect changes
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
        });
    </script>
@stop
