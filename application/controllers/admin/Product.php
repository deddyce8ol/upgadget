<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Product extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        _checkIsLogin();
        $this->load->model('Product_model');
        $this->load->model('Category_model');
        $this->load->model('Brand_model');
        $this->load->model('Product_image_model');
        $this->load->model('LogAction_model', 'logaction');
    }

    public function index()
    {
        $data['title'] = 'Product Management';
        $data['user'] = $this->db->get_where('user_data', ['email' => $this->session->userdata('email')])->row_array();

        $this->load->view('layout/layout_header', $data);
        $this->load->view('layout/layout_sidebar');
        $this->load->view('layout/layout_topbar');
        $this->load->view('admin/product/index');
        $this->load->view('layout/layout_footer');
    }

    public function get_data()
    {
        // Get filter parameters
        $filters = [];

        if ($this->input->get('filter_name')) {
            $filters['search'] = $this->input->get('filter_name');
        }

        if ($this->input->get('filter_category')) {
            $filters['category_id'] = $this->input->get('filter_category');
        }

        if ($this->input->get('filter_brand')) {
            $filters['brand_id'] = $this->input->get('filter_brand');
        }

        if ($this->input->get('filter_status') !== null && $this->input->get('filter_status') !== '') {
            $filters['is_active'] = $this->input->get('filter_status');
        }

        $products = $this->Product_model->get_all($filters);
        $data = [];

        foreach ($products as $product) {
            $row = [];
            $row[] = $product->product_id;
            $row[] = $product->main_image
                ? '<img src="' . base_url('uploads/products/' . $product->main_image) . '" style="max-width: 50px; max-height: 50px; object-fit: cover;">'
                : '<img src="' . base_url('uploads/no-image.jpg') . '" style="max-width: 50px; max-height: 50px;">';
            $row[] = $product->product_name . '<br><small class="text-muted">' . $product->sku . '</small>';
            $row[] = $product->category_name ?? '-';
            $row[] = $product->brand_name ?? '-';
            $row[] = format_rupiah($product->price);
            $row[] = $product->stock;
            $row[] = $product->is_active == 1
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-danger">Inactive</span>';

            $buttons = '
                <a href="' . base_url('admin/product/edit/' . $product->product_id) . '" class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $product->product_id . '">
                    <i class="bi bi-trash"></i>
                </button>
                <button type="button" class="btn btn-sm btn-info toggle-status-btn" data-id="' . $product->product_id . '">
                    <i class="bi bi-arrow-repeat"></i>
                </button>
            ';
            $row[] = $buttons;

            $data[] = $row;
        }

        echo json_encode(['data' => $data]);
    }

    public function create()
    {
        $data['title'] = 'Add Product';
        $data['user'] = $this->db->get_where('user_data', ['email' => $this->session->userdata('email')])->row_array();
        $data['categories'] = $this->Category_model->getAllCategory();
        $data['brands'] = $this->Brand_model->get_all(['is_active' => 1]);

        $this->load->view('layout/layout_header', $data);
        $this->load->view('layout/layout_sidebar');
        $this->load->view('layout/layout_topbar');
        $this->load->view('admin/product/form', $data);
        $this->load->view('layout/layout_footer');
    }

    public function store()
    {
        $this->form_validation->set_rules('product_name', 'Product Name', 'required|trim');
        $this->form_validation->set_rules('sku', 'SKU', 'required|trim');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric');
        $this->form_validation->set_rules('stock', 'Stock', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">' . validation_errors() . '</div>');
            redirect('admin/product/create');
            return;
        }

        // Generate unique slug
        $slug = url_title($this->input->post('product_name'), 'dash', TRUE);

        // Check if slug exists and make it unique
        if ($this->Product_model->check_slug_exists($slug)) {
            $slug = $slug . '-' . time();
        }

        // Check if SKU already exists
        $sku = $this->input->post('sku');
        if ($this->Product_model->check_sku_exists($sku)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">SKU "' . $sku . '" already exists. Please use a different SKU.</div>');
            redirect('admin/product/create');
            return;
        }

        $product_data = [
            'product_name' => $this->input->post('product_name'),
            'product_slug' => $slug,
            'sku' => $sku,
            'category_id' => $this->input->post('category_id') ?: NULL,
            'brand_id' => $this->input->post('brand_id') ?: NULL,
            'description' => $this->input->post('description'),
            'specifications' => $this->input->post('specifications'),
            'price' => $this->input->post('price'),
            'discount_price' => $this->input->post('discount_price') ?: NULL,
            'stock' => $this->input->post('stock'),
            'weight' => $this->input->post('weight') ?: 0,
            'is_featured' => $this->input->post('is_featured') == '1' ? 1 : 0,
            'is_active' => 1
        ];

        // Handle main image upload
        if (!empty($_FILES['product_image']['name'])) {
            $config['upload_path'] = './uploads/products/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif|webp'; // Support WebP
            $config['max_size'] = 5120; // 5MB
            $config['file_name'] = 'product_' . time() . '_' . uniqid();

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('product_image')) {
                $upload_data = $this->upload->data();
                $product_data['main_image'] = $upload_data['file_name'];
            }
        }

        $product_id = $this->Product_model->insert($product_data);

        if ($product_id) {
            // Handle multiple images
            $this->_handle_multiple_images($product_id);

            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Created product "' . $product_data['product_name'] . '"'
            ];
            $this->logaction->insertLog($userLogAction);

            $this->session->set_flashdata('message', '<div class="alert alert-success">Product created successfully!</div>');
            redirect('admin/product');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Failed to create product</div>');
            redirect('admin/product/create');
        }
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Product';
        $data['user'] = $this->db->get_where('user_data', ['email' => $this->session->userdata('email')])->row_array();
        $data['product'] = $this->Product_model->get_by_id($id);
        $data['categories'] = $this->Category_model->getAllCategory();
        $data['brands'] = $this->Brand_model->get_all(['is_active' => 1]);
        $data['product_images'] = $this->Product_image_model->get_by_product_id($id);

        if (!$data['product']) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Product not found</div>');
            redirect('admin/product');
        }

        $this->load->view('layout/layout_header', $data);
        $this->load->view('layout/layout_sidebar');
        $this->load->view('layout/layout_topbar');
        $this->load->view('admin/product/form', $data);
        $this->load->view('layout/layout_footer');
    }

    public function update($id)
    {
        // Debug: Log $_FILES data
        log_message('debug', 'Product Update - $_FILES data: ' . print_r($_FILES, true));

        $this->form_validation->set_rules('product_name', 'Product Name', 'required|trim');
        $this->form_validation->set_rules('sku', 'SKU', 'required|trim');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric');
        $this->form_validation->set_rules('stock', 'Stock', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">' . validation_errors() . '</div>');
            redirect('admin/product/edit/' . $id);
            return;
        }

        // Generate unique slug
        $slug = url_title($this->input->post('product_name'), 'dash', TRUE);

        // Check if slug exists for other products (exclude current product)
        if ($this->Product_model->check_slug_exists($slug, $id)) {
            // Add unique identifier to make it unique
            $slug = $slug . '-' . $id;
        }

        // Check if SKU already exists in other products (exclude current product)
        $sku = $this->input->post('sku');
        if ($this->Product_model->check_sku_exists($sku, $id)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">SKU "' . $sku . '" already exists in another product. Please use a different SKU.</div>');
            redirect('admin/product/edit/' . $id);
            return;
        }

        $product_data = [
            'product_name' => $this->input->post('product_name'),
            'product_slug' => $slug,
            'sku' => $sku,
            'category_id' => $this->input->post('category_id') ?: NULL,
            'brand_id' => $this->input->post('brand_id') ?: NULL,
            'description' => $this->input->post('description'),
            'specifications' => $this->input->post('specifications'),
            'price' => $this->input->post('price'),
            'discount_price' => $this->input->post('discount_price') ?: NULL,
            'stock' => $this->input->post('stock'),
            'weight' => $this->input->post('weight') ?: 0,
            'is_featured' => $this->input->post('is_featured') == '1' ? 1 : 0
        ];

        // Handle main image upload
        if (!empty($_FILES['product_image']['name'])) {
            // Delete old image
            $old_product = $this->Product_model->get_by_id($id);
            if ($old_product && $old_product->main_image) {
                $old_file = './uploads/products/' . $old_product->main_image;
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }

            $config['upload_path'] = './uploads/products/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif|webp'; // Support WebP
            $config['max_size'] = 5120; // 5MB
            $config['file_name'] = 'product_' . time() . '_' . uniqid();

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('product_image')) {
                $upload_data = $this->upload->data();
                $product_data['main_image'] = $upload_data['file_name'];
            } else {
                // Log upload error for debugging
                log_message('error', 'Product image upload failed: ' . $this->upload->display_errors());
                $this->session->set_flashdata('message', '<div class="alert alert-warning">Product updated, but image upload failed: ' . $this->upload->display_errors() . '</div>');
            }
        }

        $update = $this->Product_model->update($id, $product_data);

        if ($update !== false) {
            // Handle multiple images
            $this->_handle_multiple_images($id);

            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Updated product "' . $product_data['product_name'] . '"'
            ];
            $this->logaction->insertLog($userLogAction);

            $this->session->set_flashdata('message', '<div class="alert alert-success">Product updated successfully!</div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Failed to update product</div>');
        }

        redirect('admin/product');
    }

    public function delete($id)
    {
        $product = $this->Product_model->get_by_id($id);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            return;
        }

        // Delete main image
        if ($product->main_image) {
            $file_path = './uploads/products/' . $product->main_image;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        // Delete additional images
        $product_images = $this->Product_image_model->get_by_product_id($id);
        foreach ($product_images as $img) {
            $img_path = './uploads/products/' . $img->image_path;
            if (file_exists($img_path)) {
                unlink($img_path);
            }
            $this->Product_image_model->delete($img->image_id);
        }

        $delete = $this->Product_model->delete($id);

        if ($delete) {
            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Deleted product "' . $product->product_name . '"'
            ];
            $this->logaction->insertLog($userLogAction);

            echo json_encode(['success' => true, 'message' => 'Product deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete product']);
        }
    }

    public function toggle_status($id)
    {
        $product = $this->Product_model->get_by_id($id);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            return;
        }

        $new_status = $product->is_active == 1 ? 0 : 1;
        $update = $this->Product_model->update($id, ['is_active' => $new_status]);

        if ($update !== false) {
            $status_text = $new_status == 1 ? 'activated' : 'deactivated';

            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Product "' . $product->product_name . '" ' . $status_text
            ];
            $this->logaction->insertLog($userLogAction);

            echo json_encode(['success' => true, 'message' => 'Product status updated!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
    }

    public function delete_image($image_id)
    {
        $image = $this->Product_image_model->get_by_id($image_id);

        if (!$image) {
            echo json_encode(['success' => false, 'message' => 'Image not found']);
            return;
        }

        $file_path = './uploads/products/' . $image->image_path;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        $delete = $this->Product_image_model->delete($image_id);

        if ($delete) {
            echo json_encode(['success' => true, 'message' => 'Image deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete image']);
        }
    }

    public function get_categories()
    {
        $categories = $this->Category_model->getAllCategory();
        echo json_encode([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function get_brands()
    {
        $brands = $this->Brand_model->get_all();
        echo json_encode([
            'success' => true,
            'data' => $brands
        ]);
    }

    private function _handle_multiple_images($product_id)
    {
        if (!empty($_FILES['additional_images']['name'][0])) {
            $files = $_FILES['additional_images'];
            $count = count($files['name']);

            for ($i = 0; $i < $count; $i++) {
                // Skip if no file uploaded for this index
                if (empty($files['name'][$i])) {
                    continue;
                }

                $_FILES['image']['name'] = $files['name'][$i];
                $_FILES['image']['type'] = $files['type'][$i];
                $_FILES['image']['tmp_name'] = $files['tmp_name'][$i];
                $_FILES['image']['error'] = $files['error'][$i];
                $_FILES['image']['size'] = $files['size'][$i];

                $config['upload_path'] = './uploads/products/';
                $config['allowed_types'] = 'jpg|jpeg|png|gif|webp'; // Support WebP
                $config['max_size'] = 5120; // 5MB
                $config['file_name'] = 'product_' . $product_id . '_' . time() . '_' . $i;

                // Load upload library if not loaded yet
                if (!isset($this->upload)) {
                    $this->load->library('upload', $config);
                } else {
                    $this->upload->initialize($config);
                }

                if ($this->upload->do_upload('image')) {
                    $upload_data = $this->upload->data();
                    $image_data = [
                        'product_id' => $product_id,
                        'image_path' => $upload_data['file_name']
                    ];
                    $this->Product_image_model->insert($image_data);
                } else {
                    // Log error for debugging
                    log_message('error', 'Additional image upload failed (index ' . $i . '): ' . $this->upload->display_errors());
                }
            }
        }
    }
}
