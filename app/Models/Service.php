<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'store_id',
        'category_id',
        'image_url',
        'description',
        'price',
        'weight',
        'is_available',
        'is_promoted',
        'is_discounted',
        'discount_value',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
