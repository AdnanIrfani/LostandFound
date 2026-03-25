<!-- resources/views/auth/register.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Lost & Found Portal</title>
    
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
        .register-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: none;
        }
        .register-header {
            background: #198754;
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 2rem;
        }
        .form-control:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
        }
        .register-footer {
            background: #f8f9fa;
            border-radius: 0 0 15px 15px;
            padding: 1.5rem;
        }
        .btn-register {
            background: #198754;
            border: none;
            padding: 0.75rem;
            font-weight: 600;
        }
        .btn-register:hover {
            background: #157347;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }
        .step {
            text-align: center;
            flex: 1;
            position: relative;
        }
        .step-number {
            width: 40px;
            height: 40px;
            background: #dee2e6;
            color: #6c757d;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
        }
        .step.active .step-number {
            background: #198754;
            color: white;
        }
        .step-line {
            position: absolute;
            top: 20px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #dee2e6;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="container mb-5 my-5 p-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card register-card">
                    <!-- Header -->
                    <div class="register-header text-center">
                        <h2><i class="fas fa-search me-2"></i> Lost & Found Portal</h2>
                        <p class="mb-0">Create Your Account</p>
                    </div>

                    <!-- Registration Form -->
                    <div class="card-body p-5">
                        <h4 class="text-center mb-4">Student Registration</h4>
                        
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-circle"></i> Please fix the errors below
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Step Indicator
                        <div class="step-indicator">
                            <div class="step active">
                                <div class="step-number">1</div>
                                <small>Basic Info</small>
                            </div>
                            <div class="step">
                                <div class="step-number">2</div>
                                <small>Academic Info</small>
                            </div>
                            <div class="step">
                                <div class="step-number">3</div>
                                <small>Account Setup</small>
                            </div>
                            <div class="step-line"></div>
                        </div> -->

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            
                            <div class="row">
                                <!-- Username -->
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">
                                        <i class="fas fa-user me-1"></i> Username *
                                    </label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                           id="username" name="username" value="{{ old('username') }}" required>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i> Email Address *
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Phone Number -->
                                <div class="col-md-6 mb-3">
                                    <label for="phone_number" class="form-label">
                                        <i class="fas fa-phone me-1"></i> Phone Number
                                    </label>
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                           id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Student ID -->
                                <div class="col-md-6 mb-3">
                                    <label for="student_id" class="form-label">
                                        <i class="fas fa-id-card me-1"></i> Student ID (Optional)
                                    </label>
                                    <input type="text" class="form-control @error('student_id') is-invalid @enderror" 
                                           id="student_id" name="student_id" value="{{ old('student_id') }}">
                                    @error('student_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Department -->
                                <div class="col-md-6 mb-3">
                                    <label for="department" class="form-label">
                                        <i class="fas fa-graduation-cap me-1"></i> Department
                                    </label>
                                    <select class="form-control @error('department') is-invalid @enderror" 
                                            id="department" name="department">
                                        <option value="">Select Department</option>
                                        <option value="Computer Science" {{ old('department') == 'Computer Science' ? 'selected' : '' }}>Computer Science</option>
                                        <option value="Information Technology" {{ old('department') == 'Information Technology' ? 'selected' : '' }}>Information Technology</option>
                                        <option value="Electronics" {{ old('department') == 'Electronics' ? 'selected' : '' }}>Electronics</option>
                                        <option value="Mechanical" {{ old('department') == 'Mechanical' ? 'selected' : '' }}>Mechanical</option>
                                        <option value="Civil" {{ old('department') == 'Civil' ? 'selected' : '' }}>Civil</option>
                                        <option value="Electrical" {{ old('department') == 'Electrical' ? 'selected' : '' }}>Electrical</option>
                                        <option value="Other" {{ old('department') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Year -->
                                <div class="col-md-6 mb-3">
                                    <label for="year" class="form-label">
                                        <i class="fas fa-calendar-alt me-1"></i> Year
                                    </label>
                                    <select class="form-control @error('year') is-invalid @enderror" 
                                            id="year" name="year">
                                        <option value="">Select Year</option>
                                        <option value="1" {{ old('year') == '1' ? 'selected' : '' }}>1st Year</option>
                                        <option value="2" {{ old('year') == '2' ? 'selected' : '' }}>2nd Year</option>
                                        <option value="3" {{ old('year') == '3' ? 'selected' : '' }}>3rd Year</option>
                                        <option value="4" {{ old('year') == '4' ? 'selected' : '' }}>4th Year</option>
                                    </select>
                                    @error('year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <!-- Password -->
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-1"></i> Password *
                                    </label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Minimum 8 characters</small>
                                </div>

                                <!-- Confirm Password -->
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">
                                        <i class="fas fa-lock me-1"></i> Confirm Password *
                                    </label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <!-- <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input @error('terms') is-invalid @enderror" 
                                           type="checkbox" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="#" class="text-decoration-none">Terms & Conditions</a> 
                                        and <a href="#" class="text-decoration-none">Privacy Policy</a>
                                    </label>
                                    @error('terms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> -->

                            <!-- Submit Button -->
                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-register btn-lg">
                                    <i class="fas fa-user-plus me-2"></i> Create Account
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Footer -->
                    <div class="register-footer text-center">
                        <p class="mb-0">
                            Already have an account? 
                            <a href="{{ route('login') }}" class="text-decoration-none fw-bold">Login here</a>
                        </p>
                    </div>
                </div>

                <!-- Registration Notice -->
                <div class="alert alert-warning mt-3">
                    <div class="d-flex">
                        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                        <div>
                            <h6 class="alert-heading mb-1">Important Notice</h6>
                            <p class="mb-0 small">Registration is only for college students. Admin accounts cannot be created through this form. Contact college administration for admin access.</p>
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