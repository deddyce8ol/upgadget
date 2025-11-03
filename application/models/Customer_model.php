<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Customer_model extends CI_Model
{
    private $table = 'customers';
    private $primary_key = 'customer_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($is_active = null)
    {
        if ($is_active !== null) {
            $this->db->where('is_active', $is_active);
        }
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    public function count_all($is_active = null)
    {
        if ($is_active !== null) {
            $this->db->where('is_active', $is_active);
        }
        return $this->db->count_all_results($this->table);
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, [$this->primary_key => $id])->row();
    }

    public function get_by_email($email)
    {
        return $this->db->get_where($this->table, ['email' => $email])->row();
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

    public function check_email_exists($email, $exclude_id = null)
    {
        $this->db->where('email', $email);
        if ($exclude_id) {
            $this->db->where($this->primary_key . ' !=', $exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }

    public function verify_login($email, $password)
    {
        $customer = $this->get_by_email($email);
        if ($customer && $customer->is_active == 1) {
            if (password_verify($password, $customer->password)) {
                return $customer;
            }
        }
        return false;
    }

    /**
     * Login method (alias for verify_login)
     */
    public function login($email, $password)
    {
        return $this->verify_login($email, $password);
    }
}
