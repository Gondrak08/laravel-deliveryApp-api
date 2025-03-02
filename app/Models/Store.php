<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'imageUrl',
        'address',
        'description',
        'totalValue',
        'isOpen'
    ];

    /**
     * relate to user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
