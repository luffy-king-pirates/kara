@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="container">
        <!-- Filters -->
        <div class="row">
            <div class="col-md-3">
                <label for="from_date">From Date</label>
                <input type="date" id="from_date" class="form-control"
                    value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label for="to_date">To Date</label>
                <input type="date" id="to_date" class="form-control"
                    value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label for="item_id">Item</label>
                <select id="item_id" class="form-control">
                    <option value="all">All Items</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}">{{ $item->item_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="customer_id">Customer</label>
                <select id="customer_id" class="form-control">
                    <option value="all">All Customers</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mt-2">
                <label for="brand_id">Brand</label>
                <select id="brand_id" class="form-control">
                    <option value="all">All Brands</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mt-4">
            <div class="col-md-6">
                <canvas id="cashSalesBarChart"></canvas> <!-- Changed to bar chart -->
            </div>
            <div class="col-md-6">
                <canvas id="creditSalesBarChart"></canvas> <!-- Changed to bar chart -->
            </div>
        </div>

        <!-- Doughnut Chart -->
        <div class="row mt-4">
            <div class="col-md-12" style="display:flex;justify-content:center; height:200px !important">
                <h4>Sales Comparison (Cash vs Credit)</h4>
                <canvas id="salesDoughnutChart"></canvas>
            </div>
        </div>

        <!-- Top & Worst Sold Items -->
        <div class="row mt-4">
            <div class="col-md-6">
                <h4>Top Sold Items (Cash)</h4>
                <ul id="topSoldItemsCash" class="list-group"></ul>

                <h4 class="mt-4">Worst Sold Items (Cash)</h4>
                <ul id="worstSoldItemsCash" class="list-group"></ul>
            </div>
            <div class="col-md-6">
                <h4>Top Sold Items (Credit)</h4>
                <ul id="topSoldItemsCredit" class="list-group"></ul>

                <h4 class="mt-4">Worst Sold Items (Credit)</h4>
                <ul id="worstSoldItemsCredit" class="list-group"></ul>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            // Function to fetch and update charts without page reload
            function fetchChartData() {
                const fromDate = $('#from_date').val();
                const toDate = $('#to_date').val();
                const itemId = $('#item_id').val();
                const customerId = $('#customer_id').val();
                const brandId = $('#brand_id').val();

                $.ajax({
                    url: "{{ route('dashboard.getSalesData') }}",
                    method: 'GET',
                    data: {
                        from_date: fromDate,
                        to_date: toDate,
                        item_id: itemId,
                        customer_id: customerId,
                        brand_id: brandId
                    },
                    success: function(response) {
                        // Update charts and lists with response data
                        updateBarCharts(response);
                        updateDoughnutChart(response.cashSales, response.creditSales);
                        updateTopSoldItems(response.topSoldItemsCash, response.topSoldItemsCredit);
                        updateWorstSoldItems(response.worstSoldItemsCash, response
                        .worstSoldItemsCredit);
                    },
                    error: function(xhr) {
                        console.error('Error fetching data:', xhr.responseText);
                    }
                });
            }

            // Initialize empty bar charts
            const cashSalesBarChart = new Chart(document.getElementById('cashSalesBarChart'), {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Cash Sales',
                        data: [],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            const creditSalesBarChart = new Chart(document.getElementById('creditSalesBarChart'), {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Credit Sales',
                        data: [],
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Initialize Doughnut Chart
            const doughnutChart = new Chart(document.getElementById('salesDoughnutChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Cash Sales', 'Credit Sales'],
                    datasets: [{
                        label: 'Sales Comparison',
                        data: [0, 0],
                        backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)'],
                        borderColor: ['rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,


                }
            });

            // Function to update bar charts
            function updateBarCharts(data) {
                const cashSalesData = Array.isArray(data.cashSales) ? data.cashSales : [data.cashSales];
                const creditSalesData = Array.isArray(data.creditSales) ? data.creditSales : [data.creditSales];

                const labels = Array.isArray(data.labels) && data.labels.length ? data.labels : ['No Data'];

                // Update cash sales bar chart
                cashSalesBarChart.data.labels = labels;
                cashSalesBarChart.data.datasets[0].data = cashSalesData.map(Number); // Ensure numeric data
                cashSalesBarChart.update();

                // Update credit sales bar chart
                creditSalesBarChart.data.labels = labels;
                creditSalesBarChart.data.datasets[0].data = creditSalesData.map(Number);
                creditSalesBarChart.update();
            }

            // Function to update Doughnut chart
            function updateDoughnutChart(cashSales, creditSales) {
                const cashValue = Array.isArray(cashSales) ? cashSales.reduce((a, b) => a + Number(b), 0) : Number(
                    cashSales);
                const creditValue = Array.isArray(creditSales) ? creditSales.reduce((a, b) => a + Number(b), 0) :
                    Number(creditSales);

                doughnutChart.data.datasets[0].data = [cashValue, creditValue];
                doughnutChart.update();
            }

            // Function to update top sold items
            function updateTopSoldItems(cashItems, creditItems) {
                $('#topSoldItemsCash').empty();
                $('#topSoldItemsCredit').empty();

                // Handle cash items
                cashItems.forEach(item => {
                    $('#topSoldItemsCash').append(
                        `<li class="list-group-item">${item.item_name ?? 'Unnamed Item'} - ${item.total_sold} - Brand : ${item.brand_name} - Customer Name : ${item.customer_name}</li>`
                    );
                });

                // Handle credit items
                creditItems.forEach(item => {
                    $('#topSoldItemsCredit').append(
                        `<li class="list-group-item">${item.item_name ?? 'Unnamed Item'} - ${item.total_sold} - Brand :  ${item.brand_name} - Customer Name : ${item.customer_name}</li>`
                    );
                });
            }

            // Function to update worst sold items
            function updateWorstSoldItems(cashItems, creditItems) {
                $('#worstSoldItemsCash').empty();
                $('#worstSoldItemsCredit').empty();

                // Handle cash worst sold items
                cashItems.forEach(item => {
                    $('#worstSoldItemsCash').append(
                        `<li class="list-group-item">${item.item_name ?? 'Unnamed Item'} - ${item.total_sold} - Brand :  ${item.brand_name} - Customer Name : ${item.customer_name}</li>`
                    );
                });

                // Handle credit worst sold items
                creditItems.forEach(item => {
                    $('#worstSoldItemsCredit').append(
                        `<li class="list-group-item">${item.item_name ?? 'Unnamed Item'} - ${item.total_sold} - Brand :  ${item.brand_name} - Customer Name : ${item.customer_name} </li>`
                    );
                });
            }

            // Event listeners for input changes
            $('#from_date, #to_date, #item_id, #customer_id, #brand_id').change(fetchChartData);

            // Fetch initial data
            fetchChartData();
        });
    </script>
@stop
