<!-- resources/views/user/lost-form.blade.php -->
@extends('layouts.user')

@section('title', 'Report Lost Item')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <h4 class="mb-0">
                    <i class="fas fa-exclamation-circle text-danger me-2"></i> Report Lost Item
                </h4>
                <p class="text-muted mb-0">Fill in the details of your lost item</p>
            </div>
            <div class="card-body">
                <form action="{{ route('lost-items.store') }}" method="POST" enctype="multipart/form-data">
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
                        <small class="text-muted">Provide detailed description including brand, color, size, unique features, etc.</small>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Lost Date -->
                        <div class="col-md-4 mb-3">
                            <label for="lost_date" class="form-label">Lost Date *</label>
                            <input type="date" class="form-control @error('lost_date') is-invalid @enderror" 
                                   id="lost_date" name="lost_date" value="{{ old('lost_date') }}" required>
                            @error('lost_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Lost Time -->
                        <div class="col-md-4 mb-3">
                            <label for="lost_time" class="form-label">Approximate Time</label>
                            <input type="time" class="form-control @error('lost_time') is-invalid @enderror" 
                                   id="lost_time" name="lost_time" value="{{ old('lost_time') }}">
                            @error('lost_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Urgency -->
                        <div class="col-md-4 mb-3">
                            <label for="urgency" class="form-label">Urgency Level</label>
                            <select class="form-control @error('urgency') is-invalid @enderror" 
                                    id="urgency" name="urgency">
                                <option value="medium" {{ old('urgency') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="low" {{ old('urgency') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="high" {{ old('urgency') == 'high' ? 'selected' : '' }}>High</option>
                            </select>
                            @error('urgency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Location -->
                        <div class="col-md-6 mb-3">
                            <label for="lost_location" class="form-label">Lost Location *</label>
                            <input type="text" class="form-control @error('lost_location') is-invalid @enderror" 
                                   id="lost_location" name="lost_location" 
                                   placeholder="e.g., Library, Cafeteria, Room 101" 
                                   value="{{ old('lost_location') }}" required>
                            @error('lost_location')
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

                        <!-- Reward -->
                        <div class="col-md-4 mb-3">
                            <label for="reward" class="form-label">Reward (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control @error('reward') is-invalid @enderror" 
                                       id="reward" name="reward" min="0" step="0.01" 
                                       value="{{ old('reward') }}">
                            </div>
                            @error('reward')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Claim Deadline -->
                        <div class="col-md-4 mb-3">
                            <label for="claim_deadline" class="form-label">Claim Deadline</label>
                            <input type="date" class="form-control @error('claim_deadline') is-invalid @enderror" 
                                   id="claim_deadline" name="claim_deadline" value="{{ old('claim_deadline') }}">
                            <small class="text-muted">Optional - Set if you want to withdraw after this date</small>
                            @error('claim_deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Item Image -->
                    <div class="mb-3">
                        <label for="item_image" class="form-label">Item Image</label>
                        <input type="file" class="form-control @error('item_image') is-invalid @enderror" 
                               id="item_image" name="item_image" accept="image/*">
                        <small class="text-muted">Upload a clear picture of the item (Max: 2MB)</small>
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

                    <!-- Preview Images -->
                    <div id="image-preview" class="row mb-3" style="display: none;"></div>

                    <!-- Submit Button -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane me-2"></i> Submit Lost Item Report
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

@push('scripts')
<script>
    // Image preview for main image
    document.getElementById('item_image').addEventListener('change', function(e) {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';
        preview.style.display = 'block';
        
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '200px';
                img.style.maxHeight = '200px';
                img.className = 'img-thumbnail m-2';
                preview.appendChild(img);
            }
            reader.readAsDataURL(file);
        }
    });
    
    // Image preview for multiple images
    document.getElementById('additional_images').addEventListener('change', function(e) {
        const preview = document.getElementById('image-preview');
        if (preview.innerHTML === '') {
            preview.style.display = 'block';
        }
        
        const files = e.target.files;
        for (let i = 0; i < files.length; i++) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '150px';
                img.style.maxHeight = '150px';
                img.className = 'img-thumbnail m-2';
                preview.appendChild(img);
            }
            reader.readAsDataURL(files[i]);
        }
    });
    
    // Set max date for lost date to today
    document.getElementById('lost_date').max = new Date().toISOString().split('T')[0];
</script>
@endpush
@endsection