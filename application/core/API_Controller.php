<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Base API Controller
 *
 * Provides common functionality for all API endpoints including:
 * - CORS headers
 * - JSON response formatting
 * - Authentication
 * - Error handling
 * - Rate limiting (optional)
 */
class API_Controller extends CI_Controller
{
    protected $api_key_required = false;
    protected $valid_api_keys = [];

    public function __construct()
    {
        parent::__construct();

        // Set CORS headers
        $this->_set_cors_headers();

        // Handle preflight OPTIONS request
        if ($this->input->method() === 'options') {
            http_response_code(200);
            exit(0);
        }

        // Set JSON content type
        $this->output->set_content_type('application/json', 'utf-8');

        // Load valid API keys from config
        $this->config->load('api_config', TRUE);
        $this->valid_api_keys = $this->config->item('valid_api_keys', 'api_config') ?: [];

        // Check API authentication if required
        if ($this->api_key_required) {
            $this->_check_api_auth();
        }

        // Log API access
        log_message('info', 'API Request: ' . $this->input->method() . ' ' . $this->uri->uri_string() . ' - IP: ' . $this->input->ip_address());
    }

    /**
     * Set CORS headers for cross-origin requests
     */
    private function _set_cors_headers()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key, X-Requested-With');
        header('Access-Control-Max-Age: 3600');
    }

    /**
     * Check API authentication via API key
     */
    private function _check_api_auth()
    {
        // Get API key from header or query parameter
        $api_key = $this->input->get_request_header('X-API-Key', TRUE);

        if (!$api_key) {
            $api_key = $this->input->get('api_key');
        }

        // Validate API key
        if (empty($api_key) || !in_array($api_key, $this->valid_api_keys)) {
            $this->_json_response(false, 'Invalid or missing API key', null, 'ERR_UNAUTHORIZED', 401);
            exit;
        }
    }

    /**
     * Send JSON response
     *
     * @param bool $success Success status
     * @param string $message Response message
     * @param mixed $data Response data
     * @param string $error_code Error code (optional)
     * @param int $http_status HTTP status code
     * @param array $meta Additional metadata (pagination, etc)
     */
    protected function _json_response($success, $message, $data = null, $error_code = null, $http_status = 200, $meta = [])
    {
        $response = [
            'success' => $success,
            'message' => $message
        ];

        // Add meta information if provided
        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        // Add data or error
        if ($success) {
            $response['data'] = $data;
        } else {
            $response['error'] = [
                'code' => $error_code ?: 'ERR_UNKNOWN',
                'message' => $message,
                'http_status' => $http_status
            ];
            $response['data'] = null;
        }

        // Set HTTP status code
        $this->output->set_status_header($http_status);

        // Output JSON response
        $this->output->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Validate required parameters
     *
     * @param array $required_params Array of required parameter names
     * @param string $method 'get' or 'post'
     * @return bool|array Returns array of params if valid, false otherwise
     */
    protected function _validate_params($required_params, $method = 'get')
    {
        $params = [];
        $missing = [];

        foreach ($required_params as $param) {
            $value = $method === 'post'
                ? $this->input->post($param, TRUE)
                : $this->input->get($param, TRUE);

            if ($value === null || $value === '') {
                $missing[] = $param;
            } else {
                $params[$param] = $value;
            }
        }

        if (!empty($missing)) {
            $this->_json_response(
                false,
                'Missing required parameters: ' . implode(', ', $missing),
                null,
                'ERR_VALIDATION_FAILED',
                400
            );
            return false;
        }

        return $params;
    }

    /**
     * Build pagination metadata
     *
     * @param int $total Total records
     * @param int $page Current page
     * @param int $per_page Records per page
     * @param int $count Current records count
     * @return array Pagination metadata
     */
    protected function _build_pagination_meta($total, $page, $per_page, $count)
    {
        $total_pages = ceil($total / $per_page);

        return [
            'total' => (int)$total,
            'count' => (int)$count,
            'page' => (int)$page,
            'per_page' => (int)$per_page,
            'total_pages' => (int)$total_pages,
            'has_next' => $page < $total_pages,
            'has_previous' => $page > 1
        ];
    }

    /**
     * Sanitize and prepare filters from request
     *
     * @param array $allowed_filters Array of allowed filter keys
     * @param string $method 'get' or 'post'
     * @return array Sanitized filters
     */
    protected function _get_filters($allowed_filters = [], $method = 'get')
    {
        $filters = [];

        foreach ($allowed_filters as $filter) {
            $value = $method === 'post'
                ? $this->input->post($filter, TRUE)
                : $this->input->get($filter, TRUE);

            if ($value !== null && $value !== '') {
                $filters[$filter] = $value;
            }
        }

        return $filters;
    }

    /**
     * Format product data for API response
     *
     * @param object|array $product Product data from database
     * @param bool $include_images Include product images
     * @return array Formatted product data
     */
    protected function _format_product($product, $include_images = false)
    {
        if (is_object($product)) {
            $product = (array)$product;
        }

        $formatted = [
            'product_id' => (int)$product['product_id'],
            'product_name' => $product['product_name'],
            'sku' => $product['sku'],
            'slug' => $product['product_slug'],
            'description' => $product['description'] ?? '',
            'specifications' => $product['specifications'] ?? '',
            'price' => (float)$product['price'],
            'discount_price' => !empty($product['discount_price']) ? (float)$product['discount_price'] : null,
            'final_price' => !empty($product['discount_price']) ? (float)$product['discount_price'] : (float)$product['price'],
            'stock' => (int)$product['stock'],
            'weight' => (float)($product['weight'] ?? 0),
            'is_featured' => (bool)($product['is_featured'] ?? false),
            'is_active' => (bool)($product['is_active'] ?? true),
            'views' => (int)($product['views'] ?? 0)
        ];

        // Add category if available
        if (!empty($product['category_name'])) {
            $formatted['category'] = [
                'id' => (int)($product['category_id'] ?? 0),
                'name' => $product['category_name']
            ];
        } else {
            $formatted['category'] = null;
        }

        // Add brand if available
        if (!empty($product['brand_name'])) {
            $formatted['brand'] = [
                'id' => (int)($product['brand_id'] ?? 0),
                'name' => $product['brand_name']
            ];
        } else {
            $formatted['brand'] = null;
        }

        // Add main image
        if (!empty($product['main_image'])) {
            $formatted['main_image'] = base_url('uploads/products/' . $product['main_image']);
        } else {
            $formatted['main_image'] = base_url('assets/img/no-image.jpg');
        }

        // Add images if requested
        if ($include_images && !empty($product['images'])) {
            $formatted['images'] = $product['images'];
        }

        // Add timestamps
        $formatted['created_at'] = !empty($product['created_at']) ? date('c', strtotime($product['created_at'])) : null;
        $formatted['updated_at'] = !empty($product['updated_at']) ? date('c', strtotime($product['updated_at'])) : null;

        return $formatted;
    }
}
