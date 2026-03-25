<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemMatch extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'match_id';
    public $timestamps = true;

    protected $fillable = [
        'lost_id',
        'found_id',
        'matched_by_user_id',
        'similarity_score',
        'match_reason',
        'match_status',
        'verified_by',
        'verified_at',
        'verification_notes',
        'handover_date',
        'handover_notes'
    ];

    protected $casts = [
        'similarity_score' => 'decimal:2',
        'verified_at' => 'datetime',
        'handover_date' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Relationships
    public function lostItem()
    {
        return $this->belongsTo(LostItem::class, 'lost_id', 'lost_id');
    }

    public function foundItem()
    {
        return $this->belongsTo(FoundItem::class, 'found_id', 'found_id');
    }

    public function matchedBy()
    {
        return $this->belongsTo(User::class, 'matched_by_user_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function successStory()
    {
        return $this->hasOne(SuccessStory::class, 'match_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('match_status', 'pending');
    }

    public function scopeForReview($query)
    {
        return $query->where('match_status', 'review');
    }

    public function scopeVerified($query)
    {
        return $query->where('match_status', 'verified');
    }

    public function scopeCompleted($query)
    {
        return $query->where('match_status', 'completed');
    }

    public function scopeHighConfidence($query, $threshold = 80)
    {
        return $query->where('similarity_score', '>=', $threshold);
    }

    // Helper Methods
    public function verify($adminId, $notes = null)
    {
        $this->match_status = 'verified';
        $this->verified_by = $adminId;
        $this->verified_at = now();
        $this->verification_notes = $notes;
        $this->save();

        // Update the items
        $this->lostItem->markAsVerified($adminId);
        $this->foundItem->markAsVerified($adminId);
    }

    public function completeHandover($notes = null)
    {
        $this->match_status = 'completed';
        $this->handover_date = now();
        $this->handover_notes = $notes;
        $this->save();

        // Mark items as recovered/returned
        $this->lostItem->markAsResolved();
        $this->foundItem->update([
            'status' => 'returned',
            'is_resolved' => true,
            'resolved_at' => now()
        ]);

        // Create success story
        SuccessStory::create([
            'lost_id' => $this->lost_id,
            'found_id' => $this->found_id,
            'match_id' => $this->match_id,
            'days_to_recover' => $this->lostItem->lost_date->diffInDays(now())
        ]);
    }
}