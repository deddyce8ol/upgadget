<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Banner extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        _checkIsLogin();
        $this->load->model('Banner_model');
        $this->load->model('LogAction_model', 'logaction');
    }

    public function index()
    {
        $data['title'] = 'Banner Management';
        $data['user'] = $this->db->get_where('user_data', ['email' => $this->session->userdata('email')])->row_array();

        $this->load->view('layout/layout_header', $data);
        $this->load->view('layout/layout_sidebar');
        $this->load->view('layout/layout_topbar');
        $this->load->view('admin/banner/index');
        $this->load->view('layout/layout_footer');
    }

    public function get_data()
    {
        $banners = $this->Banner_model->get_all();
        $data = [];

        foreach ($banners as $banner) {
            $row = [];
            $row[] = $banner->banner_id;
            $row[] = $banner->banner_image ? '<img src="' . base_url('uploads/banners/' . $banner->banner_image) . '" style="max-width: 100px; max-height: 50px; object-fit: cover;">' : '-';
            $row[] = $banner->banner_title ?? '-';
            $row[] = $banner->banner_link ? '<a href="' . $banner->banner_link . '" target="_blank" class="text-truncate" style="max-width: 200px; display: inline-block;">' . $banner->banner_link . '</a>' : '-';
            $row[] = $banner->is_active == 1
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-danger">Inactive</span>';

            $buttons = '
                <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="' . $banner->banner_id . '">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $banner->banner_id . '">
                    <i class="bi bi-trash"></i> Delete
                </button>
                <button type="button" class="btn btn-sm btn-info toggle-status-btn" data-id="' . $banner->banner_id . '">
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
        $this->form_validation->set_rules('banner_title', 'Banner Title', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $data = [
            'banner_title' => $this->input->post('banner_title'),
            'banner_link' => $this->input->post('banner_link'),
            'is_active' => 1,
            'sort_order' => 0
        ];

        // Handle file upload
        if (!empty($_FILES['banner_image']['name'])) {
            $config['upload_path'] = './uploads/banners/';
            $config['allowed_types'] = 'jpg|jpeg|png|webp';
            $config['max_size'] = 5120; // 5MB
            $config['file_name'] = 'banner_' . time() . '_' . uniqid();

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('banner_image')) {
                $upload_data = $this->upload->data();

                // Process image: resize to 1920x600 and compress
                $processed_file = $this->_process_banner_image($upload_data['full_path']);

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
        } else {
            echo json_encode(['success' => false, 'message' => 'Banner image is required']);
            return;
        }

        $insert_id = $this->Banner_model->insert($data);

        if ($insert_id) {
            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Created banner "' . $data['banner_title'] . '"'
            ];
            $this->logaction->insertLog($userLogAction);

            echo json_encode(['success' => true, 'message' => 'Banner created successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create banner']);
        }
    }

    public function get_by_id($id)
    {
        $banner = $this->Banner_model->get_by_id($id);
        echo json_encode($banner);
    }

    public function update()
    {
        $id = $this->input->post('banner_id');
        $this->form_validation->set_rules('banner_title', 'Banner Title', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $data = [
            'banner_title' => $this->input->post('banner_title'),
            'banner_link' => $this->input->post('banner_link')
        ];

        // Handle file upload
        if (!empty($_FILES['banner_image']['name'])) {
            // Delete old image
            $old_banner = $this->Banner_model->get_by_id($id);
            if ($old_banner && $old_banner->banner_image) {
                $old_file = './uploads/banners/' . $old_banner->banner_image;
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }

            $config['upload_path'] = './uploads/banners/';
            $config['allowed_types'] = 'jpg|jpeg|png|webp';
            $config['max_size'] = 5120; // 5MB
            $config['file_name'] = 'banner_' . time() . '_' . uniqid();

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('banner_image')) {
                $upload_data = $this->upload->data();

                // Process image: resize to 1920x600 and compress
                $processed_file = $this->_process_banner_image($upload_data['full_path']);

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

        $update = $this->Banner_model->update($id, $data);

        if ($update) {
            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Updated banner "' . $data['banner_title'] . '"'
            ];
            $this->logaction->insertLog($userLogAction);

            echo json_encode(['success' => true, 'message' => 'Banner updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update banner']);
        }
    }

    public function delete($id)
    {
        $banner = $this->Banner_model->get_by_id($id);

        if (!$banner) {
            echo json_encode(['success' => false, 'message' => 'Banner not found']);
            return;
        }

        // Delete image file
        if ($banner->banner_image) {
            $file_path = './uploads/banners/' . $banner->banner_image;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        $delete = $this->Banner_model->delete($id);

        if ($delete) {
            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Deleted banner "' . $banner->banner_title . '"'
            ];
            $this->logaction->insertLog($userLogAction);

            echo json_encode(['success' => true, 'message' => 'Banner deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete banner']);
        }
    }

    public function toggle_status($id)
    {
        $banner = $this->Banner_model->get_by_id($id);

        if (!$banner) {
            echo json_encode(['success' => false, 'message' => 'Banner not found']);
            return;
        }

        $new_status = $banner->is_active == 1 ? 0 : 1;
        $update = $this->Banner_model->update($id, ['is_active' => $new_status]);

        if ($update) {
            $status_text = $new_status == 1 ? 'activated' : 'deactivated';

            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Banner "' . $banner->banner_title . '" ' . $status_text
            ];
            $this->logaction->insertLog($userLogAction);

            echo json_encode(['success' => true, 'message' => 'Banner status updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
    }

    /**
     * Process banner image - resize to 1920x600 and compress
     *
     * @param string $source_path Full path to source image
     * @return string|false Path to processed image or false on failure
     */
    private function _process_banner_image($source_path)
    {
        if (!file_exists($source_path)) {
            return false;
        }

        // Target dimensions (recommended size from banner-size-recommended.md)
        $target_width = 1920;
        $target_height = 600;
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
                log_message('info', "Banner image compressed to {$final_size} bytes (target: {$max_file_size} bytes)");
            }

            return $output_path;
        }

        return false;
    }
}
