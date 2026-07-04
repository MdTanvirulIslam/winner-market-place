<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('store.products'), 'priority' => '0.9'],
            ['loc' => route('store.about'), 'priority' => '0.5'],
            ['loc' => route('store.contact'), 'priority' => '0.5'],
            ['loc' => route('store.terms'), 'priority' => '0.3'],
            ['loc' => route('store.privacy'), 'priority' => '0.3'],
            ['loc' => route('store.refund-policy'), 'priority' => '0.3'],
        ];

        foreach (Category::whereHas('products', fn ($query) => $query->published())->get() as $category) {
            $urls[] = [
                'loc' => route('store.products', ['category' => $category->slug]),
                'priority' => '0.6',
            ];
        }

        foreach (Product::published()->get() as $product) {
            $urls[] = [
                'loc' => route('store.products.show', $product->slug),
                'lastmod' => $product->updated_at->toAtomString(),
                'priority' => '0.8',
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . e($url['loc']) . "</loc>\n";
            if (isset($url['lastmod'])) {
                $xml .= '    <lastmod>' . $url['lastmod'] . "</lastmod>\n";
            }
            $xml .= '    <priority>' . $url['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
