@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Stock Transfert Dashboard</h1>
@stop

@section('content')
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
                    Percentage Change (Line Chart)
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
        $(document).ready(function() {
            // Make the AJAX request when the page loads
            $.ajax({
                url: '/dashboard-stock-transfert/transfert-stats', // URL to your route
                method: 'GET',
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
                                    ${route.replace('_', ' ')}
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">Today's Transfers: ${stat.today}</h5>
                                    <p class="card-text">
                                        Percentage Change:
                                        ${stat.percentage_change > 0
                                            ? '<span class="text-success">+' + stat.percentage_change + '%</span>'
                                            : '<span class="text-danger">' + stat.percentage_change + '%</span>'}
                                    </p>
                                </div>
                            </div>
                        </div>
                    `;
                        container.append(card); // Add the card to the container
                    });
                },
                error: function(error) {
                    console.log("Error fetching transfert stats:", error);
                }
            });
        });


        $(document).ready(function() {
            // AJAX request to fetch transfer stats
            $.ajax({
                url: '/dashboard-stock-transfert/transfert-stats',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Prepare data for Doughnut chart
                    const labels = [];
                    const todayData = [];
                    const percentageChange = [];

                    $.each(data, function(route, stat) {
                        labels.push(route.replace('_', ' ')); // Label for each transfer route
                        todayData.push(stat.today); // Today's transfer numbers
                        percentageChange.push(stat.percentage_change); // Percentage changes
                    });

                    // Initialize Doughnut Chart
                    const doughnutCtx = document.getElementById('doughnutChart').getContext('2d');
                    const doughnutChart = new Chart(doughnutCtx, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Today\'s Transfers',
                                data: todayData,
                                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56',
                                    '#4BC0C0'
                                ], // Different colors for each route
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });

                    // Initialize Line Chart
                    const lineCtx = document.getElementById('lineChart').getContext('2d');
                    const lineChart = new Chart(lineCtx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Percentage Change',
                                data: percentageChange,
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
                                    ticks: {
                                        callback: function(value) {
                                            return value + '%'; // Add percentage symbol
                                        }
                                    }
                                }
                            }
                        }
                    });
                },
                error: function(error) {
                    console.log("Error fetching transfert stats:", error);
                }
            });
        });
    </script>
@stop
