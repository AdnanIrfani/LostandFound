<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Lost & Found Portal</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: none;
            
        }
        .login-header {
            background: #0d6efd;
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 2rem;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .login-footer {
            background: #f8f9fa;
            border-radius: 0 0 15px 15px;
            padding: 1.5rem;
        }
        .btn-login {
            background: #0d6efd;
            border: none;
            padding: 0.75rem;
            font-weight: 600;
        }
        .btn-login:hover {
            background: #0b5ed7;
        }
        .role-badge {
            font-size: 0.8rem;
            padding: 0.3rem 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container my-5 p-3">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6">
                <div class="card login-card my-3">
                    <!-- Header -->
                    <div class="login-header text-center">
                        <h2><i class="fas fa-search me-2"></i> Lost & Found Portal</h2>
                        <p class="mb-0">College Campus Item Recovery System</p>
                    </div>

                    <!-- Login Form -->
                    <div class="card-body p-5">
                        <h4 class="text-center mb-4">Login to Your Account</h4>
                        
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i> Email Address
                                </label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i> Password
                                </label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-login btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i> Login
                                </button>
                            </div>

                            <!-- Role Information 
                            <div class="text-center mb-3">
                                <p class="text-muted mb-2">Login as:</p>
                                <div class="d-flex justify-content-center gap-2">
                                    <span class="badge bg-primary role-badge">
                                        <i class="fas fa-user me-1"></i> User
                                    </span>
                                    <span class="badge bg-danger role-badge">
                                        <i class="fas fa-user-shield me-1"></i> Admin
                                    </span>
                                </div>
                                <small class="text-muted">Use same credentials, role determined automatically</small>
                            </div> -->
                        </form>
                    </div>

                    <!-- Footer -->
                    <div class="login-footer text-center p-3">
                        <p class="mb-0">
                            Don't have an account? 
                            <a href="{{ route('register') }}" class="text-decoration-none fw-bold">Register here</a>
                        </p>
                    </div>
                </div>

                <!-- Admin Notice -->
                <div class="alert alert-info mt-3">
                    <div class="d-flex">
                        <i class="fas fa-info-circle fa-2x me-3"></i>
                        <div>
                            <h6 class="alert-heading mb-1">For Administrators</h6>
                            <p class="mb-0 small">Admin accounts must be created by existing admins or via database. Contact system administrator for admin access.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>