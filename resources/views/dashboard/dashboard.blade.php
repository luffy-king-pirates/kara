@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <style>
        .tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border: 1px solid #ccc;
            margin: 0 5px;
        }

        .tab.active {
            background-color: #007bff;
            color: white;
        }
    </style>
    {{-- <div class="row">
        <div class="col-lg-3 col-xs-6">

            <div class="small-box" style="background-color:aqua">
                <div class="inner">
                    <h3>150</h3>
                    <p>New Orders</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">

            <div class="small-box bg-green">
                <div class="inner">
                    <h3>53<sup style="font-size: 20px">%</sup></h3>
                    <p>Bounce Rate</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">

            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>44</h3>
                    <p>User Registrations</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">

            <div class="small-box bg-red">
                <div class="inner">
                    <h3>65</h3>
                    <p>Unique Visitors</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

    </div> --}}


    <!-- Tabs for time period selection -->

    <div class="container mt-5">
        <!-- Tabs for time period selection -->
        <div class="tabs">
            <div class="tab active" data-filter="1day">1 Day</div>
            <div class="tab" data-filter="2days">2 Days</div>
            <div class="tab" data-filter="3days">3 Days</div>
            <div class="tab" data-filter="month">1 Month</div>
            <div class="tab" data-filter="year">1 Year</div>
        </div>

        <div class="row">
            <!-- Doughnut Chart Card -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Sales Doughnut Chart</h5>
                    </div>
                    <div class="card-body">
                        <div style="width: 100%; margin: 0 auto;">
                            <canvas id="salesDoughnutChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bar Chart Card -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Sales Bar Chart</h5>
                    </div>
                    <div class="card-body">
                        <div style="width: 100%; margin: 0 auto;">
                            <canvas id="salesBarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Sales line chart  -->
    <canvas id="salesChart" width="400" height="200"></canvas>
    <select id="timeRange">
        <option value="monthly">Monthly</option>
        <option value="hourly">Hourly</option>
        <option value="weekly">Weekly</option>

        <option value="yearly">Yearly</option>
    </select>




    <div class="container">
        <h2>Sales Chart (Cash vs Credit) Based on quantity and item </h2>

        <!-- Dropdown to select time range -->
        <form id="timeRangeForm">
            <label for="time_range">Select Time Range:</label>
            <select name="time_range" id="time_range">
                <option value="1_day">1 Day</option>
                <option value="3_days">3 Days</option>
                <option value="1_week">1 Week</option>
                <option value="2_weeks">2 Weeks</option>
                <option value="1_month">1 Month</option>
                <option value="2_months">2 Months</option>
                <option value="1_year">1 Year</option>
            </select>
        </form>

        <!-- Canvas for the chart -->
        <canvas id="salesPerDetailChart"></canvas>
    </div>




    <div class="container">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" id="top-cash-tab">Top Sold Items (Cash)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="top-credit-tab">Top Sold Items (Credit)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="worst-cash-tab">Worst Sold Items (Cash)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="worst-credit-tab">Worst Sold Items (Credit)</a>
            </li>
        </ul>

        <canvas id="itemsChart"></canvas>
    </div>
    <table id="itemsTable" class="table table-bordered">
        <thead>
            <tr>
                <th>Item</th>
                <th>Brand</th>
                <th>Quantity Sold</th>
            </tr>
        </thead>
    </table>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    @include('partials.import-cdn')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const ctx = document.getElementById('salesPerDetailChart').getContext('2d');
        let salesPerDetailChart;

        function fetchSalesLineItemData(timeRange) {
            $.ajax({
                url: "/sales/sales-item-credit-cash", // Your route to the controller method
                method: "GET",
                data: {
                    time_range: timeRange
                },
                success: function(data) {
                    const itemNames = data.itemNames; // Get item names
                    const cashData = data.cashData;
                    const creditData = data.creditData;

                    // Update the chart with item names as labels
                    updateChartSaleItem(Object.values(itemNames), cashData,
                        creditData); // Use item names as labels

                },
                error: function(err) {
                    console.error("Error fetching sales data", err);
                }
            });
        }

        function updateChartSaleItem(items, cashData, creditData) {
            if (salesPerDetailChart) {
                salesPerDetailChart.destroy(); // Destroy the previous chart instance
            }

            salesPerDetailChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: items,
                    datasets: [{
                            label: 'Cash Sales',
                            data: cashData,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Credit Sales',
                            data: creditData,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        $(document).ready(function() {

            //top and worst items
            let chartItem;
            const ctxItems = document.getElementById('itemsChart').getContext('2d');

            function fetchAndRenderData(url, label) {
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        const itemNames = response.map(item => item.item.item_name);
                        const quantities = response.map(item => item.total_sold);
                        const brands = response.map(item => item.item.brand.brand_name);

                        if (chartItem) {
                            chartItem.destroy(); // Destroy previous chart instance
                        }

                        chartItem = new Chart(ctxItems, {
                            type: 'bar',
                            data: {
                                labels: itemNames,
                                datasets: [{
                                    label: label,
                                    data: quantities,
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
                                },
                                plugins: {
                                    tooltip: {
                                        callbacks: {
                                            afterLabel: function(context) {
                                                return 'Brand: ' + brands[context
                                                    .dataIndex];
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                });
            }

            //for data table top and worst

            {{-- $('#itemsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/dashboard/items/top-sold-cash', // Use the same endpoints created earlier
                columns: [{
                        data: 'item.item_name',
                        name: 'item.item_name'
                    },
                    {
                        data: 'item.brand.brand_name',
                        name: 'item.brand.brand_name'
                    },
                    {
                        data: 'total_sold',
                        name: 'total_sold'
                    }
                ]
            }); --}}
            // Default chart on page load (top cash sold items)
            fetchAndRenderData('/dashboard/items/top-sold-cash', 'Top 10 Sold Items (Cash)');

            // Tab click event listeners
            $('#top-cash-tab').click(function() {

                $('.nav-link').removeClass('active'); // Remove active class from all tabs
                $(this).addClass('active'); // Add active class to the clicked tab

                fetchAndRenderData('/dashboard/items/top-sold-cash', 'Top 10 Sold Items (Cash)');
            });
            $('#top-credit-tab').click(function() {

                $('.nav-link').removeClass('active'); // Remove active class from all tabs
                $(this).addClass('active'); // Add active class to the clicked tab

                fetchAndRenderData('/dashboard/items/top-sold-credit', 'Top 10 Sold Items (Credit)');
            });
            $('#worst-cash-tab').click(function() {

                $('.nav-link').removeClass('active'); // Remove active class from all tabs
                $(this).addClass('active'); // Add active class to the clicked tab

                fetchAndRenderData('/dashboard/items/worst-sold-cash', 'Worst 10 Sold Items (Cash)');
            });
            $('#worst-credit-tab').click(function() {
                $('.nav-link').removeClass('active'); // Remove active class from all tabs
                $(this).addClass('active'); // Add active class to the clicked tab

                fetchAndRenderData('/dashboard/items/worst-sold-credit', 'Worst 10 Sold Items (Credit)');
            });








            // Fetch initial sales data
            fetchSalesLineItemData('1_day'); // Fetch data for the default time range on page load

            // Event listener for dropdown change
            $('#time_range').on('change', function() {
                const selectedTimeRange = $(this).val();
                fetchSalesLineItemData(selectedTimeRange); // Fetch data for the selected time range
            });




            let chart;
            const ctx = document.getElementById('salesChart').getContext('2d');

            // Function to update chart
            function updateChart(labels, cashData, creditData) {
                if (chart) chart.destroy();
                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels, // X-axis labels (Dates or Hours)
                        datasets: [{
                                label: 'Cash Sales',
                                data: cashData,
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1,
                                fill: false
                            },
                            {
                                label: 'Credit Sales',
                                data: creditData,
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1,
                                fill: false
                            }
                        ]
                    },
                    options: {
                        scales: {
                            x: {
                                ticks: {
                                    maxTicksLimit: 12 // Limit the number of ticks to avoid cluttering on the x-axis
                                }
                            },
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Function to fetch sales data
            function fetchSalesLineData(range) {
                let url = `/sales/${range}`;
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        const labels = data.labels;
                        const cashData = data.cash.map(item => item.total);
                        const creditData = data.credit.map(item => item.total);
                        updateChart(labels, cashData, creditData);
                    });
            }

            // Initial load (Monthly sales)
            fetchSalesLineData('monthly');

            // Handle range change
            document.getElementById('timeRange').addEventListener('change', function() {
                fetchSalesLineData(this.value);
            });

            // Handle range change
            document.getElementById('timeRange').addEventListener('change', function() {
                fetchSalesLineData(this.value);
            });

            let salesDoughnutChart, salesBarChart;

            // Function to fetch sales data and update the charts
            function fetchSalesData(filter) {
                $.ajax({
                    url: "{{ route('getSalesData') }}", // Laravel route for fetching sales data
                    method: 'GET',
                    data: {
                        filter: filter
                    },
                    success: function(response) {
                        const labels = ['Cash Sales', 'Credit Sales'];
                        const data = [response.cashSales, response.creditSales];

                        // Doughnut chart configuration
                        const doughnutConfig = {
                            type: 'doughnut',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Sales',
                                    data: data,
                                    backgroundColor: ['#36A2EB',
                                        '#FF6384'
                                    ], // Colors for cash and credit
                                    borderColor: ['#36A2EB', '#FF6384'],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top', // Legend at the top
                                    }
                                }
                            }
                        };

                        // Bar chart configuration
                        const barConfig = {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                        label: 'Cash Sales',
                                        data: [response.cashSales],
                                        backgroundColor: '#36A2EB'
                                    },
                                    {
                                        label: 'Credit Sales',
                                        data: [response.creditSales],
                                        backgroundColor: '#FF6384'
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    }
                                },
                                scales: {
                                    x: {
                                        stacked: true
                                    },
                                    y: {
                                        stacked: true
                                    }
                                }
                            }
                        };

                        // If chart exists, destroy it before creating a new one
                        if (salesDoughnutChart) {
                            salesDoughnutChart.destroy();
                        }
                        if (salesBarChart) {
                            salesBarChart.destroy();
                        }

                        // Create new Doughnut chart
                        const doughnutCtx = document.getElementById('salesDoughnutChart').getContext(
                            '2d');
                        salesDoughnutChart = new Chart(doughnutCtx, doughnutConfig);

                        // Create new Bar chart
                        const barCtx = document.getElementById('salesBarChart').getContext('2d');
                        salesBarChart = new Chart(barCtx, barConfig);
                    },
                    error: function(error) {
                        console.error('Error fetching sales data:', error);
                    }
                });
            }

            // Fetch initial data for '1day' on page load
            fetchSalesData('1day');

            // Handle tab click event
            $('.tab').on('click', function() {
                $('.tab').removeClass('active'); // Remove active class from all tabs
                $(this).addClass('active'); // Add active class to the clicked tab

                // Get the filter value from the clicked tab
                const filter = $(this).data('filter');

                // Fetch and update the chart data
                fetchSalesData(filter);
            });
        });
    </script>
@stop
