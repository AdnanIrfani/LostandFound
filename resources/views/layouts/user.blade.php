<!-- resources/views/layouts/user.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Lost & Found Portal</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .navbar-brand {
            font-weight: bold;
            color: #0d6efd !important;
        }
        .nav-link {
            font-weight: 500;
            padding: 0.5rem 1rem !important;
        }
        .nav-link:hover {
            background-color: rgba(13, 110, 253, 0.1);
            border-radius: 5px;
        }
        .notification-badge {
            position: relative;
            top: -10px;
            right: 5px;
            font-size: 0.7rem;
        }
        .hero-section {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 4rem 0;
            margin-bottom: 2rem;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            transition: transform 0.3s;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .footer {
            background: black;
            color:white;
            border-top: 1px solid #dee2e6;
            margin-top: 3rem;
            padding: 2rem 0;
        }
        .quick-action-btn {
            padding: 1rem;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }
        .quick-action-btn:hover {
            transform: scale(1.05);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-black shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('user.dashboard') }}">
                <i class="fas fa-search"></i> Lost & Found Portal
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" 
                           href="{{ route('user.dashboard') }}">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('user.lost-form') ? 'active' : '' }}" 
                           href="{{ route('user.lost-form') }}">
                            <i class="fas fa-exclamation-circle"></i> Report Lost
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('user.found-form') ? 'active' : '' }}" 
                           href="{{ route('user.found-form') }}">
                            <i class="fas fa-check-circle"></i> Report Found
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('user.my-lost-items') ? 'active' : '' }}" 
                           href="{{ route('user.my-lost-items') }}">
                            <i class="fas fa-list"></i> My Lost Items
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('user.my-found-items') ? 'active' : '' }}" 
                           href="{{ route('user.my-found-items') }}">
                            <i class="fas fa-list"></i> My Found Items
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('user.search') ? 'active' : '' }}" 
                           href="{{ route('user.search') }}">
                            <i class="fas fa-search"></i> Search
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                           data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            @if (Schema::hasTable('notifications'))
                                {{ auth()->user()->unreadNotifications->count() }}
                                <span class="badge bg-danger notification-badge"></span>
                            @endif 
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <h6 class="dropdown-header">Notifications</h6>
                            <!-- @if (Schema::hasTable('notifications'))
                           @foreach(auth()->user()->notifications()->latest()->limit(5)->get() as $notification)
                                <a class="dropdown-item" href="{{ route('user.notifications') }}">
                                    <small class="{{ $notification->status == 'unread' ? 'fw-bold' : '' }}">
                                        {{ Str::limit($notification->message, 50) }}
                                    </small>
                                </a>
                            @endforeach
                            @endif -->
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="{{ route('user.notifications') }}">
                                View All Notifications
                            </a>
                        </div>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" 
                           data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> {{ auth()->user()->username }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('user.profile') }}">
                                <i class="fas fa-user"></i> Profile
                            </a>
                            <a class="dropdown-item" href="{{ route('user.notifications') }}">
                                <i class="fas fa-bell"></i> Notifications
                            </a>
                            <div class="dropdown-divider"></div>
                            @if(auth()->user()->isAdmin())
                            <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-cog"></i> Admin Panel
                            </a>
                            <div class="dropdown-divider"></div>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container my-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Lost & Found Portal</h5>
                    <p class="text-muted">College Campus Item Recovery System</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="text-muted">
                        <i class="fas fa-graduation-cap"></i> College Campus &copy; {{ date('Y') }}
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
        
        // Live notification count update
        function updateNotificationCount() {
            $.ajax({
                url: '{{ route("user.notifications.unread-count") }}',
                method: 'GET',
                success: function(response) {
                    const badge = $('.notification-badge');
                    if (response.count > 0) {
                        badge.text(response.count).show();
                    } else {
                        badge.hide();
                    }
                }
            });
        }
        
        // Update every 30 seconds
        setInterval(updateNotificationCount, 30000);
    </script>
    
    @stack('scripts')
</body>
</html>