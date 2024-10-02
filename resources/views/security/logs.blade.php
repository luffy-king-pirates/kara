@extends('adminlte::page')

@section('title', 'Logs')

@section('content_header')

    <h1>Logs</h1>

@stop

@section('content')
    <div style="height: 700px; overflow-y: auto;">
        <!-- Export Logs Button -->
        @can('export-logs')
            <button id="apply-filter" class="btn btn-success">Export Filtered Logs to Excel</button>
        @endcan

        <!-- Filters for Logs -->
        @include('partials.filter-logs')

        <!-- DataTable for Logs -->
        <button id="reloadTableButton" class="btn btn-primary">
            <i class="fas fa-sync-alt"></i> Reload Table
        </button>
        <div class="container-fluid">
            <table class="table table-bordered dt-responsive nowrap" id="logs-table" style="width: 100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Action</th>
                        <th>User Name</th>
                        <th>Action Time</th>
                        <th>IP Address</th>
                        <th>payload<th>
                    </tr>
                </thead>
            </table>
        </div>

    </div>

    <!-- Revert Confirmation Modal -->
    <div class="modal fade" id="confirmRevertModal" tabindex="-1" aria-labelledby="confirmRevertModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmRevertModalLabel">Confirm Revert</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to revert this action?
                    <input type="hidden" id="revert-log-id" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="confirmRevert">Revert</button>
                </div>
            </div>
        </div>
    </div>

@stop

@section('js')
    @include('partials.import-cdn')

    <script>
        $(function() {
            var table = $('#logs-table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('logs.index') }}",
                    data: function(d) {
                        d.action = $('#filter-action').val();
                        d.user_name = $('#filter-user-name').val();
                        d.action_time = $('#filter-action-time').val();
                        d.ip_address = $('#filter-ip-address').val();
                        d.location = $('#filter-location').val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'action_time',
                        name: 'action_time'
                    },
                    {
                        data: 'ip_address',
                        name: 'ip_address'
                    },

                      {
                        data: 'payload',
                        name: 'payload'
                    },

                ]
            });

            // Filter functionality
            $('#filter-action, #filter-user-name, #filter-action-time, #filter-ip-address, #filter-location')
                .on('keyup change', function() {
                    table.draw();
                });



        });
    </script>

@stop
