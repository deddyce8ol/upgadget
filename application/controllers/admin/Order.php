<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Order extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        _checkIsLogin();
        $this->load->model('Order_model');
        $this->load->model('Order_item_model');
        $this->load->model('LogAction_model', 'logaction');
        $this->load->helper('ecommerce');
    }

    public function index()
    {
        $data['title'] = 'Order Management';
        $data['user'] = $this->db->get_where('user_data', ['email' => $this->session->userdata('email')])->row_array();

        $this->load->view('layout/layout_header', $data);
        $this->load->view('layout/layout_sidebar');
        $this->load->view('layout/layout_topbar');
        $this->load->view('admin/order/index', $data);
        $this->load->view('layout/layout_footer');
    }

    public function get_data()
    {
        // Get filter parameters from POST
        $filters = [];

        if ($this->input->post('status')) {
            $filters['status'] = $this->input->post('status');
        }

        if ($this->input->post('payment_status')) {
            $filters['payment_status'] = $this->input->post('payment_status');
        }

        if ($this->input->post('start_date') && $this->input->post('end_date')) {
            $filters['start_date'] = $this->input->post('start_date');
            $filters['end_date'] = $this->input->post('end_date');
        }

        $orders = $this->Order_model->get_all($filters);
        $data = [];

        foreach ($orders as $order) {
            $row = [];
            $row[] = '<span class="font-bold">' . $order->order_number . '</span>';
            $row[] = '<p class="font-bold mb-0">' . $order->customer_name . '</p>' .
                '<small class="text-muted">' . $order->customer_phone . '</small>';
            $row[] = '<span class="font-bold">' . format_rupiah($order->total_amount) . '</span>';
            $row[] = get_order_status_badge($order->status);
            $row[] = get_payment_status_badge($order->payment_status);
            $row[] = date('d M Y H:i', strtotime($order->created_at));

            $buttons = '
                <a href="' . base_url('admin/order/detail/' . $order->order_id) . '" class="btn btn-sm btn-info">
                    <i class="bi bi-eye"></i> View
                </a>
                <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $order->order_id . '">
                    <i class="bi bi-trash"></i>
                </button>
            ';
            $row[] = $buttons;

            $data[] = $row;
        }

        echo json_encode(['data' => $data]);
    }

    public function detail($id)
    {
        $data['title'] = 'Order Detail';
        $data['user'] = $this->db->get_where('user_data', ['email' => $this->session->userdata('email')])->row_array();
        $data['order'] = $this->Order_model->get_by_id($id);
        $data['order_items'] = $this->Order_item_model->get_by_order_id($id);

        if (!$data['order']) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Order not found</div>');
            redirect('admin/order');
        }

        $this->load->view('layout/layout_header', $data);
        $this->load->view('layout/layout_sidebar');
        $this->load->view('layout/layout_topbar');
        $this->load->view('admin/order/detail', $data);
        $this->load->view('layout/layout_footer');
    }

    public function update_status()
    {
        $order_id = $this->input->post('order_id');
        $status = $this->input->post('status');

        $order = $this->Order_model->get_by_id($order_id);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            return;
        }

        $update = $this->Order_model->update($order_id, ['status' => $status]);

        if ($update !== false) {
            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Updated order #' . $order->order_number . ' status to ' . $status
            ];
            $this->logaction->insertLog($userLogAction);

            echo json_encode(['success' => true, 'message' => 'Order status updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
        }
    }

    public function update_payment_status()
    {
        $order_id = $this->input->post('order_id');
        $payment_status = $this->input->post('payment_status');

        $order = $this->Order_model->get_by_id($order_id);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            return;
        }

        $update = $this->Order_model->update($order_id, ['payment_status' => $payment_status]);

        if ($update !== false) {
            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Updated order #' . $order->order_number . ' payment status to ' . $payment_status
            ];
            $this->logaction->insertLog($userLogAction);

            echo json_encode(['success' => true, 'message' => 'Payment status updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update payment status']);
        }
    }

    public function delete($id)
    {
        $order = $this->Order_model->get_by_id($id);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            return;
        }

        // Delete order items first
        $this->Order_item_model->delete_by_order_id($id);

        // Delete order
        $delete = $this->Order_model->delete($id);

        if ($delete) {
            // Log action
            $userLogAction = [
                'user_id' => $this->session->userdata('id_user'),
                'action' => 'Deleted order #' . $order->order_number
            ];
            $this->logaction->insertLog($userLogAction);

            echo json_encode(['success' => true, 'message' => 'Order deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete order']);
        }
    }
}
