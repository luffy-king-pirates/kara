@extends('adminlte::page')

@section('title', 'Purchase Transactions')

@section('content_header')
    <h1>Purchase Transactions</h1>
@stop

@section('content')
    <div style="height: 700px; overflow-y: auto;">
        <!-- Filter and Export Buttons -->
        <button id="apply-filter" class="btn btn-success">Export Results in Excel</button>
        <a href="/purchase/create" class="btn btn-success" id="addItemBtn">Add New Purchase Transaction</a>

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
    </div>
@stop

@section('js')
    @include('partials.import-cdn')
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
                        <!-- Edit Button -->
                        <a href="/purchase/${rowData.id}/edit" class="btn btn-warning btn-sm mr-3">
                            <i class="fas fa-edit"></i> Edit
                        </a>

                        <!-- Export Button -->
                        <a href="/export/purchase/exportDetails/${rowData.id}" class="btn btn-success btn-sm">
                            <i class="fas fa-file-export"></i> Export
                        </a>

                        {{-- <a href="/purchase/${rowData?.id}/pdf/true" class="btn btn-success btn-sm">
                            <i class="fas fa-file-export"></i> Export pdf with headers
                        </a>

                        <a href="/purchase/${rowData?.id}/pdf/false" class="btn btn-success btn-sm">
                            <i class="fas fa-file-export"></i> Export pdf no headers
                        </a> --}}
                    </div>
                `;
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
@stop
