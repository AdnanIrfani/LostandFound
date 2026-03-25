<!-- resources/views/user/my-lost-items.blade.php -->
@extends('layouts.user')

@section('title', 'My Lost Items')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>My Lost Items</h2>
        <p class="text-muted">View and manage all items you have reported as lost</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('user.lost-form') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Report New Lost Item
        </a>
    </div>
</div>

<!-- Filter Buttons -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="btn-group" role="group">
            <a href="?status=all" class="btn btn-outline-primary {{ request('status') == 'all' || !request('status') ? 'active' : '' }}">
                All ({{ auth()->user()->lostItems()->count() }})
            </a>
            <a href="?status=open" class="btn btn-outline-warning {{ request('status') == 'open' ? 'active' : '' }}">
                Open ({{ auth()->user()->lostItems()->where('status', 'open')->count() }})
            </a>
            <a href="?status=matched" class="btn btn-outline-info {{ request('status') == 'matched' ? 'active' : '' }}">
                Matched ({{ auth()->user()->lostItems()->where('status', 'matched')->count() }})
            </a>
            <a href="?status=verified" class="btn btn-outline-primary {{ request('status') == 'verified' ? 'active' : '' }}">
                Verified ({{ auth()->user()->lostItems()->where('status', 'verified')->count() }})
            </a>
            <a href="?status=recovered" class="btn btn-outline-success {{ request('status') == 'recovered' ? 'active' : '' }}">
                Recovered ({{ auth()->user()->lostItems()->where('status', 'recovered')->count() }})
            </a>
            <a href="?status=closed" class="btn btn-outline-secondary {{ request('status') == 'closed' ? 'active' : '' }}">
                Closed ({{ auth()->user()->lostItems()->where('status', 'closed')->count() }})
            </a>
        </div>
    </div>
</div>

@if($lostItems->count() > 0)
    <div class="row">
        @foreach($lostItems as $item)
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
                            <span class="badge bg-{{ $item->urgency_color }}">
                                {{ ucfirst($item->urgency) }}
                            </span>
                        </div>
                        
                        <p class="card-text text-muted small">
                            <i class="fas fa-map-marker-alt me-1"></i> {{ $item->lost_location }}<br>
                            <i class="fas fa-calendar me-1"></i> {{ $item->lost_date->format('M d, Y') }}<br>
                            <i class="fas fa-tag me-1"></i> {{ $item->category->name }}
                        </p>
                        
                        <p class="card-text">{{ Str::limit($item->description, 100) }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-{{ 
                                $item->status == 'open' ? 'warning' : 
                                ($item->status == 'matched' ? 'info' : 
                                ($item->status == 'verified' ? 'primary' : 
                                ($item->status == 'recovered' ? 'success' : 'secondary'))) 
                            }}">
                                {{ ucfirst($item->status) }}
                            </span>
                            @if($item->reward)
                                <span class="text-success fw-bold">Reward: ₹{{ $item->reward }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('lost-items.show', $item->lost_id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                            @if($item->status == 'open')
                                <a href="{{ route('lost-items.edit', $item->lost_id) }}" class="btn btn-sm btn-outline-warning">
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
            {{ $lostItems->links() }}
        </div>
    </div>
@else
    <div class="text-center py-5">
        <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
        <h4>No Lost Items Found</h4>
        <p class="text-muted">
            @if(request('status') && request('status') != 'all')
                You don't have any {{ request('status') }} lost items.
            @else
                You haven't reported any lost items yet.
            @endif
        </p>
        <a href="{{ route('user.lost-form') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Report Your First Lost Item
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
                <h3>{{ auth()->user()->lostItems()->count() }}</h3>
                <p class="text-muted mb-0">Total Reported</p>
            </div>
            <div class="col-md-3 text-center">
                <h3>{{ auth()->user()->lostItems()->where('status', 'open')->count() }}</h3>
                <p class="text-muted mb-0">Still Missing</p>
            </div>
            <div class="col-md-3 text-center">
                <h3>{{ auth()->user()->lostItems()->whereIn('status', ['matched', 'verified'])->count() }}</h3>
                <p class="text-muted mb-0">In Progress</p>
            </div>
            <div class="col-md-3 text-center">
                <h3>{{ auth()->user()->lostItems()->where('status', 'recovered')->count() }}</h3>
                <p class="text-muted mb-0">Recovered</p>
            </div>
        </div>
    </div>
</div>
@endsection