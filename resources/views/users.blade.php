@extends('adminlte::page')

@section('title', 'Users')

@section('content_header')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">

    <h1>Users</h1>
@stop

@section('content')
    <!-- Add User Button -->
    <a href="javascript:void(0)" class="btn btn-success" id="addUserBtn">Add User</a>
    <button id="apply-filter" class="btn btn-success">Export Result in Excel</button>
    <!-- DataTable for Users -->
    <table class="table table-bordered" id="users-table">
        <thead>
            <tr>
                <th>Profile Picture</th>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>


                <th>Action</th>
            </tr>
            <tr>
                <th><input type="text" id="filter-id" class="form-control" placeholder="ID"></th>
                <th><input type="text" id="filter-name" class="form-control" placeholder="Name"></th>
                <th><input type="text" id="filter-email" class="form-control" placeholder="Email"></th>
                <th><input type="text" id="filter-phone" class="form-control" placeholder="Phone"></th>
                <th></th>

                <th></th>
            </tr>
        </thead>
    </table>

    <!-- Modal for Add/Edit User -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Add/Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="user_id" id="user-id">

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">User Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="middle_name" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="middle_name" name="middle_name">
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="profile_picture" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="profile_picture" name="profile_picture"
                                accept="image/*">
                            <img id="preview" src="#" alt="Profile Picture" class="mt-2"
                                style="display: none; max-width: 100px;" />
                            <button type="button" id="removePicture" class="btn btn-danger btn-sm mt-2"
                                style="display: none;">Remove</button>
                        </div>

                        <button type="submit" id="saveUserBtn" class="btn btn-primary" disabled>Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Delete Confirmation -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteUser">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toasts for Success/Error Messages -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
        <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">User saved successfully!</div>
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
            var table = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('users.index') }}",
                    data: function(d) {
                        d.name = $('#filter-name').val();
                        d.email = $('#filter-email').val();
                        d.phone = $('#filter-phone').val();
                        d.created_at = $('#filter-created-at').val();
                        d.updated_at = $('#filter-updated-at').val();
                    }
                },
                columns: [{
                        data: 'profile_picture',
                        name: 'profile_picture',
                        render: function(data, type, row) {
                            console.log("data = ", data)
                            return data ?
                                `<img src="${data}" alt="Profile Picture" style="max-width: 50px; max-height: 50px;">` :
                                '<img src="https://res.cloudinary.com/dwzht4utm/image/upload/v1727019534/images_b5ws3b.jpg" alt="Profile Picture" style="max-width: 50px; max-height: 50px; ';
                        }
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },


                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-primary edit-user" data-id="${row.id}">Edit</button>
                                <button class="btn btn-danger delete-user" data-id="${row.id}">Delete</button>
                            `;
                        }
                    }
                ]
            });

            // Filter functionality
            $('#filter-id, #filter-name, #filter-email, #filter-phone, #filter-created-at, #filter-updated-at')
                .on('keyup change', function() {
                    table.draw();
                });

            // Add User button click
            $('#addUserBtn').click(function() {
                $('#userForm')[0].reset();
                $('#user-id').val('');
                $('#preview').hide();
                $('#removePicture').hide();
                $('#userModal').modal('show');
                $('#saveUserBtn').attr('disabled', true);
            });

            // Enable/Disable Save button based on input fields
            $('#first_name, #last_name, #email, #phone').on('input', function() {
                var isValid = $('#first_name').val().trim() && $('#last_name').val().trim() &&
                    $('#email').val().trim() && $('#phone').val().trim();
                $('#saveUserBtn').attr('disabled', !isValid);
            });

            // Profile Picture Preview and Remove
            $('#profile_picture').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#preview').attr('src', e.target.result).show();
                        $('#removePicture').show();
                    }
                    reader.readAsDataURL(file);
                }
            });

            $('#removePicture').click(function() {
                $('#profile_picture').val(null);
                $('#preview').hide();
                $(this).hide();
            });

            // Save User (AJAX Request)
            $('#userForm').submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);
                console.log(formData.get('profile_picture'))
                // Include _method field for PUT requests if updating a user
                let userId = $('#user-id').val();
                if (userId) {
                    formData.append('_method', 'PUT');
                }

                let url = userId ? "{{ route('users.update', '') }}/" + userId :
                    "{{ route('users.store') }}";
                let method = userId ? 'POST' :
                    'POST'; // POST method, as Laravel will interpret _method as PUT for update

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#userModal').modal('hide');
                        $('#users-table').DataTable().ajax.reload();
                        showSuccessToast();
                    },
                    error: function(response) {
                        showErrorToast(response.responseJSON.message);
                    }
                });
            });

            // Edit User button click
            $(document).on('click', '.edit-user', function() {
                var userId = $(this).data('id');
                $.get('/users/' + userId + '/edit', function(data) {
                    console.log(" data.profile_picture = ", '/storage' + data.profile_picture)
                    $('#user-id').val(data.id);
                    $('#first_name').val(data.first_name);
                    $('#middle_name').val(data.middle_name);
                    $('#last_name').val(data.last_name);
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    $('#phone').val(data.phone);
                    $('#profile_picture').val('');
                    $('#preview').attr('src', '/storage/' + data.profile_picture).show();
                    $('#removePicture').show();
                    $('#userModal').modal('show');
                    $('#saveUserBtn').attr('disabled', false);
                });
            });

            // Delete User button click
            $(document).on('click', '.delete-user', function() {
                var userId = $(this).data('id');
                $('#deleteUserModal').modal('show');
                $('#confirmDeleteUser').off('click').on('click', function() {
                    $.ajax({
                        url: "{{ route('users.destroy', '') }}/" + userId,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            $('#deleteUserModal').modal('hide');
                            $('#users-table').DataTable().ajax.reload();
                            showSuccessToast('User deleted successfully!');
                        },
                        error: function() {
                            showErrorToast('Error deleting user.');
                        }
                    });
                });
            });

            function showSuccessToast(message = 'User saved successfully!') {
                $('#successToast .toast-body').text(message);
                var successToast = new bootstrap.Toast(document.getElementById('successToast'));
                successToast.show();
            }

            function showErrorToast(message) {
                $('#errorToast .toast-body').text(message);
                var errorToast = new bootstrap.Toast(document.getElementById('errorToast'));
                errorToast.show();
            }
            // Select the filter button
            const filterButton = document.getElementById('apply-filter');

            // Select all the filter input elements
            const filters = {
                id: document.getElementById('filter-id'),
                name: document.getElementById('filter-name'),
                email: document.getElementById('filter-email'),
                phone: document.getElementById('filter-phone'),
            };

            // Add event listener to the filter button
            filterButton.addEventListener('click', function() {
                // Build the query string from the filter inputs
                let queryString = '?';

                for (let key in filters) {
                    const value = filters[key].value.trim(); // Get the trimmed value
                    if (value) {
                        queryString +=
                        `${key}=${encodeURIComponent(value)}&`; // encodeURIComponent to handle special characters
                    }
                }

                // Redirect the page with the updated filters in the query string (or perform AJAX request)
                window.open('/export/users' + queryString.slice(0, -1),
                '_blank'); // Update the URL to your export route
            });

        });
    </script>
@stop
