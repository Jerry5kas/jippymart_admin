@extends('layouts.app')

@section('title', 'Edit SEO Page')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Edit SEO Page: {{ $seo->page_key }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('seo.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <form action="{{ route('seo.update', $seo) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="page_key">Page Key <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('page_key') is-invalid @enderror" 
                                           id="page_key" name="page_key" value="{{ old('page_key', $seo->page_key) }}" 
                                           placeholder="e.g., home, product, restaurant" required>
                                    @error('page_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Unique identifier for this page (used in code). Use lowercase, no spaces.
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Page Title</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title', $seo->title) }}" 
                                           placeholder="Page title for search engines" maxlength="255">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Recommended: 50-60 characters
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Meta Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Description for search engines" maxlength="500">{{ old('description', $seo->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Recommended: 150-160 characters
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="keywords">Keywords</label>
                            <input type="text" class="form-control @error('keywords') is-invalid @enderror" 
                                   id="keywords" name="keywords" value="{{ old('keywords', $seo->keywords) }}" 
                                   placeholder="food delivery, grocery, online ordering" maxlength="500">
                            @error('keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Comma-separated keywords
                            </small>
                        </div>

                        <hr>
                        <h5>Open Graph (Social Media) Settings</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="og_title">OG Title</label>
                                    <input type="text" class="form-control @error('og_title') is-invalid @enderror" 
                                           id="og_title" name="og_title" value="{{ old('og_title', $seo->og_title) }}" 
                                           placeholder="Title for social media sharing" maxlength="255">
                                    @error('og_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="og_image">OG Image</label>
                                    @if($seo->og_image)
                                        <div class="mb-2">
                                            <img src="{{ asset($seo->og_image) }}" alt="Current OG Image" 
                                                 style="max-width: 200px; max-height: 100px;" class="img-thumbnail">
                                            <br>
                                            <small class="text-muted">Current image</small>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control-file @error('og_image') is-invalid @enderror" 
                                           id="og_image" name="og_image" accept="image/*">
                                    @error('og_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Recommended: 1200x630 pixels. Leave empty to keep current image.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="og_description">OG Description</label>
                            <textarea class="form-control @error('og_description') is-invalid @enderror" 
                                      id="og_description" name="og_description" rows="3" 
                                      placeholder="Description for social media sharing" maxlength="500">{{ old('og_description', $seo->og_description) }}</textarea>
                            @error('og_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- SEO Preview -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5>SEO Preview</h5>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="seo-preview">
                                            <div class="search-result">
                                                <h3 class="search-title" id="preview-title">{{ $seo->title ?: 'Your page title will appear here' }}</h3>
                                                <div class="search-url" id="preview-url">https://jippymart.in/{{ $seo->page_key }}</div>
                                                <div class="search-description" id="preview-description">{{ $seo->description ?: 'Your meta description will appear here' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update SEO Page
                        </button>
                        <a href="{{ route('seo.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.seo-preview {
    max-width: 600px;
}

.search-result {
    margin-bottom: 20px;
}

.search-title {
    color: #1a0dab;
    font-size: 18px;
    font-weight: normal;
    margin: 0 0 3px 0;
    line-height: 1.2;
}

.search-title:hover {
    text-decoration: underline;
}

.search-url {
    color: #006621;
    font-size: 14px;
    margin: 0 0 3px 0;
}

.search-description {
    color: #545454;
    font-size: 14px;
    line-height: 1.4;
    margin: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    const pageKeyInput = document.getElementById('page_key');
    
    const previewTitle = document.getElementById('preview-title');
    const previewDescription = document.getElementById('preview-description');
    const previewUrl = document.getElementById('preview-url');

    function updatePreview() {
        const title = titleInput.value || 'Your page title will appear here';
        const description = descriptionInput.value || 'Your meta description will appear here';
        const pageKey = pageKeyInput.value || 'your-page';
        
        previewTitle.textContent = title;
        previewDescription.textContent = description;
        previewUrl.textContent = `https://jippymart.in/${pageKey}`;
    }

    titleInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    pageKeyInput.addEventListener('input', updatePreview);
    
    // Character counters
    function addCharacterCounter(inputId, maxLength) {
        const input = document.getElementById(inputId);
        const counter = document.createElement('small');
        counter.className = 'form-text text-muted character-counter';
        counter.style.float = 'right';
        input.parentNode.appendChild(counter);
        
        function updateCounter() {
            const length = input.value.length;
            counter.textContent = `${length}/${maxLength}`;
            if (length > maxLength * 0.9) {
                counter.className = 'form-text text-warning character-counter';
            } else {
                counter.className = 'form-text text-muted character-counter';
            }
        }
        
        input.addEventListener('input', updateCounter);
        updateCounter();
    }
    
    addCharacterCounter('title', 60);
    addCharacterCounter('description', 160);
    addCharacterCounter('og_title', 60);
    addCharacterCounter('og_description', 160);
});
</script>
@endsection
