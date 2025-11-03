<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        _checkIsLogin();
        $this->load->model('Site_setting_model');
        $this->load->model('LogAction_model', 'logaction');
        $this->load->library('upload');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Site Settings';
        $data['user'] = $this->db->get_where('user_data', ['email' => $this->session->userdata('email')])->row_array();

        // Load all settings
        $settings = [];
        $all_settings = $this->Site_setting_model->get_all();
        foreach ($all_settings as $setting) {
            $settings[$setting->setting_key] = $setting->setting_value;
        }
        $data['settings'] = $settings;

        $this->load->view('layout/layout_header', $data);
        $this->load->view('layout/layout_sidebar');
        $this->load->view('layout/layout_topbar');
        $this->load->view('admin/settings/index', $data);
        $this->load->view('layout/layout_footer');
    }

    public function update()
    {
        // Set validation rules for required fields
        $this->form_validation->set_rules('site_name', 'Site Name', 'required|trim');
        $this->form_validation->set_rules('contact_email', 'Contact Email', 'required|trim|valid_email');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> ' . validation_errors() . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>');
            redirect('admin/settings');
            return;
        }

        $update_success = true;
        $error_message = '';

        // Handle site_logo upload
        if (!empty($_FILES['site_logo']['name'])) {
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = 2048; // 2MB
            $config['file_name'] = 'logo_' . time() . '_' . uniqid();

            $this->upload->initialize($config);

            if ($this->upload->do_upload('site_logo')) {
                $upload_data = $this->upload->data();

                // Delete old logo
                $old_logo = $this->Site_setting_model->get_value('site_logo');
                if ($old_logo && file_exists('./uploads/' . $old_logo)) {
                    unlink('./uploads/' . $old_logo);
                }

                // Update with new logo
                $this->Site_setting_model->update_by_key('site_logo', $upload_data['file_name']);
            } else {
                $error_message = $this->upload->display_errors('', '');
                $update_success = false;
            }
        }

        // Handle site_favicon upload
        if (!empty($_FILES['site_favicon']['name']) && $update_success) {
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = 2048; // 2MB
            $config['file_name'] = 'favicon_' . time() . '_' . uniqid();

            $this->upload->initialize($config);

            if ($this->upload->do_upload('site_favicon')) {
                $upload_data = $this->upload->data();

                // Delete old favicon
                $old_favicon = $this->Site_setting_model->get_value('site_favicon');
                if ($old_favicon && file_exists('./uploads/' . $old_favicon)) {
                    unlink('./uploads/' . $old_favicon);
                }

                // Update with new favicon
                $this->Site_setting_model->update_by_key('site_favicon', $upload_data['file_name']);
            } else {
                $error_message = $this->upload->display_errors('', '');
                $update_success = false;
            }
        }

        // Update other settings from POST data
        if ($update_success) {
            $settings_to_update = [
                'site_name',
                'site_tagline',
                'site_description',
                'contact_email',
                'contact_phone',
                'contact_whatsapp',
                'contact_address',
                'facebook_url',
                'instagram_url',
                'shopee_url',
                'tiktok_url',
                'tokopedia_url',
                'currency',
                'products_per_page',
                'featured_products_limit',
                'new_products_limit'
            ];

            foreach ($settings_to_update as $key) {
                $value = $this->input->post($key);
                if ($value !== null) {
                    $this->Site_setting_model->update_by_key($key, $value);
                }
            }

            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Updated site settings'
            ];
            $this->logaction->insertLog($userLogAction);

            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> Site settings updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> Failed to update settings: ' . $error_message . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>');
        }

        redirect('admin/settings');
    }
}
