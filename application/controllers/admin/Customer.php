<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Customer extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        _checkIsLogin();
        $this->load->model('Customer_model');
        $this->load->model('Order_model');
        $this->load->model('LogAction_model', 'logaction');
    }

    public function index()
    {
        $data['title'] = 'Customer Management';
        $data['user'] = $this->db->get_where('user_data', ['email' => $this->session->userdata('email')])->row_array();

        $this->load->view('layout/layout_header', $data);
        $this->load->view('layout/layout_sidebar');
        $this->load->view('layout/layout_topbar');
        $this->load->view('admin/customer/index');
        $this->load->view('layout/layout_footer');
    }

    public function get_data()
    {
        $customers = $this->Customer_model->get_all();
        $data = [];

        foreach ($customers as $customer) {
            // Count total orders for this customer
            $total_orders = $this->Order_model->count_all(['customer_id' => $customer->customer_id]);

            $row = [];
            $row[] = $customer->customer_id;
            $row[] = $customer->full_name;
            $row[] = $customer->email;
            $row[] = $customer->phone ?? '-';
            $row[] = $total_orders;
            $row[] = $customer->is_active == 1
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-danger">Inactive</span>';

            $buttons = '
                <a href="' . base_url('admin/customer/detail/' . $customer->customer_id) . '" class="btn btn-sm btn-info">
                    <i class="bi bi-eye"></i> View
                </a>
                <button type="button" class="btn btn-sm btn-warning toggle-status-btn" data-id="' . $customer->customer_id . '">
                    <i class="bi bi-arrow-repeat"></i> Toggle
                </button>
                <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $customer->customer_id . '">
                    <i class="bi bi-trash"></i> Delete
                </button>
            ';
            $row[] = $buttons;

            $data[] = $row;
        }

        echo json_encode(['data' => $data]);
    }

    public function detail($id)
    {
        $customer = $this->Customer_model->get_by_id($id);

        if (!$customer) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Customer not found</div>');
            redirect('admin/customer');
        }

        // Get customer orders
        $orders = $this->Order_model->get_all(['customer_id' => $id]);

        // Calculate statistics
        $total_orders = count($orders);
        $total_spent = 0;
        foreach ($orders as $order) {
            $total_spent += $order->total_amount;
        }

        $data['title'] = 'Customer Detail';
        $data['user'] = $this->db->get_where('user_data', ['email' => $this->session->userdata('email')])->row_array();
        $data['customer'] = $customer;
        $data['orders'] = $orders;
        $data['total_orders'] = $total_orders;
        $data['total_spent'] = $total_spent;

        $this->load->view('layout/layout_header', $data);
        $this->load->view('layout/layout_sidebar');
        $this->load->view('layout/layout_topbar');
        $this->load->view('admin/customer/detail', $data);
        $this->load->view('layout/layout_footer');
    }

    public function toggle_status($id)
    {
        $customer = $this->Customer_model->get_by_id($id);

        if (!$customer) {
            echo json_encode(['success' => false, 'message' => 'Customer not found']);
            return;
        }

        $new_status = $customer->is_active == 1 ? 0 : 1;
        $update = $this->Customer_model->update($id, ['is_active' => $new_status]);

        if ($update) {
            $status_text = $new_status == 1 ? 'activated' : 'deactivated';

            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Customer "' . $customer->full_name . '" ' . $status_text
            ];
            $this->logaction->insertLog($userLogAction);

            echo json_encode(['success' => true, 'message' => 'Customer status updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
    }

    public function delete($id)
    {
        $customer = $this->Customer_model->get_by_id($id);

        if (!$customer) {
            echo json_encode(['success' => false, 'message' => 'Customer not found']);
            return;
        }

        // Check if customer has orders
        $order_count = $this->Order_model->count_all(['customer_id' => $id]);
        if ($order_count > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete customer with existing orders. Please deactivate instead.']);
            return;
        }

        $delete = $this->Customer_model->delete($id);

        if ($delete) {
            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Deleted customer "' . $customer->full_name . '"'
            ];
            $this->logaction->insertLog($userLogAction);

            echo json_encode(['success' => true, 'message' => 'Customer deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete customer']);
        }
    }
}
