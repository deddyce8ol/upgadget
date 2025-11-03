<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Customer_address_model extends CI_Model
{
    private $table = 'customer_addresses';
    private $primary_key = 'address_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_by_customer($customer_id)
    {
        $this->db->where('customer_id', $customer_id);
        $this->db->order_by('is_default', 'DESC');
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, [$this->primary_key => $id])->row();
    }

    public function get_default_address($customer_id)
    {
        $this->db->where('customer_id', $customer_id);
        $this->db->where('is_default', 1);
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

    public function set_default($customer_id, $address_id)
    {
        // Reset all addresses for this customer
        $this->db->where('customer_id', $customer_id);
        $this->db->update($this->table, ['is_default' => 0]);

        // Set new default address
        $this->db->where($this->primary_key, $address_id);
        return $this->db->update($this->table, ['is_default' => 1]);
    }
}
