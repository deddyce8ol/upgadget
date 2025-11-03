<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/API_Controller.php';

/**
 * Products API Controller
 *
 * Handles all product-related API endpoints:
 * - list: Get paginated list of products
 * - search: Search products with filters
 * - detail: Get product detail by ID or SKU
 * - featured: Get featured products
 * - recommendations: Get product recommendations
 * - new: Get latest products
 * - popular: Get most viewed products
 */
class Products extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Product_model');
        $this->load->model('Product_image_model');
        $this->load->model('Category_model');
        $this->load->model('Brand_model');

        // Set API key requirement (set to true for production)
        $this->api_key_required = $this->config->item('api_require_key', 'api_config');
    }

    /**
     * Main endpoint - routes to different actions based on 'action' parameter
     * GET /api/v1/products?action=list
     */
    public function index()
    {
        $action = $this->input->get('action', TRUE) ?: 'list';

        switch ($action) {
            case 'list':
                $this->list_products();
                break;

            case 'search':
                $this->search();
                break;

            case 'detail':
                $this->detail();
                break;

            case 'featured':
                $this->featured();
                break;

            case 'recommendations':
                $this->recommendations();
                break;

            case 'new':
                $this->new_products();
                break;

            case 'popular':
                $this->popular();
                break;

            default:
                $this->_json_response(
                    false,
                    'Invalid action. Valid actions: list, search, detail, featured, recommendations, new, popular',
                    null,
                    'ERR_INVALID_ACTION',
                    400
                );
                break;
        }
    }

    /**
     * List all products with pagination and filters
     * GET /api/v1/products?action=list&page=1&per_page=20&category=3&brand=5
     */
    public function list_products()
    {
        // Get pagination parameters
        $page = max(1, (int)$this->input->get('page') ?: 1);
        $per_page = min(
            (int)$this->input->get('per_page') ?: $this->config->item('api_default_per_page', 'api_config'),
            $this->config->item('api_max_per_page', 'api_config')
        );

        // Get filters
        $filters = $this->_get_filters([
            'category_id',
            'brand_id',
            'is_featured',
            'min_price',
            'max_price'
        ]);

        // Always filter active products for public API
        $filters['is_active'] = 1;

        // Get sort parameter
        $sort = $this->input->get('sort', TRUE) ?: 'newest';

        // Count total products
        $total = $this->Product_model->count_all($filters);

        // Get paginated products
        $offset = ($page - 1) * $per_page;
        $products = $this->Product_model->get_paginated($per_page, $offset, $filters, $sort);

        // Format products
        $formatted_products = [];
        foreach ($products as $product) {
            $formatted_products[] = $this->_format_product($product);
        }

        // Build pagination meta
        $meta = $this->_build_pagination_meta($total, $page, $per_page, count($formatted_products));

        // Send response
        $this->_json_response(
            true,
            'Products retrieved successfully',
            $formatted_products,
            null,
            200,
            $meta
        );
    }

    /**
     * Search products with multiple filters
     * GET /api/v1/products?action=search&q=tv&category=3&brand=5
     */
    public function search()
    {
        // Get search query
        $query = $this->input->get('q', TRUE);

        if (empty($query)) {
            $this->_json_response(
                false,
                'Search query is required',
                null,
                'ERR_MISSING_QUERY',
                400
            );
            return;
        }

        // Get pagination parameters
        $page = max(1, (int)$this->input->get('page') ?: 1);
        $per_page = min(
            (int)$this->input->get('per_page') ?: $this->config->item('api_default_per_page', 'api_config'),
            $this->config->item('api_max_per_page', 'api_config')
        );

        // Get filters
        $filters = $this->_get_filters([
            'category_id',
            'brand_id'
        ]);

        // Add search query and active filter
        $filters['search'] = $query;
        $filters['is_active'] = 1;

        // Get sort parameter
        $sort = $this->input->get('sort', TRUE) ?: 'newest';

        // Count total products
        $total = $this->Product_model->count_all($filters);

        // Get paginated products
        $offset = ($page - 1) * $per_page;
        $products = $this->Product_model->get_paginated($per_page, $offset, $filters, $sort);

        // Format products
        $formatted_products = [];
        foreach ($products as $product) {
            $formatted_products[] = $this->_format_product($product);
        }

        // Build pagination meta
        $meta = $this->_build_pagination_meta($total, $page, $per_page, count($formatted_products));
        $meta['query'] = $query;

        // Send response
        $this->_json_response(
            true,
            'Search completed successfully',
            $formatted_products,
            null,
            200,
            $meta
        );
    }

    /**
     * Get product detail by ID or SKU
     * GET /api/v1/products?action=detail&id=123
     * GET /api/v1/products?action=detail&sku=TV-SAM-55
     */
    public function detail()
    {
        $id = $this->input->get('id', TRUE);
        $sku = $this->input->get('sku', TRUE);

        if (empty($id) && empty($sku)) {
            $this->_json_response(
                false,
                'Product ID or SKU is required',
                null,
                'ERR_MISSING_IDENTIFIER',
                400
            );
            return;
        }

        // Get product
        if (!empty($id)) {
            $product = $this->Product_model->get_by_id($id);
        } else {
            // Get by SKU
            $product = $this->db->get_where('products', ['sku' => $sku, 'is_active' => 1])->row();
            if ($product) {
                $product = $this->Product_model->get_by_id($product->product_id);
            }
        }

        if (!$product || $product->is_active != 1) {
            $this->_json_response(
                false,
                'Product not found',
                null,
                'ERR_NOT_FOUND',
                404
            );
            return;
        }

        // Get product images
        $product_images = $this->Product_image_model->get_by_product($product->product_id);

        $images = [];
        foreach ($product_images as $img) {
            $images[] = [
                'image_id' => (int)$img->image_id,
                'image_url' => base_url('uploads/products/' . $img->image_path),
                'is_primary' => (bool)($img->is_primary ?? false)
            ];
        }

        // Format product
        $formatted = $this->_format_product($product);
        $formatted['images'] = $images;

        // Get related products
        $related = $this->Product_model->get_related_products(
            $product->category_id,
            $product->product_id,
            4
        );

        $formatted_related = [];
        foreach ($related as $rel) {
            $formatted_related[] = $this->_format_product($rel);
        }

        $formatted['related_products'] = $formatted_related;

        // Increment views
        $this->Product_model->increment_views($product->product_id);

        // Send response
        $this->_json_response(
            true,
            'Product detail retrieved successfully',
            $formatted,
            null,
            200
        );
    }

    /**
     * Get featured products
     * GET /api/v1/products?action=featured&limit=8
     */
    public function featured()
    {
        $limit = min(
            (int)$this->input->get('limit') ?: 8,
            $this->config->item('api_max_per_page', 'api_config')
        );

        // Get featured products
        $filters = [
            'is_active' => 1,
            'is_featured' => 1
        ];

        $products = $this->Product_model->get_paginated($limit, 0, $filters, 'newest');

        // Format products
        $formatted_products = [];
        foreach ($products as $product) {
            $formatted_products[] = $this->_format_product($product);
        }

        // Send response
        $this->_json_response(
            true,
            'Featured products retrieved successfully',
            $formatted_products,
            null,
            200,
            ['count' => count($formatted_products), 'limit' => $limit]
        );
    }

    /**
     * Get product recommendations based on category or brand
     * GET /api/v1/products?action=recommendations&product_id=123&limit=4
     */
    public function recommendations()
    {
        $product_id = $this->input->get('product_id', TRUE);
        $limit = min(
            (int)$this->input->get('limit') ?: 4,
            20
        );

        if (empty($product_id)) {
            // Return random products if no product_id provided
            $products = $this->Product_model->get_paginated($limit, 0, ['is_active' => 1], 'newest');
        } else {
            // Get reference product
            $ref_product = $this->Product_model->get_by_id($product_id);

            if (!$ref_product) {
                $this->_json_response(
                    false,
                    'Reference product not found',
                    null,
                    'ERR_NOT_FOUND',
                    404
                );
                return;
            }

            // Get related products
            $products = $this->Product_model->get_related_products(
                $ref_product->category_id,
                $product_id,
                $limit
            );
        }

        // Format products
        $formatted_products = [];
        foreach ($products as $product) {
            $formatted_products[] = $this->_format_product($product);
        }

        // Send response
        $this->_json_response(
            true,
            'Recommendations retrieved successfully',
            $formatted_products,
            null,
            200,
            ['count' => count($formatted_products), 'limit' => $limit]
        );
    }

    /**
     * Get latest/new products
     * GET /api/v1/products?action=new&limit=8
     */
    public function new_products()
    {
        $limit = min(
            (int)$this->input->get('limit') ?: 8,
            $this->config->item('api_max_per_page', 'api_config')
        );

        // Get latest products
        $products = $this->Product_model->get_paginated($limit, 0, ['is_active' => 1], 'newest');

        // Format products
        $formatted_products = [];
        foreach ($products as $product) {
            $formatted_products[] = $this->_format_product($product);
        }

        // Send response
        $this->_json_response(
            true,
            'Latest products retrieved successfully',
            $formatted_products,
            null,
            200,
            ['count' => count($formatted_products), 'limit' => $limit]
        );
    }

    /**
     * Get popular products (most viewed)
     * GET /api/v1/products?action=popular&limit=5
     */
    public function popular()
    {
        $limit = min(
            (int)$this->input->get('limit') ?: 5,
            20
        );

        // Get top products
        $products = $this->Product_model->get_top_products($limit);

        // Format products
        $formatted_products = [];
        foreach ($products as $product) {
            $formatted_products[] = $this->_format_product($product);
        }

        // Send response
        $this->_json_response(
            true,
            'Popular products retrieved successfully',
            $formatted_products,
            null,
            200,
            ['count' => count($formatted_products), 'limit' => $limit]
        );
    }
}
