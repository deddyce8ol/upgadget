<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Slug Helper
 *
 * Provides utility functions for generating URL-friendly slugs
 * from category names and other strings.
 *
 * @package Putra Elektronik
 * @category Helpers
 */

if (!function_exists('generate_slug')) {
    /**
     * Generate URL-friendly slug from string
     *
     * Converts string to lowercase, replaces spaces with hyphens,
     * removes special characters, and handles Indonesian characters.
     *
     * @param string $string Input string to convert to slug
     * @return string URL-friendly slug
     *
     * @example
     * generate_slug('Televisi & Home Theater') returns 'televisi-home-theater'
     * generate_slug('Komputer Gaming') returns 'komputer-gaming'
     * generate_slug('AC (Air Conditioner)') returns 'ac-air-conditioner'
     */
    function generate_slug($string)
    {
        // Convert to lowercase
        $slug = strtolower($string);

        // Replace spaces with hyphens
        $slug = str_replace(' ', '-', $slug);

        // Remove special characters (keep only alphanumeric and hyphens)
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);

        // Remove consecutive hyphens
        $slug = preg_replace('/-+/', '-', $slug);

        // Trim hyphens from start and end
        $slug = trim($slug, '-');

        return $slug;
    }
}
