<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'core/Public_Controller.php';

/**
 * Checkout Controller
 *
 * Handles checkout process including:
 * - Multi-step checkout form
 * - Order creation
 * - WhatsApp notification
 * - Order success page
 *
 * @package    Putra Elektronik
 * @subpackage Controllers
 * @category   E-commerce
 */
class Checkout extends Public_Controller {

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Order_model');
        $this->load->model('Order_item_model');
        $this->load->model('Customer_model');
        $this->load->model('Customer_address_model');
    }

    /**
     * Display checkout page
     *
     * @return void
     */
    public function index()
    {
        // Check if cart is empty
        if ($this->cart->total_items() == 0) {
            $this->session->set_flashdata('error', 'Keranjang belanja Anda kosong');
            redirect('cart');
        }

        // Get cart items
        $data = [
            'title' => 'Checkout - ' . $this->data['site_settings']['site_name'],
            'cart_items' => $this->cart->contents(),
            'cart_total' => $this->cart->total(),
            'cart_count' => $this->cart->total_items()
        ];

        // If customer is logged in, get their data
        if ($this->data['customer_logged_in']) {
            $data['customer'] = $this->Customer_model->get_by_id($this->data['customer_data']->customer_id);
            $data['addresses'] = $this->Customer_address_model->get_by_customer($this->data['customer_data']->customer_id);
        }

        $this->render('public/checkout/index', $data);
    }

    /**
     * Process checkout and create order
     *
     * @return void (JSON response or redirect)
     */
    public function process()
    {
        // Only accept POST requests
        if ($this->input->method() !== 'post') {
            redirect('checkout');
        }

        // Check if cart is empty
        if ($this->cart->total_items() == 0) {
            $this->session->set_flashdata('error', 'Keranjang belanja Anda kosong');
            redirect('cart');
        }

        // Validate form
        $this->form_validation->set_rules('customer_name', 'Nama', 'required|trim');
        $this->form_validation->set_rules('customer_email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('customer_phone', 'No. HP', 'required|trim');
        $this->form_validation->set_rules('shipping_address', 'Alamat Pengiriman', 'required|trim');
        $this->form_validation->set_rules('shipping_city', 'Kota', 'required|trim');
        $this->form_validation->set_rules('shipping_province', 'Provinsi', 'required|trim');
        $this->form_validation->set_rules('shipping_postal_code', 'Kode Pos', 'required|trim|numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('checkout');
        }

        // Get form data
        $customer_name = $this->input->post('customer_name', true);
        $customer_email = $this->input->post('customer_email', true);
        $customer_phone = $this->input->post('customer_phone', true);
        $shipping_address = $this->input->post('shipping_address', true);
        $shipping_city = $this->input->post('shipping_city', true);
        $shipping_province = $this->input->post('shipping_province', true);
        $shipping_postal_code = $this->input->post('shipping_postal_code', true);
        $order_notes = $this->input->post('order_notes', true);

        // Calculate totals
        $subtotal = $this->cart->total();
        $shipping_cost = 0; // Will be calculated based on city/province in future
        $total = $subtotal + $shipping_cost;

        // Prepare order data
        $order_data = [
            'customer_id' => $this->data['customer_logged_in'] ? $this->data['customer_data']->customer_id : null,
            'order_number' => $this->Order_model->generate_order_number(),
            'customer_name' => $customer_name,
            'customer_email' => $customer_email,
            'customer_phone' => $customer_phone,
            'shipping_address' => $shipping_address,
            'shipping_city' => $shipping_city,
            'shipping_province' => $shipping_province,
            'shipping_postal_code' => $shipping_postal_code,
            'subtotal' => $subtotal,
            'shipping_cost' => $shipping_cost,
            'total_amount' => $total,
            'notes' => $order_notes,
            'status' => 'pending',
            'payment_status' => 'unpaid'
        ];

        // Insert order
        $order_id = $this->Order_model->insert($order_data);

        if (!$order_id) {
            $this->session->set_flashdata('error', 'Gagal membuat pesanan. Silakan coba lagi.');
            redirect('checkout');
        }

        // Insert order items
        foreach ($this->cart->contents() as $item) {
            $order_item_data = [
                'order_id' => $order_id,
                'product_id' => $item['id'],
                'product_name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['qty'],
                'subtotal' => $item['price'] * $item['qty']
            ];

            $this->Order_item_model->insert($order_item_data);
        }

        // Get complete order data for WhatsApp
        $order = $this->Order_model->get_by_id($order_id);
        $order_items = $this->Order_item_model->get_by_order($order_id);

        // Clear cart
        $this->cart->destroy();

        // Store order number in session for success page
        $this->session->set_flashdata('order_number', $order->order_number);
        $this->session->set_flashdata('order_id', $order_id);

        // Redirect to success page
        redirect('checkout/success/' . $order->order_number);
    }

    /**
     * Order success page
     *
     * @param string $order_number Order number
     * @return void
     */
    public function success($order_number = null)
    {
        if (!$order_number) {
            redirect('home');
        }

        // Get order by order number
        $order = $this->Order_model->get_by_order_number($order_number);

        if (!$order) {
            $this->session->set_flashdata('error', 'Pesanan tidak ditemukan');
            redirect('home');
        }

        // Get order items
        $order_items = $this->Order_item_model->get_by_order($order->order_id);

        // Generate WhatsApp message and URL
        $whatsapp_phone = $this->data['site_settings']['contact_whatsapp'] ?? '';
        $whatsapp_message = generate_whatsapp_message($order, $order_items);
        $whatsapp_url = generate_whatsapp_url($whatsapp_phone, $whatsapp_message);

        $data = [
            'title' => 'Pesanan Berhasil - ' . $this->data['site_settings']['site_name'],
            'order' => $order,
            'order_items' => $order_items,
            'whatsapp_url' => $whatsapp_url
        ];

        $this->render('public/checkout/success', $data);
    }
}
