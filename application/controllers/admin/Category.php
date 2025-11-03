<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Category extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        _checkIsLogin();
        $this->load->model('Category_model');
        $this->load->model('LogAction_model', 'logaction');
    }

    public function index()
    {
        $data['title'] = 'Category Management';
        $data['user'] = $this->db->get_where('user_data', ['email' => $this->session->userdata('email')])->row_array();

        $this->load->view('layout/layout_header', $data);
        $this->load->view('layout/layout_sidebar');
        $this->load->view('layout/layout_topbar');
        $this->load->view('admin/category/index');
        $this->load->view('layout/layout_footer');
    }

    public function get_data()
    {
        $categories = $this->Category_model->get_all_active();
        $data = [];

        foreach ($categories as $category) {
            $row = [];
            $row[] = $category->id;
            $row[] = $category->icon_path ? '<img src="' . base_url('uploads/categories/' . $category->icon_path) . '" style="max-width: 50px; max-height: 50px;">' : '-';
            $row[] = $category->name;
            $row[] = $category->slug;
            $row[] = $category->description ? substr($category->description, 0, 50) . '...' : '-';
            $row[] = $category->status == 1
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-danger">Inactive</span>';

            $buttons = '
                <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="' . $category->id . '">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $category->id . '">
                    <i class="bi bi-trash"></i> Delete
                </button>
                <button type="button" class="btn btn-sm btn-info toggle-status-btn" data-id="' . $category->id . '">
                    <i class="bi bi-arrow-repeat"></i> Toggle
                </button>
            ';
            $row[] = $buttons;

            $data[] = $row;
        }

        echo json_encode(['data' => $data]);
    }

    public function create()
    {
        $this->form_validation->set_rules('name', 'Category Name', 'required|trim|is_unique[categories.name]');
        $this->form_validation->set_rules('description', 'Description', 'trim');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $data = [
            'name' => $this->input->post('name'),
            'slug' => url_title($this->input->post('name'), 'dash', TRUE),
            'description' => $this->input->post('description'),
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Handle icon upload
        if (!empty($_FILES['icon_path']['name'])) {
            $config['upload_path'] = './uploads/categories/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif|svg';
            $config['max_size'] = 2048; // 2MB
            $config['file_name'] = 'category_icon_' . time() . '_' . uniqid();

            // Create directory if not exists
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('icon_path')) {
                $upload_data = $this->upload->data();
                $data['icon_path'] = $upload_data['file_name'];
            } else {
                echo json_encode(['success' => false, 'message' => $this->upload->display_errors()]);
                return;
            }
        }

        // Handle banner upload with cropping
        if (!empty($_FILES['banner_image']['name'])) {
            $config['upload_path'] = './uploads/categories/';
            $config['allowed_types'] = 'jpg|jpeg|png|webp';
            $config['max_size'] = 5120; // 5MB
            $config['file_name'] = 'category_banner_' . time() . '_' . uniqid();

            // Create directory if not exists
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('banner_image')) {
                $upload_data = $this->upload->data();

                // Process banner image: resize to 1920x400 and compress
                $processed_file = $this->_process_category_banner($upload_data['full_path']);

                if ($processed_file) {
                    $data['banner_image'] = basename($processed_file);

                    // Delete original if different from processed
                    if ($upload_data['full_path'] !== $processed_file && file_exists($upload_data['full_path'])) {
                        unlink($upload_data['full_path']);
                    }
                } else {
                    $data['banner_image'] = $upload_data['file_name'];
                }
            } else {
                echo json_encode(['success' => false, 'message' => $this->upload->display_errors()]);
                return;
            }
        }

        $insert_id = $this->Category_model->insert($data);

        if ($insert_id) {
            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Created category "' . $data['name'] . '"'
            ];
            $this->logaction->insertLog($userLogAction);

            echo json_encode(['success' => true, 'message' => 'Category created successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create category']);
        }
    }

    public function get_by_id($id)
    {
        $category = $this->Category_model->get_by_id($id);
        echo json_encode($category);
    }

    public function update()
    {
        $id = $this->input->post('category_id');

        // Get current category
        $current_category = $this->Category_model->get_by_id($id);

        // Validation - unique name except current record
        $is_unique = '';
        if ($this->input->post('name') != $current_category->name) {
            $is_unique = '|is_unique[categories.name]';
        }

        $this->form_validation->set_rules('name', 'Category Name', 'required|trim' . $is_unique);
        $this->form_validation->set_rules('description', 'Description', 'trim');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $data = [
            'name' => $this->input->post('name'),
            'slug' => url_title($this->input->post('name'), 'dash', TRUE),
            'description' => $this->input->post('description'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Handle icon upload
        if (!empty($_FILES['icon_path']['name'])) {
            // Delete old icon
            $old_category = $this->Category_model->get_by_id($id);
            if ($old_category && $old_category->icon_path) {
                $old_file = './uploads/categories/' . $old_category->icon_path;
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }

            $config['upload_path'] = './uploads/categories/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif|svg';
            $config['max_size'] = 2048; // 2MB
            $config['file_name'] = 'category_icon_' . time() . '_' . uniqid();

            // Create directory if not exists
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('icon_path')) {
                $upload_data = $this->upload->data();
                $data['icon_path'] = $upload_data['file_name'];
            } else {
                echo json_encode(['success' => false, 'message' => $this->upload->display_errors()]);
                return;
            }
        }

        // Handle banner upload with cropping
        if (!empty($_FILES['banner_image']['name'])) {
            // Delete old banner
            $old_category = $this->Category_model->get_by_id($id);
            if ($old_category && $old_category->banner_image) {
                $old_file = './uploads/categories/' . $old_category->banner_image;
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }

            $config['upload_path'] = './uploads/categories/';
            $config['allowed_types'] = 'jpg|jpeg|png|webp';
            $config['max_size'] = 5120; // 5MB
            $config['file_name'] = 'category_banner_' . time() . '_' . uniqid();

            // Create directory if not exists
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('banner_image')) {
                $upload_data = $this->upload->data();

                // Process banner image: resize to 1920x400 and compress
                $processed_file = $this->_process_category_banner($upload_data['full_path']);

                if ($processed_file) {
                    $data['banner_image'] = basename($processed_file);

                    // Delete original if different from processed
                    if ($upload_data['full_path'] !== $processed_file && file_exists($upload_data['full_path'])) {
                        unlink($upload_data['full_path']);
                    }
                } else {
                    $data['banner_image'] = $upload_data['file_name'];
                }
            } else {
                echo json_encode(['success' => false, 'message' => $this->upload->display_errors()]);
                return;
            }
        }

        $update = $this->Category_model->update($id, $data);

        if ($update) {
            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Updated category "' . $data['name'] . '"'
            ];
            $this->logaction->insertLog($userLogAction);

            echo json_encode(['success' => true, 'message' => 'Category updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update category']);
        }
    }

    public function delete($id)
    {
        $category = $this->Category_model->get_by_id($id);

        if (!$category) {
            echo json_encode(['success' => false, 'message' => 'Category not found']);
            return;
        }

        // Soft delete - set deleted_at
        $delete = $this->Category_model->soft_delete($id);

        if ($delete) {
            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Deleted category "' . $category->name . '"'
            ];
            $this->logaction->insertLog($userLogAction);

            echo json_encode(['success' => true, 'message' => 'Category deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete category']);
        }
    }

    public function toggle_status($id)
    {
        $category = $this->Category_model->get_by_id($id);

        if (!$category) {
            echo json_encode(['success' => false, 'message' => 'Category not found']);
            return;
        }

        $new_status = $category->status == 1 ? 0 : 1;
        $update = $this->Category_model->update($id, [
            'status' => $new_status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            $status_text = $new_status == 1 ? 'activated' : 'deactivated';

            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Category "' . $category->name . '" ' . $status_text
            ];
            $this->logaction->insertLog($userLogAction);

            echo json_encode(['success' => true, 'message' => 'Category status updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
    }

    /**
     * Process category banner image - resize to 1920x400 and compress
     *
     * @param string $source_path Full path to source image
     * @return string|false Path to processed image or false on failure
     */
    private function _process_category_banner($source_path)
    {
        if (!file_exists($source_path)) {
            return false;
        }

        // Target dimensions for category banner
        $target_width = 1920;
        $target_height = 400;
        $max_file_size = 400 * 1024; // 400KB in bytes

        // Get image info
        $image_info = getimagesize($source_path);
        if (!$image_info) {
            return false;
        }

        $source_width = $image_info[0];
        $source_height = $image_info[1];
        $mime_type = $image_info['mime'];

        // Create image resource based on type
        switch ($mime_type) {
            case 'image/jpeg':
            case 'image/jpg':
                $source_image = imagecreatefromjpeg($source_path);
                break;
            case 'image/png':
                $source_image = imagecreatefrompng($source_path);
                break;
            case 'image/webp':
                $source_image = imagecreatefromwebp($source_path);
                break;
            default:
                return false;
        }

        if (!$source_image) {
            return false;
        }

        // Create new image with target dimensions
        $new_image = imagecreatetruecolor($target_width, $target_height);

        // Preserve transparency for PNG
        if ($mime_type === 'image/png') {
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
            imagefilledrectangle($new_image, 0, 0, $target_width, $target_height, $transparent);
        }

        // Resize with high quality
        imagecopyresampled(
            $new_image,
            $source_image,
            0, 0, 0, 0,
            $target_width,
            $target_height,
            $source_width,
            $source_height
        );

        // Generate output path
        $path_info = pathinfo($source_path);
        $output_path = $path_info['dirname'] . '/' . $path_info['filename'] . '_processed.jpg';

        // Save with compression - start with high quality
        $quality = 85;
        $success = false;

        // Try to compress to under 400KB
        do {
            $success = imagejpeg($new_image, $output_path, $quality);

            if ($success) {
                $file_size = filesize($output_path);

                // If file size is acceptable, break
                if ($file_size <= $max_file_size || $quality <= 60) {
                    break;
                }

                // Reduce quality and try again
                $quality -= 5;
            }
        } while ($quality >= 60 && $success);

        // Free memory
        imagedestroy($source_image);
        imagedestroy($new_image);

        // If successful and file size is reasonable, return processed path
        if ($success && file_exists($output_path)) {
            $final_size = filesize($output_path);

            // Log if file is still large
            if ($final_size > $max_file_size) {
                log_message('info', "Category banner compressed to {$final_size} bytes (target: {$max_file_size} bytes)");
            }

            return $output_path;
        }

        return false;
    }
}
