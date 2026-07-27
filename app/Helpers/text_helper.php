<?php

use Illuminate\Support\Str;

if (! function_exists('textTransform')) {
    /**
     * Transforms text according to the specified case format.
     *
     * @param string $text The input text to be transformed
     * @param int    $case The transformation type to apply
     */
    function textTransform($text, $case = 0)
    {
        return match ($case) {
            1 => strtolower($text),
            2 => strtoupper($text),
            3 => ucwords($text),
            4 => ucfirst($text),
            default => $text,
        };
    }
}

if (! function_exists('slug')) {
    /**
     * Converts a string into a URL-friendly slug.
     *
     * @param string $title         The string to be converted into a slug
     * @param string $separator     The separator to use between words
     * @param int    $textTransform The text transformation to apply after slugging
     */
    function slug($title, $separator = '-', $textTransform = 0)
    {
        $slug = Str::slug($title, $separator);

        return textTransform($slug, $textTransform);
    }
}

if (! function_exists('slugToTitle')) {
    /**
     * Converts a URL-friendly slug back into a readable title.
     *
     * @param string $slug          The slug to be converted into a title
     * @param string $separator     The separator used between words in the slug
     * @param int    $textTransform The text transformation to apply to each word
     */
    function slugToTitle($slug, $separator = '-', $textTransform = 0)
    {
        return Str::of($slug)
            ->explode($separator)
            ->map(fn ($word) => textTransform($word, $textTransform))
            ->implode(' ');
    }
}
