<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Report_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get date range from existing orders
     */
    public function get_date_range()
    {
        $this->db->select('
            MIN(DATE(created_at)) as earliest,
            MAX(DATE(created_at)) as latest
        ');
        $this->db->from('orders');
        $this->db->where('payment_status', 'paid');

        return $this->db->get()->row();
    }

    /**
     * Get sales summary (total revenue, total transactions, total items sold)
     */
    public function get_sales_summary($filters = [])
    {
        $this->db->select('
            COUNT(DISTINCT orders.order_id) as total_transactions,
            COALESCE(SUM(orders.total_amount), 0) as total_revenue,
            COALESCE(SUM(order_items.quantity), 0) as total_items_sold
        ');
        $this->db->from('orders');
        $this->db->join('order_items', 'order_items.order_id = orders.order_id', 'left');

        // Apply filters
        if (isset($filters['payment_status'])) {
            $this->db->where('orders.payment_status', $filters['payment_status']);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $this->db->where('DATE(orders.created_at) >=', $filters['start_date']);
            $this->db->where('DATE(orders.created_at) <=', $filters['end_date']);
        }

        $result = $this->db->get()->row();

        return [
            'total_revenue' => $result->total_revenue ?? 0,
            'total_transactions' => $result->total_transactions ?? 0,
            'total_items_sold' => $result->total_items_sold ?? 0
        ];
    }

    /**
     * Get sales transactions for DataTable
     */
    public function get_sales_transactions($filters = [])
    {
        $this->db->select('
            orders.*,
            COUNT(order_items.order_item_id) as total_items
        ');
        $this->db->from('orders');
        $this->db->join('order_items', 'order_items.order_id = orders.order_id', 'left');

        // Apply filters
        if (isset($filters['payment_status'])) {
            $this->db->where('orders.payment_status', $filters['payment_status']);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $this->db->where('DATE(orders.created_at) >=', $filters['start_date']);
            $this->db->where('DATE(orders.created_at) <=', $filters['end_date']);
        }

        $this->db->group_by('orders.order_id');
        $this->db->order_by('orders.created_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get sales data grouped by date for chart
     */
    public function get_sales_by_date($filters = [])
    {
        $this->db->select('
            DATE(orders.created_at) as date,
            COUNT(DISTINCT orders.order_id) as total_orders,
            COALESCE(SUM(orders.total_amount), 0) as total_revenue
        ');
        $this->db->from('orders');

        // Apply filters
        if (isset($filters['payment_status'])) {
            $this->db->where('orders.payment_status', $filters['payment_status']);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $this->db->where('DATE(orders.created_at) >=', $filters['start_date']);
            $this->db->where('DATE(orders.created_at) <=', $filters['end_date']);
        }

        $this->db->group_by('DATE(orders.created_at)');
        $this->db->order_by('date', 'ASC');

        $results = $this->db->get()->result();

        // Format data for Chart.js
        $labels = [];
        $revenues = [];
        $orders = [];

        foreach ($results as $row) {
            $labels[] = date('d M', strtotime($row->date));
            $revenues[] = (float) $row->total_revenue;
            $orders[] = (int) $row->total_orders;
        }

        return [
            'labels' => $labels,
            'revenues' => $revenues,
            'orders' => $orders
        ];
    }

    /**
     * Get top selling products
     */
    public function get_top_products($filters = [], $limit = 10)
    {
        $this->db->select('
            order_items.product_id,
            order_items.product_name,
            SUM(order_items.quantity) as total_sold,
            SUM(order_items.subtotal) as total_revenue
        ');
        $this->db->from('order_items');
        $this->db->join('orders', 'orders.order_id = order_items.order_id', 'left');

        // Apply filters
        if (isset($filters['payment_status'])) {
            $this->db->where('orders.payment_status', $filters['payment_status']);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $this->db->where('DATE(orders.created_at) >=', $filters['start_date']);
            $this->db->where('DATE(orders.created_at) <=', $filters['end_date']);
        }

        $this->db->group_by('order_items.product_id');
        $this->db->order_by('total_sold', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    /**
     * Get sales by category
     */
    public function get_sales_by_category($filters = [])
    {
        $this->db->select('
            categories.category_name,
            COUNT(DISTINCT orders.order_id) as total_orders,
            SUM(order_items.quantity) as total_items,
            SUM(order_items.subtotal) as total_revenue
        ');
        $this->db->from('order_items');
        $this->db->join('orders', 'orders.order_id = order_items.order_id', 'left');
        $this->db->join('products', 'products.product_id = order_items.product_id', 'left');
        $this->db->join('categories', 'categories.category_id = products.category_id', 'left');

        // Apply filters
        if (isset($filters['payment_status'])) {
            $this->db->where('orders.payment_status', $filters['payment_status']);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $this->db->where('DATE(orders.created_at) >=', $filters['start_date']);
            $this->db->where('DATE(orders.created_at) <=', $filters['end_date']);
        }

        $this->db->group_by('categories.category_id');
        $this->db->order_by('total_revenue', 'DESC');

        return $this->db->get()->result();
    }
}
