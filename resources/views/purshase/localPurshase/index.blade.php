@extends('adminlte::page')

@section('title', 'Purchase Transactions')

@section('content_header')
    <h1>Purchase Transactions</h1>
@stop

@section('content')
    @include('partials.expiration.expire')
    <div style="height: 700px; overflow-y: auto;">
        <!-- Filter and Export Buttons -->
        @can('export-local-purchase')
            <button id="apply-filter" class="btn btn-success">Export Results in Excel</button>
        @endcan
        @can('create-local-purchase')
            <a href="/purchase/create" class="btn btn-success" id="addItemBtn">Add New Purchase Transaction</a>
        @endcan
        @can('read-local-purchase')
            <!-- DataTable for Purchase Transactions -->
            <table class="table table-bordered" id="purchase-table">
                <thead>
                    <tr>
                        <th></th> <!-- Expand button -->
                        <th>ID</th>
                        <th>Receipt Number</th>
                        <th>Supplier</th>
                        <th>Creation Date</th>

                    </tr>
                    <tr>
                        <th></th> <!-- Expand button -->
                        <th><input type="text" id="filter-id" class="form-control" placeholder="ID"></th>
                        <th><input type="text" id="filter-receipt-number" class="form-control" placeholder="Receipt Number">
                        </th>
                        <th><input type="text" id="filter-supplier" class="form-control" placeholder="Supplier"></th>
                        <th><input type="date" id="filter-creation-date" class="form-control"></th>
                    </tr>
                </thead>
            </table>
        @endcan


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
        var canEditLocalPurchase = @json($canEditLocalPurchase);
        var canDeleteLocalPurchase = @json($canDeleteLocalPurchase);
        var canExportLocalPurchase = @json($canExportLocalPurchase);
        var canExportPdfLocalPurchase = @json($canExportPdfLocalPurchase);
    </script>
    <script>
        $(function() {
            // DataTable with expandable rows for Purchase
            var table = $('#purchase-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('purchase.index') }}", // Ensure the correct route for data loading
                    data: function(d) {
                        d.id = $('#filter-id').val();
                        d.receipt_number = $('#filter-receipt-number').val();
                        d.supplier = $('#filter-supplier').val();
                        d.creation_date = $('#filter-creation-date').val();
                        d.total_amount = $('#filter-total-amount').val();
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
                        data: 'receipt_number',
                        name: 'receipt_number'
                    },
                    {
                        data: 'supplier',
                        name: 'supplier'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },

                ],
                order: [
                    [1, 'asc']
                ] // Order by ID
            });

            // Filter functionality
            $('#filter-id, #filter-receipt-number, #filter-supplier, #filter-creation-date, #filter-total-amount')
                .on('keyup change', function() {
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
                                <th>Cost</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rowData.details.map(item => `
                                                                <tr>
                                                                    <td>${item.item?.item_name}</td>
                                                                    <td>${item.quantity}</td>
                                                                    <td>${item.unit?.unit_name}</td>
                                                                    <td>${item.cost}</td>
                                                                    <td>${item.total}</td>
                                                                </tr>
                                                            `).join('')}
                        </tbody>
                         <tfoot>
                            <tr>
                                <td><strong>Total</strong></td>
                                <td><strong>${rowData.details.reduce((sum, item) => sum + item.quantity, 0)}</strong></td>
                                <td></td>
                                <td></td>
                                <td><strong>${rowData.details.reduce((sum, item) => parseFloat(sum) + parseFloat(item.total), 0)}</strong></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="btn-group" role="group" aria-label="Purchase Transaction Actions">

                `;
                if (canEditLocalPurchase) {
                    detailTable += `
 <a href="/purchase/${rowData.id}/edit" class="btn btn-warning btn-sm mr-3">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    `
                }
                if (canDeleteLocalPurchase) {
                    detailTable += `

                <a href="javascript:void(0)" class="btn mr-4 btn-danger btn-sm" onclick="openDeleteModal(${rowData.id})">
    <i class="fas fa-trash-alt"></i> Delete
</a>
                    `
                }

                if (canExportLocalPurchase) {
                    detailTable += `
 <a href="/export/purchase/exportDetails/${rowData.id}" class="btn mr-3 btn-success btn-sm">
                            <i class="fas fa-file-export"></i> Export
                        </a>
                    `
                }

                if (canExportPdfLocalPurchase) {
                    detailTable += `
     <a href="/storage/${rowData?.pdf}" download class="btn btn-success btn-sm">
                            <i class="fas fa-file-export"></i> Export purchase pdf
                        </a>
                    `
                }

                detailTable += "</div>"
                return detailTable;
            }



            // Expand row on click
            $('#purchase-table tbody').on('click', 'td.dt-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {
                    // Close the row if it is already open
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open the row to display details
                    $.get(`/purchase/${row.data().id}/details`, function(data) {
                        row.child(formatDetails(data)).show();
                        tr.addClass('shown');
                    });
                }
            });

            // Export Filtered Results
            $('#apply-filter').click(function() {
                let queryString = '?';
                queryString += 'id=' + encodeURIComponent($('#filter-id').val()) + '&';
                queryString += 'receipt_number=' + encodeURIComponent($('#filter-receipt-number').val()) +
                    '&';
                queryString += 'supplier=' + encodeURIComponent($('#filter-supplier').val()) + '&';
                queryString += 'creation_date=' + encodeURIComponent($('#filter-creation-date').val()) +
                    '&';

                window.open('/export/purchase' + queryString, '_blank');
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
                url: "{{ route('purchase.destroy', '') }}/" + itemId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    $('#deleteModal').modal('hide');
                    $('#purchase-table').DataTable().ajax.reload();
                    showToast('Deleted successfully!');
                },
                error: function() {
                    showToast('Error deleting user.');
                }
            });

        });
    </script>
@stop
