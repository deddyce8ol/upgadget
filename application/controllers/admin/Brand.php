<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Brand extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        _checkIsLogin();
        $this->load->model('Brand_model');
        $this->load->model('LogAction_model', 'logaction');
    }

    public function index()
    {
        $data['title'] = 'Brand Management';
        $data['user'] = $this->db->get_where('user_data', ['email' => $this->session->userdata('email')])->row_array();

        $this->load->view('layout/layout_header', $data);
        $this->load->view('layout/layout_sidebar');
        $this->load->view('layout/layout_topbar');
        $this->load->view('admin/brand/index');
        $this->load->view('layout/layout_footer');
    }

    public function get_data()
    {
        $brands = $this->Brand_model->get_all();
        $data = [];

        foreach ($brands as $brand) {
            $row = [];
            $row[] = $brand->brand_id;
            $row[] = $brand->brand_logo ? '<img src="' . base_url('uploads/brands/' . $brand->brand_logo) . '" style="max-width: 50px; max-height: 50px;">' : '-';
            $row[] = $brand->brand_name;
            // Description column removed as it doesn't exist in database
            $row[] = $brand->is_active == 1
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-danger">Inactive</span>';

            $buttons = '
                <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="' . $brand->brand_id . '">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $brand->brand_id . '">
                    <i class="bi bi-trash"></i> Delete
                </button>
                <button type="button" class="btn btn-sm btn-info toggle-status-btn" data-id="' . $brand->brand_id . '">
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
        $this->form_validation->set_rules('brand_name', 'Brand Name', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $data = [
            'brand_name' => $this->input->post('brand_name'),
            'brand_slug' => url_title($this->input->post('brand_name'), 'dash', TRUE),
            'is_active' => 1
        ];

        // Handle file upload
        if (!empty($_FILES['brand_logo']['name'])) {
            $config['upload_path'] = './uploads/brands/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = 2048; // 2MB
            $config['file_name'] = 'brand_' . time() . '_' . uniqid();

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('brand_logo')) {
                $upload_data = $this->upload->data();
                $data['brand_logo'] = $upload_data['file_name'];
            } else {
                echo json_encode(['success' => false, 'message' => $this->upload->display_errors()]);
                return;
            }
        }

        $insert_id = $this->Brand_model->insert($data);

        if ($insert_id) {
            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Created brand "' . $data['brand_name'] . '"'
            ];
            $this->logaction->insertLog($userLogAction);

            echo json_encode(['success' => true, 'message' => 'Brand created successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create brand']);
        }
    }

    public function get_by_id($id)
    {
        $brand = $this->Brand_model->get_by_id($id);
        echo json_encode($brand);
    }

    public function update()
    {
        $id = $this->input->post('brand_id');
        $this->form_validation->set_rules('brand_name', 'Brand Name', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $data = [
            'brand_name' => $this->input->post('brand_name'),
            'brand_slug' => url_title($this->input->post('brand_name'), 'dash', TRUE)
        ];

        // Handle file upload
        if (!empty($_FILES['brand_logo']['name'])) {
            // Delete old logo
            $old_brand = $this->Brand_model->get_by_id($id);
            if ($old_brand && $old_brand->brand_logo) {
                $old_file = './uploads/brands/' . $old_brand->brand_logo;
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }

            $config['upload_path'] = './uploads/brands/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = 2048; // 2MB
            $config['file_name'] = 'brand_' . time() . '_' . uniqid();

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('brand_logo')) {
                $upload_data = $this->upload->data();
                $data['brand_logo'] = $upload_data['file_name'];
            } else {
                echo json_encode(['success' => false, 'message' => $this->upload->display_errors()]);
                return;
            }
        }

        $update = $this->Brand_model->update($id, $data);

        if ($update) {
            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Updated brand "' . $data['brand_name'] . '"'
            ];
            $this->logaction->insertLog($userLogAction);

            echo json_encode(['success' => true, 'message' => 'Brand updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update brand']);
        }
    }

    public function delete($id)
    {
        $brand = $this->Brand_model->get_by_id($id);

        if (!$brand) {
            echo json_encode(['success' => false, 'message' => 'Brand not found']);
            return;
        }

        // Delete logo file
        if ($brand->brand_logo) {
            $file_path = './uploads/brands/' . $brand->brand_logo;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        $delete = $this->Brand_model->delete($id);

        if ($delete) {
            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Deleted brand "' . $brand->brand_name . '"'
            ];
            $this->logaction->insertLog($userLogAction);

            echo json_encode(['success' => true, 'message' => 'Brand deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete brand']);
        }
    }

    public function toggle_status($id)
    {
        $brand = $this->Brand_model->get_by_id($id);

        if (!$brand) {
            echo json_encode(['success' => false, 'message' => 'Brand not found']);
            return;
        }

        $new_status = $brand->is_active == 1 ? 0 : 1;
        $update = $this->Brand_model->update($id, ['is_active' => $new_status]);

        if ($update) {
            $status_text = $new_status == 1 ? 'activated' : 'deactivated';

            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Brand "' . $brand->brand_name . '" ' . $status_text
            ];
            $this->logaction->insertLog($userLogAction);

            echo json_encode(['success' => true, 'message' => 'Brand status updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
    }
}
