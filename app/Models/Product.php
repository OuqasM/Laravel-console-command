<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'id', 
        'name', 
        'sku', 
        'status', 
        'price', 
        'currency', 
        'deletion_reason'
    ];

    protected $dates = ['deleted_at'];

    public $timestamps = true;

    /**
     * Relationship with variations model
     */
    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }
}
