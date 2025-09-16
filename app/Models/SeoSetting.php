<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    protected $fillable = [
        'setting_key',
        'setting_value',
        'description'
    ];

    /**
     * Get a setting value by key
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    /**
     * Set a setting value by key
     */
    public static function setValue($key, $value, $description = null)
    {
        return self::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'description' => $description
            ]
        );
    }

    /**
     * Get all settings as key-value array
     */
    public static function getAllSettings()
    {
        return self::pluck('setting_value', 'setting_key')->toArray();
    }

    /**
     * Get default SEO settings
     */
    public static function getDefaultSettings()
    {
        return [
            'site_name' => 'JippyMart',
            'site_description' => 'Your one-stop destination for groceries, medicines, and daily essentials',
            'default_og_image' => '/images/og-default.jpg',
            'twitter_handle' => '@jippymart',
            'google_analytics_id' => '',
            'google_search_console_verification' => '',
            'facebook_app_id' => '',
            'default_currency' => 'INR',
            'default_language' => 'en',
            'contact_email' => 'info@jippymart.in',
            'contact_phone' => '+91-XXXXXXXXXX',
            'business_address' => 'Your Business Address',
        ];
    }
}
