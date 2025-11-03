<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'core/Public_Controller.php';

/**
 * Customer Controller
 *
 * Handles customer area including:
 * - Login & Registration
 * - Account dashboard
 * - Profile management
 * - Order history
 * - Wishlist
 *
 * @package    Putra Elektronik
 * @subpackage Controllers
 * @category   Customer Management
 */
class Customer extends Public_Controller {

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Customer_model');
        $this->load->model('Order_model');
        $this->load->model('Order_item_model');
        $this->load->model('Wishlist_model');
    }

    /**
     * Display login page
     *
     * @return void
     */
    public function login()
    {
        // If already logged in, redirect to account
        if ($this->data['customer_logged_in']) {
            redirect('customer/account');
        }

        $data = [
            'title' => 'Login - ' . $this->data['site_settings']['site_name']
        ];

        $this->render('public/customer/login', $data);
    }

    /**
     * Process login
     *
     * @return void (redirect)
     */
    public function login_process()
    {
        // Only accept POST
        if ($this->input->method() !== 'post') {
            redirect('customer/login');
        }

        // Validate form
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customer/login');
        }

        $email = $this->input->post('email', true);
        $password = $this->input->post('password', true);

        // Attempt login
        $customer = $this->Customer_model->login($email, $password);

        if ($customer) {
            // Set session
            $this->session->set_userdata([
                'customer_logged_in' => true,
                'customer_data' => $customer
            ]);

            $this->session->set_flashdata('success', 'Selamat datang kembali, ' . $customer->full_name);
            redirect('customer/account');
        } else {
            $this->session->set_flashdata('error', 'Email atau password salah');
            redirect('customer/login');
        }
    }

    /**
     * Display registration page
     *
     * @return void
     */
    public function register()
    {
        // If already logged in, redirect to account
        if ($this->data['customer_logged_in']) {
            redirect('customer/account');
        }

        $data = [
            'title' => 'Register - ' . $this->data['site_settings']['site_name']
        ];

        $this->render('public/customer/register', $data);
    }

    /**
     * Process registration
     *
     * @return void (redirect)
     */
    public function register_process()
    {
        // Only accept POST
        if ($this->input->method() !== 'post') {
            redirect('customer/register');
        }

        // Validate form
        $this->form_validation->set_rules('full_name', 'Nama Lengkap', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[customers.email]', [
            'is_unique' => 'Email sudah terdaftar'
        ]);
        $this->form_validation->set_rules('phone', 'No. HP', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('password_confirm', 'Konfirmasi Password', 'required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('customer/register');
        }

        // Prepare data
        $customer_data = [
            'full_name' => $this->input->post('full_name', true),
            'email' => $this->input->post('email', true),
            'phone' => $this->input->post('phone', true),
            'password' => password_hash($this->input->post('password', true), PASSWORD_DEFAULT),
            'is_active' => 1
        ];

        // Insert customer
        $customer_id = $this->Customer_model->insert($customer_data);

        if ($customer_id) {
            $this->session->set_flashdata('success', 'Registrasi berhasil! Silakan login.');
            redirect('customer/login');
        } else {
            $this->session->set_flashdata('error', 'Gagal melakukan registrasi. Silakan coba lagi.');
            redirect('customer/register');
        }
    }

    /**
     * Customer account dashboard
     *
     * @return void
     */
    public function account()
    {
        // Require login
        $this->check_customer_login();

        // Verify customer_data exists in session
        if (!isset($this->data['customer_data']) || !$this->data['customer_data']) {
            $this->session->set_flashdata('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            redirect('customer/login');
        }

        // Get customer data
        $customer = $this->Customer_model->get_by_id($this->data['customer_data']->customer_id);

        // Get recent orders
        $recent_orders = $this->Order_model->get_by_customer($customer->customer_id, 5);

        $data = [
            'title' => 'Akun Saya - ' . $this->data['site_settings']['site_name'],
            'customer' => $customer,
            'recent_orders' => $recent_orders
        ];

        $this->render('public/customer/account', $data);
    }

    /**
     * Customer order history
     *
     * @return void
     */
    public function orders()
    {
        // Require login
        $this->check_customer_login();

        // Get all orders
        $orders = $this->Order_model->get_by_customer($this->data['customer_data']->customer_id);

        $data = [
            'title' => 'Pesanan Saya - ' . $this->data['site_settings']['site_name'],
            'orders' => $orders
        ];

        $this->render('public/customer/orders', $data);
    }

    /**
     * Order detail
     *
     * @param int $order_id
     * @return void
     */
    public function order($order_id = null)
    {
        // Require login
        $this->check_customer_login();

        if (!$order_id) {
            redirect('customer/orders');
        }

        // Get order
        $order = $this->Order_model->get_by_id($order_id);

        if (!$order || $order->customer_id != $this->data['customer_data']->customer_id) {
            $this->session->set_flashdata('error', 'Pesanan tidak ditemukan');
            redirect('customer/orders');
        }

        // Get order items
        $order_items = $this->Order_item_model->get_by_order($order_id);

        // Generate WhatsApp link
        $whatsapp_phone = $this->data['site_settings']['site_whatsapp'] ?? '';
        $whatsapp_message = generate_whatsapp_message($order, $order_items);
        $whatsapp_url = generate_whatsapp_url($whatsapp_phone, $whatsapp_message);

        $data = [
            'title' => 'Detail Pesanan - ' . $this->data['site_settings']['site_name'],
            'order' => $order,
            'order_items' => $order_items,
            'whatsapp_url' => $whatsapp_url
        ];

        $this->render('public/customer/order_detail', $data);
    }

    /**
     * Customer wishlist
     *
     * @return void
     */
    public function wishlist()
    {
        $data = [
            'title' => 'Wishlist - ' . $this->data['site_settings']['site_name']
        ];

        // If logged in, get wishlist
        if ($this->data['customer_logged_in']) {
            $data['wishlist_items'] = $this->Wishlist_model->get_by_customer($this->data['customer_data']->customer_id);
        } else {
            $data['wishlist_items'] = [];
        }

        $this->render('public/customer/wishlist', $data);
    }

    /**
     * Add product to wishlist (AJAX)
     *
     * @return void (JSON response)
     */
    public function wishlist_add()
    {
        // Set JSON header
        header('Content-Type: application/json');

        // Check if customer is logged in
        if (!$this->data['customer_logged_in']) {
            echo json_encode([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu untuk menambahkan ke wishlist'
            ]);
            return;
        }

        // Get product_id from POST
        $product_id = $this->input->post('product_id');

        if (!$product_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Product ID tidak valid'
            ]);
            return;
        }

        // Get customer ID
        $customer_id = $this->data['customer_data']->customer_id;

        // Check if already in wishlist
        if ($this->Wishlist_model->check_exists($customer_id, $product_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Produk sudah ada di wishlist Anda'
            ]);
            return;
        }

        // Add to wishlist
        $wishlist_data = [
            'customer_id' => $customer_id,
            'product_id' => $product_id,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->Wishlist_model->insert($wishlist_data);

        if ($result) {
            // Get updated wishlist count
            $wishlist_count = $this->Wishlist_model->count_by_customer($customer_id);

            echo json_encode([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke wishlist',
                'wishlist_count' => $wishlist_count
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal menambahkan produk ke wishlist'
            ]);
        }
    }

    /**
     * Remove product from wishlist (AJAX)
     *
     * @return void (JSON response)
     */
    public function wishlist_remove()
    {
        // Set JSON header
        header('Content-Type: application/json');

        // Check if customer is logged in
        if (!$this->data['customer_logged_in']) {
            echo json_encode([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ]);
            return;
        }

        // Get product_id from POST
        $product_id = $this->input->post('product_id');

        if (!$product_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Product ID tidak valid'
            ]);
            return;
        }

        // Get customer ID
        $customer_id = $this->data['customer_data']->customer_id;

        // Remove from wishlist
        $result = $this->Wishlist_model->delete($customer_id, $product_id);

        if ($result) {
            // Get updated wishlist count
            $wishlist_count = $this->Wishlist_model->count_by_customer($customer_id);

            echo json_encode([
                'success' => true,
                'message' => 'Produk berhasil dihapus dari wishlist',
                'wishlist_count' => $wishlist_count
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal menghapus produk dari wishlist'
            ]);
        }
    }

    /**
     * Logout
     *
     * @return void (redirect)
     */
    public function logout()
    {
        // Destroy session
        $this->session->unset_userdata('customer_logged_in');
        $this->session->unset_userdata('customer_data');

        $this->session->set_flashdata('success', 'Anda telah logout');
        redirect('home');
    }
}
