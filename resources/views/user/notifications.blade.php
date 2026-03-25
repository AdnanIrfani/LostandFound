<!-- resources/views/user/notifications.blade.php -->
@extends('layouts.user')

@section('title', 'Notifications')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>Notifications</h2>
        <p class="text-muted">Your recent alerts and updates</p>
    </div>
    <div class="col-md-4 text-end">
        @if($notifications->count() > 0)
            <button class="btn btn-outline-primary" id="markAllRead">
                <i class="fas fa-check-double me-2"></i> Mark All as Read
            </button>
        @endif
    </div>
</div>




@if($notifications->count() > 0)
    <div class="card">
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @foreach($notifications as $notification)
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <div class="d-flex">
                                <div class="me-3">
                                    @switch($notification->type)
                                        @case('match_found')
                                            <i class="fas fa-link text-primary fa-2x"></i>
                                            @break
                                        @case('item_verified')
                                            <i class="fas fa-check-circle text-success fa-2x"></i>
                                            @break
                                        @case('item_recovered')
                                            <i class="fas fa-trophy text-warning fa-2x"></i>
                                            @break
                                        @case('admin_verification')
                                            <i class="fas fa-user-shield text-info fa-2x"></i>
                                            @break
                                        @default
                                            <i class="fas fa-bell text-secondary fa-2x"></i>
                                    @endswitch
                                </div>
                                <div class="flex-grow-2">
                                    <h6 class="mb-1">{{ $notification->data['title'] ?? 'Notification' }}</h6>
                                    <p class="mb-1">{{ $notification->data['message'] ?? '{item_name}' }}</p>

@if(
    is_array($notification->data)
    && array_key_exists('other_user', $notification->data)
    && array_key_exists('item', $notification->data)
)
    <div class="card alert-success mt-2 mb-4 p-3">
        <strong>Other User Details</strong><br>
        Name: {{ $notification->data['other_user']['name'] ?? '-' }}<br>
        Email: {{ $notification->data['other_user']['email'] ?? '-' }}<br>
        Phone: {{ $notification->data['other_user']['phone'] ?? '-' }}

        <hr class="my-2">

        <strong>Item Details</strong><br>
<h6 class="mb-1">
    {{ is_array($notification->data['title'] ?? null) 
        ? ($notification->data['title']['text'] ?? 'Notification') 
        : ($notification->data['title'] ?? 'Notification') }}
</h6>

<p class="mb-1">
    {{ is_array($notification->data['message'] ?? null) 
        ? ($notification->data['message']['text'] ?? '') 
        : ($notification->data['message'] ?? '') }}
</p>
    </div>
@endif

                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                        @if($notification->status == 'unread')
                                            <span class="badge bg-danger ms-2">New</span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                            <div class="text-end">
                                <button class="btn btn-sm btn-outline-secondary mark-read-btn" 
                                        data-id="{{ $notification->notification_id }}"
                                        {{ $notification->status == 'read' ? 'disabled' : '' }}>
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-btn" 
                                        data-id="{{ $notification->notification_id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>


    <!-- Pagination -->
    <div class="row mt-4">
        <div class="col-md-12">
            {{ $notifications->links() }}
        </div>
    </div>
@else
    <div class="text-center py-5">
        <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
        <h4>No Notifications</h4>
        <p class="text-muted">You don't have any notifications yet.</p>
    </div>
    
@endif

@push('scripts')
<script>
$(document).ready(function() {
    // Mark as read
    $('.mark-read-btn').click(function() {
        const btn = $(this);
        const id = btn.data('id');
        
        $.ajax({
            url: '{{ route("user.notifications.read", ":id") }}'.replace(':id', id),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                btn.prop('disabled', true);
                btn.closest('.list-group-item').find('.badge').remove();
            }
        });
    });
    
    // Delete notification
    $('.delete-btn').click(function() {
        if (confirm('Are you sure you want to delete this notification?')) {
            const btn = $(this);
            const id = btn.data('id');
            
            $.ajax({
                url: '{{ route("user.notifications.destroy", ":id") }}'.replace(':id', id),
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    btn.closest('.list-group-item').remove();
                }
            });
        }
    });
    
    // Mark all as read
    $('#markAllRead').click(function() {
        if (confirm('Mark all notifications as read?')) {
            $.ajax({
                url: '{{ route("user.notifications.mark-all-read") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('.mark-read-btn').prop('disabled', true);
                    $('.badge.bg-danger').remove();
                }
            });
        }
    });
});
</script>
@endpush
@endsection