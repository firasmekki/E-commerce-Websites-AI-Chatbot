<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'sale_price', 'stock', 'is_featured', 'sale_ends_at', 'category_id', 'image_url'];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'sale_ends_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function hasActiveSale(): bool
    {
        return $this->sale_price !== null
            && $this->sale_price < $this->price
            && (!$this->sale_ends_at || $this->sale_ends_at->isFuture());
    }

    public function effectivePrice(): float
    {
        return $this->hasActiveSale() ? (float) $this->sale_price : (float) $this->price;
    }
}
