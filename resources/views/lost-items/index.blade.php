<!-- resources/views/lost-items/index.blade.php -->
@extends('layouts.user')

@section('title', 'Browse Lost Items')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>Lost Items</h2>
        <p class="text-muted">Browse all reported lost items</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('user.lost-form') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Report Lost Item
        </a>
    </div>
</div>

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('lost-items.index') }}">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Search items..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <select class="form-control" name="category">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_id }}" 
                                {{ request('category') == $category->category_id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <select class="form-control" name="status">
                        <option value="">All Status</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="matched" {{ request('status') == 'matched' ? 'selected' : '' }}>Matched</option>
                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($lostItems->count() > 0)
    <div class="row">
        @foreach($lostItems as $item)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($item->item_image)
                        <img src="{{ $item->image_url }}" class="card-img-top" 
                             alt="{{ $item->item_name }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" 
                             style="height: 200px;">
                            <i class="fas fa-image fa-3x text-white"></i>
                        </div>
                    @endif
                    
                    <div class="card-body">
                        <h5 class="card-title">{{ $item->item_name }}</h5>
                        
                        <p class="card-text text-muted small">
                            <i class="fas fa-map-marker-alt me-1"></i> {{ $item->lost_location }}<br>
                            <i class="fas fa-calendar me-1"></i> {{ $item->lost_date->format('M d, Y') }}<br>
                            <i class="fas fa-user me-1"></i> {{ $item->user->username }}
                        </p>
                        
                        <p class="card-text">{{ Str::limit($item->description, 100) }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-info">{{ $item->category->name }}</span>
                            <span class="badge bg-{{ $item->urgency_color }}">
                                {{ ucfirst($item->urgency) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <a href="{{ route('lost-items.show', $item->lost_id) }}" 
                           class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-eye me-2"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="row mt-4">
        <div class="col-md-12">
            {{ $lostItems->links() }}
        </div>
    </div>
@else
    <div class="text-center py-5">
        <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
        <h4>No Lost Items Found</h4>
        <p class="text-muted">
            @if(request()->has('search') || request()->has('category') || request()->has('status'))
                No items match your search criteria.
            @else
                No lost items have been reported yet.
            @endif
        </p>
    </div>
@endif
@endsection