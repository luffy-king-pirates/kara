@extends('adminlte::page')

@section('title', 'Countries')

@section('content_header')
    <h1>Countries</h1>
@stop

@section('content')
    <!-- Add Country Button -->
    @can('create-country')
        <a href="javascript:void(0)" class="btn btn-success" id="addCountryBtn">Add Country</a>
    @endcan
    @can('export-country')
        <button id="apply-filter" class="btn btn-success">Export Result in Excel</button>
    @endcan

    @include('partials.filter-countries', ['users' => $users])
    @can('read-country')
        <!-- DataTable for Countries -->
        <table class="table table-bordered" id="countries-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Country Name</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Created By</th>
                    <th>Updated By</th>
                    <th>Action</th>
                </tr>

            </thead>
        </table>
    @endcan
    <!-- Modal for Add/Edit Country -->
    <div class="modal fade" id="countryModal" tabindex="-1" aria-labelledby="countryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="countryModalLabel">Add Country</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="countryForm">
                        @csrf
                        <input type="hidden" name="country_id" id="country-id">
                        <div class="mb-3 position-relative">
                            <label for="country_name" class="form-label">Country Name <span
                                    class="text-danger">*</span></label>

                            <!-- Input field with required attribute -->
                            <input type="text" class="form-control" id="country_name" name="country_name" required
                                maxlength="50" placeholder="Enter the country name">

                            <!-- Error message for country name -->
                            <div id="country_name_error" class="text-danger"></div>
                        </div>
                        <button type="submit" id="saveCountryBtn" class="btn btn-primary" disabled>Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Delete Confirmation -->
    <div class="modal fade" id="deleteCountryModal" tabindex="-1" aria-labelledby="deleteCountryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCountryModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this country?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteCountry">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toasts for Success/Error Messages -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
        <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">Country saved successfully!</div>
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
        var canEditCountry = @json($canEditCountry);
        var canDeleteCountry = @json($canDeleteCountry);
    </script>
    <script>
        $(function() {
            var table = $('#countries-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('countries.index') }}",
                    data: function(d) {
                        d.country_name = $('#filter-country-name').val();
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
                        data: 'country_name',
                        name: 'country_name'
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

                            // Check if user has permission to edit the country
                            if (canEditCountry) {
                                actionButtons +=
                                    `<button class="btn btn-primary edit-country" data-id="${row.id}">Edit</button>`;
                            }

                            // Check if user has permission to delete the country
                            if (canDeleteCountry) {
                                actionButtons +=
                                    `<button class="btn btn-danger delete-country" data-id="${row.id}">Delete</button>`;
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
            $('#filter-id, #filter-country-name, #filter-created-at, #filter-updated-at, #filter-created-by, #filter-updated-by')
                .on('keyup change', function() {
                    table.draw();
                });

            // Add Country button click
            $('#addCountryBtn').click(function() {
                $('#countryForm')[0].reset();
                $('#country-id').val('');
                $('#countryModal').modal('show');
                $('#saveCountryBtn').attr('disabled', true); // Ensure Save button is disabled initially
                $('#country_name_error').text(''); // Clear error messages
            });

            // Enable/Disable Save button based on country_name input
            $('#country_name').on('input', function() {
                var countryNameValue = $(this).val().trim();
                if (countryNameValue.length > 0) {
                    $('#saveCountryBtn').attr('disabled', false); // Enable button when input has value
                } else {
                    $('#saveCountryBtn').attr('disabled', true); // Disable button when input is empty
                }
            });

            // Edit Country button click
            $('body').on('click', '.edit-country', function() {
                var id = $(this).data('id');
                $.get('/countries/' + id + '/edit', function(data) {
                    $('#country-id').val(data.id);
                    $('#country_name').val(data.country_name);
                    $('#countryModal').modal('show');
                    $('#saveCountryBtn').attr('disabled', false); // Enable Save button during edit
                    $('#country_name_error').text(''); // Clear error messages
                });
            });

            // Handle Form Submission for Add/Edit
            $('#countryForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var method = $('#country-id').val() ? 'PUT' : 'POST';
                var url = method === 'POST' ? "{{ route('countries.store') }}" : '/countries/' + $(
                    '#country-id').val();

                $.ajax({
                    type: method,
                    url: url,
                    data: formData,
                    success: function(response) {
                        $('#countryModal').modal('hide'); // Hide the modal
                        table.ajax.reload();

                        // Show success toast
                        var successToast = new bootstrap.Toast(document.getElementById(
                            'successToast'));
                        successToast.show();
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            if (errors.country_name) {
                                $('#country_name_error').text(errors.country_name[
                                    0]); // Display error for country name
                            }
                        } else {
                            // General error message
                            $('#country_name_error').text('An unexpected error occurred.');

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
            var countryIdToDelete = null;
            $('body').on('click', '.delete-country', function() {
                countryIdToDelete = $(this).data('id');
                $('#deleteCountryModal').modal('show');
            });

            // Confirm Delete
            $('#confirmDeleteCountry').click(function() {
                $.ajax({
                    type: 'DELETE',
                    url: '/countries/' + countryIdToDelete,
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#deleteCountryModal').modal('hide');
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
                country_name: document.getElementById('filter-country-name'),
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
                window.open('/export/countries' + queryString.slice(0, -1),
                    '_blank'); // Update the URL to '/export/countries'
            });

        });
    </script>
@stop
