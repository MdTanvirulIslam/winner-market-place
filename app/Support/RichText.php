<?php

namespace App\Support;

// Rendering helpers for admin-authored rich text (Quill) that must also keep
// working for legacy plain-text values.
final class RichText
{
    /**
     * Tags allowed through in rendered rich text. Only staff author this
     * content — the allowlist is defense-in-depth, not a sandbox.
     */
    private const ALLOWED_TAGS = '<p><br><strong><em><u><s><a><ul><ol><li><h2><h3><h4><blockquote><pre><code><span>';

    /**
     * Safe HTML: legacy plain text keeps its line breaks; HTML passes
     * through the tag allowlist.
     */
    public static function html(?string $value): string
    {
        $value ??= '';

        if ($value === strip_tags($value)) {
            return nl2br(e($value));
        }

        return strip_tags($value, self::ALLOWED_TAGS);
    }

    /**
     * One entry per non-empty line — accepts both legacy plain text and
     * Quill HTML (paragraphs or bullet lists).
     *
     * @return list<string>
     */
    public static function lines(?string $value): array
    {
        $text = preg_replace('/<\/(p|li|div)>|<br\s*\/?>/i', "\n", $value ?? '');

        return array_values(array_filter(array_map(
            fn (string $line) => trim(html_entity_decode(strip_tags($line), ENT_QUOTES)),
            explode("\n", $text)
        ), fn (string $line) => $line !== ''));
    }

    /**
     * Plain text for card excerpts and meta tags.
     */
    public static function text(?string $value): string
    {
        return trim(html_entity_decode(strip_tags($value ?? ''), ENT_QUOTES));
    }
}
