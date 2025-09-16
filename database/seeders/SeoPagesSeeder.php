<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SeoPage;
use App\Models\SeoSetting;

class SeoPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed SEO Pages
        $seoPages = [
            [
                'page_key' => 'default',
                'title' => 'JippyMart - Your One-Stop Destination for Groceries & Food Delivery',
                'description' => 'Get groceries, medicines, and daily essentials delivered to your doorstep. Order from local restaurants and stores with fast delivery across India.',
                'keywords' => 'food delivery, grocery delivery, online ordering, local restaurants, medicines, daily essentials, jippymart, online shopping',
                'og_title' => 'JippyMart - Food & Grocery Delivery',
                'og_description' => 'Get groceries, medicines, and daily essentials delivered to your doorstep. Order from local restaurants and stores.',
            ],
            [
                'page_key' => 'home',
                'title' => 'JippyMart - Order Food & Groceries Online | Fast Delivery',
                'description' => 'Discover the best local restaurants and grocery stores near you. Order food and groceries online with fast delivery to your doorstep. Fresh ingredients, quality products.',
                'keywords' => 'food delivery, grocery delivery, online food ordering, local restaurants, grocery stores, fast delivery, fresh ingredients',
                'og_title' => 'JippyMart - Order Food & Groceries Online',
                'og_description' => 'Discover the best local restaurants and grocery stores near you. Fast delivery to your doorstep.',
            ],
            [
                'page_key' => 'restaurants',
                'title' => 'Restaurants Near You | Order Food Online | JippyMart',
                'description' => 'Find and order from the best restaurants near you. Browse menus, read reviews, and get your favorite food delivered fast. Wide variety of cuisines available.',
                'keywords' => 'restaurants near me, food delivery, online food ordering, restaurant menus, food reviews, cuisines, local restaurants',
                'og_title' => 'Restaurants Near You | JippyMart',
                'og_description' => 'Find and order from the best restaurants near you. Browse menus and get food delivered fast.',
            ],
            [
                'page_key' => 'restaurant',
                'title' => '{restaurant_name} - Order Online | JippyMart',
                'description' => 'Order delicious food from {restaurant_name}. Browse menu, read reviews, and get fast delivery. {cuisine_type} cuisine available.',
                'keywords' => 'restaurant, food delivery, online ordering, menu, reviews, {cuisine_type}, {restaurant_name}',
                'og_title' => '{restaurant_name} - Order Online | JippyMart',
                'og_description' => 'Order delicious food from {restaurant_name}. Browse menu and get fast delivery.',
            ],
            [
                'page_key' => 'products',
                'title' => 'Grocery Items & Daily Essentials | Order Online | JippyMart',
                'description' => 'Browse and order your favorite grocery items and daily essentials online. Fresh ingredients, quality products, fast delivery to your doorstep.',
                'keywords' => 'grocery items, daily essentials, online shopping, fresh ingredients, quality products, grocery delivery',
                'og_title' => 'Grocery Items & Daily Essentials | JippyMart',
                'og_description' => 'Browse and order your favorite grocery items and daily essentials online. Fresh ingredients, fast delivery.',
            ],
            [
                'page_key' => 'product',
                'title' => '{product_name} - Buy Online | JippyMart',
                'description' => 'Buy {product_name} online at best price. Fresh quality product with fast delivery. Order now from JippyMart.',
                'keywords' => 'buy online, {product_name}, grocery, fresh, quality, fast delivery, jippymart',
                'og_title' => '{product_name} - Buy Online | JippyMart',
                'og_description' => 'Buy {product_name} online at best price. Fresh quality product with fast delivery.',
            ],
            [
                'page_key' => 'categories',
                'title' => 'Food & Grocery Categories | Browse by Type | JippyMart',
                'description' => 'Browse food and grocery items by category. Find exactly what you\'re looking for from our wide selection of categories.',
                'keywords' => 'food categories, grocery categories, browse food, food types, grocery types, categories',
                'og_title' => 'Food & Grocery Categories | JippyMart',
                'og_description' => 'Browse food and grocery items by category. Find exactly what you\'re looking for.',
            ],
            [
                'page_key' => 'category',
                'title' => '{category_name} - Shop Online | JippyMart',
                'description' => 'Shop {category_name} items online. Wide selection of quality products with fast delivery. Order now from JippyMart.',
                'keywords' => '{category_name}, shop online, quality products, fast delivery, jippymart',
                'og_title' => '{category_name} - Shop Online | JippyMart',
                'og_description' => 'Shop {category_name} items online. Wide selection of quality products.',
            ],
            [
                'page_key' => 'mart',
                'title' => 'Mart - Grocery & Daily Essentials | JippyMart',
                'description' => 'Shop for groceries and daily essentials at JippyMart. Fresh products, competitive prices, and fast delivery to your doorstep.',
                'keywords' => 'mart, grocery, daily essentials, fresh products, competitive prices, fast delivery',
                'og_title' => 'Mart - Grocery & Daily Essentials | JippyMart',
                'og_description' => 'Shop for groceries and daily essentials at JippyMart. Fresh products, competitive prices.',
            ],
            [
                'page_key' => 'search',
                'title' => 'Search Results | Find What You Need | JippyMart',
                'description' => 'Search for restaurants, food items, and grocery products. Find exactly what you\'re looking for with our comprehensive search.',
                'keywords' => 'search, find, restaurants, food items, grocery products, search results',
                'og_title' => 'Search Results | JippyMart',
                'og_description' => 'Search for restaurants, food items, and grocery products. Find exactly what you need.',
            ],
            [
                'page_key' => 'about',
                'title' => 'About JippyMart | Your Local Food & Grocery Partner',
                'description' => 'Learn about JippyMart - your trusted local food and grocery delivery service. Connecting you with the best local businesses for quality products.',
                'keywords' => 'about jippymart, food delivery service, local business, grocery delivery, company info, our story',
                'og_title' => 'About JippyMart | Your Local Food Partner',
                'og_description' => 'Learn about JippyMart - your trusted local food and grocery delivery service.',
            ],
            [
                'page_key' => 'contact',
                'title' => 'Contact JippyMart | Customer Support | Help Center',
                'description' => 'Get in touch with JippyMart customer support. We\'re here to help with your food and grocery delivery needs. Contact us for assistance.',
                'keywords' => 'contact jippymart, customer support, help center, customer service, get help, contact us',
                'og_title' => 'Contact JippyMart | Customer Support',
                'og_description' => 'Get in touch with JippyMart customer support. We\'re here to help.',
            ],
            [
                'page_key' => 'privacy',
                'title' => 'Privacy Policy | JippyMart',
                'description' => 'Read JippyMart\'s privacy policy to understand how we collect, use, and protect your personal information.',
                'keywords' => 'privacy policy, data protection, personal information, jippymart privacy',
                'og_title' => 'Privacy Policy | JippyMart',
                'og_description' => 'Read JippyMart\'s privacy policy to understand how we protect your information.',
            ],
            [
                'page_key' => 'terms',
                'title' => 'Terms of Service | JippyMart',
                'description' => 'Read JippyMart\'s terms of service to understand the rules and guidelines for using our platform.',
                'keywords' => 'terms of service, terms and conditions, user agreement, jippymart terms',
                'og_title' => 'Terms of Service | JippyMart',
                'og_description' => 'Read JippyMart\'s terms of service to understand our platform guidelines.',
            ],
            [
                'page_key' => 'faq',
                'title' => 'Frequently Asked Questions | JippyMart Help Center',
                'description' => 'Find answers to frequently asked questions about JippyMart\'s food and grocery delivery service. Get help with common queries.',
                'keywords' => 'faq, frequently asked questions, help, support, jippymart help, common questions',
                'og_title' => 'FAQ | JippyMart Help Center',
                'og_description' => 'Find answers to frequently asked questions about JippyMart\'s services.',
            ],
            [
                'page_key' => 'offers',
                'title' => 'Special Offers & Discounts | JippyMart',
                'description' => 'Discover amazing offers and discounts on food and grocery delivery. Save money on your orders with JippyMart special deals.',
                'keywords' => 'offers, discounts, special deals, save money, jippymart offers, promotional deals',
                'og_title' => 'Special Offers & Discounts | JippyMart',
                'og_description' => 'Discover amazing offers and discounts on food and grocery delivery.',
            ],
        ];

        foreach ($seoPages as $page) {
            SeoPage::updateOrCreate(
                ['page_key' => $page['page_key']],
                $page
            );
        }

        // Seed SEO Settings
        $seoSettings = SeoSetting::getDefaultSettings();
        
        foreach ($seoSettings as $key => $value) {
            SeoSetting::setValue($key, $value, $this->getSettingDescription($key));
        }

        $this->command->info('SEO pages and settings seeded successfully!');
    }

    /**
     * Get description for SEO settings
     */
    private function getSettingDescription($key)
    {
        $descriptions = [
            'site_name' => 'The name of your website',
            'site_description' => 'Default description for your website',
            'default_og_image' => 'Default Open Graph image for social media sharing',
            'twitter_handle' => 'Your Twitter handle (without @)',
            'google_analytics_id' => 'Google Analytics tracking ID',
            'google_search_console_verification' => 'Google Search Console verification code',
            'facebook_app_id' => 'Facebook App ID for social features',
            'default_currency' => 'Default currency for your website',
            'default_language' => 'Default language for your website',
            'contact_email' => 'Contact email address',
            'contact_phone' => 'Contact phone number',
            'business_address' => 'Your business address',
        ];

        return $descriptions[$key] ?? 'SEO setting';
    }
}
