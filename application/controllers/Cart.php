<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'core/Public_Controller.php';

/**
 * Cart Controller
 *
 * Handles shopping cart functionality including:
 * - View cart
 * - Add to cart (AJAX)
 * - Update quantity (AJAX)
 * - Remove item (AJAX)
 * - Clear cart
 *
 * @package    Putra Elektronik
 * @subpackage Controllers
 * @category   E-commerce
 * @author     Your Name
 */
class Cart extends Public_Controller {

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Product_model');

        // Disable product name validation to allow special characters like / in product names
        $this->cart->product_name_safe = FALSE;
    }

    /**
     * Display shopping cart page
     *
     * @return void
     */
    public function index()
    {
        $data = [
            'title' => 'Keranjang Belanja - ' . $this->data['site_settings']['site_name'],
            'cart_items' => $this->cart->contents(),
            'cart_total' => $this->cart->total(),
            'cart_count' => $this->cart->total_items()
        ];

        $this->render('public/cart/index', $data);
    }

    /**
     * Add item to cart (AJAX)
     *
     * @return void (JSON response)
     */
    public function add()
    {
        // Only accept POST requests
        if ($this->input->method() !== 'post') {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Invalid request method'
                ]));
            return;
        }

        // Get POST data
        $product_id = $this->input->post('product_id', true);
        $quantity = $this->input->post('quantity', true) ?: 1;

        // Validate product ID
        if (empty($product_id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Product ID is required'
                ]));
            return;
        }

        // Get product details
        $product = $this->Product_model->get_by_id($product_id);

        // Check if product exists
        if (!$product) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Produk tidak ditemukan'
                ]));
            return;
        }

        // Check if product is active
        if ($product->is_active != 1) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Produk tidak tersedia'
                ]));
            return;
        }

        // Check stock availability
        if ($product->stock < $quantity) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $product->stock
                ]));
            return;
        }

        // Get final price (considering discount)
        $price = get_final_price($product);

        // Prepare cart item data
        $cart_data = [
            'id'      => $product->product_id,
            'qty'     => $quantity,
            'price'   => $price,
            'name'    => $product->product_name,
            'options' => [
                'slug'           => $product->product_slug,
                'image'          => $product->main_image,
                'stock'          => $product->stock,
                'original_price' => $product->price,
                'discount_price' => $product->discount_price
            ]
        ];

        // Add to cart
        $inserted = $this->cart->insert($cart_data);

        if ($inserted) {
            // Get updated cart info
            $cart_count = $this->cart->total_items();
            $cart_total = $this->cart->total();

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'Produk berhasil ditambahkan ke keranjang',
                    'cart_count' => $cart_count,
                    'cart_total' => format_rupiah($cart_total),
                    'cart_total_raw' => $cart_total
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Gagal menambahkan produk ke keranjang'
                ]));
        }
    }

    /**
     * Update cart item quantity (AJAX)
     *
     * @return void (JSON response)
     */
    public function update()
    {
        // Only accept POST requests
        if ($this->input->method() !== 'post') {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Invalid request method'
                ]));
            return;
        }

        // Get POST data
        $rowid = $this->input->post('rowid', true);
        $quantity = $this->input->post('quantity', true);

        // Validate input
        if (empty($rowid) || empty($quantity)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Data tidak lengkap'
                ]));
            return;
        }

        // Validate quantity
        if (!is_numeric($quantity) || $quantity < 1) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Jumlah tidak valid'
                ]));
            return;
        }

        // Get cart item
        $item = $this->cart->get_item($rowid);

        if (!$item) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Item tidak ditemukan di keranjang'
                ]));
            return;
        }

        // Check stock availability
        if ($item['options']['stock'] < $quantity) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $item['options']['stock']
                ]));
            return;
        }

        // Update cart
        $updated = $this->cart->update([
            'rowid' => $rowid,
            'qty'   => $quantity
        ]);

        if ($updated) {
            // Get updated item
            $updated_item = $this->cart->get_item($rowid);
            $item_subtotal = $updated_item['price'] * $updated_item['qty'];

            // Get updated cart info
            $cart_count = $this->cart->total_items();
            $cart_total = $this->cart->total();

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'Keranjang berhasil diperbarui',
                    'item_subtotal' => format_rupiah($item_subtotal),
                    'item_subtotal_raw' => $item_subtotal,
                    'cart_count' => $cart_count,
                    'cart_total' => format_rupiah($cart_total),
                    'cart_total_raw' => $cart_total
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Gagal memperbarui keranjang'
                ]));
        }
    }

    /**
     * Remove item from cart (AJAX)
     *
     * @return void (JSON response)
     */
    public function remove()
    {
        // Only accept POST requests
        if ($this->input->method() !== 'post') {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Invalid request method'
                ]));
            return;
        }

        // Get POST data
        $rowid = $this->input->post('rowid', true);

        // Validate input
        if (empty($rowid)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Row ID is required'
                ]));
            return;
        }

        // Remove from cart
        $removed = $this->cart->remove($rowid);

        if ($removed) {
            // Get updated cart info
            $cart_count = $this->cart->total_items();
            $cart_total = $this->cart->total();

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'Produk berhasil dihapus dari keranjang',
                    'cart_count' => $cart_count,
                    'cart_total' => format_rupiah($cart_total),
                    'cart_total_raw' => $cart_total,
                    'cart_empty' => ($cart_count == 0)
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Gagal menghapus produk dari keranjang'
                ]));
        }
    }

    /**
     * Clear all items from cart
     *
     * @return void (redirect)
     */
    public function clear()
    {
        $this->cart->destroy();

        $this->session->set_flashdata('success', 'Keranjang berhasil dikosongkan');
        redirect('cart');
    }

    /**
     * Get cart count (AJAX)
     * Used for updating cart icon badge
     *
     * @return void (JSON response)
     */
    public function count()
    {
        $cart_count = $this->cart->total_items();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'count' => $cart_count
            ]));
    }
}
