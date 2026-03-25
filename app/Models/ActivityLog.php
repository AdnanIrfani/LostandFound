<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $primaryKey = 'log_id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Helper Methods
    public static function log($action, $model, $description = null, $oldData = null, $newData = null)
    {
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'description' => $description ?? self::generateDescription($action, $model),
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    private static function generateDescription($action, $model)
    {
        $modelName = class_basename($model);
        return "{$modelName} was {$action}";
    }
}