<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Category Controller
 *
 * Handles all category management operations including view, create, edit, and delete.
 * Requires authentication and Admin role for modify operations.
 *
 * @package Putra Elektronik
 * @category Controllers
 */
class Category extends CI_Controller
{
    private $user_role;

    /**
     * Constructor
     * Checks authentication and loads required libraries
     */
    public function __construct()
    {
        parent::__construct();

        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }

        // Load required libraries and helpers
        $this->load->model('Category_model');
        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->load->helper(['slug', 'url', 'form']);

        // Get user role
        $this->user_role = $this->session->userdata('role');

        // Check minimum permission (Administrator or Staff can view)
        if (!in_array($this->user_role, ['Administrator', 'Staff'])) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses untuk halaman ini');
            redirect('dashboard');
        }
    }

    /**
     * Index page - List all categories with search and pagination
     *
     * @return void
     */
    public function index()
    {
        // Get search query
        $search = $this->input->get('search', TRUE);

        // Pagination configuration
        $this->load->library('pagination');

        $config['base_url'] = base_url('admin/category');
        $config['total_rows'] = $this->Category_model->count_categories($search);
        $config['per_page'] = 10;
        $config['uri_segment'] = 3;
        $config['use_page_numbers'] = FALSE;
        $config['reuse_query_string'] = TRUE;

        // Bootstrap 5 pagination styling
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = 'Next';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = 'Previous';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = ['class' => 'page-link'];

        $this->pagination->initialize($config);

        // Get categories
        $page = $this->input->get('per_page') ? $this->input->get('per_page') : 0;
        $categories = $this->Category_model->get_categories($search, $config['per_page'], $page);

        // Get product count for each category
        foreach ($categories as &$category) {
            $category['product_count'] = $this->Category_model->get_product_count($category['id']);
        }

        // Prepare view data
        $data = [
            'title' => 'Manajemen Kategori Produk',
            'categories' => $categories,
            'pagination' => $this->pagination->create_links(),
            'search' => $search,
            'user_role' => $this->user_role,
            'total_rows' => $config['total_rows']
        ];

        $this->load->view('category/index', $data);
    }

    /**
     * Create new category (GET: show form, POST: process form)
     *
     * @return void
     */
    public function create()
    {
        // Check Administrator permission
        if ($this->user_role !== 'Administrator') {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses untuk halaman ini');
            redirect('admin/category');
        }

        // Process form submission
        if ($this->input->post()) {
            $this->_process_form();
        } else {
            // Show create form
            $data = [
                'title' => 'Tambah Kategori Baru',
                'category' => null,
                'action' => 'create'
            ];

            $this->load->view('category/create', $data);
        }
    }

    /**
     * Edit existing category (GET: show form, POST: process form)
     *
     * @param int $id Category ID
     * @return void
     */
    public function edit($id)
    {
        // Check Administrator permission
        if ($this->user_role !== 'Administrator') {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses untuk halaman ini');
            redirect('admin/category');
        }

        // Get category
        $category = $this->Category_model->get_by_id($id);

        if (!$category) {
            show_404();
        }

        // Process form submission
        if ($this->input->post()) {
            $this->_process_form($id);
        } else {
            // Show edit form
            $data = [
                'title' => 'Edit Kategori',
                'category' => $category,
                'action' => 'edit'
            ];

            $this->load->view('category/edit', $data);
        }
    }

    /**
     * Delete category (soft delete)
     *
     * @return void
     */
    public function delete()
    {
        // Check Administrator permission
        if ($this->user_role !== 'Administrator') {
            $this->_json_response(FALSE, 'Anda tidak memiliki akses untuk halaman ini', 403);
            return;
        }

        $category_id = $this->input->post('category_id', TRUE);

        if (!$category_id) {
            $this->_json_response(FALSE, 'ID kategori tidak valid', 400);
            return;
        }

        // Check if category exists
        $category = $this->Category_model->get_by_id($category_id);

        if (!$category) {
            $this->_json_response(FALSE, 'Kategori tidak ditemukan', 404);
            return;
        }

        // Soft delete
        if ($this->Category_model->soft_delete($category_id)) {
            $this->_json_response(TRUE, 'Kategori berhasil dihapus');
        } else {
            $this->_json_response(FALSE, 'Gagal menghapus kategori', 500);
        }
    }

    /**
     * Process create/edit form submission
     *
     * @param int|null $id Category ID (null for create, int for edit)
     * @return void
     */
    private function _process_form($id = null)
    {
        // Set validation rules
        $this->form_validation->set_rules('name', 'Nama Kategori', 'required|max_length[100]');
        $this->form_validation->set_rules('slug', 'Slug', 'required|max_length[150]|alpha_dash');
        $this->form_validation->set_rules('description', 'Deskripsi', 'max_length[500]');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[0,1]');

        // Custom error messages in Indonesian
        $this->form_validation->set_message('required', '{field} harus diisi');
        $this->form_validation->set_message('max_length', '{field} maksimal {param} karakter');
        $this->form_validation->set_message('alpha_dash', '{field} hanya boleh berisi huruf, angka, dan tanda minus');
        $this->form_validation->set_message('in_list', '{field} tidak valid');

        if ($this->form_validation->run() === FALSE) {
            // Validation failed - reload form with errors
            $data = [
                'title' => $id ? 'Edit Kategori' : 'Tambah Kategori Baru',
                'category' => $id ? $this->Category_model->get_by_id($id) : null,
                'action' => $id ? 'edit' : 'create'
            ];

            $view = $id ? 'category/edit' : 'category/create';
            $this->load->view($view, $data);
            return;
        }

        // Check name uniqueness
        $name = $this->input->post('name', TRUE);
        if (!$this->Category_model->check_unique_name($name, $id)) {
            $this->session->set_flashdata('error', 'Nama kategori sudah digunakan. Gunakan nama lain.');
            redirect($id ? "admin/category/edit/$id" : 'admin/category/create');
            return;
        }

        // Check slug uniqueness
        $slug = $this->input->post('slug', TRUE);
        if (!$this->Category_model->check_unique_slug($slug, $id)) {
            $this->session->set_flashdata('error', 'Slug sudah digunakan. Gunakan slug lain.');
            redirect($id ? "admin/category/edit/$id" : 'admin/category/create');
            return;
        }

        // Prepare data
        $data = [
            'name' => $this->security->xss_clean($name),
            'slug' => $this->security->xss_clean($slug),
            'description' => $this->security->xss_clean($this->input->post('description', TRUE)),
            'status' => $this->input->post('status', TRUE)
        ];

        // Handle file upload
        $upload_result = $this->_handle_file_upload($id);

        if ($upload_result['status'] === TRUE) {
            if ($upload_result['file_path']) {
                $data['icon_path'] = $upload_result['file_path'];
            }
        } else {
            $this->session->set_flashdata('error', $upload_result['message']);
            redirect($id ? "admin/category/edit/$id" : 'admin/category/create');
            return;
        }

        // Insert or update
        if ($id) {
            $result = $this->Category_model->update($id, $data);
            $message = 'Kategori berhasil diperbarui';
        } else {
            $result = $this->Category_model->insert($data);
            $message = 'Kategori berhasil ditambahkan';
        }

        if ($result) {
            $this->session->set_flashdata('success', $message);
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan kategori');
        }

        redirect('admin/category');
    }

    /**
     * Handle file upload for category icon
     *
     * @param int|null $id Category ID (for edit, to delete old file)
     * @return array Result array with status, message, and file_path
     */
    private function _handle_file_upload($id = null)
    {
        if (empty($_FILES['icon']['name'])) {
            return ['status' => TRUE, 'file_path' => null, 'message' => ''];
        }

        // Upload configuration
        $config['upload_path'] = './assets/uploads/categories/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size'] = 2048; // 2MB
        $config['encrypt_name'] = FALSE;
        $config['file_name'] = time() . '_' . bin2hex(random_bytes(8)) . '_' . $_FILES['icon']['name'];

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('icon')) {
            return ['status' => FALSE, 'file_path' => null, 'message' => $this->upload->display_errors('', '')];
        }

        $upload_data = $this->upload->data();

        // Validate MIME type (security)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $upload_data['full_path']);
        finfo_close($finfo);

        $allowed_mimes = ['image/jpeg', 'image/png'];
        if (!in_array($mime, $allowed_mimes)) {
            unlink($upload_data['full_path']);
            return ['status' => FALSE, 'file_path' => null, 'message' => 'Format file tidak valid'];
        }

        // Delete old file if editing
        if ($id) {
            $old_category = $this->Category_model->get_by_id($id);
            if ($old_category && $old_category['icon_path']) {
                $old_file = './' . $old_category['icon_path'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
        }

        return [
            'status' => TRUE,
            'file_path' => 'assets/uploads/categories/' . $upload_data['file_name'],
            'message' => ''
        ];
    }

    /**
     * Send JSON response
     *
     * @param bool $success Success status
     * @param string $message Message
     * @param int $http_code HTTP status code
     * @return void
     */
    private function _json_response($success, $message, $http_code = 200)
    {
        $this->output
            ->set_status_header($http_code)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => $success,
                'message' => $message
            ]));
    }
}
