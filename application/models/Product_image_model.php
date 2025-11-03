<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Product_image_model extends CI_Model
{
    private $table = 'product_images';
    private $primary_key = 'image_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_by_product($product_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->order_by('is_primary', 'DESC');
        $this->db->order_by('sort_order', 'ASC');
        return $this->db->get($this->table)->result();
    }

    // Alias method for consistency
    public function get_by_product_id($product_id)
    {
        return $this->get_by_product($product_id);
    }

    public function get_by_id($id)
    {
        $this->db->where($this->primary_key, $id);
        return $this->db->get($this->table)->row();
    }

    public function get_primary_image($product_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('is_primary', 1);
        return $this->db->get($this->table)->row();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $this->db->where($this->primary_key, $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where($this->primary_key, $id);
        return $this->db->delete($this->table);
    }

    public function set_primary($product_id, $image_id)
    {
        // Reset all images for this product
        $this->db->where('product_id', $product_id);
        $this->db->update($this->table, ['is_primary' => 0]);

        // Set new primary image
        $this->db->where($this->primary_key, $image_id);
        return $this->db->update($this->table, ['is_primary' => 1]);
    }

    public function delete_by_product($product_id)
    {
        $this->db->where('product_id', $product_id);
        return $this->db->delete($this->table);
    }
}
