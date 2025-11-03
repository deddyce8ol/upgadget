<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Product_model extends CI_Model
{
    private $table = 'products';
    private $primary_key = 'product_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($filters = [])
    {
        $this->db->select('products.*, categories.name as category_name, brands.brand_name');
        $this->db->from($this->table);
        $this->db->join('categories', 'categories.id = products.category_id', 'left');
        $this->db->join('brands', 'brands.brand_id = products.brand_id', 'left');

        // Apply filters
        if (isset($filters['is_active'])) {
            $this->db->where('products.is_active', $filters['is_active']);
        }
        if (isset($filters['is_featured'])) {
            $this->db->where('products.is_featured', $filters['is_featured']);
        }
        if (isset($filters['category_id'])) {
            $this->db->where('products.category_id', $filters['category_id']);
        }
        if (isset($filters['brand_id'])) {
            $this->db->where('products.brand_id', $filters['brand_id']);
        }
        if (isset($filters['search'])) {
            $this->db->group_start();
            $this->db->like('products.product_name', $filters['search']);
            $this->db->or_like('products.description', $filters['search']);
            $this->db->or_like('products.sku', $filters['search']);
            $this->db->group_end();
        }

        $this->db->order_by('products.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get_paginated($limit, $offset, $filters = [], $sort = 'newest')
    {
        $this->db->select('products.*, categories.name as category_name, brands.brand_name');
        $this->db->from($this->table);
        $this->db->join('categories', 'categories.id = products.category_id', 'left');
        $this->db->join('brands', 'brands.brand_id = products.brand_id', 'left');

        // Apply filters
        if (isset($filters['is_active'])) {
            $this->db->where('products.is_active', $filters['is_active']);
        }
        if (isset($filters['is_featured'])) {
            $this->db->where('products.is_featured', $filters['is_featured']);
        }
        if (isset($filters['category_id'])) {
            $this->db->where('products.category_id', $filters['category_id']);
        }
        if (isset($filters['brand_id'])) {
            $this->db->where('products.brand_id', $filters['brand_id']);
        }
        if (isset($filters['search'])) {
            $this->db->group_start();
            $this->db->like('products.product_name', $filters['search']);
            $this->db->or_like('products.description', $filters['search']);
            $this->db->or_like('products.sku', $filters['search']);
            $this->db->group_end();
        }

        // Apply sorting
        switch ($sort) {
            case 'popular':
                $this->db->order_by('products.views', 'DESC');
                break;
            case 'price_low':
                // Sort by final price (considering discount)
                $this->db->order_by('IF(products.discount_price > 0, products.discount_price, products.price)', 'ASC', FALSE);
                break;
            case 'price_high':
                // Sort by final price (considering discount)
                $this->db->order_by('IF(products.discount_price > 0, products.discount_price, products.price)', 'DESC', FALSE);
                break;
            case 'newest':
            default:
                $this->db->order_by('products.created_at', 'DESC');
                break;
        }

        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    public function count_all($filters = [])
    {
        $this->db->from($this->table);

        if (isset($filters['is_active'])) {
            $this->db->where('is_active', $filters['is_active']);
        }
        if (isset($filters['is_featured'])) {
            $this->db->where('is_featured', $filters['is_featured']);
        }
        if (isset($filters['category_id'])) {
            $this->db->where('category_id', $filters['category_id']);
        }
        if (isset($filters['brand_id'])) {
            $this->db->where('brand_id', $filters['brand_id']);
        }
        if (isset($filters['search'])) {
            $this->db->group_start();
            $this->db->like('product_name', $filters['search']);
            $this->db->or_like('description', $filters['search']);
            $this->db->or_like('sku', $filters['search']);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    public function get_by_id($id)
    {
        $this->db->select('products.*, categories.name as category_name, brands.brand_name');
        $this->db->from($this->table);
        $this->db->join('categories', 'categories.id = products.category_id', 'left');
        $this->db->join('brands', 'brands.brand_id = products.brand_id', 'left');
        $this->db->where('products.' . $this->primary_key, $id);
        return $this->db->get()->row();
    }

    public function get_by_slug($slug)
    {
        $this->db->select('products.*, categories.name as category_name, categories.slug as category_slug, brands.brand_name');
        $this->db->from($this->table);
        $this->db->join('categories', 'categories.id = products.category_id', 'left');
        $this->db->join('brands', 'brands.brand_id = products.brand_id', 'left');
        $this->db->where('products.product_slug', $slug);
        $this->db->where('products.is_active', 1);
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

    public function increment_views($id)
    {
        $this->db->set('views', 'views+1', FALSE);
        $this->db->where($this->primary_key, $id);
        return $this->db->update($this->table);
    }

    public function update_stock($id, $quantity, $operation = 'decrease')
    {
        if ($operation === 'decrease') {
            $this->db->set('stock', 'stock - ' . (int)$quantity, FALSE);
        } else {
            $this->db->set('stock', 'stock + ' . (int)$quantity, FALSE);
        }
        $this->db->where($this->primary_key, $id);
        return $this->db->update($this->table);
    }

    public function check_slug_exists($slug, $exclude_id = null)
    {
        $this->db->where('product_slug', $slug);
        if ($exclude_id) {
            $this->db->where($this->primary_key . ' !=', $exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }

    public function check_sku_exists($sku, $exclude_id = null)
    {
        $this->db->where('sku', $sku);
        if ($exclude_id) {
            $this->db->where($this->primary_key . ' !=', $exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }

    public function get_related_products($category_id, $product_id, $limit = 4)
    {
        $this->db->select('products.*');
        $this->db->from($this->table);
        $this->db->where('category_id', $category_id);
        $this->db->where($this->primary_key . ' !=', $product_id);
        $this->db->where('is_active', 1);
        $this->db->where('stock >', 0);
        $this->db->order_by('RAND()');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    public function get_top_products($limit = 5)
    {
        $this->db->select('products.*, brands.brand_name, categories.name as category_name');
        $this->db->from($this->table);
        $this->db->join('brands', 'brands.brand_id = products.brand_id', 'left');
        $this->db->join('categories', 'categories.id = products.category_id', 'left');
        $this->db->where('products.is_active', 1);
        $this->db->order_by('products.views', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }
}
