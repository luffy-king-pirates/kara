@extends('adminlte::page')

@section('title', 'Cash Transactions')

@section('content_header')
    <h1>Cash Transactions</h1>
@stop

@section('content')
    @include('partials.expiration.expire')
    <div style="height: 700px; overflow-y: auto;">
        <!-- Filter and Export Buttons -->
        @can('export-cash-sale')
            <button id="apply-filter" class="btn btn-success">Export Results in Excel</button>
        @endcan
        @can('create-cash-sale')
            <a href="/cash/create" class="btn btn-success" id="addItemBtn">Add New Cash Transaction</a>
        @endcan
        @can('read-cash-sale')
            <!-- DataTable for Cash Transactions -->
            <table class="table table-bordered" id="cash-table">
                <thead>
                    <tr>
                        <th></th> <!-- Expand button -->
                        <th>ID</th>
                        <th>Cash Number</th>
                        <th>Creation Date</th>
                        <th>Total Amount</th>
                    </tr>
                    <tr>
                        <th></th> <!-- Expand button -->
                        <th><input type="text" id="filter-id" class="form-control" placeholder="ID"></th>
                        <th><input type="text" id="filter-cash-number" class="form-control" placeholder="Cash Number"></th>
                        <th><input type="date" id="filter-creation-date" class="form-control"></th>
                        <th><input type="text" id="filter-total-amount" class="form-control" placeholder="Total Amount"></th>
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
        var canEditCashSale = @json($canEditCashSale);
        var canDeleteCashSale = @json($canDeleteCashSale);
        var canExportCashSale = @json($canExportCashSale);
        var canCashSalePdfWithHeaders = @json($canCashSalePdfWithHeaders);
        var canCashSalePdfWithoutHeaders = @json($canCashSalePdfWithoutHeaders);
    </script>
    <script>
        $(function() {
            // DataTable with expandable rows
            var table = $('#cash-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('cash.index') }}", // Ensure the correct route for data loading
                    data: function(d) {
                        d.id = $('#filter-id').val();
                        d.cash_number = $('#filter-cash-number').val();
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
                        data: 'cash_number',
                        name: 'cash_number'
                    },
                    {
                        data: 'creation_date',
                        name: 'creation_date'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    }
                ],
                order: [
                    [1, 'asc']
                ] // Order by ID
            });

            // Filter functionality
            $('#filter-id, #filter-cash-number, #filter-creation-date, #filter-total-amount').on('keyup change',
                function() {
                    table.draw();
                });

            // Row detail format function to show details
            function formatDetails(rowData) {
                console.log(rowData)
                var detailTable = `
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rowData.details.map(item => `
                                                                                                        <tr>
                                                                                                            <td>${item.item?.item_name}</td>
                                                                                                            <td>${item.quantity}</td>
                                                                                                            <td>${item.unit?.unit_name}</td>
                                                                                                            <td>${item.price}</td>
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
                    <table>
  <tr>

            <td>Created By<td>

            <td style="color:red">${rowData?.created_by_user?.name}<td>

            <td>Updated By<td>

            <td style="color:red">${rowData?.updated_by_user?.name}<td>

        </tr>
        </table>
        <br>
                    <div class="btn-group" role="group" aria-label="Cash Transaction Actions">

                `;

                if (canEditCashSale) {
                    detailTable += `
 <a href="/cash/${rowData.id}/edit" class="btn mr-4 btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    `
                }
                if (canDeleteCashSale) {
                    detailTable += `
  <a href="javascript:void(0)" class="btn mr-4 btn-danger btn-sm" onclick="openDeleteModal(${rowData.id})">
    <i class="fas fa-trash-alt"></i> Delete
</a>
                    `
                }
    if(canExportCashSale){
        detailTable+= `
     <a href="/export/cash/exportDetails/${rowData.id}" class="btn mr-4 btn-success btn-sm">
                            <i class="fas fa-file-export"></i> Export
                        </a>
        `
    }

    if(canCashSalePdfWithHeaders){
        detailTable+=`
     <a href="/cash/${rowData?.id}/pdf/true" class="btn btn-success mr-4 btn-sm">
    <i class="fas fa-file-export"></i> Export pdf with headers
</a>
        `
    }

    if(canCashSalePdfWithoutHeaders){
        detailTable+=`
      <a href="/cash/${rowData?.id}/pdf/false" class="btn btn-success mr-4 btn-sm">
    <i class="fas fa-file-export"></i> Export pdf no headers
</a>
        `
    }
    detailTable+="</div>"

                return detailTable;
            }

            // Expand row on click
            $('#cash-table tbody').on('click', 'td.dt-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {
                    // Close the row if it is already open
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open the row to display details
                    $.get(`/cash/${row.data().id}/details`, function(data) {
                        row.child(formatDetails(data)).show();
                        tr.addClass('shown');
                    });
                }
            });

            // Export Filtered Results
            $('#apply-filter').click(function() {
                let queryString = '?';
                queryString += 'id=' + encodeURIComponent($('#filter-id').val()) + '&';
                queryString += 'cash_number=' + encodeURIComponent($('#filter-cash-number').val()) + '&';
                queryString += 'creation_date=' + encodeURIComponent($('#filter-creation-date').val()) +
                    '&';
                queryString += 'total_amount=' + encodeURIComponent($('#filter-total-amount').val());

                window.open('/export/cash' + queryString, '_blank');
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
                url: "{{ route('cash.destroy', '') }}/" + itemId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    $('#deleteModal').modal('hide');
                    $('#cash-table').DataTable().ajax.reload();
                    showToast('Deleted successfully!');
                },
                error: function() {
                    showToast('Error deleting user.');
                }
            });

        });
    </script>


@stop
