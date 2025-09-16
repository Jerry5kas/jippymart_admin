@php
    use App\Models\SeoPage;
    use App\Models\SeoSetting;
    
    // Get current page SEO data
    $currentPage = request()->route()->getName() ?? 'home';
    $seoData = SeoPage::getPageSeo($currentPage) ?? SeoPage::getDefaultSeo();
    $settings = SeoSetting::getAllSettings();
    
    // Replace dynamic placeholders if needed
    $title = $seoData->title ?? $settings['site_name'] ?? 'JippyMart';
    $description = $seoData->description ?? $settings['site_description'] ?? '';
    $keywords = $seoData->keywords ?? '';
    $ogTitle = $seoData->og_title ?? $title;
    $ogDescription = $seoData->og_description ?? $description;
    $ogImage = $seoData->og_image_url ?? asset($settings['default_og_image'] ?? '/images/og-default.jpg');
    
    // Get current URL
    $currentUrl = request()->url();
    $siteName = $settings['site_name'] ?? 'JippyMart';
@endphp

<!-- SEO Meta Tags -->
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<meta name="author" content="{{ $siteName }}">

<!-- Open Graph Meta Tags -->
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:url" content="{{ $currentUrl }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $siteName }}">

<!-- Twitter Card Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $ogTitle }}">
<meta name="twitter:description" content="{{ $ogDescription }}">
<meta name="twitter:image" content="{{ $ogImage }}">
@if(isset($settings['twitter_handle']) && $settings['twitter_handle'])
<meta name="twitter:site" content="@{{ $settings['twitter_handle'] }}">
@endif

<!-- Additional SEO Meta Tags -->
<meta name="robots" content="index, follow">
<meta name="googlebot" content="index, follow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="UTF-8">

<!-- Canonical URL -->
<link rel="canonical" href="{{ $currentUrl }}">

<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

<!-- Google Analytics -->
@if(isset($settings['google_analytics_id']) && $settings['google_analytics_id'])
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $settings['google_analytics_id'] }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{{ $settings['google_analytics_id'] }}');
</script>
@endif

<!-- Google Search Console Verification -->
@if(isset($settings['google_search_console_verification']) && $settings['google_search_console_verification'])
<meta name="google-site-verification" content="{{ $settings['google_search_console_verification'] }}">
@endif

<!-- Facebook App ID -->
@if(isset($settings['facebook_app_id']) && $settings['facebook_app_id'])
<meta property="fb:app_id" content="{{ $settings['facebook_app_id'] }}">
@endif

<!-- Structured Data (JSON-LD) -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "{{ $siteName }}",
  "url": "{{ url('/') }}",
  "logo": "{{ asset('images/logo.png') }}",
  "description": "{{ $settings['site_description'] ?? 'Your one-stop destination for groceries, medicines, and daily essentials' }}",
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "{{ $settings['contact_phone'] ?? '+91-XXXXXXXXXX' }}",
    "contactType": "customer service",
    "email": "{{ $settings['contact_email'] ?? 'info@jippymart.in' }}"
  },
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "{{ $settings['business_address'] ?? 'Your Business Address' }}",
    "addressCountry": "IN"
  },
  "sameAs": [
    @if(isset($settings['twitter_handle']) && $settings['twitter_handle'])
    "https://twitter.com/{{ $settings['twitter_handle'] }}",
    @endif
    "https://jippymart.in"
  ]
}
</script>
