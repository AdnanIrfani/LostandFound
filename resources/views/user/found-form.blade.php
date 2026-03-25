<!-- resources/views/user/found-form.blade.php -->
@extends('layouts.user')

@section('title', 'Report Found Item')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <h4 class="mb-0">
                    <i class="fas fa-check-circle text-success me-2"></i> Report Found Item
                </h4>
                <p class="text-muted mb-0">Help reunite lost items with their owners</p>
            </div>
            <div class="card-body">
                <form action="{{ route('found-items.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <!-- Item Name -->
                        <div class="col-md-6 mb-3">
                            <label for="item_name" class="form-label">Item Name *</label>
                            <input type="text" class="form-control @error('item_name') is-invalid @enderror" 
                                   id="item_name" name="item_name" value="{{ old('item_name') }}" required>
                            @error('item_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">Category *</label>
                            <select class="form-control @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->category_id }}" 
                                        {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                        <small class="text-muted">Provide detailed description including brand, color, size, condition, etc.</small>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Found Date -->
                        <div class="col-md-4 mb-3">
                            <label for="found_date" class="form-label">Found Date *</label>
                            <input type="date" class="form-control @error('found_date') is-invalid @enderror" 
                                   id="found_date" name="found_date" value="{{ old('found_date', date('Y-m-d')) }}" required>
                            @error('found_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Found Time -->
                        <div class="col-md-4 mb-3">
                            <label for="found_time" class="form-label">Approximate Time</label>
                            <input type="time" class="form-control @error('found_time') is-invalid @enderror" 
                                   id="found_time" name="found_time" value="{{ old('found_time') }}">
                            @error('found_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Item Condition -->
                        <div class="col-md-4 mb-3">
                            <label for="item_condition" class="form-label">Item Condition</label>
                            <select class="form-control @error('item_condition') is-invalid @enderror" 
                                    id="item_condition" name="item_condition">
                                <option value="good" {{ old('item_condition') == 'good' ? 'selected' : '' }}>Good</option>
                                <option value="excellent" {{ old('item_condition') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="fair" {{ old('item_condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                                <option value="poor" {{ old('item_condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                            </select>
                            @error('item_condition')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Location -->
                        <div class="col-md-6 mb-3">
                            <label for="found_location" class="form-label">Found Location *</label>
                            <input type="text" class="form-control @error('found_location') is-invalid @enderror" 
                                   id="found_location" name="found_location" 
                                   placeholder="e.g., Library, Cafeteria, Room 101" 
                                   value="{{ old('found_location') }}" required>
                            @error('found_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Building -->
                        <div class="col-md-6 mb-3">
                            <label for="building" class="form-label">Building</label>
                            <input type="text" class="form-control @error('building') is-invalid @enderror" 
                                   id="building" name="building" 
                                   placeholder="e.g., Main Building, Science Block"
                                   value="{{ old('building') }}">
                            @error('building')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Room Number -->
                        <div class="col-md-4 mb-3">
                            <label for="room_number" class="form-label">Room Number</label>
                            <input type="text" class="form-control @error('room_number') is-invalid @enderror" 
                                   id="room_number" name="room_number" value="{{ old('room_number') }}">
                            @error('room_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Storage Location -->
                        <div class="col-md-4 mb-3">
                            <label for="storage_location" class="form-label">Current Storage</label>
                            <input type="text" class="form-control @error('storage_location') is-invalid @enderror" 
                                   id="storage_location" name="storage_location" 
                                   placeholder="e.g., With me, Security Office"
                                   value="{{ old('storage_location') }}">
                            @error('storage_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Handover to Admin -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Handover Options</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       id="handover_to_admin" name="handover_to_admin" 
                                       value="1" {{ old('handover_to_admin') ? 'checked' : '' }}>
                                <label class="form-check-label" for="handover_to_admin">
                                    Handover to Admin Office
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Contact Information for Claim</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="handover_person" class="form-label">Contact Person Name</label>
                                    <input type="text" class="form-control @error('handover_person') is-invalid @enderror" 
                                           id="handover_person" name="handover_person" 
                                           value="{{ old('handover_person', auth()->user()->username) }}">
                                    @error('handover_person')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="handover_contact" class="form-label">Contact Number</label>
                                    <input type="text" class="form-control @error('handover_contact') is-invalid @enderror" 
                                           id="handover_contact" name="handover_contact" 
                                           value="{{ old('handover_contact', auth()->user()->phone_number) }}">
                                    @error('handover_contact')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item Image -->
                    <div class="mb-3">
                        <label for="item_image" class="form-label">Item Image</label>
                        <input type="file" class="form-control @error('item_image') is-invalid @enderror" 
                               id="item_image" name="item_image" accept="image/*">
                        <small class="text-muted">Upload a clear picture of the found item (Max: 2MB)</small>
                        @error('item_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Additional Images -->
                    <div class="mb-3">
                        <label for="additional_images" class="form-label">Additional Images</label>
                        <input type="file" class="form-control @error('additional_images') is-invalid @enderror" 
                               id="additional_images" name="additional_images[]" multiple accept="image/*">
                        <small class="text-muted">You can upload multiple images (Max 5 images, 2MB each)</small>
                        @error('additional_images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-hands-helping me-2"></i> Submit Found Item Report
                        </button>
                        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection