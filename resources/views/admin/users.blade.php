<!-- resources/views/admin/users.blade.php -->
@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h3>User Management</h3>
        <p class="text-muted">Manage all registered users</p>
    </div>
    <div class="col-md-4 text-end">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Search users...">
            <button class="btn btn-primary" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Users ({{ $users->total() }})</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Contact</th>
                        <th>Role</th>
                        <th>Stats</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>#{{ $user->user_id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $user->profile_picture_url }}" 
                                         class="rounded-circle me-3" 
                                         width="40" height="40" 
                                         alt="{{ $user->username }}">
                                    <div>
                                        <strong>{{ $user->username }}</strong><br>
                                        <small class="text-muted">{{ $user->email }}</small><br>
                                        @if($user->student_id)
                                            <small class="badge bg-info">{{ $user->student_id }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($user->phone_number)
                                    <i class="fas fa-phone me-1"></i> {{ $user->phone_number }}<br>
                                @endif
                                @if($user->department)
                                    <small class="text-muted">{{ $user->department }} (Year {{ $user->year }})</small>
                                @endif
                            </td>
                            <td>
                                @if($user->isAdmin())
                                    <span class="badge bg-danger">
                                        <i class="fas fa-user-shield me-1"></i> Admin
                                    </span>
                                @else
                                    <span class="badge bg-primary">
                                        <i class="fas fa-user me-1"></i> User
                                    </span>
                                @endif
                            </td>
                            <td>
                                <small>
                                    Lost: {{ $user->lost_items_count }}<br>
                                    Found: {{ $user->found_items_count }}<br>
                                    Recovered: {{ $user->total_recovered_items }}
                                </small>
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i> Active
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-ban me-1"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td>
                                {{ $user->created_at->format('M d, Y') }}<br>
                                <small class="text-muted">
                                    Last: {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                </small>
                            </td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if(!$user->isAdmin() && $user->is_active)
                                    <button class="btn btn-sm btn-danger">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- User Statistics -->
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Total Users</h5>
                <h2>{{ $users->total() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Active Users</h5>
                <h2>{{ $users->where('is_active', true)->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Admins</h5>
                <h2>{{ $users->where('role', 'admin')->count() }}</h2>
            </div>
        </div>
    </div>
</div>
@endsection