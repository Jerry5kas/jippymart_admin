<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class MigrationController extends Controller
{
    /**
     * Show the migration page
     */
    public function index()
    {
        return view('admin.migration');
    }
    
    /**
     * Run the media path fix migration
     */
    public function fixMediaPaths(Request $request)
    {
        try {
            $isDryRun = $request->input('dry_run', true);
            
            Log::info('Starting media path migration', ['dry_run' => $isDryRun]);
            
            // Run the artisan command
            $exitCode = Artisan::call('fix:media-paths', [
                '--dry-run' => $isDryRun
            ]);
            
            $output = Artisan::output();
            
            if ($exitCode === 0) {
                // Parse the output to extract useful information
                $totalFixed = $this->extractTotalFixed($output);
                $collectionsProcessed = $this->extractCollectionsProcessed($output);
                
                return response()->json([
                    'success' => true,
                    'total_fixed' => $totalFixed,
                    'collections_processed' => $collectionsProcessed,
                    'details' => $output,
                    'message' => $isDryRun ? 'Dry run completed successfully' : 'Migration completed successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Migration failed: ' . $output
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Migration failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Migration failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Extract total fixed count from command output
     */
    private function extractTotalFixed($output)
    {
        if (preg_match('/Total documents fixed: (\d+)/', $output, $matches)) {
            return (int) $matches[1];
        }
        return 0;
    }
    
    /**
     * Extract collections processed from command output
     */
    private function extractCollectionsProcessed($output)
    {
        $collections = [];
        if (preg_match_all('/Processing collection: ([a-z_]+)/', $output, $matches)) {
            $collections = $matches[1];
        }
        return count($collections);
    }
}
