@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Media Path Migration</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">Media Path Migration</li>
            </ol>
        </div>
    </div>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Fix Media Paths from /media/ to /images/</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="mdi mdi-information-outline"></i> What this migration does:</h5>
                            <ul>
                                <li>Fixes photo URLs that use the wrong <code>/media/</code> path</li>
                                <li>Changes them to the correct <code>/images/</code> path</li>
                                <li>Adds missing file extensions (.jpg) where needed</li>
                                <li>Updates all collections: mart_categories, mart_items, vendors, etc.</li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h5><i class="mdi mdi-alert"></i> Important Notes:</h5>
                            <ul>
                                <li>This will update your Firestore database</li>
                                <li>Run the dry-run first to see what will be changed</li>
                                <li>Make sure you have a backup before running the actual migration</li>
                            </ul>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-body text-center">
                                        <h5 class="card-title text-primary">Step 1: Dry Run</h5>
                                        <p class="card-text">See what will be changed without making any modifications</p>
                                        <button type="button" class="btn btn-outline-primary" onclick="runMigration(true)">
                                            <i class="mdi mdi-eye"></i> Run Dry Run
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <h5 class="card-title text-success">Step 2: Apply Changes</h5>
                                        <p class="card-text">Actually fix the media paths in your database</p>
                                        <button type="button" class="btn btn-success" onclick="runMigration(false)">
                                            <i class="mdi mdi-check"></i> Apply Migration
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="migration-results" class="mt-4" style="display: none;">
                            <h5>Migration Results:</h5>
                            <div id="results-content" class="border p-3 bg-light" style="max-height: 400px; overflow-y: auto;">
                                <!-- Results will be displayed here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function runMigration(isDryRun) {
    const button = event.target;
    const originalText = button.innerHTML;
    
    // Disable button and show loading
    button.disabled = true;
    button.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Running...';
    
    // Show results area
    document.getElementById('migration-results').style.display = 'block';
    document.getElementById('results-content').innerHTML = '<div class="text-center"><i class="mdi mdi-loading mdi-spin"></i> Running migration...</div>';
    
    // Make AJAX request
    fetch('{{ route("admin.migration.fix-media-paths") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            dry_run: isDryRun
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('results-content').innerHTML = `
                <div class="alert alert-success">
                    <h6><i class="mdi mdi-check-circle"></i> Migration ${isDryRun ? 'Dry Run' : 'Completed'} Successfully!</h6>
                    <p><strong>Total documents fixed:</strong> ${data.total_fixed}</p>
                    <p><strong>Collections processed:</strong> ${data.collections_processed}</p>
                    ${data.details ? `<pre class="mt-2">${data.details}</pre>` : ''}
                </div>
            `;
        } else {
            document.getElementById('results-content').innerHTML = `
                <div class="alert alert-danger">
                    <h6><i class="mdi mdi-alert-circle"></i> Migration Failed</h6>
                    <p>${data.message}</p>
                </div>
            `;
        }
    })
    .catch(error => {
        document.getElementById('results-content').innerHTML = `
            <div class="alert alert-danger">
                <h6><i class="mdi mdi-alert-circle"></i> Error</h6>
                <p>${error.message}</p>
            </div>
        `;
    })
    .finally(() => {
        // Re-enable button
        button.disabled = false;
        button.innerHTML = originalText;
    });
}
</script>
@endsection
