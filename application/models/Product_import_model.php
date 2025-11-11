<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Product Import Model
 *
 * Handle database operations for product import feature
 *
 * @package     UPGADGET
 * @subpackage  Models
 * @category    Import
 * @author      PT. Qapuas Media Technologies
 * @since       Version 1.0.0
 */
class Product_import_model extends CI_Model {

    /**
     * Category mapping from Excel to Database
     * Maps Excel category names to existing database category IDs
     */
    private $category_mapping = [
        'IPHONE'        => 10,  // Smartphones
        'IPAD'          => 11,  // Tablets
        'MACBOOK'       => 12,  // Laptops
        'APPLE WATCH'   => 13,  // Smartwatches & Wearables
        'AIRPODS'       => 14,  // Audio & Earbuds
        'ACCESSORIES'   => 15,  // Accessories
        'APPLE TV'      => 16,  // TVs & Smart Displays
    ];

    /**
     * Brand detection keywords
     * Maps keywords in product name to brand IDs
     */
    private $brand_keywords = [
        'IPHONE'    => 165,  // APPLE
        'IPAD'      => 165,  // APPLE
        'MACBOOK'   => 165,  // APPLE
        'AIRPODS'   => 165,  // APPLE
        'APPLE'     => 165,  // APPLE
        'SAMSUNG'   => 161,  // SAMSUNG
        'GALAXY'    => 161,  // SAMSUNG
        'XIAOMI'    => 162,  // XIAOMI
        'REDMI'     => 162,  // XIAOMI
        'POCO'      => 170,  // POCO
        'OPPO'      => 163,  // OPPO
        'VIVO'      => 164,  // VIVO
        'REALME'    => 167,  // REALME
        'INFINIX'   => 168,  // INFINIX
        'TECNO'     => 166,  // TECNO
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Check if SKU already exists in database
     *
     * @param string $sku Product SKU
     * @return object|null Product data if exists, null if not found
     */
    public function check_sku_exists($sku)
    {
        $query = $this->db->get_where('products', ['sku' => $sku], 1);
        return $query->row();
    }

    /**
     * Insert new product
     *
     * @param array $data Product data
     * @return int|bool Insert ID on success, FALSE on failure
     */
    public function insert_product($data)
    {
        $this->db->insert('products', $data);
        return $this->db->insert_id();
    }

    /**
     * Update existing product
     *
     * @param int $product_id Product ID
     * @param array $data Product data to update
     * @return bool TRUE on success, FALSE on failure
     */
    public function update_product($product_id, $data)
    {
        $this->db->where('product_id', $product_id);
        return $this->db->update('products', $data);
    }

    /**
     * Get category by name
     *
     * @param string $name Category name
     * @return object|null Category data if found
     */
    public function get_category_by_name($name)
    {
        $this->db->where('name', $name);
        $this->db->where('deleted_at', NULL);
        $query = $this->db->get('categories', 1);
        return $query->row();
    }

    /**
     * Insert new category
     *
     * @param array $data Category data
     * @return int|bool Insert ID on success, FALSE on failure
     */
    public function insert_category($data)
    {
        $this->db->insert('categories', $data);
        return $this->db->insert_id();
    }

    /**
     * Map Excel category to database category ID
     * If category doesn't exist, create new one
     *
     * @param string $excel_category Category name from Excel
     * @return int|null Category ID
     */
    public function map_category($excel_category)
    {
        if (empty($excel_category)) {
            return null;
        }

        $excel_category = strtoupper(trim($excel_category));

        // Check if we have a predefined mapping
        if (isset($this->category_mapping[$excel_category])) {
            return $this->category_mapping[$excel_category];
        }

        // Try to find category by name (case-insensitive)
        $existing = $this->get_category_by_name($excel_category);
        if ($existing) {
            return $existing->id;
        }

        // Create new category if not found
        $this->load->library('slug_generator');
        $slug = $this->slug_generator->make_unique_category($excel_category);

        $category_data = [
            'name'          => $excel_category,
            'slug'          => $slug,
            'description'   => 'Auto-generated from import',
            'status'        => 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        $category_id = $this->insert_category($category_data);
        return $category_id ?: null;
    }

    /**
     * Detect brand from product name
     *
     * @param string $product_name Product name
     * @return int|null Brand ID if detected, null otherwise
     */
    public function detect_brand($product_name)
    {
        if (empty($product_name)) {
            return null;
        }

        $product_name_upper = strtoupper($product_name);

        // Check each keyword
        foreach ($this->brand_keywords as $keyword => $brand_id) {
            if (strpos($product_name_upper, $keyword) !== false) {
                return $brand_id;
            }
        }

        // Default to APPLE for UPGADGET store
        return 165;
    }

    /**
     * Validate product data before import
     *
     * @param array $row_data Product data from Excel
     * @param int $row_number Row number in Excel
     * @return array ['valid' => bool, 'error' => string]
     */
    public function validate_product_data($row_data, $row_number)
    {
        $errors = [];

        // Validate SKU
        if (empty($row_data['sku'])) {
            $errors[] = "Baris {$row_number}: SKU kosong";
        }

        // Validate product name
        if (empty($row_data['product_name'])) {
            $errors[] = "Baris {$row_number}: Nama produk kosong";
        }

        // Validate price
        if (!isset($row_data['price']) || $row_data['price'] <= 0) {
            $errors[] = "Baris {$row_number}: Harga tidak valid (harus > 0)";
        }

        // Validate stock
        if (!isset($row_data['stock']) || $row_data['stock'] < 0) {
            $errors[] = "Baris {$row_number}: Stok tidak valid (tidak boleh negatif)";
        }

        // Validate category
        if (empty($row_data['category_id'])) {
            $errors[] = "Baris {$row_number}: Kategori tidak ditemukan";
        }

        return [
            'valid' => empty($errors),
            'error' => empty($errors) ? '' : implode(', ', $errors)
        ];
    }

    /**
     * Get brand name by ID
     *
     * @param int $brand_id
     * @return string|null
     */
    public function get_brand_name($brand_id)
    {
        if (empty($brand_id)) {
            return null;
        }

        $query = $this->db->select('brand_name')
                          ->where('brand_id', $brand_id)
                          ->get('brands', 1);

        $result = $query->row();
        return $result ? $result->brand_name : null;
    }

    /**
     * Get category name by ID
     *
     * @param int $category_id
     * @return string|null
     */
    public function get_category_name($category_id)
    {
        if (empty($category_id)) {
            return null;
        }

        $query = $this->db->select('name')
                          ->where('id', $category_id)
                          ->where('deleted_at', NULL)
                          ->get('categories', 1);

        $result = $query->row();
        return $result ? $result->name : null;
    }

    /**
     * Get import statistics
     *
     * @return array Statistics data
     */
    public function get_import_stats()
    {
        $stats = [];

        // Total products
        $stats['total_products'] = $this->db->count_all('products');

        // Products with SKU
        $stats['products_with_sku'] = $this->db->where('sku IS NOT NULL')->count_all_results('products');

        // Products without SKU
        $stats['products_without_sku'] = $stats['total_products'] - $stats['products_with_sku'];

        // Active products
        $stats['active_products'] = $this->db->where('is_active', 1)->count_all_results('products');

        // Total categories
        $stats['total_categories'] = $this->db->where('deleted_at', NULL)->count_all_results('categories');

        // Total brands
        $stats['total_brands'] = $this->db->count_all('brands');

        return $stats;
    }
}
