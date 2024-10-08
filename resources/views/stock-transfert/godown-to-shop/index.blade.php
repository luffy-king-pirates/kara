@extends('adminlte::page')

@section('title', 'Godown to Shop Transactions')

@section('content_header')
    <h1>Godown to Shop Transactions</h1>
@stop

@section('content')
    @include('partials.expiration.expire')
    <div style="height: 700px; overflow-y: auto;">
        <!-- Filter and Export Buttons -->
        @can('export-godwan-to-shop')
            <button id="apply-filter" class="btn btn-success">Export Results in Excel</button>
        @endcan
        @can('create-godwan-to-shop')
            <a href="/godownshop/create" class="btn btn-success" id="addItemBtn">Add New Godown to Shop Transfer</a>
        @endcan
        @can('read-godwan-to-shop')
            <!-- DataTable for Godown to Shop Transactions -->
            <table class="table table-bordered" id="godownshop-table">
                <thead>
                    <tr>
                        <th></th> <!-- Expand button -->
                        <th>ID</th>
                        <th>Transfer Number</th>
                        <th>Approuvee </th>
                        <th>Transfert Date</th>
                    </tr>
                    <tr>
                        <th></th> <!-- Expand button -->
                        <th><input type="text" id="filter-id" class="form-control" placeholder="ID"></th>
                        <th><input type="text" id="filter-transfert-number" class="form-control"
                                placeholder="Transfer Number"></th>
                        <th><input type="text" id="filter-transfert-number" class="form-control"
                                placeholder="Transfer Number"></th>
                        <th><input type="date" id="filter-creation-date" class="form-control"></th>
                    </tr>
                </thead>
            </table>
        @endcan
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Approve Transfer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="approveForm">
                        <div class="form-group">
                            <label for="receiver">Receiver</label>
                            <input type="text" class="form-control" id="receiver" name="receiver" required>
                        </div>
                        <div class="form-group">
                            <label for="transporter">Transporter</label>
                            <input type="text" class="form-control" id="transporter" name="transporter" required>
                        </div>
                        <input type="hidden" id="transaction-id" name="transaction_id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveApproval">Save changes</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="deleteToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">

            <div class="toast-body" id="toastMessage">
                <!-- Toast message will be dynamically inserted here -->
            </div>
        </div>
    </div>

@stop

