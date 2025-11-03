<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Import Product Model
 *
 * Handles product import operations from CSV
 * - Brand extraction and auto-creation
 * - Category matching
 * - SKU validation and duplicate handling
 * - Batch processing with transactions
 */
class Import_product_model extends CI_Model
{
    private $batch_size = 200;
    private $default_category_id = null;
    private $categories_cache = [];
    private $brands_cache = [];
    private $existing_skus = [];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Product_model');
        $this->load->model('Brand_model');
        $this->load->model('Category_model');
        $this->load->helper('import');
    }

    /**
     * Initialize import session
     * Cache categories, brands, and existing SKUs for performance
     */
    public function initialize_import()
    {
        // Load all categories
        $this->categories_cache = $this->Category_model->get_all_active();

        // Load all brands
        $this->brands_cache = $this->Brand_model->get_all();

        // Get or create default "Uncategorized" category
        $this->default_category_id = $this->get_or_create_default_category();

        // Emergency fallback - if still no category_id, get ANY category
        if (!$this->default_category_id) {
            $fallback = $this->db->select('id')
                ->where('deleted_at IS NULL')
                ->limit(1)
                ->get('categories')
                ->row();

            if ($fallback) {
                $this->default_category_id = $fallback->id;
            }
        }

        // Load existing SKUs for duplicate check
        $this->load_existing_skus();
    }

    /**
     * Load all existing SKUs from database into memory
     * For faster duplicate checking
     */
    private function load_existing_skus()
    {
        $query = $this->db->select('sku')->from('products')->get();
        $this->existing_skus = array_column($query->result_array(), 'sku');
        $this->existing_skus = array_flip($this->existing_skus); // Use as hashmap for O(1) lookup
    }

    /**
     * Get or create default "Uncategorized" category
     *
     * @return int Category ID
     */
    private function get_or_create_default_category()
    {
        // Check if Uncategorized category exists (without soft delete filter)
        $this->db->where('slug', 'uncategorized');
        $this->db->where('deleted_at IS NULL');
        $category = $this->db->get('categories')->row();

        if ($category) {
            return $category->id;
        }

        // Create default category
        $data = [
            'name' => 'Uncategorized',
            'slug' => 'uncategorized',
            'description' => 'Products without specific category',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'deleted_at' => NULL,
        ];

        $this->db->insert('categories', $data);
        $category_id = $this->db->insert_id();

        // If still no category_id, use first available category as fallback
        if (!$category_id) {
            $first_category = $this->db->select('id')
                ->where('status', 1)
                ->where('deleted_at IS NULL')
                ->limit(1)
                ->get('categories')
                ->row();

            if ($first_category) {
                return $first_category->id;
            }
        }

        return $category_id;
    }

    /**
     * Process CSV file and import products in batches
     *
     * @param string $csv_path Path to CSV file
     * @param int $batch_number Current batch number (for pagination)
     * @return array Import results
     */
    public function process_import_batch($csv_path, $batch_number = 1)
    {
        $stats = [
            'total' => 0,
            'success' => 0,
            'skipped' => 0,
            'failed' => 0,
            'brands_created' => 0,
            'errors' => [],
            'has_more' => false,
        ];

        // Re-initialize if not already initialized (for multiple batch calls)
        if (empty($this->categories_cache) || !$this->default_category_id) {
            $this->initialize_import();
        }

        if (!file_exists($csv_path)) {
            $stats['errors'][] = 'CSV file not found';
            return $stats;
        }

        // Open CSV file
        $handle = fopen($csv_path, 'r');
        if (!$handle) {
            $stats['errors'][] = 'Unable to open CSV file';
            return $stats;
        }

        // Skip header
        fgetcsv($handle);

        // Skip to current batch
        $skip_rows = ($batch_number - 1) * $this->batch_size;
        for ($i = 0; $i < $skip_rows; $i++) {
            if (fgetcsv($handle) === false) {
                fclose($handle);
                return $stats; // No more data
            }
        }

        // Start transaction
        $this->db->trans_start();

        $batch_count = 0;

        // Process batch
        while (($row = fgetcsv($handle)) !== false && $batch_count < $this->batch_size) {
            $stats['total']++;
            $batch_count++;

            // Sanitize data
            $csv_data = sanitize_csv_data($row);

            // Validate data
            $validation = validate_product_data($csv_data);
            if (!$validation['valid']) {
                $stats['failed']++;
                $stats['errors'][] = [
                    'row' => $skip_rows + $batch_count + 1, // +1 for header
                    'sku' => $csv_data['sku'],
                    'errors' => $validation['errors']
                ];
                continue;
            }

            // Check if SKU already exists in database
            if (isset($this->existing_skus[$csv_data['sku']])) {
                $stats['skipped']++;
                $stats['errors'][] = [
                    'row' => $skip_rows + $batch_count + 1,
                    'sku' => $csv_data['sku'],
                    'errors' => ['SKU already exists in database']
                ];
                continue;
            }

            // Process and insert product
            try {
                $result = $this->process_single_product($csv_data);

                if ($result['success']) {
                    $stats['success']++;
                    if ($result['brand_created']) {
                        $stats['brands_created']++;
                    }
                    // Add to existing SKUs to prevent duplicates in same batch
                    $this->existing_skus[$csv_data['sku']] = true;
                } else {
                    $stats['failed']++;
                    $stats['errors'][] = [
                        'row' => $skip_rows + $batch_count + 1,
                        'sku' => $csv_data['sku'],
                        'errors' => $result['errors']
                    ];
                }
            } catch (Exception $e) {
                $stats['failed']++;
                $stats['errors'][] = [
                    'row' => $skip_rows + $batch_count + 1,
                    'sku' => $csv_data['sku'],
                    'errors' => [$e->getMessage()]
                ];
            }
        }

        // Check if there's more data
        if (fgetcsv($handle) !== false) {
            $stats['has_more'] = true;
        }

        fclose($handle);

        // Complete transaction
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $stats['errors'][] = 'Transaction failed - batch rolled back';
            $stats['success'] = 0;
            $stats['failed'] = $stats['total'];
        }

        return $stats;
    }

    /**
     * Process single product from CSV data
     *
     * @param array $csv_data Sanitized CSV data
     * @return array Result with success status and errors
     */
    private function process_single_product($csv_data)
    {
        $result = [
            'success' => false,
            'brand_created' => false,
            'errors' => []
        ];

        try {
            // Extract and get/create brand
            $brand_name = extract_brand_from_name($csv_data['product_name']);
            $brand_id = $this->get_or_create_brand($brand_name);

            if (!$brand_id) {
                $result['errors'][] = 'Failed to get or create brand';
                return $result;
            }

            // Check if brand was just created
            $existing_brand = false;
            foreach ($this->brands_cache as $b) {
                if ($b->brand_id == $brand_id) {
                    $existing_brand = true;
                    break;
                }
            }

            if (!$existing_brand) {
                $result['brand_created'] = true;
                // Refresh brands cache
                $this->brands_cache = $this->Brand_model->get_all();
            }

            // Match category
            $category_id = match_category_from_name($csv_data['product_name'], $this->categories_cache);
            if (!$category_id) {
                $category_id = $this->default_category_id;
            }

            // Final safety check - ensure category_id is not null
            if (!$category_id) {
                // Emergency: try to get default category directly from database
                $default_cat = $this->db->select('id')
                    ->where('slug', 'uncategorized')
                    ->where('deleted_at IS NULL')
                    ->limit(1)
                    ->get('categories')
                    ->row();

                if ($default_cat) {
                    $category_id = $default_cat->id;
                } else {
                    // Last resort: get any category
                    $any_cat = $this->db->select('id')
                        ->where('deleted_at IS NULL')
                        ->where('status', 1)
                        ->limit(1)
                        ->get('categories')
                        ->row();

                    if ($any_cat) {
                        $category_id = $any_cat->id;
                    } else {
                        $result['errors'][] = 'Failed to get category ID - No categories available in database';
                        return $result;
                    }
                }
            }

            // Generate unique slug
            $slug = generate_unique_slug($csv_data['product_name'], $this, $csv_data['sku']);

            // Prepare product data
            $product_data = [
                'product_name' => $csv_data['product_name'],
                'product_slug' => $slug,
                'category_id' => $category_id,
                'brand_id' => $brand_id,
                'sku' => $csv_data['sku'],
                'description' => $csv_data['description'],
                'price' => $csv_data['price'],
                'stock' => $csv_data['stock'],
                'weight' => 0,
                'discount_price' => null,
                'main_image' => null,
                'specifications' => null,
                'is_featured' => 0,
                'is_active' => 1,
                'views' => 0,
            ];

            // Insert product
            $product_id = $this->Product_model->insert($product_data);

            if ($product_id) {
                $result['success'] = true;
            } else {
                $result['errors'][] = 'Failed to insert product into database';
            }

        } catch (Exception $e) {
            $result['errors'][] = 'Exception: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Get existing brand or create new one
     *
     * @param string $brand_name
     * @return int|false Brand ID or false on failure
     */
    private function get_or_create_brand($brand_name)
    {
        // Check in cached brands first
        foreach ($this->brands_cache as $brand) {
            if (strtolower($brand->brand_name) == strtolower($brand_name)) {
                return $brand->brand_id;
            }
        }

        // Brand not found, create new one
        $this->load->helper('url');
        $brand_slug = url_title($brand_name, 'dash', TRUE);

        // Check if slug exists, append number if needed
        $original_slug = $brand_slug;
        $counter = 1;
        while ($this->Brand_model->check_slug_exists($brand_slug)) {
            $brand_slug = $original_slug . '-' . $counter;
            $counter++;
        }

        $brand_data = [
            'brand_name' => ucfirst($brand_name),
            'brand_slug' => $brand_slug,
            'brand_description' => 'Auto-created from import',
            'is_active' => 1,
        ];

        $brand_id = $this->Brand_model->insert($brand_data);

        return $brand_id;
    }

    /**
     * Count total rows in CSV file (excluding header)
     *
     * @param string $csv_path
     * @return int Total rows
     */
    public function count_csv_rows($csv_path)
    {
        if (!file_exists($csv_path)) {
            return 0;
        }

        $count = 0;
        $handle = fopen($csv_path, 'r');

        if ($handle) {
            // Skip header
            fgetcsv($handle);

            // Count rows
            while (fgetcsv($handle) !== false) {
                $count++;
            }

            fclose($handle);
        }

        return $count;
    }

    /**
     * Preview CSV data (first N rows)
     *
     * @param string $csv_path
     * @param int $limit Number of rows to preview
     * @return array Preview data with headers and rows
     */
    public function preview_csv($csv_path, $limit = 50)
    {
        $preview = [
            'headers' => [],
            'rows' => [],
            'total_rows' => 0,
        ];

        if (!file_exists($csv_path)) {
            return $preview;
        }

        $handle = fopen($csv_path, 'r');
        if (!$handle) {
            return $preview;
        }

        // Get headers
        $preview['headers'] = fgetcsv($handle);

        // Get preview rows
        $count = 0;
        while (($row = fgetcsv($handle)) !== false && $count < $limit) {
            $preview['rows'][] = $row;
            $count++;
        }

        fclose($handle);

        // Count total rows
        $preview['total_rows'] = $this->count_csv_rows($csv_path);

        return $preview;
    }

    /**
     * Get batch size for processing
     *
     * @return int
     */
    public function get_batch_size()
    {
        return $this->batch_size;
    }
}
