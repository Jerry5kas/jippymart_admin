<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SeoPage;
use App\Models\SeoSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class SeoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of SEO pages
     */
    public function index()
    {
        $pages = SeoPage::orderBy('page_key')->get();
        $settings = SeoSetting::getAllSettings();
        
        return view('seo.index', compact('pages', 'settings'));
    }

    /**
     * Show the form for creating a new SEO page
     */
    public function create()
    {
        return view('seo.create');
    }

    /**
     * Store a newly created SEO page
     */
    public function store(Request $request)
    {
        $request->validate([
            'page_key' => 'required|string|unique:seo_pages,page_key|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'keywords' => 'nullable|string|max:500',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'page_key', 'title', 'description', 'keywords', 
            'og_title', 'og_description'
        ]);

        // Handle OG image upload
        if ($request->hasFile('og_image')) {
            $image = $request->file('og_image');
            $imageName = 'seo/' . time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public', $imageName);
            $data['og_image'] = 'storage/' . $imageName;
        }

        SeoPage::create($data);

        return redirect()->route('seo.index')
            ->with('success', 'SEO page created successfully.');
    }

    /**
     * Show the form for editing the specified SEO page
     */
    public function edit(SeoPage $seo)
    {
        return view('seo.edit', compact('seo'));
    }

    /**
     * Update the specified SEO page
     */
    public function update(Request $request, SeoPage $seo)
    {
        $request->validate([
            'page_key' => 'required|string|max:255|unique:seo_pages,page_key,' . $seo->id,
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'keywords' => 'nullable|string|max:500',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'page_key', 'title', 'description', 'keywords', 
            'og_title', 'og_description'
        ]);

        // Handle OG image upload
        if ($request->hasFile('og_image')) {
            // Delete old image if exists
            if ($seo->og_image && Storage::exists('public/' . str_replace('storage/', '', $seo->og_image))) {
                Storage::delete('public/' . str_replace('storage/', '', $seo->og_image));
            }

            $image = $request->file('og_image');
            $imageName = 'seo/' . time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public', $imageName);
            $data['og_image'] = 'storage/' . $imageName;
        }

        $seo->update($data);

        return redirect()->route('seo.index')
            ->with('success', 'SEO page updated successfully.');
    }

    /**
     * Remove the specified SEO page
     */
    public function destroy(SeoPage $seo)
    {
        // Delete OG image if exists
        if ($seo->og_image && Storage::exists('public/' . str_replace('storage/', '', $seo->og_image))) {
            Storage::delete('public/' . str_replace('storage/', '', $seo->og_image));
        }

        $seo->delete();

        return redirect()->route('seo.index')
            ->with('success', 'SEO page deleted successfully.');
    }

    /**
     * Update SEO settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'required|string|max:500',
            'default_og_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'twitter_handle' => 'nullable|string|max:255',
            'google_analytics_id' => 'nullable|string|max:255',
            'google_search_console_verification' => 'nullable|string|max:255',
            'facebook_app_id' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'business_address' => 'nullable|string|max:500',
        ]);

        $settings = $request->only([
            'site_name', 'site_description', 'twitter_handle', 
            'google_analytics_id', 'google_search_console_verification',
            'facebook_app_id', 'contact_email', 'contact_phone', 'business_address'
        ]);

        // Handle default OG image upload
        if ($request->hasFile('default_og_image')) {
            $image = $request->file('default_og_image');
            $imageName = 'seo/default_og_' . time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public', $imageName);
            $settings['default_og_image'] = 'storage/' . $imageName;
        }

        foreach ($settings as $key => $value) {
            SeoSetting::setValue($key, $value);
        }

        return redirect()->route('seo.index')
            ->with('success', 'SEO settings updated successfully.');
    }

    /**
     * Generate sitemap manually
     */
    public function generateSitemap()
    {
        try {
            Artisan::call('generate:sitemap');
            return redirect()->back()
                ->with('success', 'Sitemap generated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error generating sitemap: ' . $e->getMessage());
        }
    }

    /**
     * Preview sitemap
     */
    public function previewSitemap()
    {
        $sitemapPath = public_path('sitemap.xml');
        
        if (!file_exists($sitemapPath)) {
            return redirect()->back()
                ->with('error', 'Sitemap not found. Please generate it first.');
        }

        $sitemapContent = file_get_contents($sitemapPath);
        
        return response($sitemapContent, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'inline; filename="sitemap.xml"'
        ]);
    }

    /**
     * Get sitemap statistics
     */
    public function sitemapStats()
    {
        $sitemapPath = public_path('sitemap.xml');
        
        if (!file_exists($sitemapPath)) {
            return response()->json([
                'exists' => false,
                'message' => 'Sitemap not found'
            ]);
        }

        $sitemapContent = file_get_contents($sitemapPath);
        $urlCount = substr_count($sitemapContent, '<url>');
        $lastModified = date('Y-m-d H:i:s', filemtime($sitemapPath));
        $fileSize = filesize($sitemapPath);

        return response()->json([
            'exists' => true,
            'url_count' => $urlCount,
            'last_modified' => $lastModified,
            'file_size' => $fileSize,
            'file_size_formatted' => $this->formatBytes($fileSize)
        ]);
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
