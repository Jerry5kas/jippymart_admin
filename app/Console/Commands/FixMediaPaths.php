<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google\Cloud\Firestore\FirestoreClient;

class FixMediaPaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:media-paths {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix media paths from /media/ to /images/ in Firebase Storage and Firestore';

    protected $firestore;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔧 Starting Media Path Fix Process...');
        
        try {
        // Initialize Firebase client
        $this->initializeFirebase();
            
            $isDryRun = $this->option('dry-run');
            
            if ($isDryRun) {
                $this->warn('🔍 DRY RUN MODE - No changes will be made');
            }
            
            // Collections to check for photo fields
            $collections = [
                'mart_categories' => ['photo'],
                'mart_subcategories' => ['photo'],
                'mart_items' => ['photo'],
                'vendor_cuisines' => ['photo'],
                'categories' => ['photo'],
                'foods' => ['photo'],
                'vendors' => ['photo'],
                'restaurants' => ['photo'],
                'media' => ['image_path']
            ];
            
            $totalFixed = 0;
            
            foreach ($collections as $collectionName => $photoFields) {
                $this->info("📋 Processing collection: {$collectionName}");
                
                $fixed = $this->fixCollectionPaths($collectionName, $photoFields, $isDryRun);
                $totalFixed += $fixed;
                
                $this->info("✅ Fixed {$fixed} documents in {$collectionName}");
            }
            
            $this->info("🎉 Migration completed! Total documents fixed: {$totalFixed}");
            
            if ($isDryRun) {
                $this->warn('🔍 This was a dry run. Run without --dry-run to apply changes.');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error during migration: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function initializeFirebase()
    {
        $this->info('🔌 Initializing Firebase connection...');
        
        // Initialize Firestore
        $this->firestore = new FirestoreClient([
            'projectId' => config('firestore.project_id'),
            'keyFilePath' => config('firestore.credentials'),
        ]);
        
        $this->info('✅ Firebase connection initialized');
    }
    
    private function fixCollectionPaths($collectionName, $photoFields, $isDryRun)
    {
        $fixed = 0;
        
        try {
            $collection = $this->firestore->collection($collectionName);
            $documents = $collection->documents();
            
            foreach ($documents as $document) {
                if (!$document->exists()) {
                    continue;
                }
                
                $data = $document->data();
                $updated = false;
                $updateData = [];
                
                foreach ($photoFields as $field) {
                    if (isset($data[$field]) && !empty($data[$field])) {
                        $oldUrl = $data[$field];
                        $newUrl = $this->fixPhotoUrl($oldUrl);
                        
                        if ($oldUrl !== $newUrl) {
                            $updateData[$field] = $newUrl;
                            $updated = true;
                            
                            $this->line("  📝 {$collectionName}/{$document->id()}: {$field}");
                            $this->line("     Old: {$oldUrl}");
                            $this->line("     New: {$newUrl}");
                        }
                    }
                }
                
                if ($updated && !$isDryRun) {
                    // Convert updateData to the format expected by Firestore
                    $updateFields = [];
                    foreach ($updateData as $field => $value) {
                        $updateFields[] = ['path' => $field, 'value' => $value];
                    }
                    $collection->document($document->id())->update($updateFields);
                    $fixed++;
                } elseif ($updated && $isDryRun) {
                    $fixed++;
                }
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error processing collection {$collectionName}: " . $e->getMessage());
        }
        
        return $fixed;
    }
    
    private function fixPhotoUrl($url)
    {
        if (empty($url) || !is_string($url)) {
            return $url;
        }
        
        // Check if URL contains the problematic /media/ path without extension
        if (strpos($url, '/media%2Fmedia_') !== false || strpos($url, '/media/') !== false) {
            // Keep the /media/ path (since files are actually there) but add .jpg extension if missing
            if (!preg_match('/\.(jpg|jpeg|png|gif)$/i', $url)) {
                // Find the position before the query parameters
                $queryPos = strpos($url, '?');
                if ($queryPos !== false) {
                    $url = substr($url, 0, $queryPos) . '.jpg' . substr($url, $queryPos);
                } else {
                    $url .= '.jpg';
                }
            }
        }
        
        return $url;
    }
}
