@extends('adminlte::page')

@section('title', 'Units')

@section('content_header')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">

    <h1>Units</h1>
@stop

@section('content')
    <!-- Add Unit Button -->
    <a href="javascript:void(0)" class="btn btn-success" id="addUnitBtn">Add Unit</a>

    <!-- DataTable for Units -->
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
            <tr>
                <th><input type="text" id="filter-id" class="form-control" placeholder="ID"></th>
                <th><input type="text" id="filter-unit-name" class="form-control" placeholder="Unit Name"></th>
                <th><input type="date" id="filter-created-at" class="form-control"></th>
                <th><input type="date" id="filter-updated-at" class="form-control"></th>
                <th>
                    <select id="filter-created-by" class="form-control">
                        <option value="">Select Creator</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </th>
                <th>
                    <select id="filter-updated-by" class="form-control">
                        <option value="">Select Updater</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </th>
                <th></th>
            </tr>
        </thead>
    </table>

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
                        <div class="mb-3">
                            <label for="unit_name" class="form-label">Unit Name</label>
                            <input type="text" class="form-control" id="unit_name" name="unit_name" required
                                maxlength="50">
                            <div id="unit_name_error" class="text-danger"></div> <!-- Error message for unit name -->
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
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 11;">
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


@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(function() {
            var table = $('#units-table').DataTable({
                processing: true,
                serverSide: true,
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
                            return `
                            <button class="btn btn-primary edit-unit" data-id="${row.id}">Edit</button>
                            <button class="btn btn-danger delete-unit" data-id="${row.id}">Delete</button>
                        `;
                        }
                    }
                ]
            });

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
        });
    </script>

@stop
