<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_uuid',
        'uuid',
        'title',
        'price',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_uuid', 'uuid');
    }

    /**
     * Get the brand UUID from metadata.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'metadata->brand', 'uuid');
    }

    /**
     * Get the image UUID from metadata.
     */
    public function image()
    {
        return $this->belongsTo(File::class, 'metadata->image', 'uuid');
    }
}