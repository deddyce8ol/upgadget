<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Import Product Controller
 *
 * Handles CSV import functionality for products
 * - Upload CSV file
 * - Preview data
 * - Process import in batches
 * - Download error log
 */
class Import_product extends CI_Controller
{
    private $upload_path = './uploads/import/';

    public function __construct()
    {
        parent::__construct();
        _checkIsLogin();
        $this->load->model('Import_product_model');
        $this->load->model('LogAction_model', 'logaction');
        $this->load->helper(['import', 'form', 'url']);

        // Create upload directory if not exists
        if (!is_dir($this->upload_path)) {
            mkdir($this->upload_path, 0755, true);
        }
    }

    /**
     * Main import page
     */
    public function index()
    {
        $data['title'] = 'Import Products from CSV';
        $data['user'] = $this->db->get_where('user_data', ['email' => $this->session->userdata('email')])->row_array();

        // Clear any old import session data
        $this->session->unset_userdata('import_csv_path');
        $this->session->unset_userdata('import_stats');

        $this->load->view('layout/layout_header', $data);
        $this->load->view('layout/layout_sidebar');
        $this->load->view('layout/layout_topbar');
        $this->load->view('admin/import_product/index', $data);
        $this->load->view('layout/layout_footer');
    }

    /**
     * Handle CSV file upload
     */
    public function upload()
    {
        $response = [
            'success' => false,
            'message' => '',
            'data' => []
        ];

        try {
            // Configure upload
            $config['upload_path'] = $this->upload_path;
            $config['allowed_types'] = 'csv';
            $config['max_size'] = 10240; // 10MB
            $config['file_name'] = 'import_' . time() . '_' . uniqid();

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('csv_file')) {
                $response['message'] = $this->upload->display_errors('', '');
            } else {
                $upload_data = $this->upload->data();
                $csv_path = $upload_data['full_path'];

                // Validate CSV format
                $validation = $this->validate_csv_format($csv_path);
                if (!$validation['valid']) {
                    // Delete invalid file
                    unlink($csv_path);
                    $response['message'] = $validation['message'];
                } else {
                    // Store CSV path in session
                    $this->session->set_userdata('import_csv_path', $csv_path);

                    // Get preview data
                    $preview = $this->Import_product_model->preview_csv($csv_path, 50);

                    $response['success'] = true;
                    $response['message'] = 'CSV file uploaded successfully';
                    $response['data'] = [
                        'preview' => $preview,
                        'file_name' => $upload_data['file_name']
                    ];
                }
            }
        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        }

