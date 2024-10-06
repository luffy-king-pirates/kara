@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Stock Transfer Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <label for="from_date">From Date</label>
            <input type="date" id="from_date" class="form-control" value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
        </div>
        <div class="col-md-4">
            <label for="to_date">To Date</label>
            <input type="date" id="to_date" class="form-control" value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
        </div>
        <div class="col-md-4">
            <label for="item_id">Item</label>
            <select id="item_id" class="form-control">
                <option value="all">All Items</option>
                @foreach ($items as $item)
                    <option value="{{ $item->id }}">{{ $item->item_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row" id="transfert-stats-container">
        <!-- Cards will be dynamically inserted here -->
    </div>
    <div class="row">
        <!-- Doughnut Chart Card -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Transfers Distribution (Doughnut Chart)
                </div>
                <div class="card-body">
                    <canvas id="doughnutChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Line Chart Card -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Total Transfers (Line Chart)
                </div>
                <div class="card-body">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let doughnutChart;
        let lineChart;

        function fetchTransferData() {
            const fromDate = $('#from_date').val();
            const toDate = $('#to_date').val();
            const itemId = $('#item_id').val();

            // AJAX request to fetch transfer stats
            $.ajax({
                url: '/dashboard-stock-transfert/transfert-stats',
                method: 'GET',
                data: {
                    from_date: fromDate,
                    to_date: toDate,
                    item_id: itemId
                },
                dataType: 'json',
                success: function(data) {
                    let container = $('#transfert-stats-container');
                    container.empty(); // Clear the container

                    // Loop through the data and generate the cards dynamically
                    $.each(data, function(route, stat) {
                        let card = `
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-header">
                                    ${stat.route.replace('_', ' ')}
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">Total Transfers: ${stat.total_quantity}</h5>
                                </div>
                            </div>
                        </div>
                    `;
                        container.append(card); // Add the card to the container
                    });

                    // Prepare data for Doughnut and Line charts
                    const labels = [];
                    const todayData = []; // For Doughnut Chart
                    const lineChartData = []; // For Line Chart

                    $.each(data, function(route, stat) {
                        labels.push(stat.route.replace('_', ' ')); // Label for each transfer route
                        todayData.push(stat.total_quantity); // Total transfer quantities for Doughnut
                        lineChartData.push(stat.total_quantity); // Total transfer quantities for Line Chart
                    });

                    // Destroy existing doughnut chart if it exists
                    if (doughnutChart) {
                        doughnutChart.destroy();
                    }
                    // Initialize Doughnut Chart
                    const doughnutCtx = document.getElementById('doughnutChart').getContext('2d');
                    doughnutChart = new Chart(doughnutCtx, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Total Transfers',
                                data: todayData,
                                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'], // Different colors for each route
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });

                    // Destroy existing line chart if it exists
                    if (lineChart) {
                        lineChart.destroy();
                    }
                    // Initialize Line Chart with total quantities
                    const lineCtx = document.getElementById('lineChart').getContext('2d');
                    lineChart = new Chart(lineCtx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Total Transfers',
                                data: lineChartData,
                                borderColor: '#36A2EB',
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                }
                            }
                        }
                    });
                },
                error: function(error) {
                    console.log("Error fetching transfer stats:", error);
                }
            });
        }

        $(document).ready(function() {
            // Fetch initial data
            fetchTransferData();

            // Event listeners for input changes
            $('#from_date, #to_date, #item_id').change(fetchTransferData);
        });
    </script>
@stop
