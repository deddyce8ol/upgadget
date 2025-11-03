<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Public_Controller extends CI_Controller
{
    protected $data = [];

    public function __construct()
    {
        parent::__construct();

        // Load required models
        $this->load->model('Site_setting_model');
        $this->load->model('Category_model');
        $this->load->model('Product_model');

        // Get site settings
        $this->data['site_settings'] = $this->Site_setting_model->get_all_as_array();

        // Get all active categories for navigation
        $categories_raw = $this->Category_model->get_categories('', 100, 0);
        $this->data['categories'] = [];
        foreach ($categories_raw as $cat) {
            if ($cat['status'] == 1) {
                $this->data['categories'][] = (object)$cat;
            }
        }

        // Get cart count from CodeIgniter Cart library
        $this->data['cart_count'] = $this->cart->total_items();

        // Customer session data
        $this->data['customer_logged_in'] = $this->session->userdata('customer_logged_in') ?? false;
        $this->data['customer_data'] = $this->session->userdata('customer_data') ?? null;

        // For backward compatibility
        if ($this->data['customer_data']) {
            $this->data['customer_name'] = $this->data['customer_data']->full_name ?? '';
            $this->data['customer_email'] = $this->data['customer_data']->email ?? '';
            $this->data['customer_id'] = $this->data['customer_data']->customer_id ?? null;
        } else {
            $this->data['customer_name'] = '';
            $this->data['customer_email'] = '';
            $this->data['customer_id'] = null;
        }

        // Get wishlist count if customer logged in
        $this->data['wishlist_count'] = 0;
        if ($this->data['customer_logged_in'] && $this->data['customer_id']) {
            $this->load->model('Wishlist_model');
            $this->data['wishlist_count'] = $this->Wishlist_model->count_by_customer($this->data['customer_id']);
        }

        // Page meta defaults
        $this->data['page_title'] = $this->data['site_settings']['site_name'] ?? 'Putra Elektronik';
        $this->data['meta_description'] = $this->data['site_settings']['meta_description'] ?? '';
        $this->data['meta_keywords'] = $this->data['site_settings']['meta_keywords'] ?? '';

        // Open Graph defaults
        $this->data['og_title'] = $this->data['site_settings']['site_name'] ?? 'Putra Elektronik';
        $this->data['og_description'] = $this->data['site_settings']['meta_description'] ?? '';
        $this->data['og_image'] = base_url('assets/images/logo-putra-elektronik.png');
        $this->data['og_url'] = current_url();
    }

    /**
     * Render public view with layout
     */
    protected function render($view, $data = [])
    {
        $data = array_merge($this->data, $data);
        $data['content_view'] = $view;

        $this->load->view('public/layouts/header', $data);
        $this->load->view($view, $data);
        $this->load->view('public/layouts/footer', $data);
    }

    /**
     * Check if customer is logged in
     */
    protected function check_customer_login()
    {
        if (!$this->session->userdata('customer_logged_in')) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu');
            redirect('customer/login');
        }
    }

    /**
     * Get cart from session
     */
    protected function get_cart()
    {
        $cart = $this->session->userdata('cart');
        return is_array($cart) ? $cart : [];
    }

    /**
     * Update cart in session
     */
    protected function update_cart($cart)
    {
        $this->session->set_userdata('cart', $cart);
    }

    /**
     * Calculate cart totals
     */
    protected function calculate_cart_totals($cart_items)
    {
        $subtotal = 0;
        foreach ($cart_items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        return [
            'subtotal' => $subtotal,
            'shipping' => 0, // Will be calculated based on location
            'total' => $subtotal
        ];
    }

    /**
     * Set page meta
     */
    protected function set_meta($title = '', $description = '', $keywords = '', $og_image = '')
    {
        if ($title) {
            $this->data['page_title'] = $title . ' - ' . $this->data['site_settings']['site_name'];
            $this->data['og_title'] = $title . ' - ' . $this->data['site_settings']['site_name'];
        }
        if ($description) {
            $this->data['meta_description'] = $description;
            $this->data['og_description'] = $description;
        }
        if ($keywords) {
            $this->data['meta_keywords'] = $keywords;
        }
        if ($og_image) {
            $this->data['og_image'] = $og_image;
        }
        $this->data['og_url'] = current_url();
    }

    /**
     * JSON response
     */
    protected function json_response($data)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
