@extends('layouts.app')

@section('title', 'SEO Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-search"></i> SEO Management
                    </h3>
                    <div>
                        <button type="button" class="btn btn-info btn-sm" onclick="generateSitemap()">
                            <i class="fas fa-sitemap"></i> Generate Sitemap
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="previewSitemap()">
                            <i class="fas fa-eye"></i> Preview Sitemap
                        </button>
                        <a href="{{ route('seo.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New SEO Page
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Sitemap Status Card -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-sitemap"></i> Sitemap Status
                                    </h5>
                                    <div id="sitemap-stats">
                                        <div class="d-flex justify-content-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Pages Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Page Key</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>OG Image</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pages as $page)
                                    <tr>
                                        <td>
                                            <strong>{{ $page->page_key }}</strong>
                                            @if($page->page_key === 'default')
                                                <span class="badge badge-info">Default</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ Str::limit($page->title, 50) }}
                                        </td>
                                        <td>
                                            {{ Str::limit($page->description, 80) }}
                                        </td>
                                        <td>
                                            @if($page->og_image)
                                                <img src="{{ asset($page->og_image) }}" alt="OG Image" 
                                                     style="width: 50px; height: 50px; object-fit: cover;" 
                                                     class="rounded">
                                            @else
                                                <span class="text-muted">No image</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $page->created_at->format('M d, Y') }}
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('seo.edit', $page) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                @if($page->page_key !== 'default')
                                                    <form action="{{ route('seo.destroy', $page) }}" 
                                                          method="POST" style="display: inline-block;"
                                                          onsubmit="return confirm('Are you sure you want to delete this SEO page?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            No SEO pages found. <a href="{{ route('seo.create') }}">Create your first one</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEO Settings Card -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog"></i> Global SEO Settings
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('seo.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="site_name">Site Name</label>
                                    <input type="text" class="form-control" id="site_name" name="site_name" 
                                           value="{{ $settings['site_name'] ?? 'JippyMart' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="twitter_handle">Twitter Handle</label>
                                    <input type="text" class="form-control" id="twitter_handle" name="twitter_handle" 
                                           value="{{ $settings['twitter_handle'] ?? '' }}" 
                                           placeholder="@jippymart">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="site_description">Site Description</label>
                            <textarea class="form-control" id="site_description" name="site_description" rows="3" required>{{ $settings['site_description'] ?? '' }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="google_analytics_id">Google Analytics ID</label>
                                    <input type="text" class="form-control" id="google_analytics_id" name="google_analytics_id" 
                                           value="{{ $settings['google_analytics_id'] ?? '' }}" 
                                           placeholder="GA_MEASUREMENT_ID">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_email">Contact Email</label>
                                    <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                           value="{{ $settings['contact_email'] ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="default_og_image">Default OG Image</label>
                            @if(isset($settings['default_og_image']) && $settings['default_og_image'])
                                <div class="mb-2">
                                    <img src="{{ asset($settings['default_og_image']) }}" alt="Current OG Image" 
                                         style="max-width: 200px; max-height: 100px;" class="img-thumbnail">
                                    <br>
                                    <small class="text-muted">Current image</small>
                                </div>
                            @endif
                            <input type="file" class="form-control-file" id="default_og_image" name="default_og_image" accept="image/*">
                            <small class="form-text text-muted">Recommended: 1200x630 pixels</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateSitemap() {
    if (confirm('Generate sitemap now? This may take a few moments.')) {
        window.location.href = '{{ route("seo.generate-sitemap") }}';
    }
}

function previewSitemap() {
    window.open('{{ route("seo.preview-sitemap") }}', '_blank');
}

// Load sitemap stats
document.addEventListener('DOMContentLoaded', function() {
    fetch('{{ route("seo.sitemap-stats") }}')
        .then(response => response.json())
        .then(data => {
            const statsDiv = document.getElementById('sitemap-stats');
            if (data.exists) {
                statsDiv.innerHTML = `
                    <div class="row">
                        <div class="col-md-3">
                            <strong>URLs:</strong> ${data.url_count}
                        </div>
                        <div class="col-md-3">
                            <strong>File Size:</strong> ${data.file_size_formatted}
                        </div>
                        <div class="col-md-6">
                            <strong>Last Modified:</strong> ${data.last_modified}
                        </div>
                    </div>
                `;
            } else {
                statsDiv.innerHTML = `
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle"></i> Sitemap not found. Click "Generate Sitemap" to create one.
                    </div>
                `;
            }
        })
        .catch(error => {
            document.getElementById('sitemap-stats').innerHTML = `
                <div class="alert alert-danger mb-0">
                    <i class="fas fa-exclamation-circle"></i> Error loading sitemap stats.
                </div>
            `;
        });
});
</script>
@endsection
