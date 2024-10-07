@extends('adminlte::page')

@section('title', 'Godown to Shop (Ashok) Transactions')

@section('content_header')
    <h1>Godown to Shop (Ashok)</h1>
@stop

@section('content')
    @include('partials.expiration.expire')
    <div style="height: 700px; overflow-y: auto;">
        <!-- Filter and Export Buttons -->
        <button id="apply-filter" class="btn btn-success">Export Results in Excel</button>
        <a href="/godownShopAshok/create" class="btn btn-success" id="addItemBtn">Add New Godown to Shop Transfer</a>

        <!-- DataTable for Godown to Shop Transactions -->
        <table class="table table-bordered" id="godownshop-table">
            <thead>
                <tr>
                    <th></th> <!-- Expand button -->
                    <th>ID</th>
                    <th>Transfer Number</th>
                    <th>Transfert Date</th>
                </tr>
                <tr>
                    <th></th> <!-- Expand button -->
                    <th><input type="text" id="filter-id" class="form-control" placeholder="ID"></th>
                    <th><input type="text" id="filter-transfert-number" class="form-control"
                            placeholder="Transfer Number"></th>
                    <th><input type="date" id="filter-creation-date" class="form-control"></th>
                </tr>
            </thead>
        </table>
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
    </div>
@stop

@section('js')
    @include('partials.import-cdn')
    <script>
        $(function() {
            // DataTable with expandable rows
            var table = $('#godownshop-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('godownShopAshok.index') }}", // Ensure the correct route for data loading
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
                                                                <td>${item.quantity}</td>
                                                                <td>${item.unit?.unit_name}</td>
                                                            </tr>
                                                        `).join('')}
                        </tbody>
                    </table>

                    <div class="btn-group  " role="group" aria-label="Godown to Shop Transaction Actions">
                        <!-- Edit Button -->
                        <a href="/godownShopAshok/${rowData.id}/edit" class="btn mr-3 btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>

         <a href="javascript:void(0)" class="btn mr-4 btn-danger btn-sm" onclick="openDeleteModal(${rowData.id})">
    <i class="fas fa-trash-alt"></i> Delete
</a>
                        <!-- Export Button -->
                        <a href="/export/godownShopAshok/exportDetails/${rowData.id}" class="btn  mr-3 btn-success btn-sm">
                            <i class="fas fa-file-export"></i> Export
                        </a>
                             <a href="/godownShopAshok/${rowData.id}/pdf/true" class="btn btn-success btn-sm">
                            <i class="fas fa-file-export"></i> Export pdf
                        </a>

                    </div>
                `;
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
                    $.get(`/godownShopAshok/${row.data().id}/details`, function(data) {
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

                window.open('/export/godownShopAshok' + queryString, '_blank');
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
                url: "{{ route('godownShopAshok.destroy', '') }}/" + itemId,
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
