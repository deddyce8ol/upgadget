<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/API_Controller.php';

/**
 * Categories API Controller
 *
 * Handles all category-related API endpoints:
 * - list: Get all categories
 * - detail: Get category detail with products
 * - tree: Get categories in tree structure
 */
class Categories extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Category_model');
        $this->load->model('Product_model');

        // Set API key requirement
        $this->api_key_required = $this->config->item('api_require_key', 'api_config');
    }

    /**
     * Main endpoint
     * GET /api/v1/categories?action=list
     */
    public function index()
    {
        $action = $this->input->get('action', TRUE) ?: 'list';

        switch ($action) {
            case 'list':
                $this->list_categories();
                break;

            case 'detail':
                $this->detail();
                break;

            case 'tree':
                $this->tree();
                break;

            default:
                $this->_json_response(
                    false,
                    'Invalid action. Valid actions: list, detail, tree',
                    null,
                    'ERR_INVALID_ACTION',
                    400
                );
                break;
        }
    }

    /**
     * Get all categories
     * GET /api/v1/categories?action=list
     */
    public function list_categories()
    {
        // Get all active categories
        $categories = $this->Category_model->get_categories('', 1000, 0);

        // Filter only active categories (using 'status' field from database)
        $active_categories = array_filter($categories, function ($cat) {
            return isset($cat['status']) && $cat['status'] == 1;
        });

        // Format categories
        $formatted_categories = [];
        foreach ($active_categories as $category) {
            $formatted_categories[] = $this->_format_category($category);
        }

        // Send response
        $this->_json_response(
            true,
            'Categories retrieved successfully',
            $formatted_categories,
            null,
            200,
            ['count' => count($formatted_categories)]
        );
    }

    /**
     * Get category detail with products
     * GET /api/v1/categories?action=detail&id=3
     * GET /api/v1/categories?action=detail&slug=televisi
     */
    public function detail()
    {
        $id = $this->input->get('id', TRUE);
        $slug = $this->input->get('slug', TRUE);

        if (empty($id) && empty($slug)) {
            $this->_json_response(
                false,
                'Category ID or slug is required',
                null,
                'ERR_MISSING_IDENTIFIER',
                400
            );
            return;
        }

        // Get category
        $categories = $this->Category_model->get_categories('', 1000, 0);
        $category = null;

        foreach ($categories as $cat) {
            if ((!empty($id) && $cat['id'] == $id) || (!empty($slug) && $cat['slug'] == $slug)) {
                $category = $cat;
                break;
            }
        }

        if (!$category || (isset($category['status']) && $category['status'] != 1)) {
            $this->_json_response(
                false,
                'Category not found',
                null,
                'ERR_NOT_FOUND',
                404
            );
            return;
        }

        // Format category
        $formatted = $this->_format_category($category);

        // Get products in this category
        $limit = min((int)$this->input->get('products_limit') ?: 12, 50);
        $page = max(1, (int)$this->input->get('page') ?: 1);
        $offset = ($page - 1) * $limit;

        $filters = [
            'is_active' => 1,
            'category_id' => $category['id']
        ];

        $total_products = $this->Product_model->count_all($filters);
        $products = $this->Product_model->get_paginated($limit, $offset, $filters, 'newest');

        // Format products
        $formatted_products = [];
        foreach ($products as $product) {
            $formatted_products[] = $this->_format_product($product);
        }

        $formatted['products'] = $formatted_products;
        $formatted['products_meta'] = $this->_build_pagination_meta(
            $total_products,
            $page,
            $limit,
            count($formatted_products)
        );

        // Send response
        $this->_json_response(
            true,
            'Category detail retrieved successfully',
            $formatted,
            null,
            200
        );
    }

    /**
     * Get categories in tree structure (parent-child)
     * GET /api/v1/categories?action=tree
     */
    public function tree()
    {
        // Get all active categories
        $categories = $this->Category_model->get_categories('', 1000, 0);

        // Filter only active categories (using 'status' field from database)
        $active_categories = array_filter($categories, function ($cat) {
            return isset($cat['status']) && $cat['status'] == 1;
        });

        // Build tree structure
        $tree = $this->_build_category_tree($active_categories);

        // Send response
        $this->_json_response(
            true,
            'Category tree retrieved successfully',
            $tree,
            null,
            200,
            ['count' => count($tree)]
        );
    }

    /**
     * Format category data for API response
     */
    private function _format_category($category)
    {
        $formatted = [
            'id' => (int)$category['id'],
            'name' => $category['name'],
            'slug' => $category['slug'],
            'description' => $category['description'] ?? '',
            'icon' => !empty($category['icon_path']) ? base_url('uploads/categories/' . $category['icon_path']) : null,
            'banner_image' => !empty($category['banner_image']) ? base_url('uploads/categories/' . $category['banner_image']) : null,
            'parent_id' => !empty($category['parent_id']) ? (int)$category['parent_id'] : null,
            'is_active' => (bool)($category['status'] ?? 1)
        ];

        if (!empty($category['created_at'])) {
            $formatted['created_at'] = date('c', strtotime($category['created_at']));
        }

        return $formatted;
    }

    /**
     * Build category tree structure
     */
    private function _build_category_tree($categories, $parent_id = null)
    {
        $tree = [];

        foreach ($categories as $category) {
            $cat_parent_id = !empty($category['parent_id']) ? $category['parent_id'] : null;

            if ($cat_parent_id == $parent_id) {
                $formatted = $this->_format_category($category);

                // Get children
                $children = $this->_build_category_tree($categories, $category['id']);
                if (!empty($children)) {
                    $formatted['children'] = $children;
                }

                $tree[] = $formatted;
            }
        }

        return $tree;
    }
}
