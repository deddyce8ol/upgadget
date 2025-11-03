<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/Public_Controller.php';

class Product extends Public_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Product_model');
        $this->load->model('Brand_model');
        $this->load->model('Product_image_model');
        $this->load->library('pagination');
    }

    /**
     * Product listing page - All products
     */
    public function index()
    {
        // Pagination config
        $per_page = $this->data['site_settings']['products_per_page'] ?? 12;
        $page = $this->input->get('page') ?? 0;

        // Get filters from query string
        $filters = [
            'is_active' => 1,
            'search' => $this->input->get('q'),
            'category_id' => $this->input->get('cat'),
            'brand_id' => $this->input->get('brand')
        ];

        // Get sort option
        $sort = $this->input->get('sort') ?? 'newest';

        // Count total products
        $total_products = $this->Product_model->count_all($filters);

        // Pagination config
        $config['base_url'] = base_url('product');
        $config['total_rows'] = $total_products;
        $config['per_page'] = $per_page;
        $config['use_page_numbers'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['reuse_query_string'] = TRUE;

        // Pagination styling
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo;';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo;';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        // Get products
        $offset = ($page - 1) * $per_page;
        $offset = $offset < 0 ? 0 : $offset;
        $data['products'] = $this->Product_model->get_paginated($per_page, $offset, $filters, $sort);

        // Get brands for filter
        $data['brands'] = $this->Brand_model->get_all(1);

        // Pagination links
        $data['pagination'] = $this->pagination->create_links();
        $data['total_products'] = $total_products;
        $data['current_page'] = $page;
        $data['per_page'] = $per_page;
        $data['filters'] = $filters;
        $data['sort'] = $sort;

        $this->set_meta(
            'Produk',
            'Jelajahi koleksi produk elektronik terlengkap dengan harga terbaik',
            'produk elektronik, tv, kulkas, ac, mesin cuci'
        );

        $this->render('public/product/index', $data);
    }

    /**
     * Product detail page
     */
    public function detail($slug = '')
    {
        if (empty($slug)) {
            show_404();
        }

        // Get product by slug
        $data['product'] = $this->Product_model->get_by_slug($slug);

        if (!$data['product']) {
            show_404();
        }

        // Increment product views
        $this->Product_model->increment_views($data['product']->product_id);

        // Get product images
        $data['product_images'] = $this->Product_image_model->get_by_product($data['product']->product_id);

        // Get related products (same category)
        $data['related_products'] = $this->Product_model->get_related_products(
            $data['product']->category_id,
            $data['product']->product_id,
            4
        );

        // Determine OG image - use primary image if available, otherwise use no-image placeholder
        $og_image = base_url('assets/img/no-image.jpg');
        if (!empty($data['product_images'])) {
            // Find primary image or use first image
            $primary_image = null;
            foreach ($data['product_images'] as $img) {
                if ($img->is_primary == 1) {
                    $primary_image = $img;
                    break;
                }
            }
            // If no primary image, use first image
            if (!$primary_image && isset($data['product_images'][0])) {
                $primary_image = $data['product_images'][0];
            }
            // Set OG image URL
            if ($primary_image && !empty($primary_image->image_path)) {
                $og_image = base_url('uploads/products/' . $primary_image->image_path);
            }
        }

        $this->set_meta(
            $data['product']->product_name,
            strip_tags(substr($data['product']->description, 0, 160)),
            $data['product']->product_name . ', ' . $data['product']->category_name,
            $og_image
        );

        $this->render('public/product/detail', $data);
    }

    /**
     * Products by category
     */
    public function category($slug = '')
    {
        if (empty($slug)) {
            redirect('product');
        }

        // Get category
        $this->load->model('Category_model');
        $categories = $this->Category_model->get_categories('', 1000, 0);
        $category = null;
        foreach ($categories as $cat) {
            if ($cat['slug'] == $slug) {
                $category = (object)$cat;
                break;
            }
        }

        if (!$category) {
            show_404();
        }

        // Pagination config
        $per_page = $this->data['site_settings']['products_per_page'] ?? 12;
        $page = $this->input->get('page') ?? 0;

        // Get sort option
        $sort = $this->input->get('sort') ?? 'newest';

        // Get filters
        $filters = [
            'is_active' => 1,
            'category_id' => $category->id,
            'brand_id' => $this->input->get('brand')
        ];

        // Count total products
        $total_products = $this->Product_model->count_all($filters);

        // Pagination config
        $config['base_url'] = base_url('product/category/' . $slug);
        $config['total_rows'] = $total_products;
        $config['per_page'] = $per_page;
        $config['use_page_numbers'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['reuse_query_string'] = TRUE;

        // Pagination styling
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo;';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo;';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        // Get products
        $offset = ($page - 1) * $per_page;
        $offset = $offset < 0 ? 0 : $offset;
        $data['products'] = $this->Product_model->get_paginated($per_page, $offset, $filters, $sort);

        // Get brands for filter
        $data['brands'] = $this->Brand_model->get_all(1);

        // Category data
        $data['category'] = $category;
        $data['pagination'] = $this->pagination->create_links();
        $data['total_products'] = $total_products;
        $data['current_page'] = $page;
        $data['per_page'] = $per_page;
        $data['filters'] = $filters;
        $data['sort'] = $sort;

        // Determine OG image - use category banner if available, otherwise use no-image placeholder
        $og_image = base_url('assets/img/no-image.jpg');
        if (!empty($category->banner_image)) {
            $og_image = base_url('uploads/categories/' . $category->banner_image);
        }

        $this->set_meta(
            $category->name,
            $category->description ?? 'Produk ' . $category->name . ' terlengkap dengan harga terbaik',
            $category->name . ', produk ' . $category->name,
            $og_image
        );

        $this->render('public/product/category', $data);
    }

    /**
     * Search products
     */
    public function search()
    {
        $keyword = $this->input->get('q');

        if (empty($keyword)) {
            redirect('product');
        }

        // Pagination config
        $per_page = $this->data['site_settings']['products_per_page'] ?? 12;
        $page = $this->input->get('page') ?? 0;

        // Get sort option
        $sort = $this->input->get('sort') ?? 'newest';

        // Get filters
        $filters = [
            'is_active' => 1,
            'search' => $keyword,
            'category_id' => $this->input->get('cat'),
            'brand_id' => $this->input->get('brand')
        ];

        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return !empty($value) || $value === 0;
        });

        // Count total products
        $total_products = $this->Product_model->count_all($filters);

        // Pagination config
        $config['base_url'] = base_url('product/search');
        $config['total_rows'] = $total_products;
        $config['per_page'] = $per_page;
        $config['use_page_numbers'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['reuse_query_string'] = TRUE;

        // Pagination styling (same as above)
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        // Get products
        $offset = ($page - 1) * $per_page;
        $offset = $offset < 0 ? 0 : $offset;
        $data['products'] = $this->Product_model->get_paginated($per_page, $offset, $filters, $sort);

        // Get brands for filter
        $data['brands'] = $this->Brand_model->get_all(1);

        $data['keyword'] = $keyword;
        $data['pagination'] = $this->pagination->create_links();
        $data['total_products'] = $total_products;
        $data['current_page'] = $page;
        $data['per_page'] = $per_page;
        $data['filters'] = $filters;
        $data['sort'] = $sort;

        $this->set_meta(
            'Pencarian: ' . $keyword,
            'Hasil pencarian produk untuk: ' . $keyword,
            $keyword
        );

        $this->render('public/product/search', $data);
    }
}
