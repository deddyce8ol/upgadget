<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Banner_model extends CI_Model
{
    private $table = 'banners';
    private $primary_key = 'banner_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($is_active = null)
    {
        if ($is_active !== null) {
            $this->db->where('is_active', $is_active);
        }
        $this->db->order_by('sort_order', 'ASC');
        return $this->db->get($this->table)->result();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, [$this->primary_key => $id])->row();
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
        $banner = $this->get_by_id($id);
        if ($banner) {
            $new_status = $banner->is_active == 1 ? 0 : 1;
            return $this->update($id, ['is_active' => $new_status]);
        }
        return false;
    }
}
