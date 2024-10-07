@extends('adminlte::page')

@section('title', 'Shop to Godown')

@section('content_header')
    <h1>Internal Transfers</h1>
@stop

@section('content')
    @include('partials.expiration.expire')
    <div style="height: 700px; overflow-y: auto;">
        <!-- Filter and Export Buttons -->
        <button id="apply-filter" class="btn btn-success">Export Results in Excel</button>

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
    </div>
@stop

@section('js')
    @include('partials.import-cdn')
    <script>
        $(function() {
            let detail_link = ""
            // DataTable with expandable rows
            var table = $('#godownshop-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('existingTranfers.index') }}", // Ensure the correct route for data loading
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

                    <div class="btn-group" role="group" aria-label="Godown to Shop Transaction Actions">
                        <!-- Edit Button -->
                        <a href="/${detail_link}/${rowData.id}/edit" class="btn  mr-3 btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>

                        <!-- Export Button -->
                        <a href="/export/${detail_link}/exportDetails/${rowData.id}" class="btn  mr-3 btn-success btn-sm">
                            <i class="fas fa-file-export"></i> Export
                        </a>
                             <a href="/${detail_link}/${rowData.id}/pdf/true" class="btn  mr-3 btn-success btn-sm">
                            <i class="fas fa-file-export"></i> Export pdf with headers
                        </a>
                              <a href="/${detail_link}/${rowData.id}/pdf/false" class="btn btn-success btn-sm">
                            <i class="fas fa-file-export"></i> Export pdf without headers
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
                    console.log("row.data()  = ", row.data())

                    if (row.data()?.transfert_from === "godown" && row.data()?.transfert_to === "shop") {
                        detail_link = "godownshop"
                    } else if (row.data()?.transfert_from === "godown" && row.data()?.transfert_to ===
                        "shop_ashok") {
                        detail_link = "godownShopAshok"
                    } else if (row.data()?.transfert_from === "shop-service" && row.data()?.transfert_to ===
                        "godown") {
                        detail_link = "services"
                    } else if (row.data()?.transfert_from === "shop" && row.data()?.transfert_to ===
                        "godown") {
                        detail_link = "shopGodown"
                    }
                    // Open the row to display details
                    $.get(`/${detail_link}/${row.data().id}/details`, function(data) {
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

                window.open('/export/existence' + queryString, '_blank');
            });
        });
    </script>
@stop
