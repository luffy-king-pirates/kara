@extends('adminlte::page')

@section('title', 'Proforma Transactions')

@section('content_header')
    <h1>Proforma Transactions</h1>
@stop

@section('content')
    <div style="height: 700px; overflow-y: auto;">
        <!-- Filter and Export Buttons -->
        <button id="apply-filter" class="btn btn-success">Export Results in Excel</button>
        <a href="/proforma/create" class="btn btn-success" id="addItemBtn">Add New Proforma Transaction</a>

        <!-- DataTable for Proforma Transactions -->
        <table class="table table-bordered" id="proforma-table">
            <thead>
                <tr>
                    <th></th> <!-- Expand button -->
                    <th>ID</th>
                    <th>Proforma Number</th>
                    <th>Creation Date</th>
                    <th>Total Amount</th>
                </tr>
                <tr>
                    <th></th> <!-- Expand button -->
                    <th><input type="text" id="filter-id" class="form-control" placeholder="ID"></th>
                    <th><input type="text" id="filter-proforma-number" class="form-control" placeholder="Proforma Number"></th>
                    <th><input type="date" id="filter-creation-date" class="form-control"></th>
                    <th><input type="text" id="filter-total-amount" class="form-control" placeholder="Total Amount"></th>
                </tr>
            </thead>
        </table>
    </div>
@stop

@section('js')
    @include('partials.import-cdn')
    <script>
        $(function() {
            // DataTable with expandable rows
            var table = $('#proforma-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('proforma.index') }}", // Ensure the correct route for data loading
                    data: function(d) {
                        d.id = $('#filter-id').val();
                        d.proforma_number = $('#filter-proforma-number').val();
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
                        data: 'proforma_number',
                        name: 'proforma_number'
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
            $('#filter-id, #filter-proforma-number, #filter-creation-date, #filter-total-amount').on('keyup change',
                function() {
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

                    <div class="btn-group" role="group" aria-label="Proforma Transaction Actions">
                        <!-- Edit Button -->
                        <a href="/proforma/${rowData.id}/edit" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>

                        <!-- Export Button -->
                        <a href="/export/proforma/exportDetails/${rowData.id}" class="btn btn-success btn-sm">
                            <i class="fas fa-file-export"></i> Export
                        </a>

                        <a href="/proforma/${rowData?.id}/pdf/true" class="btn btn-success btn-sm">
    <i class="fas fa-file-export"></i> Export PDF with headers
</a>
    <a href="/proforma/${rowData?.id}/pdf/false" class="btn btn-success btn-sm">
    <i class="fas fa-file-export"></i> Export PDF no headers
</a>
                    </div>
                `;
                return detailTable;
            }

            // Expand row on click
            $('#proforma-table tbody').on('click', 'td.dt-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {
                    // Close the row if it is already open
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open the row to display details
                    $.get(`/proforma/${row.data().id}/details`, function(data) {
                        row.child(formatDetails(data)).show();
                        tr.addClass('shown');
                    });
                }
            });

            // Export Filtered Results
            $('#apply-filter').click(function() {
                let queryString = '?';
                queryString += 'id=' + encodeURIComponent($('#filter-id').val()) + '&';
                queryString += 'proforma_number=' + encodeURIComponent($('#filter-proforma-number').val()) + '&';
                queryString += 'creation_date=' + encodeURIComponent($('#filter-creation-date').val()) + '&';
                queryString += 'total_amount=' + encodeURIComponent($('#filter-total-amount').val());

                window.open('/export/proforma' + queryString, '_blank');
            });
        });
    </script>
@stop
