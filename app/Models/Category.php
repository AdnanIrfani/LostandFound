<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'category_id';
    public $timestamps = true;

    protected $fillable = [
        'category_name',
        'parent_category_id'
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_category_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_category_id');
    }

    public function lostItems()
    {
        return $this->hasMany(LostItem::class, 'category_id');
    }

    public function foundItems()
    {
        return $this->hasMany(FoundItem::class, 'category_id');
    }

    public function getFullPathAttribute()
    {
        if ($this->parent) {
            return $this->parent->category_name . ' > ' . $this->category_name;
        }
        return $this->category_name;
    }
}