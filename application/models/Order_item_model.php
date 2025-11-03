<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Order_item_model extends CI_Model
{
    private $table = 'order_items';
    private $primary_key = 'order_item_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_by_order($order_id)
    {
        $this->db->select('order_items.*, products.main_image');
        $this->db->from($this->table);
        $this->db->join('products', 'products.product_id = order_items.product_id', 'left');
        $this->db->where('order_items.order_id', $order_id);
        return $this->db->get()->result();
    }

    public function get_by_order_id($order_id)
    {
        $this->db->where('order_id', $order_id);
        return $this->db->get($this->table)->result();
    }

    public function delete_by_order_id($order_id)
    {
        $this->db->where('order_id', $order_id);
        return $this->db->delete($this->table);
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function insert_batch($data)
    {
        return $this->db->insert_batch($this->table, $data);
    }

    public function delete_by_order($order_id)
    {
        $this->db->where('order_id', $order_id);
        return $this->db->delete($this->table);
    }
}
