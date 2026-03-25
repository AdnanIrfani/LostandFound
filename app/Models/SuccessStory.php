<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class SuccessStory extends Model
{
    protected $primaryKey = 'story_id';
    public $timestamps = true;

    protected $fillable = [
        'lost_id',
        'found_id',
        'match_id',
        'recovery_story',
        'recovery_images',
        'days_to_recover',
        'satisfaction_rating',
        'testimonial',
        'is_featured',
        'is_published',
        'view_count'
    ];

    protected $casts = [
        'recovery_images' => 'array',
        'days_to_recover' => 'integer',
        'satisfaction_rating' => 'integer',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'view_count' => 'integer'
    ];

    // Relationships
    public function lostItem()
    {
        return $this->belongsTo(LostItem::class, 'lost_id');
    }

    public function foundItem()
    {
        return $this->belongsTo(FoundItem::class, 'found_id');
    }

    public function match()
    {
        return $this->belongsTo(ItemMatch::class, 'match_id');
    }

    public function getRecoveryImagesUrlsAttribute()
    {
        if (!$this->recovery_images) return [];
        
        return array_map(function($image) {
            return asset('storage/success-stories/' . $image);
        }, $this->recovery_images);
    }
}