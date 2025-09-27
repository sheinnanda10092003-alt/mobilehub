<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'ProductID';

    protected $fillable = [
        'Name',
        'Description',
        'Price',
        'Stock',
        'image_url',
        'SupplierID',
    ];
    
    // Add accessor for sanitized image URL
    public function getSanitizedImageUrlAttribute()
    {
        if (empty($this->image_url)) {
            return null;
        }
        
        // Basic URL validation and sanitization
        $url = trim($this->image_url);
        
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }
        
        // Allow common manufacturer and CDN domains
        $validDomains = [
            'images.samsung.com', 'samsung.com',
            'googleusercontent.com', 'google.com',
            'oasis.opstatics.com', 'oneplus.com',
            'appmifile.com', 'xiaomi.com', 'mi.com',
            'sony.com', 'sonyentertainmentnetwork.com',
            'apple.com', 'cdn-apple.com',
            'imgur.com', 'cloudinary.com', 'unsplash.com', 'pexels.com', 'pixabay.com',
            'amazonaws.com', 'cloudfront.net', 'fastly.com'
        ];
        
        $domain = parse_url($url, PHP_URL_HOST);
        
        // Check if domain contains any valid domain
        if ($this->isFromValidDomain($domain, $validDomains)) {
            return $url;
        }
        
        // Also check for valid image extensions
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        
        if (in_array($extension, $validExtensions)) {
            return $url;
        }
        
        return null;
    }
    
    // Helper method to check valid domains
    private function isFromValidDomain($domain, $validDomains)
    {
        foreach ($validDomains as $validDomain) {
            if (strpos($domain, $validDomain) !== false) {
                return true;
            }
        }
        return false;
    }
    
    // Helper method to check if product has valid image
    public function hasValidImage()
    {
        return !is_null($this->sanitized_image_url);
    }

    // Product belongs to a Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'SupplierID');
    }
    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'ProductID');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'ProductID');
    }
    
    // Product has many variants
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'ProductID');
    }
    
    // Get active variants only
    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class, 'ProductID')->where('is_active', true);
    }
    
    // Get available variants (in stock)
    public function availableVariants()
    {
        return $this->hasMany(ProductVariant::class, 'ProductID')
                   ->where('is_active', true)
                   ->where('Stock', '>', 0);
    }
    
    // Get total stock across all variants
    public function getTotalStockAttribute()
    {
        return $this->variants()->sum('Stock');
    }
    
    // Get minimum price across all variants
    public function getMinPriceAttribute()
    {
        return $this->variants()->where('is_active', true)->min('Price') ?: $this->Price;
    }
    
    // Get maximum price across all variants
    public function getMaxPriceAttribute()
    {
        return $this->variants()->where('is_active', true)->max('Price') ?: $this->Price;
    }
    
    // Check if product has variants
    public function hasVariants()
    {
        return $this->variants()->where('is_active', true)->count() > 0;
    }
}
