<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin | Lost & Found Portal</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            padding: 0.75rem 1rem;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background: #495057;
        }
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .stat-card {
            border-radius: 10px;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .table-actions {
            white-space: nowrap;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 col-lg-2 p-0 sidebar">
                <div class="p-3 text-white">
                    <h5 class="text-center mb-4">
                        <i class="fas fa-search"></i> Admin Panel
                    </h5>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.verifications') ? 'active' : '' }}" 
                       href="{{ route('admin.verifications') }}">
                        <i class="fas fa-check-circle"></i> Verifications
                        @if($pendingCount = \App\Models\ItemMatch::pending()->count())
                            <span class="badge bg-danger float-end">{{ $pendingCount }}</span>
                        @endif
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}" 
                       href="{{ route('admin.users') }}">
                        <i class="fas fa-users"></i> Users
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.categories') ? 'active' : '' }}" 
                       href="{{ route('admin.categories') }}">
                        <i class="fas fa-tags"></i> Categories
                    </a>
                    <!-- <a class="nav-link {{ request()->routeIs('admin.success-stories') ? 'active' : '' }}" 
                       href="{{ route('admin.success-stories') }}">
                        <i class="fas fa-trophy"></i> Success Stories
                    </a> -->
                    <a class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}" 
                       href="{{ route('admin.reports') }}">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                    <hr class="text-white">
                    <a class="nav-link" href="{{ route('user.dashboard') }}">
                        <i class="fas fa-exchange-alt"></i> Switch to User
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link text-start w-100" style="background: none; border: none;">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 col-lg-10 p-0 main-content">
                <!-- Top Bar -->
                <nav class="navbar navbar-light bg-white shadow-sm">
                    <div class="container-fluid">
                        <span class="navbar-brand mb-0 h6">
                            <i class="fas fa-user-cog"></i> Welcome, {{ auth()->user()->username }}
                        </span>
                        <div class="d-flex">
                            <span class="badge bg-primary me-3">
                                <i class="fas fa-shield-alt"></i> Administrator
                            </span>
                            
                        </div>
                    </div>
                </nav>

                <!-- Content -->
                <div class="container-fluid p-4">
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
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    
    <script>
        $(document).ready(function() {
            $('.data-table').DataTable({
                pageLength: 10,
                responsive: true
            });
        });
        
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    </script>
    
    @stack('scripts')
</body>
</html>