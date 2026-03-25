<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LostItem extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'lost_id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'category_id',
        'item_name',
        'description',
        'lost_date',
        'lost_time',
        'lost_location',
        'building',
        'room_number',
        'item_image',
        'additional_images',
        'reward',
        'urgency',
        'claim_deadline',
        'status',
        'is_verified',
        'verified_at',
        'verified_by',
        'view_count',
        'is_resolved',
        'resolved_at'
    ];

    protected $casts = [
        'lost_date' => 'date',
        'claim_deadline' => 'date',
        'reward' => 'decimal:2',
        'additional_images' => 'array',
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
        return $this->hasMany(ItemMatch::class, 'lost_id');
    }

    public function successStories()
    {
        return $this->hasMany(SuccessStory::class, 'lost_id');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
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

    // Helper Methods
    public function getImageUrlAttribute()
    {
        if ($this->item_image) {
            return asset('storage/lost-items/' . $this->item_image);
        }
        return asset('images/default-item.jpg');
    }

    public function getAdditionalImagesUrlsAttribute()
    {
        if (!$this->additional_images) return [];
        
        return array_map(function($image) {
            return asset('storage/lost-items/additional/' . $image);
        }, $this->additional_images);
    }

    public function getUrgencyColorAttribute()
    {
        return [
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger'
        ][$this->urgency] ?? 'secondary';
    }

    public function getDaysLostAttribute()
    {
        return $this->lost_date->diffInDays(now());
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

    public function markAsResolved()
    {
        $this->is_resolved = true;
        $this->resolved_at = now();
        $this->status = 'recovered';
        $this->save();
    }
}