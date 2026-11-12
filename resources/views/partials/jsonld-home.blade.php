{{--
    partials/jsonld-home.blade.php — structured data for Google (schema.org).
    Tells search engines who the business is (Organization) and what the site
    is (WebSite), which helps them show a richer result.
--}}
@php
    $sameAs = array_column(social_links(), 'url');
    $organization = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => setting('site_name'),
        'alternateName' => setting('site_name_ar') ?: null,
        'description' => setting('seo_description'),
        'url' => route('home'),
        'logo' => asset('assets/brand/rakiza-mark.png'),
        'email' => setting('contact_email') ?: null,
        'telephone' => setting('contact_phone') ?: null,
        'address' => setting('location') ? ['@type' => 'PostalAddress', 'addressLocality' => setting('location'), 'addressCountry' => 'EG'] : null,
        'sameAs' => $sameAs ?: null,
    ]);
    $website = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => setting('site_name'),
        'url' => route('home'),
        'inLanguage' => 'en',
    ];
@endphp
<script type="application/ld+json">@json($organization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG)</script>
<script type="application/ld+json">@json($website, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG)</script>
