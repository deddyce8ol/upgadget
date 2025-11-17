<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Brand_model extends CI_Model
{
    private $table = 'brands';
    private $primary_key = 'brand_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($filters = null)
    {
        // Support both old way (integer) and new way (array)
        if ($filters !== null) {
            if (is_array($filters)) {
                // New way: array of filters
                foreach ($filters as $key => $value) {
                    $this->db->where($key, $value);
                }
            } else {
                // Old way: single is_active value for backward compatibility
                $this->db->where('is_active', $filters);
            }
        }
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('brand_id', 'ASC');
        return $this->db->get($this->table)->result();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, [$this->primary_key => $id])->row();
    }

    public function get_by_slug($slug)
    {
        return $this->db->get_where($this->table, ['brand_slug' => $slug])->row();
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

    public function toggle_status($id)
    {
        $brand = $this->get_by_id($id);
        if ($brand) {
            $new_status = $brand->is_active == 1 ? 0 : 1;
            return $this->update($id, ['is_active' => $new_status]);
        }
        return false;
    }

    public function check_slug_exists($slug, $exclude_id = null)
    {
        $this->db->where('brand_slug', $slug);
        if ($exclude_id) {
            $this->db->where($this->primary_key . ' !=', $exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }

    public function update_sort_order($brand_id, $order)
    {
        $this->db->where($this->primary_key, $brand_id);
        return $this->db->update($this->table, ['sort_order' => $order]);
    }

    public function update_bulk_order($order_data)
    {
        $this->db->trans_start();
        foreach ($order_data as $brand_id => $order) {
            $this->update_sort_order($brand_id, $order);
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_max_sort_order()
    {
        $this->db->select_max('sort_order');
        $result = $this->db->get($this->table)->row();
        return $result ? (int)$result->sort_order : 0;
    }
}
