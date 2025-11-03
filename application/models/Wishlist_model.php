<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Wishlist_model extends CI_Model
{
    private $table = 'wishlist';
    private $primary_key = 'wishlist_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_by_customer($customer_id)
    {
        $this->db->select('wishlist.*, products.product_name, products.product_slug, products.price, products.discount_price, products.main_image, products.stock, products.is_active');
        $this->db->from($this->table);
        $this->db->join('products', 'products.product_id = wishlist.product_id');
        $this->db->where('wishlist.customer_id', $customer_id);
        $this->db->order_by('wishlist.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function check_exists($customer_id, $product_id)
    {
        $this->db->where('customer_id', $customer_id);
        $this->db->where('product_id', $product_id);
        return $this->db->count_all_results($this->table) > 0;
    }

    public function insert($data)
    {
        // Check if already exists
        if ($this->check_exists($data['customer_id'], $data['product_id'])) {
            return false;
        }
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function delete($customer_id, $product_id)
    {
        $this->db->where('customer_id', $customer_id);
        $this->db->where('product_id', $product_id);
        return $this->db->delete($this->table);
    }

    public function count_by_customer($customer_id)
    {
        $this->db->where('customer_id', $customer_id);
        return $this->db->count_all_results($this->table);
    }
}
