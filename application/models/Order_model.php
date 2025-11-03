<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Order_model extends CI_Model
{
    private $table = 'orders';
    private $primary_key = 'order_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($filters = [])
    {
        $this->db->select('orders.*, customers.full_name as customer_full_name, customers.email as customer_email_data');
        $this->db->from($this->table);
        $this->db->join('customers', 'customers.customer_id = orders.customer_id', 'left');

        if (isset($filters['status'])) {
            $this->db->where('orders.status', $filters['status']);
        }
        if (isset($filters['payment_status'])) {
            $this->db->where('orders.payment_status', $filters['payment_status']);
        }
        if (isset($filters['customer_id'])) {
            $this->db->where('orders.customer_id', $filters['customer_id']);
        }
        if (isset($filters['search'])) {
            $this->db->group_start();
            $this->db->like('orders.order_number', $filters['search']);
            $this->db->or_like('orders.customer_name', $filters['search']);
            $this->db->or_like('orders.customer_phone', $filters['search']);
            $this->db->group_end();
        }
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $this->db->where('DATE(orders.created_at) >=', $filters['start_date']);
            $this->db->where('DATE(orders.created_at) <=', $filters['end_date']);
        }

        $this->db->order_by('orders.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get_paginated($limit, $offset, $filters = [])
    {
        $this->db->select('orders.*, customers.full_name as customer_full_name');
        $this->db->from($this->table);
        $this->db->join('customers', 'customers.customer_id = orders.customer_id', 'left');

        if (isset($filters['status'])) {
            $this->db->where('orders.status', $filters['status']);
        }
        if (isset($filters['payment_status'])) {
            $this->db->where('orders.payment_status', $filters['payment_status']);
        }
        if (isset($filters['customer_id'])) {
            $this->db->where('orders.customer_id', $filters['customer_id']);
        }
        if (isset($filters['search'])) {
            $this->db->group_start();
            $this->db->like('orders.order_number', $filters['search']);
            $this->db->or_like('orders.customer_name', $filters['search']);
            $this->db->or_like('orders.customer_phone', $filters['search']);
            $this->db->group_end();
        }
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $this->db->where('DATE(orders.created_at) >=', $filters['start_date']);
            $this->db->where('DATE(orders.created_at) <=', $filters['end_date']);
        }

        $this->db->limit($limit, $offset);
        $this->db->order_by('orders.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function count_all($filters = [])
    {
        $this->db->from($this->table);

        if (isset($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        if (isset($filters['payment_status'])) {
            $this->db->where('payment_status', $filters['payment_status']);
        }
        if (isset($filters['customer_id'])) {
            $this->db->where('customer_id', $filters['customer_id']);
        }
        if (isset($filters['search'])) {
            $this->db->group_start();
            $this->db->like('order_number', $filters['search']);
            $this->db->or_like('customer_name', $filters['search']);
            $this->db->or_like('customer_phone', $filters['search']);
            $this->db->group_end();
        }
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $this->db->where('DATE(created_at) >=', $filters['start_date']);
            $this->db->where('DATE(created_at) <=', $filters['end_date']);
        }

        return $this->db->count_all_results();
    }

    public function get_by_id($id)
    {
        $this->db->select('orders.*, customers.full_name as customer_full_name, customers.email as customer_email_data');
        $this->db->from($this->table);
        $this->db->join('customers', 'customers.customer_id = orders.customer_id', 'left');
        $this->db->where('orders.' . $this->primary_key, $id);
        return $this->db->get()->row();
    }

    public function get_by_order_number($order_number)
    {
        $this->db->select('orders.*, customers.full_name as customer_full_name');
        $this->db->from($this->table);
        $this->db->join('customers', 'customers.customer_id = orders.customer_id', 'left');
        $this->db->where('orders.order_number', $order_number);
        return $this->db->get()->row();
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

    public function generate_order_number()
    {
        $prefix = 'PTE';
        $date = date('Ymd');

        // Get last order number for today
        $this->db->select('order_number');
        $this->db->from($this->table);
        $this->db->like('order_number', $prefix . $date, 'after');
        $this->db->order_by('order_number', 'DESC');
        $this->db->limit(1);
        $last_order = $this->db->get()->row();

        if ($last_order) {
            $last_number = (int)substr($last_order->order_number, -4);
            $new_number = $last_number + 1;
        } else {
            $new_number = 1;
        }

        return $prefix . $date . str_pad($new_number, 4, '0', STR_PAD_LEFT);
    }

    public function get_dashboard_stats()
    {
        $stats = [];

        // Total orders
        $stats['total_orders'] = $this->db->count_all_results($this->table);

        // Pending orders
        $this->db->where('status', 'pending');
        $stats['pending_orders'] = $this->db->count_all_results($this->table);

        // Total revenue
        $this->db->select_sum('total_amount');
        $this->db->where('payment_status', 'paid');
        $result = $this->db->get($this->table)->row();
        $stats['total_revenue'] = $result->total_amount ?? 0;

        // Today's orders
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $stats['today_orders'] = $this->db->count_all_results($this->table);

        return $stats;
    }

    public function count_by_status($status)
    {
        $this->db->where('status', $status);
        return $this->db->count_all_results($this->table);
    }

    public function get_total_revenue()
    {
        $this->db->select_sum('total_amount');
        $result = $this->db->get($this->table)->row();
        return $result->total_amount ?? 0;
    }

    public function get_recent_orders($limit = 10)
    {
        $this->db->select('orders.*, customers.full_name as customer_full_name');
        $this->db->from($this->table);
        $this->db->join('customers', 'customers.customer_id = orders.customer_id', 'left');
        $this->db->order_by('orders.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Get orders by customer ID
     *
     * @param int $customer_id
     * @param int|null $limit Optional limit for number of orders
     * @return array
     */
    public function get_by_customer($customer_id, $limit = null)
    {
        $this->db->select('orders.*');
        $this->db->from($this->table);
        $this->db->where('orders.customer_id', $customer_id);
        $this->db->order_by('orders.created_at', 'DESC');

        if ($limit !== null) {
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }
}
