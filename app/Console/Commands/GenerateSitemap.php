<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SeoPage;
use App\Models\SeoSetting;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sitemap.xml for JippyMart customer site';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🚀 Starting sitemap generation...');
        
        try {
            $sitemapContent = $this->generateSitemapXml();
            
            // Write sitemap to public directory
            file_put_contents(public_path('sitemap.xml'), $sitemapContent);
            
            $this->info('✅ Sitemap generated successfully!');
            $this->info('📍 Location: ' . public_path('sitemap.xml'));
            $this->info('🌐 URL: ' . url('sitemap.xml'));
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Error generating sitemap: ' . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Generate sitemap XML content
     */
    private function generateSitemapXml()
    {
        $baseUrl = url('/');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Add static pages
        $this->addStaticPagesToXml($xml, $baseUrl);
        
        // Add dynamic pages from database
        $this->addDynamicPagesToXml($xml, $baseUrl);
        
        // Add Firestore content (if Firebase is configured)
        $this->addFirestoreContentToXml($xml, $baseUrl);
        
        $xml .= '</urlset>';
        
        return $xml;
    }
    
    /**
     * Add static pages to XML
     */
    private function addStaticPagesToXml(&$xml, $baseUrl)
    {
        $this->info('📄 Adding static pages...');
        
        $staticPages = [
            '/' => ['priority' => 1.0, 'changefreq' => 'daily'],
            '/restaurants' => ['priority' => 0.9, 'changefreq' => 'daily'],
            '/products' => ['priority' => 0.9, 'changefreq' => 'daily'],
            '/categories' => ['priority' => 0.8, 'changefreq' => 'weekly'],
            '/mart' => ['priority' => 0.8, 'changefreq' => 'daily'],
            '/about' => ['priority' => 0.6, 'changefreq' => 'monthly'],
            '/contact' => ['priority' => 0.6, 'changefreq' => 'monthly'],
            '/privacy' => ['priority' => 0.4, 'changefreq' => 'yearly'],
            '/terms' => ['priority' => 0.4, 'changefreq' => 'yearly'],
            '/faq' => ['priority' => 0.5, 'changefreq' => 'monthly'],
            '/offers' => ['priority' => 0.7, 'changefreq' => 'weekly']
        ];
        
        foreach ($staticPages as $url => $config) {
            $fullUrl = $baseUrl . $url;
            $lastmod = now()->format('Y-m-d');
            
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$fullUrl}</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "    <changefreq>{$config['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$config['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }
        
        $this->info('✅ Added ' . count($staticPages) . ' static pages');
    }
    
    /**
     * Add dynamic pages from database to XML
     */
    private function addDynamicPagesToXml(&$xml, $baseUrl)
    {
        $this->info('🗄️ Adding dynamic pages from database...');
        
        // Add SEO pages that have specific URLs
        $seoPages = SeoPage::whereNotNull('title')->get();
        $count = 0;
        
        foreach ($seoPages as $page) {
            if ($page->page_key !== 'default' && $page->page_key !== 'home') {
                $url = $this->getUrlForPageKey($page->page_key);
                if ($url) {
                    $fullUrl = $baseUrl . $url;
                    $lastmod = $page->updated_at->format('Y-m-d');
                    
                    $xml .= "  <url>\n";
                    $xml .= "    <loc>{$fullUrl}</loc>\n";
                    $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
                    $xml .= "    <changefreq>weekly</changefreq>\n";
                    $xml .= "    <priority>0.7</priority>\n";
                    $xml .= "  </url>\n";
                    $count++;
                }
            }
        }
        
        $this->info("✅ Added {$count} dynamic pages from SEO settings");
    }
    
    /**
     * Add Firestore content to XML (restaurants, products, categories)
     */
    private function addFirestoreContentToXml(&$xml, $baseUrl)
    {
        $this->info('🔥 Adding Firestore content...');
        
        try {
            // Check if Firebase is configured
            if (!config('firebase.project_id')) {
                $this->warn('⚠️ Firebase not configured, skipping Firestore content');
                return;
            }
            
            // Initialize Firebase
            $firestore = app('firebase.firestore');
            $count = 0;
            
            // Add restaurants
            $restaurants = $firestore->database()->collection('vendors')
                ->where('isOpen', '=', true)
                ->where('publish', '=', true)
                ->documents();
                
            foreach ($restaurants as $restaurant) {
                $data = $restaurant->data();
                if (isset($data['id']) && isset($data['name'])) {
                    $slug = $this->createSlug($data['name']);
                    $url = "/restaurant/{$data['id']}/{$slug}";
                    $fullUrl = $baseUrl . $url;
                    $lastmod = now()->format('Y-m-d');
                    
                    $xml .= "  <url>\n";
                    $xml .= "    <loc>{$fullUrl}</loc>\n";
                    $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
                    $xml .= "    <changefreq>daily</changefreq>\n";
                    $xml .= "    <priority>0.8</priority>\n";
                    $xml .= "  </url>\n";
                    $count++;
                }
            }
            
            // Add products
            $products = $firestore->database()->collection('mart_items')
                ->where('publish', '=', true)
                ->where('isAvailable', '=', true)
                ->documents();
                
            foreach ($products as $product) {
                $data = $product->data();
                if (isset($data['id']) && isset($data['name'])) {
                    $url = "/product/{$data['id']}";
                    $fullUrl = $baseUrl . $url;
                    $lastmod = now()->format('Y-m-d');
                    
                    $xml .= "  <url>\n";
                    $xml .= "    <loc>{$fullUrl}</loc>\n";
                    $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
                    $xml .= "    <changefreq>weekly</changefreq>\n";
                    $xml .= "    <priority>0.7</priority>\n";
                    $xml .= "  </url>\n";
                    $count++;
                }
            }
            
            // Add categories
            $categories = $firestore->database()->collection('mart_categories')
                ->where('publish', '=', true)
                ->documents();
                
            foreach ($categories as $category) {
                $data = $category->data();
                if (isset($data['id']) && isset($data['name'])) {
                    $slug = $this->createSlug($data['name']);
                    $url = "/category/{$data['id']}/{$slug}";
                    $fullUrl = $baseUrl . $url;
                    $lastmod = now()->format('Y-m-d');
                    
                    $xml .= "  <url>\n";
                    $xml .= "    <loc>{$fullUrl}</loc>\n";
                    $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
                    $xml .= "    <changefreq>weekly</changefreq>\n";
                    $xml .= "    <priority>0.6</priority>\n";
                    $xml .= "  </url>\n";
                    $count++;
                }
            }
            
            $this->info("✅ Added {$count} Firestore content pages");
            
        } catch (\Exception $e) {
            $this->warn("⚠️ Error adding Firestore content: " . $e->getMessage());
        }
    }
    
    /**
     * Get URL for page key
     */
    private function getUrlForPageKey($pageKey)
    {
        $urlMap = [
            'restaurants' => '/restaurants',
            'products' => '/products',
            'categories' => '/categories',
            'mart' => '/mart',
            'search' => '/search',
            'about' => '/about',
            'contact' => '/contact',
            'privacy' => '/privacy',
            'terms' => '/terms',
            'faq' => '/faq',
            'offers' => '/offers'
        ];
        
        return $urlMap[$pageKey] ?? null;
    }
    
    /**
     * Create URL-friendly slug
     */
    private function createSlug($text)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
    }
}
