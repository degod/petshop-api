<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'title',
        'slug',
    ];

    /**
     * Get the products for the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Product>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'uuid', 'category_uuid');
    }
}
