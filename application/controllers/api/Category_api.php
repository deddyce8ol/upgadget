<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Category API Controller
 *
 * Handles AJAX operations for category management (status toggle, etc.)
 * All responses are in JSON format.
 *
 * @package Putra Elektronik
 * @category Controllers
 */
class Category_api extends CI_Controller
{
    /**
     * Constructor
     * Validates AJAX request, authentication, and Admin role
     */
    public function __construct()
    {
        parent::__construct();

        // Check if request is AJAX
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        // Load model
        $this->load->model('Category_model');

        // Check authentication
        if (!$this->session->userdata('user_id')) {
            $this->_json_response(FALSE, 'Anda harus login', 'ERR_NOT_AUTHENTICATED', 401);
            exit;
        }

        // Check administrator permission
        if ($this->session->userdata('role') !== 'Administrator') {
            $this->_json_response(FALSE, 'Anda tidak memiliki akses untuk halaman ini', 'ERR_PERMISSION_DENIED', 403);
            exit;
        }
    }

    /**
     * Toggle category status (Aktif <-> Tidak Aktif)
     *
     * @return void JSON response
     */
    public function toggle_status()
    {
        // Validate input
        $category_id = $this->input->post('category_id', TRUE);
        $status = $this->input->post('status', TRUE);

        if (!$category_id || !is_numeric($category_id)) {
            $this->_json_response(FALSE, 'ID kategori tidak valid', 'ERR_VALIDATION_FAILED', 400);
            return;
        }

        if (!in_array($status, ['0', '1'], TRUE)) {
            $this->_json_response(FALSE, 'Status harus 0 atau 1', 'ERR_VALIDATION_FAILED', 400);
            return;
        }

        // Check if category exists and not deleted
        $category = $this->Category_model->get_by_id($category_id);

        if (!$category) {
            $this->_json_response(FALSE, 'Kategori tidak ditemukan', 'ERR_CATEGORY_NOT_FOUND', 404);
            return;
        }

        // Update status
        $result = $this->Category_model->update_status($category_id, $status);

        if ($result) {
            $status_text = ($status == 1) ? 'Aktif' : 'Tidak Aktif';

            $this->_json_response(TRUE, 'Status berhasil diubah', null, 200, [
                'category_id' => (int)$category_id,
                'new_status' => (int)$status,
                'status_text' => $status_text
            ]);
        } else {
            $this->_json_response(FALSE, 'Terjadi kesalahan saat mengubah status. Silakan coba lagi.', 'ERR_SERVER_ERROR', 500);
        }
    }

    /**
     * Send JSON response
     *
     * @param bool $success Success status
     * @param string $message Message to display
     * @param string|null $error_code Error code (optional)
     * @param int $http_code HTTP status code
     * @param array|null $data Additional data (optional)
     * @return void
     */
    private function _json_response($success, $message, $error_code = null, $http_code = 200, $data = null)
    {
        $response = [
            'success' => $success,
            'message' => $message
        ];

        if ($error_code) {
            $response['error_code'] = $error_code;
        }

        if ($data) {
            $response['data'] = $data;
        }

        $this->output
            ->set_status_header($http_code)
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
