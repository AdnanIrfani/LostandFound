<!-- resources/views/user/search-results.blade.php -->
@extends('layouts.user')

@section('title', 'Search Results')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>Search Results</h2>
        <p class="text-muted">
            @if($type == 'lost')
                Searching lost items for: "{{ $query }}"
            @else
                Searching found items for: "{{ $query }}"
            @endif
        </p>
    </div>
    <div class="col-md-4 text-end">
        <div class="btn-group" role="group">
            <a href="{{ route('user.search') }}?type=lost&q={{ $query }}" 
               class="btn {{ $type == 'lost' ? 'btn-primary' : 'btn-outline-primary' }}">
                Lost Items
            </a>
            <a href="{{ route('user.search') }}?type=found&q={{ $query }}" 
               class="btn {{ $type == 'found' ? 'btn-success' : 'btn-outline-success' }}">
                Found Items
            </a>
        </div>
    </div>
</div>

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('user.search') }}" method="GET">
            <div class="input-group">
                <input type="text" class="form-control" name="q" 
                       placeholder="Search items by name, description, or location..." 
                       value="{{ $query }}">
                <select class="form-control w-auto" name="type">
                    <option value="lost" {{ $type == 'lost' ? 'selected' : '' }}>Lost Items</option>
                    <option value="found" {{ $type == 'found' ? 'selected' : '' }}>Found Items</option>
                </select>
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

@if($items->count() > 0)
    <div class="row">
        @foreach($items as $item)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($item->item_image)
                        <img src="{{ $type == 'lost' ? $item->image_url : $item->image_url }}" 
                             class="card-img-top" alt="{{ $item->item_name }}" 
                             style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" 
                             style="height: 200px;">
                            <i class="fas fa-image fa-3x text-white"></i>
                        </div>
                    @endif
                    
                    <div class="card-body">
                        <h5 class="card-title">{{ $item->item_name }}</h5>
                        
                        <p class="card-text text-muted small">
                            @if($type == 'lost')
                                <i class="fas fa-map-marker-alt me-1"></i> {{ $item->lost_location }}<br>
                                <i class="fas fa-calendar me-1"></i> {{ $item->lost_date->format('M d, Y') }}<br>
                            @else
                                <i class="fas fa-map-marker-alt me-1"></i> {{ $item->found_location }}<br>
                                <i class="fas fa-calendar me-1"></i> {{ $item->found_date->format('M d, Y') }}<br>
                            @endif
                            <i class="fas fa-user me-1"></i> {{ $item->user->username }}
                        </p>
                        
                        <p class="card-text">{{ Str::limit($item->description, 100) }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-info">{{ $item->category->name }}</span>
                            @if($type == 'lost')
                                <span class="badge bg-{{ $item->urgency_color }}">
                                    {{ ucfirst($item->urgency) }}
                                </span>
                            @else
                                <span class="badge bg-{{ $item->condition_color }}">
                                    {{ ucfirst($item->item_condition) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <a href="{{ $type == 'lost' ? route('lost-items.show', $item->lost_id) : route('found-items.show', $item->found_id) }}" 
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
            {{ $items->links() }}
        </div>
    </div>
@else
    <div class="text-center py-5">
        <i class="fas fa-search fa-4x text-muted mb-3"></i>
        <h4>No Items Found</h4>
        <p class="text-muted">
            No {{ $type }} items found matching "{{ $query }}"
        </p>
        <a href="{{ route('user.dashboard') }}" class="btn btn-primary">
            <i class="fas fa-home me-2"></i> Back to Dashboard
        </a>
    </div>
@endif
@endsection