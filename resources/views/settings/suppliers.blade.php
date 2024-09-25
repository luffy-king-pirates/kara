@extends('adminlte::page')

@section('title', 'Suppliers')

@section('content_header')
    <h1>Suppliers</h1>

@stop

@section('content')
    <div style="height: 700px; overflow-y: auto;">
        <!-- Add Supplier Button -->
        @can('create-supplier')
            <a href="javascript:void(0)" class="btn btn-success" id="addSupplierBtn">Add Supplier</a>
        @endcan
        @can('export-supplier')
            <button id="apply-filter" class="btn btn-success">Export Result in Excel</button>
        @endcan

        <!-- DataTable for Suppliers -->
        @include('partials.filter-suppliers', ['users' => $users])
        @can('read-supplier')
            <table class="table table-bordered" id="suppliers-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Supplier Name</th>
                        <th>Supplier Location</th>
                        <th>Supplier Contact</th>
                        <th>Supplier Reference</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Created By</th>
                        <th>Updated By</th>
                        <th>Action</th>
                    </tr>

                </thead>
            </table>
        @endcan
        <!-- Modal for Add/Edit Supplier -->
        <div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="supplierModalLabel">Add Supplier</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="supplierForm">
                            @csrf
                            <input type="hidden" name="supplier_id" id="supplier-id">
                            <div class="mb-3 position-relative">
                                <label for="supplier_name" class="form-label">Supplier Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="supplier_name" name="supplier_name" required
                                    maxlength="50" placeholder="Enter supplier name">
                                <div id="supplier_name_error" class="text-danger"></div>
                            </div>

                            <div class="mb-3 position-relative">
                                <label for="supplier_location" class="form-label">Supplier Location <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="supplier_location" name="supplier_location"
                                    required maxlength="50" placeholder="Enter supplier location">
                                <div id="supplier_location_error" class="text-danger"></div>
                            </div>

                            <div class="mb-3 position-relative">
                                <label for="supplier_contact" class="form-label">Supplier Contact <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="supplier_contact" name="supplier_contact"
                                    required maxlength="50" placeholder="Enter supplier contact">
                                <div id="supplier_contact_error" class="text-danger"></div>
                            </div>

                            <div class="mb-3 position-relative">
                                <label for="supplier_reference" class="form-label">Supplier Reference <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="supplier_reference" name="supplier_reference"
                                    required maxlength="50" placeholder="Enter supplier reference">
                                <div id="supplier_reference_error" class="text-danger"></div>
                            </div>

                            <button type="submit" id="saveSupplierBtn" class="btn btn-primary" disabled>Save
                                changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Delete Confirmation -->
        <div class="modal fade" id="deleteSupplierModal" tabindex="-1" aria-labelledby="deleteSupplierModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteSupplierModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this supplier?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteSupplier">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toasts for Success/Error Messages -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
            <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">Supplier saved successfully!</div>
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
        var canEditSupplier = @json($canEditSupplier);
        var canDeleteSupplier = @json($canDeleteSupplier);
    </script>
    <script>
        $(function() {
            var table = $('#suppliers-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('suppliers.index') }}",
                    data: function(d) {
                        d.supplier_name = $('#filter-supplier-name').val();
                        d.supplier_location = $('#filter-supplier-location').val();
                        d.supplier_contact = $('#filter-supplier-contact').val();
                        d.supplier_reference = $('#filter-supplier-reference').val();
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
                        data: 'supplier_name',
                        name: 'supplier_name'
                    },
                    {
                        data: 'supplier_location',
                        name: 'supplier_location'
                    },
                    {
                        data: 'supplier_contact',
                        name: 'supplier_contact'
                    },
                    {
                        data: 'supplier_reference',
                        name: 'supplier_reference'
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

                            // Check if the user has permission to edit the supplier
                            if (canEditSupplier) {
                                actionButtons +=
                                    `<button class="btn btn-primary edit-supplier" data-id="${row.id}">Edit</button>`;
                            }

                            // Check if the user has permission to delete the supplier
                            if (canDeleteSupplier) {
                                actionButtons +=
                                    `<button class="btn btn-danger delete-supplier" data-id="${row.id}">Delete</button>`;
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
            $('#filter-id, #filter-supplier-name, #filter-supplier-location, #filter-supplier-contact, #filter-supplier-reference, #filter-created-at, #filter-updated-at, #filter-created-by, #filter-updated-by')
                .on('keyup change', function() {
                    table.draw();
                });

            // Add Supplier button click
            $('#addSupplierBtn').click(function() {
                $('#supplierForm')[0].reset();
                $('#supplier-id').val('');
                $('#supplierModal').modal('show');
                $('#saveSupplierBtn').attr('disabled', true); // Ensure Save button is disabled initially
                $('#supplier_name_error').text(''); // Clear error messages
                $('#supplier_location_error').text(''); // Clear error messages
                $('#supplier_contact_error').text(''); // Clear error messages
                $('#supplier_reference_error').text(''); // Clear error messages
            });

            // Enable/Disable Save button based on supplier_name input
            $('#supplier_name, #supplier_location, #supplier_contact, #supplier_reference').on('input', function() {
                var supplierNameValue = $('#supplier_name').val().trim();
                var supplierLocationValue = $('#supplier_location').val().trim();
                var supplierContactValue = $('#supplier_contact').val().trim();
                var supplierReferenceValue = $('#supplier_reference').val().trim();
                if (supplierNameValue.length > 0 && supplierLocationValue.length > 0 && supplierContactValue
                    .length > 0 && supplierReferenceValue.length > 0) {
                    $('#saveSupplierBtn').attr('disabled', false); // Enable button when input has value
                } else {
                    $('#saveSupplierBtn').attr('disabled', true); // Disable button when input is empty
                }
            });

            // Edit Supplier button click
            $('body').on('click', '.edit-supplier', function() {
                var id = $(this).data('id');
                $.get('/suppliers/' + id + '/edit', function(data) {
                    $('#supplier-id').val(data.id);
                    $('#supplier_name').val(data.supplier_name);
                    $('#supplier_location').val(data.supplier_location);
                    $('#supplier_contact').val(data.supplier_contact);
                    $('#supplier_reference').val(data.supplier_reference);
                    $('#supplierModal').modal('show');
                    $('#saveSupplierBtn').attr('disabled', false); // Enable Save button during edit
                    $('#supplier_name_error').text(''); // Clear error messages
                    $('#supplier_location_error').text(''); // Clear error messages
                    $('#supplier_contact_error').text(''); // Clear error messages
                    $('#supplier_reference_error').text(''); // Clear error messages
                });
            });

            // Handle Form Submission for Add/Edit
            $('#supplierForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var method = $('#supplier-id').val() ? 'PUT' : 'POST';
                var url = method === 'POST' ? "{{ route('suppliers.store') }}" : '/suppliers/' + $(
                        '#supplier-id')
                    .val();

                $.ajax({
                    type: method,
                    url: url,
                    data: formData,
                    success: function(response) {
                        $('#supplierModal').modal('hide'); // Hide the modal
                        table.ajax.reload();

                        // Show success toast
                        var successToast = new bootstrap.Toast(document.getElementById(
                            'successToast'));
                        successToast.show();
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            if (errors.supplier_name) {
                                $('#supplier_name_error').text(errors.supplier_name[
                                    0]); // Display error for supplier name
                            }
                            if (errors.supplier_location) {
                                $('#supplier_location_error').text(errors.supplier_location[
                                    0]); // Display error for supplier location
                            }
                            if (errors.supplier_contact) {
                                $('#supplier_contact_error').text(errors.supplier_contact[
                                    0]); // Display error for supplier contact
                            }
                            if (errors.supplier_reference) {
                                $('#supplier_reference_error').text(errors.supplier_reference[
                                    0]); // Display error for supplier reference
                            }
                        } else {
                            // General error message
                            $('#supplier_name_error').text('An unexpected error occurred.');

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
            var supplierIdToDelete = null;
            $('body').on('click', '.delete-supplier', function() {
                supplierIdToDelete = $(this).data('id');
                $('#deleteSupplierModal').modal('show');
            });

            // Confirm Delete
            $('#confirmDeleteSupplier').click(function() {
                $.ajax({
                    type: 'DELETE',
                    url: '/suppliers/' + supplierIdToDelete,
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#deleteSupplierModal').modal('hide');
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
                supplier_name: document.getElementById('filter-supplier-name'),
                supplier_location: document.getElementById('filter-supplier-location'),
                supplier_contact: document.getElementById('filter-supplier-contact'),
                supplier_reference: document.getElementById('filter-supplier-reference'),
                created_at: document.getElementById('filter-created-at'),
                updated_at: document.getElementById('filter-updated-at'),
                created_by: document.getElementById('filter-created-by'),
                updated_by: document.getElementById('filter-updated-by')
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

                // Remove the last '&' and ensure the URL is correct for the export
                const finalQueryString = queryString.slice(0, -1);

                // Redirect the page with the updated filters in the query string
                window.open('/export/suppliers' + finalQueryString,
                    '_blank'); // Adjust URL for your export endpoint
            });

        });
    </script>

@stop
