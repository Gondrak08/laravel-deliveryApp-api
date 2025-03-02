<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'store_id', 'shopping_list', 'quantity', 'price', 'address', 'payment'];

    protected $casts = [
        'shopping_list' => 'array'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