@section('js')
    @include('partials.import-cdn')
    <script>
        var canEditGodwanToShop = @json($canEditGodwanToShop);
        var canDeleteGodwanToShop = @json($canDeleteGodwanToShop);
        var canExportGodwanToShop = @json($canExportGodwanToShop);
        var canExportPdfGodwanToShop = @json($canExportPdfGodwanToShop);
        var canApprouveGodwanToShop = @json($canApprouveGodwanToShop);
    </script>

    <script>
        $(function() {



            $('#godownshop-table').on('click', '.approve-btn', function() {
                var transactionId = $(this).data('id');
                $('#transaction-id').val(transactionId); // Set the transaction ID in the hidden input
                $('#approveModal').modal('show'); // Show the modal
            });

            // Handle the save approval button click
            $('#saveApproval').click(function() {
                var receiver = $('#receiver').val();
                var transporter = $('#transporter').val();
                var transactionId = $('#transaction-id').val();

                // Perform an AJAX request to submit the approval data
                $.ajax({
                    url: '/godownshop/' + transactionId + '/approve',
                    method: 'PUT',
                    data: {
                        receiver: receiver,
                        transporter: transporter,
                        _token: "{{ csrf_token() }}" // CSRF token for security
                    },
                    success: function(response) {
                        // Handle success (e.g., show a success message, close the modal)
                        $('#approveModal').modal('hide');
                        alert('Transaction approved successfully!');
                        table.draw(); // Optionally, redraw the table
                    },
                    error: function(error) {
                        // Handle error (e.g., show an error message)
                        console.error(error);
                        alert('An error occurred while approving the transaction.');
                    }
                });
            });



            // DataTable with expandable rows
            var table = $('#godownshop-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('godownshop.index') }}", // Ensure the correct route for data loading
                    data: function(d) {
                        d.id = $('#filter-id').val();
                        d.transfer_number = $('#filter-transfer-number').val();
                        d.creation_date = $('#filter-creation-date').val();
                    }
                },
                columns: [{
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: ''
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },

                    {
                        data: 'transfert_number',
                        name: 'transfert_number'
                    },
                    {
                        data: 'is_approved', // Add this line to retrieve the status
                        name: 'is_approved',
                        render: function(data) {
                            // Use Font Awesome icons for status display
                            return data ?
                                '<i class="fas fa-circle text-success" ></i>' :
                                '<i class="fas fa-circle text-danger" ></i>';
                        }
                    },

                    {
                        data: 'transfert_date',
                        name: 'transfert_date'
                    }
                ],
                order: [
                    [1, 'asc']
                ] // Order by ID
            });

            // Filter functionality
            $('#filter-id, #filter-transfer-number, #filter-creation-date').on('keyup change', function() {
                table.draw();
            });

            // Row detail format function to show details
            function formatDetails(rowData) {
                var detailTable = `
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rowData.details.map(item => `
                                                                                                                        <tr>
                                                                                                                            <td>${item.item?.item_name}</td>

                                                                                                                            <td>${item.unit?.unit_name}</td>
                                                                                                                               <td>${item.quantity}</td>
                                                                                                                        </tr>
                                                                                                                    `).join('')}
                        </tbody>
                        <tfoot>
    <tr>
        <td colspan="2"><strong>Total Quantity</strong></td>
        <td>
            ${rowData.details.reduce((total, item) => total + item.quantity, 0)}
        </td>
    </tr>
</tfoot>
                    </table>

                    <div class="btn-group  " role="group" aria-label="Godown to Shop Transaction Actions">






                `;



                // Edit Button
                if (canEditGodwanToShop) {
                    detailTable += `
        <a href="/godownshop/${rowData.id}/edit" class="btn mr-3 btn-warning btn-sm">
            <i class="fas fa-edit"></i> Edit
        </a>`;
                }

                // Delete Button
                if (canDeleteGodwanToShop) {
                    detailTable += `
        <a href="javascript:void(0)" class="btn mr-4 btn-danger btn-sm" onclick="openDeleteModal(${rowData.id})">
            <i class="fas fa-trash-alt"></i> Delete
        </a>`;
                }

                // Export Button
                if (canExportGodwanToShop) {
                    detailTable += `
        <a href="/export/godownshop/exportDetails/${rowData.id}" class="btn mr-3 btn-success btn-sm">
            <i class="fas fa-file-export"></i> Export
        </a>`;
                }

                // Export PDF Button
                if (canExportPdfGodwanToShop) {
                    detailTable += `
        <a href="/godownshop/${rowData.id}/pdf/true" class="btn mr-3 btn-success btn-sm">
            <i class="fas fa-file-export"></i> Export PDF
        </a>`;
                }

                // Approve Button
                if (canApprouveGodwanToShop) {
                    detailTable += `
        <button class="btn btn-primary btn-sm approve-btn" data-id="${rowData.id}">
            <i class="fas fa-check"></i> Approve
        </button>`;
                }
                detailTable += "   </div>"
                return detailTable;
            }

            // Expand row on click
            $('#godownshop-table tbody').on('click', 'td.dt-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {
                    // Close the row if it is already open
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open the row to display details
                    $.get(`/godownshop/${row.data().id}/details`, function(data) {
                        row.child(formatDetails(data)).show();
                        tr.addClass('shown');
                    });
                }
            });

            // Export Filtered Results
            $('#apply-filter').click(function() {
                let queryString = '?';
                queryString += 'id=' + encodeURIComponent($('#filter-id').val()) + '&';
                queryString += 'transfer_number=' + encodeURIComponent($('#filter-transfer-number').val()) +
                    '&';
                queryString += 'creation_date=' + encodeURIComponent($('#filter-creation-date').val());

                window.open('/export/godownshop' + queryString, '_blank');
            });
        });
    </script>
    <script>
        let itemId; // Store the item ID when delete is clicked

        // Function to open the delete modal and set the item ID
        function openDeleteModal(id) {
            itemId = id; // Assign the item ID to the global variable
            // Show the Bootstrap modal
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        // Function to show toast notification
        function showToast(message, success = true) {
            const toastElement = document.getElementById('deleteToast');
            const toastMessage = document.getElementById('toastMessage');
            const toast = new bootstrap.Toast(toastElement);

            // Set the message
            toastMessage.innerText = message;

            // Change the toast style based on success or failure
            if (success) {
                toastElement.classList.add('bg-success');
                toastElement.classList.remove('bg-danger');
            } else {
                toastElement.classList.add('bg-danger');
                toastElement.classList.remove('bg-success');
            }

            // Show the toast
            toast.show();
        }

        // Function to handle the deletion once "Confirm Delete" is clicked
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            $.ajax({
                url: "{{ route('godownshop.destroy', '') }}/" + itemId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    $('#deleteModal').modal('hide');
                    $('#godownshop-table').DataTable().ajax.reload();
                    showToast('Deleted successfully!');
                },
                error: function() {
                    showToast('Error deleting user.');
                }
            });

        });
    </script>
@stop
