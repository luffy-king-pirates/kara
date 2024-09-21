@extends('adminlte::page')

@section('title', 'Brands')

@section('content_header')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <h1>Brands</h1>
@stop

@section('content')
    <!-- Add Brand Button -->
    <a href="javascript:void(0)" class="btn btn-success" id="addBrandBtn">Add Brand</a>

    <!-- DataTable for Brands -->
    <table class="table table-bordered" id="brands-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Brand Name</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th>Action</th>
            </tr>
            <tr>
                <th><input type="text" id="filter-id" class="form-control" placeholder="ID"></th>
                <th><input type="text" id="filter-brand-name" class="form-control" placeholder="Brand Name"></th>
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

    <!-- Modal for Add/Edit Brand -->
    <div class="modal fade" id="brandModal" tabindex="-1" aria-labelledby="brandModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="brandModalLabel">Add Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="brandForm">
                        @csrf
                        <input type="hidden" name="brand_id" id="brand-id">
                        <div class="mb-3">
                            <label for="brand_name" class="form-label">Brand Name</label>
                            <input type="text" class="form-control" id="brand_name" name="brand_name" required
                                maxlength="50">
                            <div id="brand_name_error" class="text-danger"></div> <!-- Error message for brand name -->
                        </div>
                        <button type="submit" id="saveBrandBtn" class="btn btn-primary" disabled>Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Delete Confirmation -->
    <div class="modal fade" id="deleteBrandModal" tabindex="-1" aria-labelledby="deleteBrandModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteBrandModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this brand?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBrand">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toasts for Success/Error Messages -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
        <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">Brand saved successfully!</div>
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
            var table = $('#brands-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('brands.index') }}", // Ensure the correct route is used for data loading
                    data: function(d) {
                        d.brand_name = $('#filter-brand-name').val();
                        d.created_at = $('#filter-created-at').val();
                        d.updated_at = $('#filter-updated-at').val();
                        d.created_by = $('#filter-created-by').val();
                        d.updated_by = $('#filter-updated-by').val();
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'brand_name', name: 'brand_name' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'created_by', name: 'created_by' },
                    { data: 'updated_by', name: 'updated_by' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-primary edit-brand" data-id="${row.id}">Edit</button>
                                <button class="btn btn-danger delete-brand" data-id="${row.id}">Delete</button>
                            `;
                        }
                    }
                ]
            });

            // Filter functionality
            $('#filter-id, #filter-brand-name, #filter-created-at, #filter-updated-at, #filter-created-by, #filter-updated-by')
                .on('keyup change', function() {
                    table.draw();
                });

            // Add Brand button click
            $('#addBrandBtn').click(function() {
                $('#brandForm')[0].reset();
                $('#brand-id').val('');
                $('#brandModal').modal('show');
                $('#saveBrandBtn').attr('disabled', true); // Ensure Save button is disabled initially
                $('#brand_name_error').text(''); // Clear error messages
            });

            // Enable/Disable Save button based on brand_name input
            $('#brand_name').on('input', function() {
                var brandNameValue = $(this).val().trim();
                if (brandNameValue.length > 0) {
                    $('#saveBrandBtn').attr('disabled', false); // Enable button when input has value
                } else {
                    $('#saveBrandBtn').attr('disabled', true); // Disable button when input is empty
                }
            });

            // Edit Brand button click
            $('body').on('click', '.edit-brand', function() {
                var id = $(this).data('id');
                $.get(`/brands/${id}/edit`, function(data) { // Correct route for fetching edit data
                    $('#brand-id').val(data.id);
                    $('#brand_name').val(data.brand_name);
                    $('#brandModal').modal('show');
                    $('#saveBrandBtn').attr('disabled', false); // Enable Save button during edit
                    $('#brand_name_error').text(''); // Clear error messages
                });
            });

            // Handle Form Submission for Add/Edit
            $('#brandForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var id = $('#brand-id').val();
                var method = id ? 'PUT' : 'POST';
                var url = id ? `/brands/${id}` : '/brands'; // Update URL to 'brands'

                $.ajax({
                    type: method,
                    url: url,
                    data: formData,
                    success: function(response) {
                        $('#brandModal').modal('hide');
                        $('#successToast').toast('show');
                        table.draw();
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            if (xhr.responseJSON.errors.brand_name) {
                                $('#brand_name_error').text(xhr.responseJSON.errors.brand_name[0]);
                            }
                        }
                        $('#errorToastMessage').text('Error saving the brand.');
                        $('#errorToast').toast('show');
                    }
                });
            });

            // Delete Brand button click
            $('body').on('click', '.delete-brand', function() {
                var id = $(this).data('id');
                $('#deleteBrandModal').modal('show');
                $('#confirmDeleteBrand').data('id', id);
            });

            // Confirm Delete button click
            $('#confirmDeleteBrand').click(function() {
                var id = $(this).data('id');
                $.ajax({
                    type: 'DELETE',
                    url: `/brands/${id}`, // Correct URL for deleting brand
                    data: { _token: "{{ csrf_token() }}" }, // Ensure CSRF token is included
                    success: function(response) {
                        $('#deleteBrandModal').modal('hide');
                        $('#successToast').toast('show');
                        table.draw();
                    },
                    error: function() {
                        $('#errorToastMessage').text('Error deleting the brand.');
                        $('#errorToast').toast('show');
                    }
                });
            });
        });
    </script>
@stop