        echo json_encode($response);
    }

    /**
     * Validate CSV file format
     */
    private function validate_csv_format($csv_path)
    {
        $result = [
            'valid' => false,
            'message' => ''
        ];

        if (!file_exists($csv_path)) {
            $result['message'] = 'CSV file not found';
            return $result;
        }

        $handle = fopen($csv_path, 'r');
        if (!$handle) {
            $result['message'] = 'Unable to open CSV file';
            return $result;
        }

        // Check headers
        $headers = fgetcsv($handle);
        fclose($handle);

        $expected_headers = ['sku', 'price', 'stock', 'product_name', 'description'];

        if (count($headers) < 5) {
            $result['message'] = 'Invalid CSV format. Expected 5 columns: ' . implode(', ', $expected_headers);
            return $result;
        }

        // Normalize headers for comparison
        $headers_normalized = array_map('strtolower', array_map('trim', $headers));
        $missing_headers = [];

        foreach ($expected_headers as $expected) {
            if (!in_array(strtolower($expected), $headers_normalized)) {
                $missing_headers[] = $expected;
            }
        }

        if (!empty($missing_headers)) {
            $result['message'] = 'Missing required columns: ' . implode(', ', $missing_headers);
            return $result;
        }

        $result['valid'] = true;
        $result['message'] = 'CSV format is valid';
        return $result;
    }

    /**
     * Start import process
     */
    public function start_import()
    {
        $response = [
            'success' => false,
            'message' => '',
            'data' => []
        ];

        try {
            $csv_path = $this->session->userdata('import_csv_path');

            if (!$csv_path || !file_exists($csv_path)) {
                $response['message'] = 'CSV file not found. Please upload again.';
                echo json_encode($response);
                return;
            }

            // Initialize import (cache categories, brands, etc)
            $this->Import_product_model->initialize_import();

            // Get total rows for progress calculation
            $total_rows = $this->Import_product_model->count_csv_rows($csv_path);
            $batch_size = $this->Import_product_model->get_batch_size();
            $total_batches = ceil($total_rows / $batch_size);

            // Initialize stats in session
            $this->session->set_userdata('import_stats', [
                'total' => 0,
                'success' => 0,
                'skipped' => 0,
                'failed' => 0,
                'brands_created' => 0,
                'errors' => []
            ]);

            $response['success'] = true;
            $response['message'] = 'Import initialized';
            $response['data'] = [
                'total_rows' => $total_rows,
                'batch_size' => $batch_size,
                'total_batches' => $total_batches
            ];

            // Log action
            $this->logaction->insertLog([
                'user_id' => $this->session->userdata('id_user'),
                'action' => "Started product import - Importing $total_rows products from CSV"
            ]);

        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        }

        echo json_encode($response);
    }

    /**
     * Process import batch
     */
    public function process_batch()
    {
        $response = [
            'success' => false,
            'message' => '',
            'data' => []
        ];

        try {
            $batch_number = (int) $this->input->post('batch_number');
            $csv_path = $this->session->userdata('import_csv_path');

            if (!$csv_path || !file_exists($csv_path)) {
                $response['message'] = 'CSV file not found';
                echo json_encode($response);
                return;
            }

            // Process batch
            $batch_stats = $this->Import_product_model->process_import_batch($csv_path, $batch_number);

            // Update cumulative stats in session
            $cumulative_stats = $this->session->userdata('import_stats');
            $cumulative_stats['total'] += $batch_stats['total'];
            $cumulative_stats['success'] += $batch_stats['success'];
            $cumulative_stats['skipped'] += $batch_stats['skipped'];
            $cumulative_stats['failed'] += $batch_stats['failed'];
            $cumulative_stats['brands_created'] += $batch_stats['brands_created'];
            $cumulative_stats['errors'] = array_merge($cumulative_stats['errors'], $batch_stats['errors']);

            $this->session->set_userdata('import_stats', $cumulative_stats);

            $response['success'] = true;
            $response['message'] = "Batch $batch_number processed";
            $response['data'] = [
                'batch_stats' => $batch_stats,
                'cumulative_stats' => $cumulative_stats,
                'has_more' => $batch_stats['has_more']
            ];

        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        }

        echo json_encode($response);
    }

    /**
     * Get final import results
     */
    public function get_results()
    {
        $response = [
            'success' => false,
            'message' => '',
            'data' => []
        ];

        try {
            $stats = $this->session->userdata('import_stats');

            if (!$stats) {
                $response['message'] = 'No import statistics found';
                echo json_encode($response);
                return;
            }

            $response['success'] = true;
            $response['data'] = $stats;

            // Log completion
            $this->logaction->insertLog([
                'user_id' => $this->session->userdata('id_user'),
                'action' => sprintf(
                    "Completed product import: %d success, %d skipped, %d failed out of %d total",
                    $stats['success'],
                    $stats['skipped'],
                    $stats['failed'],
                    $stats['total']
                )
            ]);

            // Cleanup CSV file
            $csv_path = $this->session->userdata('import_csv_path');
            if ($csv_path && file_exists($csv_path)) {
                unlink($csv_path);
            }

        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        }

        echo json_encode($response);
    }

    /**
     * Download error log as CSV
     */
    public function download_errors()
    {
        $stats = $this->session->userdata('import_stats');

        if (!$stats || empty($stats['errors'])) {
            show_error('No errors to download');
            return;
        }

        // Generate CSV
        $filename = 'import_errors_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // CSV headers
        fputcsv($output, ['Row Number', 'SKU', 'Error Message']);

        // Error rows
        foreach ($stats['errors'] as $error) {
            fputcsv($output, [
                $error['row'] ?? 'N/A',
                $error['sku'] ?? 'N/A',
                is_array($error['errors']) ? implode('; ', $error['errors']) : $error['errors']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Cancel import and cleanup
     */
    public function cancel()
    {
        $csv_path = $this->session->userdata('import_csv_path');

        if ($csv_path && file_exists($csv_path)) {
            unlink($csv_path);
        }

        $this->session->unset_userdata('import_csv_path');
        $this->session->unset_userdata('import_stats');

        redirect('admin/import_product');
    }
}
