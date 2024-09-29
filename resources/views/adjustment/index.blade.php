@extends('adminlte::page')

@section('title', 'Adjustments')

@section('content_header')
    <h1>Adjustments</h1>
@stop

@section('content')
    <div style="height: 700px; overflow-y: auto;">
        <!-- Filter and Export Buttons -->
        <button id="apply-filter" class="btn btn-success">Export d Results in Excel</button>
        <a href="/adjustments/create" class="btn btn-success" id="addItemBtn">Adjust Stock</a>

        <!-- DataTable for Adjustments -->
        <table class="table table-bordered" id="adjustments-table">
            <thead>
                <tr>
                    <th></th> <!-- Expand button -->
                    <th>ID</th>
                    <th>Adjustment Number</th>
                    <th>Adjustment Date</th>
                </tr>
                <tr>
                    <th></th> <!-- Expand button -->
                    <th><input type="text" id="filter-id" class="form-control" placeholder="ID"></th>
                    <th><input type="text" id="filter-adjustment-number" class="form-control"
                            placeholder="Adjustment Number"></th>
                    <th><input type="date" id="filter-adjustment-date" class="form-control"></th>
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
            var table = $('#adjustments-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('adjustments.index') }}", // Ensure the correct route for data loading
                    data: function(d) {
                        d.id = $('#filter-id').val();
                        d.adjustment_number = $('#filter-adjustment-number').val();
                        d.adjustment_date = $('#filter-adjustment-date').val();
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
                        data: 'adjustment_number',
                        name: 'adjustment_number'
                    },
                    {
                        data: 'adjustment_date',
                        name: 'adjustment_date'
                    }
                ],
                order: [
                    [1, 'asc']
                ] // Order by ID
            });

            // Filter functionality
            $('#filter-id, #filter-adjustment-number, #filter-adjustment-date').on('keyup change', function() {
                table.draw();
            });

            // Row detail format function to show details
            function formatDetails(rowData) {
                var detailTable = `
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item ID</th>
                                <th>Stock Type</th>
                                <th>Godown</th>
                                <th>Shop</th>
                                <th>Quantity</th>
                                <th>Unit</th>

                            </tr>
                        </thead>
                        <tbody>
                            ${rowData.details.map(item => `
                                                <tr>
                                                    <td>${item.item?.item_name}</td>
                                                    <td>${item.stock_type?.stock_type_name}</td>
                                                    <td>${item.godown || 0}</td>
                                                    <td>${item.shop || 0}</td>
                                                    <td>${item.quantity}</td>
                                                    <td>${item.unit ? item.unit.unit_name : ''}</td>

                                                </tr>
                                            `).join('')}
                        </tbody>

                 <div class="btn-group" role="group" aria-label="Adjustment Actions">
    <!-- Edit Button -->
    <a href="/adjustments/${rowData.id}/edit" class="btn btn-warning btn-sm">
        <i class="fas fa-edit"></i> Edit
    </a>

    <!-- Export Button -->
    <a href="/export/adjustments/exportDetails/${rowData.id}" class="btn btn-success btn-sm">
        <i class="fas fa-file-export"></i> Export
    </a>
</div>

                    </table>
                `;
                return detailTable;
            }

            // Expand row on click
            $('#adjustments-table tbody').on('click', 'td.dt-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {
                    // Close the row if it is already open
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open the row to display details
                    $.get(`/adjustments/${row.data().id}/details`, function(data) {
                        row.child(formatDetails(data)).show();
                        tr.addClass('shown');
                    });
                }
            });

            // Export Filtered Results
            $('#apply-filter').click(function() {
                let queryString = '?';
                queryString += 'id=' + encodeURIComponent($('#filter-id').val()) + '&';
                queryString += 'adjustment_number=' + encodeURIComponent($('#filter-adjustment-number')
                    .val()) + '&';
                queryString += 'adjustment_date=' + encodeURIComponent($('#filter-adjustment-date').val());

                window.open('/export/adjustments' + queryString, '_blank');
            });
        });
    </script>
@stop
