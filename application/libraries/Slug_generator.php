<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Slug Generator Library
 *
 * Generate URL-friendly slugs from strings with duplicate handling
 *
 * @package     UPGADGET
 * @subpackage  Libraries
 * @category    Utilities
 * @author      PT. Qapuas Media Technologies
 * @since       Version 1.0.0
 */
class Slug_generator {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    /**
     * Generate URL-friendly slug from string
     *
     * @param string $text The text to convert to slug
     * @param int $max_length Maximum length of slug (default: 200)
     * @return string The generated slug
     */
    public function generate($text, $max_length = 200)
    {
        // Convert to lowercase
        $slug = strtolower($text);

        // Replace Indonesian characters
        $slug = str_replace(
            array('á', 'à', 'â', 'ã', 'ä', 'é', 'è', 'ê', 'ë', 'í', 'ì', 'î', 'ï', 'ó', 'ò', 'ô', 'õ', 'ö', 'ú', 'ù', 'û', 'ü', 'ñ', 'ç'),
            array('a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'n', 'c'),
            $slug
        );

        // Replace spaces and special characters with dash
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

        // Remove multiple dashes
        $slug = preg_replace('/-+/', '-', $slug);

        // Remove dash from start and end
        $slug = trim($slug, '-');

        // Limit length
        if (strlen($slug) > $max_length) {
            $slug = substr($slug, 0, $max_length);
            // Remove trailing dash if any
            $slug = rtrim($slug, '-');
        }

        return $slug;
    }

    /**
     * Generate unique slug for products table
     *
     * @param string $text The text to convert to slug
     * @param int|null $exclude_id Product ID to exclude from uniqueness check (for updates)
     * @param int $max_length Maximum length of slug
     * @return string Unique slug
     */
    public function make_unique($text, $exclude_id = null, $max_length = 200)
    {
        $slug = $this->generate($text, $max_length);
        $original_slug = $slug;
        $counter = 1;

        // Check if slug exists in database
        while ($this->slug_exists($slug, $exclude_id)) {
            // Append counter to make it unique
            $suffix = '-' . $counter;
            $max_base_length = $max_length - strlen($suffix);

            if (strlen($original_slug) > $max_base_length) {
                $base_slug = substr($original_slug, 0, $max_base_length);
                $base_slug = rtrim($base_slug, '-');
            } else {
                $base_slug = $original_slug;
            }

            $slug = $base_slug . $suffix;
            $counter++;

            // Safety limit to prevent infinite loop
            if ($counter > 1000) {
                // Append timestamp as last resort
                $slug = $original_slug . '-' . time();
                break;
            }
        }

        return $slug;
    }

    /**
     * Check if slug already exists in products table
     *
     * @param string $slug The slug to check
     * @param int|null $exclude_id Product ID to exclude from check
     * @return bool True if exists, False if unique
     */
    private function slug_exists($slug, $exclude_id = null)
    {
        $this->CI->db->where('product_slug', $slug);

        if ($exclude_id !== null) {
            $this->CI->db->where('product_id !=', $exclude_id);
        }

        $query = $this->CI->db->get('products');

        return $query->num_rows() > 0;
    }

    /**
     * Generate slug for category
     *
     * @param string $text Category name
     * @param int|null $exclude_id Category ID to exclude
     * @return string Unique category slug
     */
    public function make_unique_category($text, $exclude_id = null)
    {
        $slug = $this->generate($text, 150);
        $original_slug = $slug;
        $counter = 1;

        while ($this->category_slug_exists($slug, $exclude_id)) {
            $slug = $original_slug . '-' . $counter;
            $counter++;

            if ($counter > 1000) {
                $slug = $original_slug . '-' . time();
                break;
            }
        }

        return $slug;
    }

    /**
     * Check if category slug exists
     *
     * @param string $slug The slug to check
     * @param int|null $exclude_id Category ID to exclude
     * @return bool
     */
    private function category_slug_exists($slug, $exclude_id = null)
    {
        $this->CI->db->where('slug', $slug);

        if ($exclude_id !== null) {
            $this->CI->db->where('id !=', $exclude_id);
        }

        $query = $this->CI->db->get('categories');

        return $query->num_rows() > 0;
    }
}
