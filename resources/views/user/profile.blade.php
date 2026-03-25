<!-- resources/views/user/profile.blade.php -->
@extends('layouts.user')

@section('title', 'My Profile')

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Profile Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="{{ auth()->user()->profile_picture_url }}" 
                     class="rounded-circle mb-3" 
                     width="150" height="150"
                     alt="{{ auth()->user()->username }}">
                <h4>{{ auth()->user()->username }}</h4>
                <p class="text-muted">
                    @if(auth()->user()->isAdmin())
                        <span class="badge bg-danger">Administrator</span>
                    @else
                        <span class="badge bg-primary">Student</span>
                    @endif
                </p>
                
                <div class="list-group list-group-flush text-start">
                    <div class="list-group-item">
                        <i class="fas fa-envelope me-2"></i> {{ auth()->user()->email }}
                    </div>
                    @if(auth()->user()->phone_number)
                        <div class="list-group-item">
                            <i class="fas fa-phone me-2"></i> {{ auth()->user()->phone_number }}
                        </div>
                    @endif
                    @if(auth()->user()->student_id)
                        <div class="list-group-item">
                            <i class="fas fa-id-card me-2"></i> {{ auth()->user()->student_id }}
                        </div>
                    @endif
                    @if(auth()->user()->department)
                        <div class="list-group-item">
                            <i class="fas fa-graduation-cap me-2"></i> 
                            {{ auth()->user()->department }}
                            @if(auth()->user()->year)
                                (Year {{ auth()->user()->year }})
                            @endif
                        </div>
                    @endif
                    <div class="list-group-item">
                        <i class="fas fa-calendar me-2"></i> 
                        Member since {{ auth()->user()->created_at->format('M Y') }}
                    </div>
                    <div class="list-group-item">
                        <i class="fas fa-sign-in-alt me-2"></i> 
                        Last login: {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'First login' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Your Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h4>{{ auth()->user()->total_lost_items }}</h4>
                        <small class="text-muted">Lost Items</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4>{{ auth()->user()->total_found_items }}</h4>
                        <small class="text-muted">Found Items</small>
                    </div>
                    <div class="col-6">
                        <h4>{{ auth()->user()->total_recovered_items }}</h4>
                        <small class="text-muted">Recovered</small>
                    </div>
                    <div class="col-6">
                        <h4>{{ auth()->user()->recovery_rate }}%</h4>
                        <small class="text-muted">Recovery Rate</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Edit Profile Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Edit Profile</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username *</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                   id="username" name="username" 
                                   value="{{ old('username', auth()->user()->username) }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" 
                                   value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                   id="phone_number" name="phone_number" 
                                   value="{{ old('phone_number', auth()->user()->phone_number) }}">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="profile_picture" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control @error('profile_picture') is-invalid @enderror" 
                                   id="profile_picture" name="profile_picture" accept="image/*">
                            @error('profile_picture')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Max 2MB, JPG/PNG format</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="department" class="form-label">Department</label>
                            <select class="form-control @error('department') is-invalid @enderror" 
                                    id="department" name="department">
                                <option value="">Select Department</option>
                                <option value="Computer Science" {{ old('department', auth()->user()->department) == 'Computer Science' ? 'selected' : '' }}>Computer Science</option>
                                <option value="Information Technology" {{ old('department', auth()->user()->department) == 'Information Technology' ? 'selected' : '' }}>Information Technology</option>
                                <option value="Electronics" {{ old('department', auth()->user()->department) == 'Electronics' ? 'selected' : '' }}>Electronics</option>
                                <option value="Mechanical" {{ old('department', auth()->user()->department) == 'Mechanical' ? 'selected' : '' }}>Mechanical</option>
                                <option value="Civil" {{ old('department', auth()->user()->department) == 'Civil' ? 'selected' : '' }}>Civil</option>
                                <option value="Electrical" {{ old('department', auth()->user()->department) == 'Electrical' ? 'selected' : '' }}>Electrical</option>
                                <option value="Other" {{ old('department', auth()->user()->department) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="year" class="form-label">Year</label>
                            <select class="form-control @error('year') is-invalid @enderror" 
                                    id="year" name="year">
                                <option value="">Select Year</option>
                                <option value="1" {{ old('year', auth()->user()->year) == '1' ? 'selected' : '' }}>1st Year</option>
                                <option value="2" {{ old('year', auth()->user()->year) == '2' ? 'selected' : '' }}>2nd Year</option>
                                <option value="3" {{ old('year', auth()->user()->year) == '3' ? 'selected' : '' }}>3rd Year</option>
                                <option value="4" {{ old('year', auth()->user()->year) == '4' ? 'selected' : '' }}>4th Year</option>
                            </select>
                            @error('year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Update Profile
                    </button>
                </form>
            </div>
        </div>

        <!-- Change Password Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Change Password</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('user.change-password') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password *</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                               id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="new_password" class="form-label">New Password *</label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                   id="new_password" name="new_password" required>
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="new_password_confirmation" class="form-label">Confirm New Password *</label>
                            <input type="password" class="form-control" 
                                   id="new_password_confirmation" name="new_password_confirmation" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key me-2"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection