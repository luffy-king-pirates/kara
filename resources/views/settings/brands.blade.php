@extends('adminlte::page')

@section('title', 'Brands')

@section('content_header')

    <h1>Brands</h1>
@stop

@section('content')
    <div style="height: 700px; overflow-y: auto;">
        <!-- Add Brand Button -->
        @can('create-brand')
            <a href="javascript:void(0)" class="btn btn-success" id="addBrandBtn">Add Brand</a>
        @endcan
        @can('export-brand')
            <button id="apply-filter" class="btn btn-success">Export Result in Excel</button>
        @endcan

        @if (session('successes'))
            <div class="alert alert-success">
                <ul>
                    @foreach (session('successes') as $success)
                        <li>{{ $success }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('errors'))
            <div class="alert alert-danger">
                <ul>
                    @foreach (session('errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- DataTable for Brands -->
        @include('partials.filter-brands', ['users' => $users])


        @include('batsh.brands')
        @can('read-brand')
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
        @endcan


        @include('partials.expiration.expire')

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
                            <div class="mb-3 position-relative">
                                <label for="brand_name" class="form-label">Brand Name <span
                                        class="text-danger">*</span></label>

                                <!-- Input field with required attribute -->
                                <input type="text" class="form-control" id="brand_name" name="brand_name" required
                                    maxlength="50" placeholder="Enter your brand name">

                                <!-- Error message for brand name -->
                                <div id="brand_name_error" class="text-danger"></div>
                            </div>
                            <button type="submit" id="saveBrandBtn" class="btn btn-primary" disabled>Save changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Delete Confirmation -->
        <div class="modal fade" id="deleteBrandModal" tabindex="-1" aria-labelledby="deleteBrandModalLabel"
            aria-hidden="true">
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




    </div>
@stop

@section('js')
    @include('partials.import-cdn')
    <script>
        var canEditBrand = @json($canEditBrand);
        var canDeleteBrand = @json($canDeleteBrand);
    </script>
    <script>
        $(function() {
            $('#fileUploadForm').on('submit', function() {
                $('#uploadModal').modal('hide');
            });
            var table = $('#brands-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
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
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'brand_name',
                        name: 'brand_name'
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

                            if (canEditBrand) {
                                actionButtons +=
                                    `<button class="btn btn-primary edit-brand" data-id="${row.id}">Edit</button>`;
                            }

                            if (canDeleteBrand) {
                                actionButtons +=
                                    `<button class="btn btn-danger delete-brand" data-id="${row.id}">Delete</button>`;
                            }

                            return actionButtons;
                        }
                    }
                ]

            });


            // Add the buttons to the table
            table.buttons().container().appendTo('#assignedRoles-table_wrapper .col-md-6:eq(0)');


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
                                $('#brand_name_error').text(xhr.responseJSON.errors.brand_name[
                                    0]);
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
                    data: {
                        _token: "{{ csrf_token() }}"
                    }, // Ensure CSRF token is included
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


            const filterButton = document.getElementById('apply-filter');

            // Select all the filter input elements
            const filters = {
                id: document.getElementById('filter-id'),
                brand_name: document.getElementById('filter-brand-name'), // Updated to 'brand_name'
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
                window.open('/export/brands' + queryString.slice(0, -1),
                    '_blank'); // Update the URL to '/export/brands'
            });

            function validateBrandName() {
                const brandNameInput = document.getElementById('brand_name');
                const errorDiv = document.getElementById('brand_name_error');
                const counterDiv = document.getElementById('brand_name_counter');
                const maxLength = brandNameInput.maxLength;

                // Update character counter
                const currentLength = brandNameInput.value.length;
                counterDiv.textContent = `${currentLength} / ${maxLength} characters`;

                // Example validation: brand name must be at least 3 characters
                if (currentLength < 3) {
                    brandNameInput.classList.add('is-invalid');
                    errorDiv.textContent = 'Brand name must be at least 3 characters.';
                } else {
                    brandNameInput.classList.remove('is-invalid');
                    brandNameInput.classList.add('is-valid');
                    errorDiv.textContent = ''; // Clear error message
                }
            }
        });
    </script>
@stop
