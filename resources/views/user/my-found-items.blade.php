<!-- resources/views/user/my-found-items.blade.php -->
@extends('layouts.user')

@section('title', 'My Found Items')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>My Found Items</h2>
        <p class="text-muted">View and manage all items you have found</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('user.found-form') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i> Report New Found Item
        </a>
    </div>
</div>

<!-- Filter Buttons -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="btn-group" role="group">
            <a href="?status=all" class="btn btn-outline-primary {{ request('status') == 'all' || !request('status') ? 'active' : '' }}">
                All ({{ auth()->user()->foundItems()->count() }})
            </a>
            <a href="?status=unclaimed" class="btn btn-outline-warning {{ request('status') == 'unclaimed' ? 'active' : '' }}">
                Unclaimed ({{ auth()->user()->foundItems()->where('status', 'unclaimed')->count() }})
            </a>
            <a href="?status=claimed" class="btn btn-outline-info {{ request('status') == 'claimed' ? 'active' : '' }}">
                Claimed ({{ auth()->user()->foundItems()->where('status', 'claimed')->count() }})
            </a>
            <a href="?status=verified" class="btn btn-outline-primary {{ request('status') == 'verified' ? 'active' : '' }}">
                Verified ({{ auth()->user()->foundItems()->where('status', 'verified')->count() }})
            </a>
            <a href="?status=returned" class="btn btn-outline-success {{ request('status') == 'returned' ? 'active' : '' }}">
                Returned ({{ auth()->user()->foundItems()->where('status', 'returned')->count() }})
            </a>
        </div>
    </div>
</div>

@if($foundItems->count() > 0)
    <div class="row">
        @foreach($foundItems as $item)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($item->item_image)
                        <img src="{{ $item->image_url }}" class="card-img-top" alt="{{ $item->item_name }}" 
                             style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" 
                             style="height: 200px;">
                            <i class="fas fa-image fa-3x text-white"></i>
                        </div>
                    @endif
                    
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title">{{ $item->item_name }}</h5>
                            <span class="badge bg-{{ $item->condition_color }}">
                                {{ ucfirst($item->item_condition) }}
                            </span>
                        </div>
                        
                        <p class="card-text text-muted small">
                            <i class="fas fa-map-marker-alt me-1"></i> {{ $item->found_location }}<br>
                            <i class="fas fa-calendar me-1"></i> {{ $item->found_date->format('M d, Y') }}<br>
                            <i class="fas fa-tag me-1"></i> {{ $item->category->name }}
                        </p>
                        
                        <p class="card-text">{{ Str::limit($item->description, 100) }}</p>
                        
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-archive me-1"></i> 
                                Storage: {{ $item->storage_info }}
                            </small>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-{{ $item->status_color }}">
                                {{ ucfirst($item->status) }}
                            </span>
                            <small class="text-muted">
                                {{ $item->days_found }} days ago
                            </small>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('found-items.show', $item->found_id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                            @if($item->status == 'unclaimed')
                                <a href="{{ route('found-items.edit', $item->found_id) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @endif
                            @if($item->matches->count() > 0)
                                <span class="badge bg-info align-self-center">
                                    {{ $item->matches->count() }} match(es)
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="row mt-4">
        <div class="col-md-12">
            {{ $foundItems->links() }}
        </div>
    </div>
@else
    <div class="text-center py-5">
        <i class="fas fa-hands-helping fa-4x text-muted mb-3"></i>
        <h4>No Found Items</h4>
        <p class="text-muted">
            @if(request('status') && request('status') != 'all')
                You don't have any {{ request('status') }} found items.
            @else
                You haven't reported any found items yet.
            @endif
        </p>
        <a href="{{ route('user.found-form') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i> Report Your First Found Item
        </a>
    </div>
@endif

<!-- Stats Summary -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Summary</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 text-center">
                <h3>{{ auth()->user()->foundItems()->count() }}</h3>
                <p class="text-muted mb-0">Total Found</p>
            </div>
            <div class="col-md-3 text-center">
                <h3>{{ auth()->user()->foundItems()->where('status', 'unclaimed')->count() }}</h3>
                <p class="text-muted mb-0">Awaiting Claim</p>
            </div>
            <div class="col-md-3 text-center">
                <h3>{{ auth()->user()->foundItems()->where('status', 'claimed')->count() }}</h3>
                <p class="text-muted mb-0">Claimed</p>
            </div>
            <div class="col-md-3 text-center">
                <h3>{{ auth()->user()->foundItems()->where('status', 'returned')->count() }}</h3>
                <p class="text-muted mb-0">Returned</p>
            </div>
        </div>
    </div>
</div>
@endsection