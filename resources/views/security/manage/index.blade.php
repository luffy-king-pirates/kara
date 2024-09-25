@extends('adminlte::page')

@section('title', 'Manage Permissions')

@section('content_header')
    <h1 class="m-0 text-dark">Manage Permissions</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            @foreach($roles as $role)
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-user-shield"></i> {{ $role->role_name }}
                            </h3>
                            <span class="badge bg-warning">{{ $role->permissions_count }} Permissions</span>
                        </div>
                        <div class="card-body">
                            <p>{{ Str::limit($role->description, 100, '...') }}</p>
                        </div>
                        <div class="card-footer text-right">
                            <a href="{{ route('managePermissions.show', $role->role_name) }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@stop
