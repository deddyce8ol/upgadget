<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Report extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        _checkIsLogin();
        $this->load->model('Report_model');
        $this->load->model('Order_model');
        $this->load->model('Order_item_model');
        $this->load->helper('ecommerce');
    }

    public function index()
    {
        redirect('admin/report/sales');
    }

    public function sales()
    {
        $data['title'] = 'Penjualan';
        $data['user'] = $this->db->get_where('user_data', ['email' => $this->session->userdata('email')])->row_array();

        // Get default date range from actual data in database
        $date_range = $this->Report_model->get_date_range();
        if ($date_range && $date_range->latest) {
            // Use the month of the latest order
            $latest_date = $date_range->latest;
            $data['start_date'] = date('Y-m-01', strtotime($latest_date));
            $data['end_date'] = date('Y-m-t', strtotime($latest_date));
        } else {
            // Fallback to current month
            $data['start_date'] = date('Y-m-01');
            $data['end_date'] = date('Y-m-d');
        }

        // Get summary data
        $filters = [
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'payment_status' => 'paid'
        ];

        $data['summary'] = $this->Report_model->get_sales_summary($filters);

        $this->load->view('layout/layout_header', $data);
        $this->load->view('layout/layout_sidebar');
        $this->load->view('layout/layout_topbar');
        $this->load->view('admin/report/sales', $data);
        $this->load->view('layout/layout_footer');
    }

    public function get_sales_data()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $filters = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'payment_status' => 'paid'
        ];

        $orders = $this->Report_model->get_sales_transactions($filters);
        $data = [];

        $no = 1;
        foreach ($orders as $order) {
            $row = [];
            $row[] = $no++;
            $row[] = '<span class="font-bold">' . $order->order_number . '</span>';
            $row[] = date('d M Y', strtotime($order->created_at));
            $row[] = '<p class="mb-0">' . $order->customer_name . '</p>' .
                '<small class="text-muted">' . $order->customer_phone . '</small>';
            $row[] = '<span class="text-end d-block">' . $order->total_items . ' item(s)</span>';
            $row[] = '<span class="font-bold text-end d-block">' . format_rupiah($order->total_amount) . '</span>';
            $row[] = get_order_status_badge($order->status);

            $buttons = '
                <a href="' . base_url('admin/order/detail/' . $order->order_id) . '" class="btn btn-sm btn-info" title="View Detail">
                    <i class="bi bi-eye"></i>
                </a>
            ';
            $row[] = $buttons;

            $data[] = $row;
        }

        echo json_encode(['data' => $data]);
    }

    public function get_summary_data()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $filters = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'payment_status' => 'paid'
        ];

        $summary = $this->Report_model->get_sales_summary($filters);

        echo json_encode([
            'success' => true,
            'data' => $summary
        ]);
    }

    public function get_chart_data()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $filters = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'payment_status' => 'paid'
        ];

        $chart_data = $this->Report_model->get_sales_by_date($filters);

        echo json_encode([
            'success' => true,
            'data' => $chart_data
        ]);
    }

    public function export_excel()
    {
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (!$start_date || !$end_date) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Tanggal tidak valid</div>');
            redirect('admin/report/sales');
            return;
        }

        $filters = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'payment_status' => 'paid'
        ];

        // Load PhpSpreadsheet via Composer autoload
        require_once FCPATH . 'vendor/autoload.php';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'LAPORAN PENJUALAN');
        $sheet->setCellValue('A2', 'Periode: ' . date('d M Y', strtotime($start_date)) . ' - ' . date('d M Y', strtotime($end_date)));

        // Merge cells for title
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');

        // Style title
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set column headers
        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'No. Order');
        $sheet->setCellValue('C4', 'Tanggal');
        $sheet->setCellValue('D4', 'Customer');
        $sheet->setCellValue('E4', 'Jumlah Item');
        $sheet->setCellValue('F4', 'Total');
        $sheet->setCellValue('G4', 'Status');

        // Style header
        $sheet->getStyle('A4:G4')->getFont()->setBold(true);
        $sheet->getStyle('A4:G4')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');
        $sheet->getStyle('A4:G4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Get data
        $orders = $this->Report_model->get_sales_transactions($filters);

        $row = 5;
        $no = 1;
        $total_revenue = 0;

        foreach ($orders as $order) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $order->order_number);
            $sheet->setCellValue('C' . $row, date('d M Y', strtotime($order->created_at)));
            $sheet->setCellValue('D' . $row, $order->customer_name);
            $sheet->setCellValue('E' . $row, $order->total_items);
            $sheet->setCellValue('F' . $row, $order->total_amount);
            $sheet->setCellValue('G' . $row, ucfirst($order->status));

            $total_revenue += $order->total_amount;
            $row++;
        }

        // Add total row
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $sheet->setCellValue('F' . $row, $total_revenue);
        $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);
        $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);

        // Format currency column
        $sheet->getStyle('F5:F' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');

        // Set border
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A4:G' . $row)->applyFromArray($styleArray);

        // Generate file
        $filename = 'Laporan_Penjualan_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
