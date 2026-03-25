<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoundItem extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'found_id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'category_id',
        'item_name',
        'description',
        'found_date',
        'found_time',
        'found_location',
        'building',
        'room_number',
        'item_image',
        'additional_images',
        'item_condition',
        'storage_location',
        'handover_person',
        'handover_contact',
        'handover_to_admin',
        'status',
        'is_verified',
        'verified_at',
        'verified_by',
        'view_count',
        'is_resolved',
        'resolved_at'
    ];

    protected $casts = [
        'found_date' => 'date',
        'additional_images' => 'array',
        'handover_to_admin' => 'boolean',
        'is_verified' => 'boolean',
        'is_resolved' => 'boolean',
        'verified_at' => 'datetime',
        'resolved_at' => 'datetime',
        'view_count' => 'integer',
        'deleted_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function matches()
    {
        return $this->hasMany(ItemMatch::class, 'found_id');
    }

    public function successStories()
    {
        return $this->hasMany(SuccessStory::class, 'found_id');
    }

    // Scopes
    public function scopeUnclaimed($query)
    {
        return $query->where('status', 'unclaimed');
    }

    public function scopeClaimed($query)
    {
        return $query->where('status', 'claimed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    public function scopeResolved($query)
    {
        return $query->where('is_resolved', true);
    }

    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeWithAdmin($query)
    {
        return $query->where('handover_to_admin', true);
    }

    // Helper Methods
    public function getImageUrlAttribute()
    {
        if ($this->item_image) {
            return asset('storage/found-items/' . $this->item_image);
        }
        return asset('images/default-item.jpg');
    }

    public function getAdditionalImagesUrlsAttribute()
    {
        if (!$this->additional_images) return [];
        
        return array_map(function($image) {
            return asset('storage/found-items/additional/' . $image);
        }, $this->additional_images);
    }

    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'secondary',
            'unclaimed' => 'warning',
            'claimed' => 'info',
            'verified' => 'primary',
            'returned' => 'success'
        ][$this->status] ?? 'secondary';
    }

    public function getConditionColorAttribute()
    {
        return [
            'excellent' => 'success',
            'good' => 'primary',
            'fair' => 'warning',
            'poor' => 'danger'
        ][$this->item_condition] ?? 'secondary';
    }

    public function getDaysFoundAttribute()
    {
        return $this->found_date->diffInDays(now());
    }

    public function incrementViewCount()
    {
        $this->view_count++;
        $this->save();
    }

    public function markAsVerified($adminId)
    {
        $this->is_verified = true;
        $this->verified_at = now();
        $this->verified_by = $adminId;
        $this->save();
    }

    public function markAsClaimed()
    {
        $this->status = 'claimed';
        $this->save();
    }

    public function markAsReturned()
    {
        $this->status = 'returned';
        $this->is_resolved = true;
        $this->resolved_at = now();
        $this->save();
    }

    public function isAvailableForClaim()
    {
        return $this->status === 'unclaimed' && !$this->is_resolved;
    }

    public function getStorageInfoAttribute()
    {
        if ($this->handover_to_admin) {
            return "Admin Office - " . ($this->storage_location ?? 'Not specified');
        }
        
        if ($this->handover_person) {
            return "With: " . $this->handover_person . " (" . $this->handover_contact . ")";
        }
        
        return "With finder";
    }
}