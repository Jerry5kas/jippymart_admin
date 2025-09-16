<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SeoPage;
use App\Models\SeoSetting;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

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
            $sitemap = Sitemap::create();
            
            // Add static pages
            $this->addStaticPagesToSitemap($sitemap);
            
            // Add dynamic pages from database
            $this->addDynamicPagesToSitemap($sitemap);
            
            // Add Firestore content (if Firebase is configured)
            $this->addFirestoreContentToSitemap($sitemap);
            
            // Write sitemap to admin public directory
            $adminSitemapPath = public_path('sitemap.xml');
            $sitemap->writeToFile($adminSitemapPath);
            
            // Also copy to customer site root directory
            $customerSitemapPath = dirname(public_path()) . '/../sitemap.xml';
            copy($adminSitemapPath, $customerSitemapPath);
            
            $this->info('✅ Sitemap generated successfully!');
            $this->info('📍 Admin Location: ' . $adminSitemapPath);
            $this->info('📍 Customer Location: ' . $customerSitemapPath);
            $this->info('🌐 Admin URL: https://admin.jippymart.in/sitemap.xml');
            $this->info('🌐 Customer URL: https://jippymart.in/sitemap.xml');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Error generating sitemap: ' . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Add static pages to sitemap
     */
    private function addStaticPagesToSitemap(Sitemap $sitemap)
    {
        $this->info('📄 Adding static pages...');
        
        $baseUrl = 'https://jippymart.in';
        
        $staticPages = [
            '/' => ['priority' => 1.0, 'changefreq' => 'daily'],
            '/categories' => ['priority' => 0.9, 'changefreq' => 'weekly'],
            '/products' => ['priority' => 0.9, 'changefreq' => 'daily'],
            '/restaurants' => ['priority' => 0.9, 'changefreq' => 'daily'],
            '/page/about-us' => ['priority' => 0.6, 'changefreq' => 'monthly'],
            '/contact-us' => ['priority' => 0.6, 'changefreq' => 'monthly'],
        ];
        
        foreach ($staticPages as $url => $config) {
            $sitemap->add(
                Url::create($baseUrl . $url)
                    ->setLastModificationDate(now())
                    ->setChangeFrequency($config['changefreq'])
                    ->setPriority($config['priority'])
            );
        }
        
        $this->info('✅ Added ' . count($staticPages) . ' static pages');
    }
    
    /**
     * Add dynamic pages from database to sitemap
     */
    private function addDynamicPagesToSitemap(Sitemap $sitemap)
    {
        $this->info('🗄️ Adding dynamic pages from database...');
        
        $baseUrl = 'https://jippymart.in';
        
        // Add SEO pages that have specific URLs
        $seoPages = SeoPage::whereNotNull('title')->get();
        $count = 0;
        
        foreach ($seoPages as $page) {
            if ($page->page_key !== 'default' && $page->page_key !== 'home') {
                $url = $this->getUrlForPageKey($page->page_key);
                if ($url) {
                    $sitemap->add(
                        Url::create($baseUrl . $url)
                            ->setLastModificationDate($page->updated_at)
                            ->setChangeFrequency('weekly')
                            ->setPriority(0.7)
                    );
                    $count++;
                }
            }
        }
        
        $this->info("✅ Added {$count} dynamic pages from SEO settings");
    }
    
    /**
     * Add Firestore content to sitemap (restaurants, products, categories)
     */
    private function addFirestoreContentToSitemap(Sitemap $sitemap)
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
            $baseUrl = 'https://jippymart.in';
            
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
                    
                    $sitemap->add(
                        Url::create($baseUrl . $url)
                            ->setLastModificationDate(now())
                            ->setChangeFrequency('daily')
                            ->setPriority(0.8)
                    );
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
                    
                    $sitemap->add(
                        Url::create($baseUrl . $url)
                            ->setLastModificationDate(now())
                            ->setChangeFrequency('weekly')
                            ->setPriority(0.7)
                    );
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
                    
                    $sitemap->add(
                        Url::create($baseUrl . $url)
                            ->setLastModificationDate(now())
                            ->setChangeFrequency('weekly')
                            ->setPriority(0.6)
                    );
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
            'home' => '/',
            'restaurants' => '/restaurants',
            'products' => '/products',
            'categories' => '/categories',
            'mart' => '/mart',
            'search' => '/search',
            'about' => '/page/about-us',
            'contact' => '/contact-us',
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
