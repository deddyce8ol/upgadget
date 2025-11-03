<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/API_Controller.php';

/**
 * Brands API Controller
 *
 * Handles all brand-related API endpoints:
 * - list: Get all brands
 * - detail: Get brand detail with products
 */
class Brands extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Brand_model');
        $this->load->model('Product_model');

        // Set API key requirement
        $this->api_key_required = $this->config->item('api_require_key', 'api_config');
    }

    /**
     * Main endpoint
     * GET /api/v1/brands?action=list
     */
    public function index()
    {
        $action = $this->input->get('action', TRUE) ?: 'list';

        switch ($action) {
            case 'list':
                $this->list_brands();
                break;

            case 'detail':
                $this->detail();
                break;

            default:
                $this->_json_response(
                    false,
                    'Invalid action. Valid actions: list, detail',
                    null,
                    'ERR_INVALID_ACTION',
                    400
                );
                break;
        }
    }

    /**
     * Get all brands
     * GET /api/v1/brands?action=list
     */
    public function list_brands()
    {
        // Get all active brands
        $brands = $this->Brand_model->get_all(['is_active' => 1]);

        // Format brands
        $formatted_brands = [];
        foreach ($brands as $brand) {
            $formatted_brands[] = $this->_format_brand($brand);
        }

        // Send response
        $this->_json_response(
            true,
            'Brands retrieved successfully',
            $formatted_brands,
            null,
            200,
            ['count' => count($formatted_brands)]
        );
    }

    /**
     * Get brand detail with products
     * GET /api/v1/brands?action=detail&id=5
     */
    public function detail()
    {
        $id = $this->input->get('id', TRUE);

        if (empty($id)) {
            $this->_json_response(
                false,
                'Brand ID is required',
                null,
                'ERR_MISSING_IDENTIFIER',
                400
            );
            return;
        }

        // Get brand
        $brand = $this->Brand_model->get_by_id($id);

        if (!$brand || $brand->is_active != 1) {
            $this->_json_response(
                false,
                'Brand not found',
                null,
                'ERR_NOT_FOUND',
                404
            );
            return;
        }

        // Format brand
        $formatted = $this->_format_brand($brand);

        // Get products for this brand
        $limit = min((int)$this->input->get('products_limit') ?: 12, 50);
        $page = max(1, (int)$this->input->get('page') ?: 1);
        $offset = ($page - 1) * $limit;

        $filters = [
            'is_active' => 1,
            'brand_id' => $id
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
            'Brand detail retrieved successfully',
            $formatted,
            null,
            200
        );
    }

    /**
     * Format brand data for API response
     */
    private function _format_brand($brand)
    {
        if (is_object($brand)) {
            $brand = (array)$brand;
        }

        $formatted = [
            'brand_id' => (int)$brand['brand_id'],
            'brand_name' => $brand['brand_name'],
            'description' => $brand['description'] ?? '',
            'logo' => !empty($brand['logo']) ? base_url('uploads/brands/' . $brand['logo']) : null,
            'is_active' => (bool)($brand['is_active'] ?? true)
        ];

        if (!empty($brand['created_at'])) {
            $formatted['created_at'] = date('c', strtotime($brand['created_at']));
        }

        return $formatted;
    }
}
