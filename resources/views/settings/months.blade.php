@extends('adminlte::page')

@section('title', 'Months')

@section('content_header')

    <h1>Months</h1>
@stop

@section('content')
    <div style="height: 700px; overflow-y: auto;">
        @include('partials.expiration.expire')
        <!-- Add Month Button -->
        @can('create-month')
            <a href="javascript:void(0)" class="btn btn-success" id="addMonthBtn">Add Month</a>
        @endcan
        @can('export-month')
            <button id="apply-filter" class="btn btn-success">Export Result in Excel</button>
        @endcan

        @include('partials.filter-months', ['users' => $users])
        @can('read-month')
            <!-- DataTable for Months -->
            <table class="table table-bordered" id="months-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Month Name</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Created By</th>
                        <th>Updated By</th>
                        <th>Action</th>
                    </tr>

                </thead>
            </table>
        @endcan
        <!-- Modal for Add/Edit Month -->
        <div class="modal fade" id="monthModal" tabindex="-1" aria-labelledby="monthModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="monthModalLabel">Add Month</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="monthForm">
                            @csrf
                            <input type="hidden" name="month_id" id="month-id">
                            <div class="mb-3 position-relative">
                                <label for="month_name" class="form-label">Month Name <span
                                        class="text-danger">*</span></label>

                                <!-- Input field with required attribute -->
                                <input type="text" class="form-control" id="month_name" name="month_name" required
                                    maxlength="50" placeholder="Enter the month name">

                                <!-- Error message for month name -->
                                <div id="month_name_error" class="text-danger"></div>
                            </div>
                            <button type="submit" id="saveMonthBtn" class="btn btn-primary" disabled>Save changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Delete Confirmation -->
        <div class="modal fade" id="deleteMonthModal" tabindex="-1" aria-labelledby="deleteMonthModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteMonthModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this month?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteMonth">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toasts for Success/Error Messages -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
            <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">Month saved successfully!</div>
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
        var canEditMonth = @json($canEditMonth);
        var canDeleteMonth = @json($canDeleteMonth);
    </script>
    <script>
        $(function() {
            var table = $('#months-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('months.index') }}", // Ensure the correct route is used for data loading
                    data: function(d) {
                        d.month_name = $('#filter-month-name').val();
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
                        data: 'month_name',
                        name: 'month_name'
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

                            // Check if the user has permission to edit the month
                            if (canEditMonth) {
                                actionButtons +=
                                    `<button class="btn btn-primary edit-month" data-id="${row.id}">Edit</button>`;
                            }

                            // Check if the user has permission to delete the month
                            if (canDeleteMonth) {
                                actionButtons +=
                                    `<button class="btn btn-danger delete-month" data-id="${row.id}">Delete</button>`;
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
            $('#filter-id, #filter-month-name, #filter-created-at, #filter-updated-at, #filter-created-by, #filter-updated-by')
                .on('keyup change', function() {
                    table.draw();
                });

            // Add Month button click
            $('#addMonthBtn').click(function() {
                $('#monthForm')[0].reset();
                $('#month-id').val('');
                $('#monthModal').modal('show');
                $('#saveMonthBtn').attr('disabled', true);
                $('#month_name_error').text('');
            });

            // Enable/Disable Save button based on month_name input
            $('#month_name').on('change', function() {
                var monthNameValue = $(this).val();
                if (monthNameValue) {
                    $('#saveMonthBtn').attr('disabled', false);
                } else {
                    $('#saveMonthBtn').attr('disabled', true);
                }
            });

            // Edit Month button click
            $('body').on('click', '.edit-month', function() {
                var id = $(this).data('id');
                $.get(`/months/${id}/edit`, function(data) {
                    $('#month-id').val(data.id);
                    $('#month_name').val(data.month_name);
                    $('#monthModal').modal('show');
                    $('#saveMonthBtn').attr('disabled', false);
                });
            });

            // Save Month functionality
            $('#monthForm').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var url = $('#month-id').val() ? `/months/${$('#month-id').val()}` :
                    "{{ route('months.store') }}";
                var method = $('#month-id').val() ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    success: function(response) {
                        $('#monthModal').modal('hide');
                        table.draw();
                        showSuccessToast('Month saved successfully!');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var error = xhr.responseJSON.errors.month_name[0];
                            $('#month_name_error').text(error);
                        } else {
                            showErrorToast('An error occurred while saving the month.');
                        }
                    }
                });
            });

            // Delete Month functionality
            $('body').on('click', '.delete-month', function() {
                var id = $(this).data('id');
                $('#deleteMonthModal').modal('show');

                $('#confirmDeleteMonth').off().click(function() {
                    $.ajax({
                        url: `/months/${id}`,
                        data: {
                            _token: "{{ csrf_token() }}"
                        }, // Ensure CSRF token is included
                        type: 'DELETE',
                        success: function(response) {
                            $('#deleteMonthModal').modal('hide');
                            table.draw();
                            showSuccessToast('Month deleted successfully!');
                        },
                        error: function() {
                            showErrorToast(
                                'An error occurred while deleting the month.');
                        }
                    });
                });
            });

            // Success toast function
            function showSuccessToast(message) {
                $('#successToast .toast-body').text(message);
                $('#successToast').toast('show');
            }

            // Error toast function
            function showErrorToast(message) {
                $('#errorToastMessage').text(message);
                $('#errorToast').toast('show');
            }

            const filterButton = document.getElementById('apply-filter');

            // Select all the filter input elements
            const filters = {
                id: document.getElementById('filter-id'),
                month_name: document.getElementById('filter-month-name'),
                created_at: document.getElementById('filter-created-at'),
                updated_at: document.getElementById('filter-updated-at'),
                created_by: document.getElementById('filter-created-by'),
            };

            // Add event listener to the filter button
            filterButton?.addEventListener('click', function() {
                // Build the query string from the filter inputs
                let queryString = '?';

                for (let key in filters) {
                    const value = filters[key].value;
                    if (value) {
                        queryString +=
                            `${key}=${encodeURIComponent(value)}&`; // Encode value for URL safety
                    }
                }

                // Redirect the page with the updated filters in the query string
                window.open('/export/months' + queryString.slice(0, -1),
                    '_blank'); // Adjust URL for your endpoint
            });

        });
    </script>
@stop
