<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Category Model
 *
 * Handles all database operations for product categories including
 * CRUD operations, search, validation, and soft delete filtering.
 *
 * @package Putra Elektronik
 * @category Models
 */
class Category_model extends CI_Model
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Private method to filter soft-deleted records
     * Always excludes categories where deleted_at IS NOT NULL
     *
     * @return CI_DB_query_builder
     */
    private function _filter_deleted()
    {
        $this->db->where('deleted_at IS NULL');
        return $this;
    }

    /**
     * Get all active categories (no pagination)
     * Used for dropdowns and select lists
     *
     * @return array Array of category records
     */
    public function getAllCategory()
    {
        $this->_filter_deleted();
        $this->db->where('status', 1); // Only active categories
        $this->db->order_by('name', 'ASC');

        $query = $this->db->get('categories');
        return $query->result_array();
    }

    /**
     * Get all categories including inactive (no pagination)
     * Used for admin listing
     *
     * @return array Array of category objects
     */
    public function get_all_active()
    {
        $this->_filter_deleted();
        $this->db->order_by('name', 'ASC');

        $query = $this->db->get('categories');
        return $query->result();
    }

    /**
     * Get categories with optional search and pagination
     *
     * @param string $search Search term for category name (optional)
     * @param int $limit Number of records to return
     * @param int $offset Starting position for pagination
     * @return array Array of category records
     */
    public function get_categories($search = '', $limit = 10, $offset = 0)
    {
        $this->_filter_deleted();

        if (!empty($search)) {
            $this->db->like('name', $search);
        }

        $this->db->order_by('name', 'ASC');
        $this->db->limit($limit, $offset);

        $query = $this->db->get('categories');
        return $query->result_array();
    }

    /**
     * Count total categories (with optional search filter)
     *
     * @param string $search Search term for category name (optional)
     * @return int Total count of categories
     */
    public function count_categories($search = '')
    {
        $this->_filter_deleted();

        if (!empty($search)) {
            $this->db->like('name', $search);
        }

        return $this->db->count_all_results('categories');
    }

    /**
     * Get single category by ID
     *
     * @param int $category_id Category ID
     * @return object|null Category record or null if not found
     */
    public function get_by_id($category_id)
    {
        $this->_filter_deleted();
        $this->db->where('id', $category_id);

        $query = $this->db->get('categories');
        return $query->row();
    }

    /**
     * Get product count for a category
     *
     * Note: Returns 0 if products table doesn't exist yet
     *
     * @param int $category_id Category ID
     * @return int Number of products in this category
     */
    public function get_product_count($category_id)
    {
        // Check if products table exists
        if (!$this->db->table_exists('products')) {
            return 0;
        }

        $this->db->where('category_id', $category_id);
        return $this->db->count_all_results('products');
    }

    /**
     * Check if category name is unique
     * Excludes soft-deleted categories and optionally current category being edited
     *
     * @param string $name Category name to check
     * @param int|null $exclude_id Category ID to exclude from check (for edit)
     * @return bool TRUE if name is unique, FALSE if duplicate exists
     */
    public function check_unique_name($name, $exclude_id = null)
    {
        $this->_filter_deleted();
        $this->db->where('LOWER(name)', strtolower($name));

        if ($exclude_id !== null) {
            $this->db->where('id !=', $exclude_id);
        }

        $count = $this->db->count_all_results('categories');
        return $count === 0;
    }

    /**
     * Check if category slug is unique
     * Excludes soft-deleted categories and optionally current category being edited
     *
     * @param string $slug Category slug to check
     * @param int|null $exclude_id Category ID to exclude from check (for edit)
     * @return bool TRUE if slug is unique, FALSE if duplicate exists
     */
    public function check_unique_slug($slug, $exclude_id = null)
    {
        $this->_filter_deleted();
        $this->db->where('LOWER(slug)', strtolower($slug));

        if ($exclude_id !== null) {
            $this->db->where('id !=', $exclude_id);
        }

        $count = $this->db->count_all_results('categories');
        return $count === 0;
    }

    /**
     * Insert new category
     *
     * @param array $data Category data
     * @return int|bool Insert ID on success, FALSE on failure
     */
    public function insert($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        if ($this->db->insert('categories', $data)) {
            return $this->db->insert_id();
        }

        return FALSE;
    }

    /**
     * Update existing category
     *
     * @param int $category_id Category ID
     * @param array $data Category data to update
     * @return bool TRUE on success, FALSE on failure
     */
    public function update($category_id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->_filter_deleted();
        $this->db->where('id', $category_id);

        return $this->db->update('categories', $data);
    }

    /**
     * Update category status only
     *
     * @param int $category_id Category ID
     * @param int $status Status value (0 or 1)
     * @return bool TRUE on success, FALSE on failure
     */
    public function update_status($category_id, $status)
    {
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->_filter_deleted();
        $this->db->where('id', $category_id);

        return $this->db->update('categories', $data);
    }

    /**
     * Soft delete category
     * Sets deleted_at timestamp instead of actually deleting the record
     *
     * @param int $category_id Category ID
     * @return bool TRUE on success, FALSE on failure
     */
    public function soft_delete($category_id)
    {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('id', $category_id);
        return $this->db->update('categories', $data);
    }
}
