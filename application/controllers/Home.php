<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/Public_Controller.php';

class Home extends Public_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Banner_model');
        $this->load->model('Product_model');
    }

    public function index()
    {
        // Get active banners
        $data['banners'] = $this->Banner_model->get_all(1);

        // Get featured products
        $data['featured_products'] = $this->Product_model->get_all([
            'is_active' => 1,
            'is_featured' => 1
        ]);

        // Limit featured products
        $featured_limit = $this->data['site_settings']['featured_products_limit'] ?? 8;
        $data['featured_products'] = array_slice($data['featured_products'], 0, $featured_limit);

        // Get new products (latest)
        $data['new_products'] = $this->Product_model->get_paginated(8, 0, [
            'is_active' => 1
        ]);

        // Get products by category (for category showcase)
        $data['categories_with_products'] = [];
        $active_categories = array_slice($this->data['categories'], 0, 3);
        foreach ($active_categories as $category) {
            $products = $this->Product_model->get_paginated(4, 0, [
                'is_active' => 1,
                'category_id' => $category->id
            ]);
            if (!empty($products)) {
                $data['categories_with_products'][] = [
                    'category' => $category,
                    'products' => $products
                ];
            }
        }

        $this->set_meta(
            'Home',
            $this->data['site_settings']['meta_description'] ?? '',
            $this->data['site_settings']['meta_keywords'] ?? '',
            base_url('uploads/logo_1762168678_69088f66e69d3.jpg')
        );

        $this->render('public/home/index', $data);
    }
}
