<?php
namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $primaryKey = 'user_id';
    public $timestamps = true;

    protected $fillable = [
        'username',
        'email',
        'password',
        'phone_number',
        'role',
        'profile_picture',
        'student_id',
        'department',
        'year',
        'is_active',
        'last_login_at',
        'total_lost_items',
        'total_found_items',
        'total_recovered_items'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'additional_images' => 'array',
        'total_lost_items' => 'integer',
        'total_found_items' => 'integer',
        'total_recovered_items' => 'integer',
        'deleted_at' => 'datetime'
    ];

    // Relationships
    public function lostItems()
    {
        return $this->hasMany(LostItem::class, 'user_id');
    }

    public function foundItems()
    {
        return $this->hasMany(FoundItem::class, 'user_id');
    }

    public function matchesCreated()
    {
        return $this->hasMany(ItemMatch::class, 'matched_by_user_id');
    }

    public function verifications()
    {
        return $this->hasMany(LostItem::class, 'verified_by')->whereNotNull('verified_by');
    }

    // public function notifications()
    // {
    //     return $this->hasMany(Notification::class, 'user_id');
    // }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    // Scopes
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeStudents($query)
    {
        return $query->whereNotNull('student_id');
    }

    // Helper Methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    public function getRecoveryRateAttribute()
    {
        if ($this->total_lost_items == 0) return 0;
        return round(($this->total_recovered_items / $this->total_lost_items) * 100, 2);
    }

    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/profile-pictures/' . $this->profile_picture);
        }
        return asset('images/default-avatar.png');
    }

    public function getUnreadNotificationsCountAttribute()
    {
        return $this->notifications()->where('status', 'unread')->count();
    }
}