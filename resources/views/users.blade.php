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

    <!-- DataTable for Users -->
    <table class="table table-bordered" id="users-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>User Name</th>
                <th>Profile Picture</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Action</th>
            </tr>
            <tr>
                <th><input type="text" id="filter-id" class="form-control" placeholder="ID"></th>
                <th><input type="text" id="filter-first-name" class="form-control" placeholder="First Name"></th>
                <th><input type="text" id="filter-middle-name" class="form-control" placeholder="Middle Name"></th>
                <th><input type="text" id="filter-last-name" class="form-control" placeholder="Last Name"></th>
                <th><input type="text" id="filter-phone" class="form-control" placeholder="Phone Number"></th>
                <th><input type="text" id="filter-email" class="form-control" placeholder="Email"></th>

                <th></th>
            </tr>
        </thead>
    </table>

    <!-- Modal for Add/Edit User -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="user_id" id="user-id">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required
                                maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label for="middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name" maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required
                                maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div id="email_error" class="text-danger"></div>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">User Name</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required
                                minlength="8">
                        </div>
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="profile_picture" name="profile_picture"
                                accept="image/*">
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
            console.log("AJAX URL:", "{{ route('users.index') }}");
            var table = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {

                    url: "{{ route('users.index') }}",
                    data: function(d) {
                        d.first_name = $('#filter-first-name').val();
                        d.middle_name = $('#filter-middle-name').val();
                        d.last_name = $('#filter-last-name').val();
                        d.phone = $('#filter-phone').val();
                        d.email = $('#filter-email').val();

                    },
                    success: function(data) {
                        console.log('Server response:', data); // Check what the server is sending
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error:', textStatus, errorThrown);
                    },


                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'first_name',
                        name: 'first_name'
                    },
                    {
                        data: 'middle_name',
                        name: 'middle_name'
                    },
                    {
                        data: 'last_name',
                        name: 'last_name'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'profile_picture',
                        name: 'profile_picture',
                        render: function(data) {
                            console.log(data); // Check what data is being received
                            if (data) {
                                return `<img src="/storage/${data}" width="50" height="50" alt="Profile Picture">`;
                            } else {
                                return '<span>No image</span>';
                            }
                        }
                    },


                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Filtering logic
            $('#filter-first-name, #filter-middle-name, #filter-last-name, #filter-phone, #filter-email')
                .on('keyup change', function() {
                    table.draw();
                });

            // Form submission
            $('#userForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: "{{ route('users.store') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#userModal').modal('hide');
                            table.ajax.reload();
                            $('#successToast').toast('show');
                        } else {
                            $('#errorToastMessage').text(response.message);
                            $('#errorToast').toast('show');
                        }
                    },
                    error: function(error) {
                        $('#errorToastMessage').text(
                            'An error occurred while saving the user.');
                        $('#errorToast').toast('show');
                    }
                });
            });

            // Add User Button Click
            $('#addUserBtn').on('click', function() {
                $('#userForm')[0].reset();
                $('#userModalLabel').text('Add User');
                $('#user-id').val('');
                $('#userModal').modal('show');
            });

            // Handle email validation and enable save button
            $('#email').on('keyup', function() {
                var email = $(this).val();
                if (validateEmail(email)) {
                    $('#email_error').text('');
                    $('#saveUserBtn').prop('disabled', false);
                } else {
                    $('#email_error').text('Invalid email address.');
                    $('#saveUserBtn').prop('disabled', true);
                }
            });

            function validateEmail(email) {
                var re = /\S+@\S+\.\S+/;
                return re.test(email);
            }

            // Confirm deletion
            $('#confirmDeleteUser').on('click', function() {
                // Add AJAX request for deletion here
            });
        });
    </script>
@stop
