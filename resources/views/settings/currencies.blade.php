@extends('adminlte::page')

@section('title', 'Currencies')

@section('content_header')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">

    <h1>Currencies</h1>
@stop

@section('content')
    <!-- Add Currency Button -->
    <a href="javascript:void(0)" class="btn btn-success" id="addCurrencyBtn">Add Currency</a>
    <button id="apply-filter" class="btn btn-success">Export Result in Excel</button>

    <!-- DataTable for Currencies -->
    <table class="table table-bordered" id="currencies-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Currency Name</th>
                <th>Currency Value</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th>Action</th>
            </tr>
            <tr>
                <th><input type="text" id="filter-id" class="form-control" placeholder="ID"></th>
                <th><input type="text" id="filter-currencie-name" class="form-control" placeholder="Currency Name"></th>
                <th><input type="number" id="filter-currencie-value" class="form-control" placeholder="Currency Value">
                </th>
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

    <!-- Modal for Add/Edit Currency -->
    <div class="modal fade" id="currencyModal" tabindex="-1" aria-labelledby="currencyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="currencyModalLabel">Add Currency</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="currencyForm">
                        @csrf
                        <input type="hidden" name="currency_id" id="currency-id">
                        <div class="mb-3">
                            <label for="currencie_name" class="form-label">Currency Name</label>
                            <input type="text" class="form-control" id="currencie_name" name="currencie_name" required
                                maxlength="50">
                            <div id="currencie_name_error" class="text-danger"></div>
                            <!-- Error message for currency name -->
                        </div>
                        <div class="mb-3">
                            <label for="currencie_value" class="form-label">Currency Value</label>
                            <input type="number" class="form-control" id="currencie_value" name="currencie_value" required>
                            <div id="currencie_value_error" class="text-danger"></div>
                            <!-- Error message for currency value -->
                        </div>
                        <button type="submit" id="saveCurrencyBtn" class="btn btn-primary" disabled>Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Delete Confirmation -->
    <div class="modal fade" id="deleteCurrencyModal" tabindex="-1" aria-labelledby="deleteCurrencyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCurrencyModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this currency?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteCurrency">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toasts for Success/Error Messages -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
        <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">Currency saved successfully!</div>
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
            var table = $('#currencies-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('currencies.index') }}",
                    data: function(d) {
                        d.currencie_name = $('#filter-currencie-name').val();
                        d.currencie_value = $('#filter-currencie-value').val();
                        d.created_at = $('#filter-created-at').val();
                        d.updated_at = $('#filter-updated-at').val();
                        d.created_by = $('#filter-created-by').val();
                        d.updated_by = $('#filter-updated-by').val();
                    }
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'currencie_name'
                    },
                    {
                        data: 'currencie_value'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'updated_at'
                    },
                    {
                        data: 'created_by'
                    },
                    {
                        data: 'updated_by'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-primary edit-currency" data-id="${row.id}">Edit</button>
                                <button class="btn btn-danger delete-currency" data-id="${row.id}">Delete</button>
                            `;
                        }
                    }
                ]
            });

            // Filter functionality
            $('#filter-id, #filter-currencie-name, #filter-currencie-value, #filter-created-at, #filter-updated-at, #filter-created-by, #filter-updated-by')
                .on('keyup change', function() {
                    table.draw();
                });

            // Add Currency button click
            $('#addCurrencyBtn').click(function() {
                $('#currencyForm')[0].reset();
                $('#currency-id').val('');
                $('#currencyModal').modal('show');
                $('#saveCurrencyBtn').attr('disabled', true); // Ensure Save button is disabled initially
                $('#currencie_name_error, #currencie_value_error').text(''); // Clear error messages
            });

            // Enable/Disable Save button based on inputs
            $('#currencie_name, #currencie_value').on('input', function() {
                var currencyNameValue = $('#currencie_name').val().trim();
                var currencyValueValue = $('#currencie_value').val().trim();
                $('#saveCurrencyBtn').attr('disabled', !(currencyNameValue &&
                currencyValueValue)); // Enable button only if both fields have values
            });

            // Edit Currency button click
            $('body').on('click', '.edit-currency', function() {
                var id = $(this).data('id');
                $.get('/currencies/' + id + '/edit', function(data) {
                    $('#currency-id').val(data.id);
                    $('#currencie_name').val(data.currencie_name);
                    $('#currencie_value').val(data.currencie_value);
                    $('#currencyModal').modal('show');
                    $('#saveCurrencyBtn').attr('disabled', false); // Enable Save button during edit
                    $('#currencie_name_error, #currencie_value_error').text(
                    ''); // Clear error messages
                });
            });

            // Handle Form Submission for Add/Edit
            $('#currencyForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var method = $('#currency-id').val() ? 'PUT' : 'POST';
                var url = method === 'POST' ? "{{ route('currencies.store') }}" : '/currencies/' + $(
                    '#currency-id').val();

                $.ajax({
                    type: method,
                    url: url,
                    data: formData,
                    success: function(response) {
                        $('#currencyModal').modal('hide'); // Hide the modal
                        table.ajax.reload();

                        // Show success toast
                        var successToast = new bootstrap.Toast(document.getElementById(
                            'successToast'));
                        successToast.show();
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            if (errors.currencie_name) {
                                $('#currencie_name_error').text(errors.currencie_name[
                                0]); // Display error for currency name
                            }
                            if (errors.currencie_value) {
                                $('#currencie_value_error').text(errors.currencie_value[
                                0]); // Display error for currency value
                            }
                        } else {
                            // General error message
                            $('#currencie_name_error, #currencie_value_error').text(
                                'An unexpected error occurred.');

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
            var currencyIdToDelete = null;
            $('body').on('click', '.delete-currency', function() {
                currencyIdToDelete = $(this).data('id');
                $('#deleteCurrencyModal').modal('show');
            });

            // Confirm Delete
            $('#confirmDeleteCurrency').click(function() {
                $.ajax({
                    type: 'DELETE',
                    url: '/currencies/' + currencyIdToDelete,
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#deleteCurrencyModal').modal('hide');
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
                currencie_name: document.getElementById('filter-currencie-name'), // Currency Name field
                currencie_value: document.getElementById('filter-currencie-value'), // Currency Value field
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
                        queryString +=
                        `${key}=${encodeURIComponent(value)}&`; // encodeURIComponent to handle special characters
                    }
                }

                // Redirect the page with the updated filters in the query string
                window.open('/export/currencies' + queryString.slice(0, -1),
                '_blank'); // Update the URL to '/export/currencies'
            });

        });
    </script>
@stop
