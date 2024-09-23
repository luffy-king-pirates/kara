@extends('adminlte::page')

@section('title', 'Customers')

@section('content_header')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">

    <h1>Customers</h1>
@stop

@section('content')
    <!-- Add Customer Button -->
    <a href="javascript:void(0)" class="btn btn-success" id="addCustomerBtn">Add Customer</a>
    <button id="apply-filter" class="btn btn-success">Export Result in Excel</button>
    <!-- DataTable for Customers -->
    <table class="table table-bordered" id="customers-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Tin Number</th>
                <th>Vrn Number</th>
                <th>Location</th>
                <th>Address</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>Is Active</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>

    <!-- Modal for Add/Edit Customer -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalLabel">Add Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="customerForm">
                        @csrf
                        <input type="hidden" name="customer_id" id="customer-id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="customer_name" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" required
                                    maxlength="100">
                                <div id="customer_name_error" class="text-danger"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_tin" class="form-label">Customer Tin Number</label>
                                <input type="text" class="form-control" id="customer_tin" name="customer_tin" required
                                    maxlength="50">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_vrn" class="form-label">Customer Vrn Number</label>
                                <input type="text" class="form-control" id="customer_vrn" name="customer_vrn" required
                                    maxlength="50">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_location" class="form-label">Customer Location</label>
                                <input type="text" class="form-control" id="customer_location" name="customer_location"
                                    required maxlength="150">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_address" class="form-label">Customer Address</label>
                                <input type="text" class="form-control" id="customer_address" name="customer_address"
                                    required maxlength="200">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_mobile" class="form-label">Customer Mobile</label>
                                <input type="text" class="form-control" id="customer_mobile" name="customer_mobile"
                                    required maxlength="15">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_email" class="form-label">Customer Email</label>
                                <input type="email" class="form-control" id="customer_email" name="customer_email"
                                    required>
                                <div id="customer_email_error" class="text-danger"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="is_active" class="form-label">Is Active</label>
                                <select class="form-control" id="is_active" name="is_active" required>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" id="saveCustomerBtn" class="btn btn-primary" disabled>Save changes</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal for Delete Confirmation -->
    <div class="modal fade" id="deleteCustomerModal" tabindex="-1" aria-labelledby="deleteCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCustomerModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this customer?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteCustomer">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toasts for Success/Error Messages -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
        <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">Customer saved successfully!</div>
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
            var table = $('#customers-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('customers.index') }}",
                    data: function(d) {
                        d.customer_name = $('#filter-customer-name').val();
                        d.customer_tin = $('#filter-customer-tin').val();
                        d.customer_vrn = $('#filter-customer-vrn').val();
                        d.customer_location = $('#filter-customer-location').val();
                        d.customer_address = $('#filter-customer-address').val();
                        d.customer_mobile = $('#filter-customer-mobile').val();
                        d.customer_email = $('#filter-customer-email').val();
                        d.is_active = $('#filter-is-active').val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name',
                        render: function(data) {
                            return data ? data : '<span class="text-danger">Not Set</span>';
                        }
                    },
                    {
                        data: 'customer_tin',
                        name: 'customer_tin',
                        render: function(data) {
                            return data ? data : '<span class="text-danger">Not Set</span>';
                        }
                    },
                    {
                        data: 'customer_vrn',
                        name: 'customer_vrn',
                        render: function(data) {
                            return data ? data : '<span class="text-danger">Not Set</span>';
                        }
                    },
                    {
                        data: 'customer_location',
                        name: 'customer_location',
                        render: function(data) {
                            return data ? data : '<span class="text-danger">Not Set</span>';
                        }
                    },
                    {
                        data: 'customer_address',
                        name: 'customer_address',
                        render: function(data) {
                            return data ? data : '<span class="text-danger">Not Set</span>';
                        }
                    },
                    {
                        data: 'customer_mobile',
                        name: 'customer_mobile',
                        render: function(data) {
                            return data ? data : '<span class="text-danger">Not Set</span>';
                        }
                    },
                    {
                        data: 'customer_email',
                        name: 'customer_email',
                        render: function(data) {
                            return data ? data : '<span class="text-danger">Not Set</span>';
                        }
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        render: function(data) {
                            if (data) {
                                return '<i class="fa fa-check-circle text-success"></i>'; // Green checkbox for "Yes"
                            } else {
                                return '<i class="fa fa-times-circle text-danger"></i>'; // Red checkbox for "No"
                            }
                        }
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
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-primary edit-customers" data-id="${row.id}">Edit</button>
                                <button class="btn btn-danger delete-customers" data-id="${row.id}">Delete</button>
                            `;
                        }
                    }
                ]
            });

            // Add Customer button click
            $('#addCustomerBtn').click(function() {
                $('#customerForm')[0].reset();
                $('#customer-id').val('');
                $('#customerModal').modal('show');
                $('#saveCustomerBtn').attr('disabled', false);
            });

            // Form submission
            $('#customerForm').submit(function(e) {


                  e.preventDefault();
                var formData = $(this).serialize();
                var url = $('#customer-id').val() ? `/customers/${$('#customer-id').val()}` : "{{ route('customers.store') }}";
                var method = $('#customer-id').val() ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    success: function(response) {
                        $('#customerModal').modal('hide');
                        table.draw();
                        showSuccessToast('Customer saved successfully!');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var error = xhr.responseJSON.errors.customer_name[0];
                            $('#customer_name_error').text(error);
                        } else {
                            showErrorToast('An error occurred while saving the customer.');
                        }
                    }
                });
            });
            //edit customers
            $('body').on('click', '.edit-customers', function() {
            var customerId = $(this).data('id');

            // Send AJAX request to fetch the customer data
            $.get(`/customers/${customerId}/edit`, function(data) {
                // Populate the modal fields with the fetched customer data
                $('#customer-id').val(data.id);
                $('#customer_name').val(data.customer_name);
                $('#customer_tin').val(data.customer_tin);
                $('#customer_vrn').val(data.customer_vrn);
                $('#customer_location').val(data.customer_location);
                $('#customer_address').val(data.customer_address);
                $('#customer_mobile').val(data.customer_mobile);
                $('#customer_email').val(data.customer_email);
                $('#is_active').val(data.is_active ? '1' : '0');

                // Show the modal
                $('#customerModal').modal('show');
                $('#saveCustomerBtn').attr('disabled', false);
            });
        });


            // Delete Month functionality
            $('body').on('click', '.delete-customers', function() {
                var id = $(this).data('id');
                $('#deleteCustomerModal').modal('show');

                $('#confirmDeleteCustomer').off().click(function() {
                    $.ajax({
                        url: `/customers/${id}`,
                         data: { _token: "{{ csrf_token() }}" }, // Ensure CSRF token is included
                        type: 'DELETE',
                        success: function(response) {
                            $('#deleteCustomerModal').modal('hide');
                            table.draw();
                            showSuccessToast('Customer deleted successfully!');
                        },
                        error: function() {
                            showErrorToast('An error occurred while deleting customer.');
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
                   filterButton.addEventListener('click', function() {
                       window.open('/export/customers' ,
                '_blank');
                   })

        });
    </script>
@stop
