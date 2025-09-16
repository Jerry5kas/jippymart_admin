<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google\Cloud\Firestore\FirestoreClient;

class RevertMediaPaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revert:media-paths {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revert media paths from /images/ back to /media/ with proper extensions';

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
        $this->info('🔧 Starting Media Path Revert Process...');
        
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
                
                $fixed = $this->revertCollectionPaths($collectionName, $photoFields, $isDryRun);
                $totalFixed += $fixed;
                
                $this->info("✅ Fixed {$fixed} documents in {$collectionName}");
            }
            
            $this->info("🎉 Revert completed! Total documents fixed: {$totalFixed}");
            
            if ($isDryRun) {
                $this->warn('🔍 This was a dry run. Run without --dry-run to apply changes.');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error during revert: ' . $e->getMessage());
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
    
    private function revertCollectionPaths($collectionName, $photoFields, $isDryRun)
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
                        $newUrl = $this->revertPhotoUrl($oldUrl);
                        
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
    
    private function revertPhotoUrl($url)
    {
        if (empty($url) || !is_string($url)) {
            return $url;
        }
        
        // Check if URL contains /images/ path that should be reverted to /media/
        if (strpos($url, '/images%2Fmedia_') !== false || strpos($url, '/images/media_') !== false) {
            // Replace /images/ with /media/ but keep the .jpg extension
            $url = str_replace('/images%2F', '/media%2F', $url);
            $url = str_replace('/images/', '/media/', $url);
        }
        
        return $url;
    }
}
