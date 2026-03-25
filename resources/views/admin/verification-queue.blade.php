<!-- resources/views/admin/verification-queue.blade.php -->
@extends('layouts.admin')

@section('title', 'Verification Queue')

@section('content')


@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="row mb-4">
    <div class="col-md-8">
        <h3>Verification Queue</h3>
        <p class="text-muted">Review and verify matches between lost and found items</p>
    </div>
    <div class="col-md-4 text-end">
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-filter me-2"></i> Filter
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">All Matches</a></li>
                <li><a class="dropdown-item" href="#">Pending Only</a></li>
                <li><a class="dropdown-item" href="#">High Priority</a></li>
            </ul>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Pending Items Summary -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="mb-0 text-white"><i class="fas fa-exclamation-circle me-2"></i> Unverified Lost Items</h5>
            </div>
            <div class="card-body">
                @if($pendingItems['lost']->count() > 0)
                    <div class="list-group">
                        @foreach($pendingItems['lost'] as $item)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $item->item_name }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i> {{ $item->user->username }}
                                            | <i class="fas fa-calendar me-1"></i> {{ $item->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <span class="badge bg-{{ $item->urgency_color }}">
                                        {{ ucfirst($item->urgency) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="text-muted mb-0">All lost items are verified</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info">
                <h5 class="mb-0 text-white"><i class="fas fa-check-circle me-2"></i> Unverified Found Items</h5>
            </div>
            <div class="card-body">
                @if($pendingItems['found']->count() > 0)
                    <div class="list-group">
                        @foreach($pendingItems['found'] as $item)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $item->item_name }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i> {{ $item->user->username }}
                                            | <i class="fas fa-calendar me-1"></i> {{ $item->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <span class="badge bg-{{ $item->condition_color }}">
                                        {{ ucfirst($item->item_condition) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="text-muted mb-0">All found items are verified</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Matches Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Pending Matches for Verification</h5>
    </div>
    <div class="card-body">
 @if($matches->count() > 0 || 
    $pendingItems['lost']->count() > 0 || 
    $pendingItems['found']->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover data-table">
                    <thead>
                        <tr>
                            <th>Match ID</th>
                            <th>Lost Item</th>
                            <th>Found Item</th>
                            <th>Similarity</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($matches as $match)
                            <tr>
                                <td>#{{ $match->match_id }}</td>
                                <td>
                                    <strong>{{ $match->lostItem->item_name }}</strong><br>
                                    <small class="text-muted">
                                        Owner: {{ $match->lostItem->user->username }}<br>
                                        Lost: {{ $match->lostItem->lost_location }}
                                    </small>
                                </td>
                                <td>
                                    <strong>{{ $match->foundItem->item_name }}</strong><br>
                                    <small class="text-muted">
                                        Finder: {{ $match->foundItem->user->username }}<br>
                                        Found: {{ $match->foundItem->found_location }}
                                    </small>
                                </td>
                                <td>
                                    @php
                                     $percent = round($match->similarity_score > 1 ? $match->similarity_score : $match->similarity_score * 100);
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar
                                            @if($percent >= 80) bg-success
                                            @elseif($percent >= 60) bg-warning
                                            @else bg-danger @endif"
                                            style="width: {{ $percent }}%">
                                            {{ $percent }}%
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $match->match_reason }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $match->match_status == 'pending' ? 'warning' : 
                                        ($match->match_status == 'review' ? 'info' : 
                                        ($match->match_status == 'verified' ? 'success' : 'danger')) 
                                    }}">
                                        {{ ucfirst($match->match_status) }}
                                    </span>
                                </td>
                                <td>{{ $match->created_at->format('M d, Y') }}</td>
                                <td class="table-actions">
                                    <button type="button" class="btn btn-sm btn-success" 
                                            data-bs-toggle="modal" data-bs-target="#verifyModal{{ $match->match_id }}">
                                        <i class="fas fa-check"></i> Verify
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#rejectModal{{ $match->match_id }}">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                    <a href="#" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Details
                                    </a>
                                </td>
                            </tr>

                            <!-- Verify Modal -->
                            <div class="modal fade" id="verifyModal{{ $match->match_id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Verify Match #{{ $match->match_id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ url('/admin/verify-match') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $match->match_id }}">

                                            <div class="modal-body">
                                                <p>Are you sure you want to verify this match?</p>
                                                
                                                <!-- <div class="mb-3">
                                                    <label for="verification_notes" class="form-label">Verification Notes</label>
                                                    <textarea class="form-control" id="verification_notes" 
                                                              name="verification_notes" rows="3" 
                                                              placeholder="Optional notes about this verification..."></textarea>
                                                </div>
                                                
                                                <div class="alert alert-info">
                                                    <h6>Verification will:</h6>
                                                    <ul class="mb-0">
                                                        <li>Mark both items as verified</li>
                                                        <li>Send notifications to both users</li>
                                                        <li>Share contact details between users</li>
                                                    </ul>
                                                </div> -->
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success">Confirm Verification</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                           
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $matches->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h4>No Pending Matches</h4>
                <p class="text-muted">All matches have been verified. Great job!</p>
            </div>
        @endif
    </div>
</div>

<!-- Verification Guidelines -->
<div class="card mt-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Verification Guidelines</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="alert alert-success">
                    <h6><i class="fas fa-check-circle me-2"></i> Verify When:</h6>
                    <ul class="mb-0">
                        <li>Items descriptions match</li>
                        <li>Location/time are consistent</li>
                        <li>Similarity score > 70%</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-warning">
                    <h6><i class="fas fa-question-circle me-2"></i> Review When:</h6>
                    <ul class="mb-0">
                        <li>Similarity 50-70%</li>
                        <li>Incomplete information</li>
                        <li>Needs user confirmation</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-danger">
                    <h6><i class="fas fa-times-circle me-2"></i> Reject When:</h6>
                    <ul class="mb-0">
                        <li>Clearly different items</li>
                        <li>Mismatched locations/times</li>
                        <li>Fake/spam reports</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection