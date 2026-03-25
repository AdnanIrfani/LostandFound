<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="row mb-4">
    <div class="col-md-12">
        <h3>Admin Dashboard</h3>
        <p class="text-muted">Welcome to the admin panel</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Users</h5>
                        <h2 class="mb-0">{{ $stats['total_users'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                </div>
                <small class="opacity-75">Registered users</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Lost Items</h5>
                        <h2 class="mb-0">{{ $stats['total_lost_items'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-circle fa-3x opacity-50"></i>
                    </div>
                </div>
                <small class="opacity-75">Total reported</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Found Items</h5>
                        <h2 class="mb-0">{{ $stats['total_found_items'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
                <small class="opacity-75">Total reported</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Pending Matches</h5>
                        <h2 class="mb-0">{{ $stats['pending_matches'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-3x opacity-50"></i>
                    </div>
                </div>
                <small class="opacity-75">Awaiting verification</small>
            </div>
        </div>
    </div>
</div>

<!-- More Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stat-card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Verifications</h5>
                        <h2 class="mb-0">{{ $stats['pending_verifications'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-tasks fa-3x opacity-50"></i>
                    </div>
                </div>
                <small class="opacity-75">Pending verification</small>
            </div>
        </div>
    </div>

    <!-- <div class="col-md-4">
        <div class="card stat-card bg-secondary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Success Stories</h5>
                        <h2 class="mb-0">{{ $stats['recent_success_stories'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-trophy fa-3x opacity-50"></i>
                    </div>
                </div>
                <small class="opacity-75">Recovered items</small>
            </div>
        </div>
    </div> -->

    <div class="col-md-4">
        <div class="card stat-card bg-dark text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Active Today</h5>
                        <h2 class="mb-0">{{ $stats['active_today'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-check fa-3x opacity-50"></i>
                    </div>
                </div>
                <small class="opacity-75">Users logged in today</small>
            </div>
        </div>
    </div>
</div>

<!-- Recent Matches -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Matches</h5>
            </div>
            <div class="card-body">
                @if($recentMatches->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Lost Item</th>
                                    <th>Found Item</th>
                                    <th>Similarity</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentMatches as $match)
                                    <tr>
                                        <td>#{{ $match->match_id }}</td>
                                        <td>{{ $match->lostItem->item_name }}</td>
                                        <td>{{ $match->foundItem->item_name }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" 
                                                     style="width: {{ $match->similarity_score }}%">
                                                    {{ $match->similarity_score }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $match->match_status == 'verified' ? 'success' : 'warning' }}">
                                                {{ ucfirst($match->match_status) }}
                                            </span>
                                        </td>
                                        <td>{{ $match->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.verifications') }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No matches found</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Items -->
<div class="row">
    <!-- Recent Lost Items -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Recent Lost Items</h5>
            </div>
            <div class="card-body">
                @if($recentItems['lost']->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentItems['lost'] as $item)
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $item->item_name }}</h6>
                                    <span class="badge bg-{{ $item->urgency_color }}">
                                        {{ ucfirst($item->urgency) }}
                                    </span>
                                </div>
                                <p class="mb-1 text-muted small">
                                    <i class="fas fa-user me-1"></i> {{ $item->user->username }}
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i> 
                                    {{ $item->created_at->format('M d, Y') }}
                                </small>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No lost items reported</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Found Items -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Recent Found Items</h5>
            </div>
            <div class="card-body">
                @if($recentItems['found']->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentItems['found'] as $item)
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $item->item_name }}</h6>
                                    <span class="badge bg-{{ $item->status_color }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </div>
                                <p class="mb-1 text-muted small">
                                    <i class="fas fa-user me-1"></i> {{ $item->user->username }}
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i> 
                                    {{ $item->created_at->format('M d, Y') }}
                                </small>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-hands-helping fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No found items reported</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.verifications') }}" class="btn btn-primary w-100">
                            <i class="fas fa-check-circle me-2"></i> Verify Matches
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.users') }}" class="btn btn-success w-100">
                            <i class="fas fa-users me-2"></i> Manage Users
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.categories') }}" class="btn btn-warning w-100">
                            <i class="fas fa-tags me-2"></i> Categories
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.reports') }}" class="btn btn-info w-100">
                            <i class="fas fa-chart-bar me-2"></i> View Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection