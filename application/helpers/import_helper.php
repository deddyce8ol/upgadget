<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Import Helper
 *
 * Helper functions for product import from CSV
 * - Brand extraction
 * - Category keyword matching
 * - Slug generation
 * - Data sanitization
 */

if (!function_exists('extract_brand_from_name')) {
    /**
     * Extract brand name from product name
     * Usually brand is the first word/phrase in product name
     *
     * @param string $product_name
     * @return string Brand name
     */
    function extract_brand_from_name($product_name)
    {
        // Remove leading/trailing spaces
        $name = trim($product_name);

        // Common brand patterns - check for specific brands first
        $known_brands = [
            'SHARP/POLYTRON' => 'Sharp',
            'MASPION' => 'Maspion',
            'COSMOS' => 'Cosmos',
            'PHILIPS' => 'Philips',
            'SAMSUNG' => 'Samsung',
            'LG' => 'LG',
            'PANASONIC' => 'Panasonic',
            'SONY' => 'Sony',
            'POLYTRON' => 'Polytron',
            'SHARP' => 'Sharp',
            'MIYAKO' => 'Miyako',
            'SANKEN' => 'Sanken',
            'DENPOO' => 'Denpoo',
            'MODENA' => 'Modena',
            'ELECTROLUX' => 'Electrolux',
            'ARISTON' => 'Ariston',
            'RINNAI' => 'Rinnai',
            'QUANTUM' => 'Quantum',
            'OXONE' => 'Oxone',
            'AMONO' => 'Amono',
            'SAMONO' => 'Samono',
            'GREE' => 'Gree',
            'RSA' => 'RSA',
            'TOSHIBA' => 'Toshiba',
            'HAIER' => 'Haier',
            'MIDEA' => 'Midea',
            'DAIKIN' => 'Daikin',
            'AQUA' => 'Aqua',
            'CHANGHONG' => 'Changhong',
        ];

        // Check for known brands (case insensitive)
        $name_upper = strtoupper($name);
        foreach ($known_brands as $pattern => $brand) {
            if (strpos($name_upper, $pattern) === 0) {
                return $brand;
            }
        }

        // If no known brand found, extract first word
        $words = explode(' ', $name);
        if (count($words) > 0) {
            $first_word = trim($words[0]);

            // Clean up - remove special characters but keep alphanumeric
            $first_word = preg_replace('/[^a-zA-Z0-9]/', '', $first_word);

            // If it's too short or numeric only, try next word
            if (strlen($first_word) < 2 || is_numeric($first_word)) {
                if (isset($words[1])) {
                    $first_word = trim($words[1]);
                    $first_word = preg_replace('/[^a-zA-Z0-9]/', '', $first_word);
                }
            }

            // Capitalize first letter
            return ucfirst(strtolower($first_word));
        }

        return 'Unknown';
    }
}

if (!function_exists('match_category_from_name')) {
    /**
     * Match category from product name using keyword matching
     *
     * @param string $product_name
     * @param array $categories List of existing categories from database
     * @return int|null Category ID or null if no match
     */
    function match_category_from_name($product_name, $categories = [])
    {
        $name_lower = strtolower($product_name);

        // Category keywords mapping
        $category_keywords = [
            'Televisi' => ['tv', 'televisi', 'television', 'smart tv', 'led tv', 'lcd'],
            'Kulkas' => ['kulkas', 'lemari es', 'refrigerator', 'freezer', 'chest freezer', 'showcase'],
            'Mesin Cuci' => ['mesin cuci', 'washing machine', 'washer'],
            'AC (Air Conditioner)' => ['ac ', 'air conditioner', 'air conditioning', 'a/c'],
            'Kipas Angin' => ['kipas', 'fan', 'kipas angin', 'stand fan', 'desk fan', 'wall fan', 'exhaust fan'],
            'Blender' => ['blender', 'juicer', 'mixer', 'chopper', 'food processor', 'meat grinder'],
            'Rice Cooker' => ['rice cooker', 'magic com', 'penanak nasi'],
            'Kompor' => ['kompor', 'stove', 'kompor gas', 'gas stove'],
            'Dispenser' => ['dispenser', 'water dispenser'],
            'Setrika' => ['setrika', 'iron', 'gosokan'],
            'Vacuum Cleaner' => ['vacuum', 'vakum', 'penghisap debu'],
            'Microwave' => ['microwave', 'oven', 'microwave oven'],
            'Air Fryer' => ['air fryer', 'airfryer', 'penggorengan'],
            'Water Heater' => ['water heater', 'pemanas air'],
            'Audio & Speaker' => ['speaker', 'audio', 'sound', 'home theater', 'subwoofer'],
        ];

        // Try to match with keywords
        foreach ($category_keywords as $category_name => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($name_lower, $keyword) !== false) {
                    // Find matching category in database
                    foreach ($categories as $cat) {
                        if (stripos($cat->name, $category_name) !== false ||
                            stripos($category_name, $cat->name) !== false) {
                            return $cat->id;
                        }
                    }
                }
            }
        }

        // If no match found, return null (will use default category)
        return null;
    }
}

if (!function_exists('generate_unique_slug')) {
    /**
     * Generate unique slug for product
     *
     * @param string $product_name
     * @param object $ci CodeIgniter instance
     * @param string|null $sku Optional SKU for uniqueness
     * @return string Unique slug
     */
    function generate_unique_slug($product_name, $ci, $sku = null)
    {
        // Generate base slug from product name
        $ci->load->helper('url');
        $base_slug = url_title($product_name, 'dash', TRUE);

        // Limit length to avoid too long slugs
        if (strlen($base_slug) > 200) {
            $base_slug = substr($base_slug, 0, 200);
        }

        $slug = $base_slug;
        $counter = 1;

        // Check if slug exists and append number if needed
        while ($ci->Product_model->check_slug_exists($slug)) {
            if ($counter == 1 && $sku) {
                // First try: append SKU
                $slug = $base_slug . '-' . $sku;
            } else {
                // Subsequent tries: append counter
                $slug = $base_slug . '-' . $counter;
            }
            $counter++;

            // Safety limit to prevent infinite loop
            if ($counter > 1000) {
                // If we've tried 1000 times, append random string
                $slug = $base_slug . '-' . uniqid();
                break;
            }
        }

        return $slug;
    }
}

if (!function_exists('sanitize_csv_data')) {
    /**
     * Sanitize and validate CSV row data
     *
     * @param array $row CSV row data
     * @return array Sanitized data
     */
    function sanitize_csv_data($row)
    {
        return [
            'sku' => trim($row[0] ?? ''),
            'price' => floatval($row[1] ?? 0),
            'stock' => intval($row[2] ?? 0),
            'product_name' => trim($row[3] ?? ''),
            'description' => trim($row[4] ?? ''),
        ];
    }
}

if (!function_exists('validate_product_data')) {
    /**
     * Validate required product data
     *
     * @param array $data Product data
     * @return array ['valid' => bool, 'errors' => array]
     */
    function validate_product_data($data)
    {
        $errors = [];

        if (empty($data['sku'])) {
            $errors[] = 'SKU is required';
        }

        if (empty($data['product_name'])) {
            $errors[] = 'Product name is required';
        }

        if ($data['price'] <= 0) {
            $errors[] = 'Price must be greater than 0';
        }

        if ($data['stock'] < 0) {
            $errors[] = 'Stock cannot be negative';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}

if (!function_exists('format_import_summary')) {
    /**
     * Format import summary for display
     *
     * @param array $stats Import statistics
     * @return string HTML formatted summary
     */
    function format_import_summary($stats)
    {
        $html = '<div class="import-summary">';
        $html .= '<h5>Import Summary</h5>';
        $html .= '<ul>';
        $html .= '<li><strong>Total Processed:</strong> ' . $stats['total'] . '</li>';
        $html .= '<li><strong>Success:</strong> <span class="text-success">' . $stats['success'] . '</span></li>';
        $html .= '<li><strong>Skipped:</strong> <span class="text-warning">' . $stats['skipped'] . '</span></li>';
        $html .= '<li><strong>Failed:</strong> <span class="text-danger">' . $stats['failed'] . '</span></li>';

        if (!empty($stats['brands_created'])) {
            $html .= '<li><strong>New Brands Created:</strong> ' . $stats['brands_created'] . '</li>';
        }

        $html .= '</ul>';
        $html .= '</div>';

        return $html;
    }
}
