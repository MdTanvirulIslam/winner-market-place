<?php

namespace Tests\Unit;

use App\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductRichTextTest extends TestCase
{
    private function product(array $attributes): Product
    {
        return (new Product)->forceFill($attributes);
    }

    public function test_feature_list_parses_legacy_plain_text(): void
    {
        $product = $this->product(['features' => "Responsive design\n\nAdmin dashboard\n"]);

        $this->assertSame(['Responsive design', 'Admin dashboard'], $product->featureList());
    }

    public function test_feature_list_parses_quill_bullet_lists_and_paragraphs(): void
    {
        $product = $this->product([
            'features' => '<ul><li>Responsive <strong>design</strong></li><li>Admin dashboard</li></ul>',
            'requirements' => '<p>PHP 8.2+</p><p>MySQL 5.7+</p><p><br></p>',
        ]);

        $this->assertSame(['Responsive design', 'Admin dashboard'], $product->featureList());
        $this->assertSame(['PHP 8.2+', 'MySQL 5.7+'], $product->requirementList());
    }

    public function test_description_html_keeps_allowed_tags_and_strips_scripts(): void
    {
        $product = $this->product([
            'description' => '<h2>Overview</h2><p>Great <strong>app</strong>.</p><script>alert(1)</script>',
        ]);

        $html = $product->descriptionHtml();
        $this->assertStringContainsString('<h2>Overview</h2>', $html);
        $this->assertStringContainsString('<strong>app</strong>', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_description_html_preserves_legacy_plain_text_line_breaks(): void
    {
        $product = $this->product(['description' => "First line\nSecond line"]);

        $this->assertSame("First line<br />\nSecond line", $product->descriptionHtml());
    }

    public function test_short_description_text_strips_markup(): void
    {
        $product = $this->product(['short_description' => '<p>A &amp; B <em>store</em></p>']);

        $this->assertSame('A & B store', $product->shortDescriptionText());
    }
}
