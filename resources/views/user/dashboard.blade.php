<!-- resources/views/user/dashboard.blade.php -->
@extends('layouts.user')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<div class="hero-section rounded-3 mb-4">
    <div class="container text-center">
        <h1 class="display-5 fw-bold">Welcome to Lost & Found Portal</h1>
        <p class="lead">Find your lost items or help others find theirs</p>
        <div class="row mt-4 justify-content-center">
            <div class="col-md-3">
                <a href="{{ route('user.lost-form') }}" class="btn btn-light btn-lg w-100">
                    <i class="fas fa-exclamation-circle me-2"></i> Report Lost
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('user.found-form') }}" class="btn btn-outline-light btn-lg w-100">
                    <i class="fas fa-check-circle me-2"></i> Report Found
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body text-center">
                <h6 class="card-title">My Lost Items</h6>
                <h3>{{ auth()->user()->lostItems()->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body text-center">
                <h6 class="card-title">My Found Items</h6>
                <h3>{{ auth()->user()->foundItems()->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body text-center">
                <h6 class="card-title">Recovered Items</h6>
                <h3>{{ auth()->user()->total_recovered_items }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body text-center">
                <h6 class="card-title">Recovery Rate</h6>
                <h3>{{ auth()->user()->recovery_rate }}%</h3>
            </div>
        </div>
    </div>
</div>

<!-- Success Stories Section -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="fas fa-trophy text-warning me-2"></i> Recently Recovered Items
        </h5>
    </div>
    <div class="card-body">
        @if($successStories->count() > 0)
            <div class="row">
                @foreach($successStories as $story)
                    <div class="col-md-4 mb-3">
                        <div class="card card-hover h-100">
                            <div class="card-body">
                                <h6 class="card-title">{{ $story->lostItem->item_name }}</h6>
                                <p class="card-text text-muted small">
                                    <i class="fas fa-calendar me-1"></i> 
                                    Recovered after {{ $story->days_to_recover }} days
                                </p>
                                @if($story->testimonial)
                                    <p class="card-text">
                                        "{{ Str::limit($story->testimonial, 100) }}"
                                    </p>
                                @endif
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        By: {{ $story->lostItem->user->username }}
                                    </small>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check"></i> Recovered
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">No success stories yet. Be the first to recover an item!</p>
            </div>
        @endif
    </div>
</div>

<!-- My Recent Items -->
<div class="row">
    <!-- My Recent Lost Items -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-circle text-danger me-2"></i> My Recent Lost Items
                </h5>
            </div>
            <div class="card-body">
                @if($userLostItems->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($userLostItems as $item)
                            <a href="{{ route('lost-items.show', $item->lost_id) }}" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $item->item_name }}</h6>
                                    <span class="badge bg-{{ $item->urgency_color }}">
                                        {{ ucfirst($item->urgency) }}
                                    </span>
                                </div>
                                <p class="mb-1 text-muted small">
                                    <i class="fas fa-map-marker-alt me-1"></i> {{ $item->lost_location }}
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i> 
                                    {{ $item->lost_date->format('M d, Y') }}
                                </small>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No lost items reported yet</p>
                        <a href="{{ route('user.lost-form') }}" class="btn btn-sm btn-outline-primary mt-2">
                            Report a Lost Item
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- My Recent Found Items -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-check-circle text-success me-2"></i> My Recent Found Items
                </h5>
            </div>
            <div class="card-body">
                @if($userFoundItems->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($userFoundItems as $item)
                            <a href="{{ route('found-items.show', $item->found_id) }}" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $item->item_name }}</h6>
                                    <span class="badge bg-{{ $item->status == 'claimed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </div>
                                <p class="mb-1 text-muted small">
                                    <i class="fas fa-map-marker-alt me-1"></i> {{ $item->found_location }}
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i> 
                                    {{ $item->found_date->format('M d, Y') }}
                                </small>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-hands-helping fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No found items reported yet</p>
                        <a href="{{ route('user.found-form') }}" class="btn btn-sm btn-outline-success mt-2">
                            Report a Found Item
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Matches -->
@if($userMatches->count() > 0)
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="fas fa-link text-primary me-2"></i> Recent Matches
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Lost Item</th>
                        <th>Found Item</th>
                        <th>Similarity</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($userMatches as $match)
                        <tr>
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
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>
@endif
@endsection