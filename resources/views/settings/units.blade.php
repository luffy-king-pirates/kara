@extends('adminlte::page')

@section('title', 'Units')

@section('content_header')

    <h1>Units</h1>
@stop

@section('content')
    <div style="height: 700px; overflow-y: auto;">
        @include('partials.expiration.expire')
        <!-- Add Unit Button -->
        @can('create-unit')
            <a href="javascript:void(0)" class="btn btn-success" id="addUnitBtn">Add Unit</a>
        @endcan
        @can('export-unit')
            <button id="apply-filter" class="btn btn-success">Export Result in Excel</button>
        @endcan

        @include('partials.filter-units', ['users' => $users])
        <!-- DataTable for Units -->
        @can('read-unit')
            <table class="table table-bordered" id="units-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Unit Name</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Created By</th>
                        <th>Updated By</th>
                        <th>Action</th>
                    </tr>

                </thead>
            </table>
        @endcan
        <!-- Modal for Add/Edit Unit -->
        <div class="modal fade" id="unitModal" tabindex="-1" aria-labelledby="unitModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="unitModalLabel">Add Unit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="unitForm">
                            @csrf
                            <input type="hidden" name="unit_id" id="unit-id">
                            <div class="mb-3 position-relative">
                                <label for="unit_name" class="form-label">Unit Name <span
                                        class="text-danger">*</span></label>

                                <!-- Input field with required attribute -->
                                <input type="text" class="form-control" id="unit_name" name="unit_name" required
                                    maxlength="50" placeholder="Enter the unit name">

                                <!-- Error message for unit name -->
                                <div id="unit_name_error" class="text-danger"></div>
                            </div>
                            <button type="submit" id="saveUnitBtn" class="btn btn-primary" disabled>Save changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Delete Confirmation -->
        <div class="modal fade" id="deleteUnitModal" tabindex="-1" aria-labelledby="deleteUnitModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteUnitModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this unit?
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
                    <div class="toast-body">Unit saved successfully!</div>
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
    </div>

@stop

@section('js')
    @include('partials.import-cdn')
    <script>
        var canEditUnit = @json($canEditUnit);
        var canDeleteUnit = @json($canDeleteUnit);
    </script>
    <script>
        $(function() {

            var table = $('#units-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('units.index') }}",
                    data: function(d) {
                        d.unit_name = $('#filter-unit-name').val();
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
                        data: 'unit_name',
                        name: 'unit_name'
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

                            // Check if the user has permission to edit the unit
                            if (canEditUnit) {
                                actionButtons +=
                                    `<button class="btn btn-primary edit-unit" data-id="${row.id}">Edit</button>`;
                            }

                            // Check if the user has permission to delete the unit
                            if (canDeleteUnit) {
                                actionButtons +=
                                    `<button class="btn btn-danger delete-unit" data-id="${row.id}">Delete</button>`;
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
            $('#filter-id, #filter-unit-name, #filter-created-at, #filter-updated-at, #filter-created-by, #filter-updated-by')
                .on('keyup change', function() {
                    table.draw();
                });

            // Add Unit button click
            $('#addUnitBtn').click(function() {
                $('#unitForm')[0].reset();
                $('#unit-id').val('');
                $('#unitModal').modal('show');
                $('#saveUnitBtn').attr('disabled', true); // Ensure Save button is disabled initially
                $('#unit_name_error').text(''); // Clear error messages
            });

            // Enable/Disable Save button based on unit_name input
            $('#unit_name').on('input', function() {
                var unitNameValue = $(this).val().trim();
                if (unitNameValue.length > 0) {
                    $('#saveUnitBtn').attr('disabled', false); // Enable button when input has value
                } else {
                    $('#saveUnitBtn').attr('disabled', true); // Disable button when input is empty
                }
            });

            // Edit Unit button click
            $('body').on('click', '.edit-unit', function() {
                var id = $(this).data('id');
                $.get('/units/' + id + '/edit', function(data) {
                    $('#unit-id').val(data.id);
                    $('#unit_name').val(data.unit_name);
                    $('#unitModal').modal('show');
                    $('#saveUnitBtn').attr('disabled', false); // Enable Save button during edit
                    $('#unit_name_error').text(''); // Clear error messages
                });
            });

            // Handle Form Submission for Add/Edit
            $('#unitForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var method = $('#unit-id').val() ? 'PUT' : 'POST';
                var url = method === 'POST' ? "{{ route('units.store') }}" : '/units/' + $('#unit-id')
                    .val();

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
                            if (errors.unit_name) {
                                $('#unit_name_error').text(errors.unit_name[
                                    0]); // Display error for unit name
                            }
                        } else {
                            // General error message
                            $('#unit_name_error').text('An unexpected error occurred.');

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
            var unitIdToDelete = null;
            $('body').on('click', '.delete-unit', function() {
                unitIdToDelete = $(this).data('id');
                $('#deleteUnitModal').modal('show');
            });

            // Confirm Delete
            $('#confirmDeleteUnit').click(function() {
                $.ajax({
                    type: 'DELETE',
                    url: '/units/' + unitIdToDelete,
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

            const filterButton = document.getElementById('apply-filter');

            // Select all the filter input elements
            const filters = {
                id: document.getElementById('filter-id'),
                unit_name: document.getElementById('filter-unit-name'),
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
                        queryString += `${key}=${value}&`;
                    }
                }

                // Redirect the page with the updated filters in the query string

                window.open('/export/units' + queryString.slice(0, -1), '_blank');
            });
        });
    </script>

@stop
