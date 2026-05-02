<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Sitemap index — lists all sub-sitemaps.
     */
    public function index(): Response
    {
        $categories = Category::where('is_active', true)->count();
        $listingsCount = Listing::where('status', 'active')->count();
        $listingPages = max(1, (int) ceil($listingsCount / 1000));

        $sitemaps = [];

        // Static pages
        $sitemaps[] = route('sitemap.static');

        // Categories
        if ($categories > 0) {
            $sitemaps[] = route('sitemap.categories');
        }

        // Listings (paginated — 1k per sitemap, Google's 50k/50MB limit is generous)
        for ($i = 1; $i <= $listingPages; $i++) {
            $sitemaps[] = route('sitemap.listings', ['page' => $i]);
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($sitemaps as $loc) {
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>" . e($loc) . "</loc>\n";
            $xml .= "    <lastmod>" . now()->toW3cString() . "</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }

        $xml .= '</sitemapindex>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    /**
     * Static pages sitemap.
     */
    public function static(): Response
    {
        $staticRoutes = [
            ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => route('search'), 'priority' => '0.8', 'changefreq' => 'weekly'],
        ];

        $xml = $this->buildUrlset($staticRoutes);

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    /**
     * Categories sitemap.
     */
    public function categories(): Response
    {
        $categories = Category::where('is_active', true)->get();
        $urls = [];

        foreach ($categories as $category) {
            $urls[] = [
                'loc' => route('category.show', $category->slug),
                'priority' => '0.9',
                'changefreq' => 'daily',
            ];
        }

        return response($this->buildUrlset($urls), 200, ['Content-Type' => 'application/xml']);
    }

    /**
     * Listings sitemap (paginated — 1,000 per page).
     */
    public function listings(int $page = 1): Response
    {
        $listings = Listing::where('status', 'active')
            ->orderBy('updated_at', 'desc')
            ->skip(($page - 1) * 1000)
            ->take(1000)
            ->get(['slug', 'updated_at']);

        $urls = [];

        foreach ($listings as $listing) {
            $urls[] = [
                'loc' => route('listing.show', $listing->slug),
                'priority' => '0.7',
                'changefreq' => 'weekly',
                'lastmod' => $listing->updated_at->toW3cString(),
            ];
        }

        return response($this->buildUrlset($urls), 200, ['Content-Type' => 'application/xml']);
    }

    /**
     * Generate robots.txt dynamically so the sitemap URL always reflects APP_URL.
     */
    public function robots(): Response
    {
        $sitemapUrl = route('sitemap.index');

        $body = <<<TXT
User-agent: *
Allow: /

# Block internal/auth pages from indexing
Disallow: /dashboard
Disallow: /login
Disallow: /register
Disallow: /password/
Disallow: /admin/
Disallow: /notifications
Disallow: /my-listings
Disallow: /favorites
Disallow: /offers
Disallow: /trashed
Disallow: /conversations

Sitemap: {$sitemapUrl}
TXT;

        return response($body, 200, ['Content-Type' => 'text/plain']);
    }

    /**
     * Build a <urlset> XML string from an array of URLs.
     */
    private function buildUrlset(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . e($url['loc']) . "</loc>\n";

            if (isset($url['lastmod'])) {
                $xml .= "    <lastmod>{$url['lastmod']}</lastmod>\n";
            }

            if (isset($url['changefreq'])) {
                $xml .= "    <changefreq>{$url['changefreq']}</changefreq>\n";
            }

            if (isset($url['priority'])) {
                $xml .= "    <priority>{$url['priority']}</priority>\n";
            }

            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
