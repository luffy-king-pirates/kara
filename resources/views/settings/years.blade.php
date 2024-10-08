@extends('adminlte::page')

@section('title', 'Years')

@section('content_header')

    <h1>Years</h1>
@stop

@section('content')
    <div style="height: 700px; overflow-y: auto;">
        @include('partials.expiration.expire')
        <!-- Add Year Button -->
        @can('create-year')
            <a href="javascript:void(0)" class="btn btn-success" id="addYearBtn">Add Year</a>
        @endcan
        @can('export-year')
            <button id="apply-filter" class="btn btn-success">Export Result in Excel</button>
        @endcan
        @include('partials.filter-years', ['users' => $users])
        <!-- DataTable for Years -->
        @can('read-year')
            <table class="table table-bordered" id="years-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Year</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Created By</th>
                        <th>Updated By</th>
                        <th>Action</th>
                    </tr>

                </thead>
            </table>
        @endcan

        <!-- Modal for Add/Edit Year -->
        <div class="modal fade" id="yearModal" tabindex="-1" aria-labelledby="yearModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="yearModalLabel">Add Year</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="yearForm">
                            @csrf
                            <input type="hidden" name="year_id" id="year-id">
                            <div class="mb-3 position-relative">
                                <label for="year_name" class="form-label">Year <span class="text-danger">*</span></label>

                                <!-- Input field with required attribute -->
                                <input type="number" class="form-control" id="year_name" name="year_name" required
                                    placeholder="Enter the year">

                                <!-- Error message for year -->
                                <div id="year_name_error" class="text-danger"></div>
                            </div>

                            <button type="submit" id="saveYearBtn" class="btn btn-primary" disabled>Save changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Delete Confirmation -->
        <div class="modal fade" id="deleteYearModal" tabindex="-1" aria-labelledby="deleteYearModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteYearModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this year?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteYear">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toasts for Success/Error Messages -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
            <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">Year saved successfully!</div>
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
        var canEditYear = @json($canEditYear);
        var canDeleteYear = @json($canDeleteYear);
    </script>
    <script>
        $(function() {
            var table = $('#years-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('years.index') }}",
                    data: function(d) {
                        d.year_name = $('#filter-year-name').val();
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
                        data: 'year_name',
                        name: 'year_name'
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

                            if (canEditYear) {
                                actionButtons +=
                                    `<button class="btn btn-primary edit-year" data-id="${row.id}">Edit</button>`;
                            }

                            if (canDeleteYear) {
                                actionButtons +=
                                    `<button class="btn btn-danger delete-year" data-id="${row.id}">Delete</button>`;
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
            $('#filter-id, #filter-year-name, #filter-created-at, #filter-updated-at, #filter-created-by, #filter-updated-by')
                .on('keyup change', function() {
                    table.draw();
                });

            // Add Year button click
            $('#addYearBtn').click(function() {
                $('#yearForm')[0].reset();
                $('#year-id').val('');
                $('#yearModal').modal('show');
                $('#saveYearBtn').attr('disabled', true); // Ensure Save button is disabled initially
                $('#year_name_error').text(''); // Clear error messages
            });

            // Enable/Disable Save button based on year_name input
            $('#year_name').on('input', function() {
                var yearNameValue = $(this).val().trim();
                $('#saveYearBtn').attr('disabled', yearNameValue.length ===
                    0); // Enable/Disable based on input
            });

            // Edit Year button click
            $('body').on('click', '.edit-year', function() {
                var id = $(this).data('id');
                $.get('/years/' + id + '/edit', function(data) {
                    $('#year-id').val(data.id);
                    $('#year_name').val(data.year_name);
                    $('#yearModal').modal('show');
                    $('#saveYearBtn').attr('disabled', false); // Enable Save button during edit
                    $('#year_name_error').text(''); // Clear error messages
                });
            });

            // Handle Form Submission for Add/Edit
            $('#yearForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var method = $('#year-id').val() ? 'PUT' : 'POST';
                var url = method === 'POST' ? "{{ route('years.store') }}" : '/years/' + $('#year-id')
                    .val();

                $.ajax({
                    type: method,
                    url: url,
                    data: formData,
                    success: function(response) {
                        $('#yearModal').modal('hide'); // Hide the modal
                        table.ajax.reload();

                        // Show success toast
                        var successToast = new bootstrap.Toast(document.getElementById(
                            'successToast'));
                        successToast.show();
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            if (errors.year_name) {
                                $('#year_name_error').text(errors.year_name[
                                    0]); // Display error for year
                            }
                        } else {
                            // General error message
                            $('#year_name_error').text('An unexpected error occurred.');

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
            var yearIdToDelete = null;
            $('body').on('click', '.delete-year', function() {
                yearIdToDelete = $(this).data('id');
                $('#deleteYearModal').modal('show');
            });

            // Confirm Delete
            $('#confirmDeleteYear').click(function() {
                $.ajax({
                    type: 'DELETE',
                    url: '/years/' + yearIdToDelete,
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#deleteYearModal').modal('hide');
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
                year_name: document.getElementById(
                    'filter-year-name'), // Updated from 'unit_name' to 'year_name'
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
                window.open('/export/years' + queryString.slice(0, -1),
                    '_blank'); // Update the URL to '/export/years'
            });

        });
    </script>
@stop
