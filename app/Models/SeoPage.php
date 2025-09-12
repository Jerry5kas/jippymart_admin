<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoPage extends Model
{
    protected $fillable = [
        'page_key',
        'title',
        'description',
        'keywords',
        'og_title',
        'og_description',
        'og_image',
        'extra'
    ];

    protected $casts = [
        'extra' => 'array'
    ];

    /**
     * Get SEO data for a specific page
     */
    public static function getPageSeo($pageKey)
    {
        return self::where('page_key', $pageKey)->first();
    }

    /**
     * Get default SEO data if page-specific data doesn't exist
     */
    public static function getDefaultSeo()
    {
        return self::where('page_key', 'default')->first() ?? new self([
            'title' => 'JippyMart - Your One-Stop Destination for Groceries & Food Delivery',
            'description' => 'Get groceries, medicines, and daily essentials delivered to your doorstep. Order from local restaurants and stores with fast delivery.',
            'keywords' => 'food delivery, grocery delivery, online ordering, local restaurants, medicines, daily essentials, jippymart',
            'og_title' => 'JippyMart - Food & Grocery Delivery',
            'og_description' => 'Get groceries, medicines, and daily essentials delivered to your doorstep. Order from local restaurants and stores.',
        ]);
    }

    /**
     * Get formatted keywords as array
     */
    public function getKeywordsArrayAttribute()
    {
        if (!$this->keywords) return [];
        return array_map('trim', explode(',', $this->keywords));
    }

    /**
     * Get full OG image URL
     */
    public function getOgImageUrlAttribute()
    {
        if (!$this->og_image) return null;
        
        if (str_starts_with($this->og_image, 'http')) {
            return $this->og_image;
        }
        
        return asset($this->og_image);
    }
}
