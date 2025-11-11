<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Product Import Controller
 *
 * Handle product import from Excel files
 *
 * @package     UPGADGET
 * @subpackage  Controllers/Admin
 * @category    Import
 * @author      PT. Qapuas Media Technologies
 * @since       Version 1.0.0
 */
class Product_import extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        // Check if admin is logged in
        _checkIsLogin();

        // Load required models and libraries
        $this->load->model('Product_import_model');
        $this->load->library('slug_generator');
        $this->load->helper(['form', 'url']);

        // Increase memory and execution time for large files
        ini_set('memory_limit', '256M');
        ini_set('max_execution_time', 300); // 5 minutes
    }

    /**
     * Display import page
     */
    public function index()
    {
        $data['title'] = 'Import Produk';
        $data['user'] = $this->db->get_where('user_data', ['email' => $this->session->userdata('email')])->row_array();
        $data['stats'] = $this->Product_import_model->get_import_stats();

        $this->load->view('layout/layout_header', $data);
        $this->load->view('layout/layout_sidebar');
        $this->load->view('layout/layout_topbar');
        $this->load->view('admin/product_import/index', $data);
        $this->load->view('layout/layout_footer');
    }

    /**
     * Handle file upload and generate preview
     */
    public function upload()
    {
        // Set response as JSON
        $this->output->set_content_type('application/json');

        // Check if file was uploaded
        if (empty($_FILES['excel_file']['name'])) {
            $this->output->set_output(json_encode([
                'status' => false,
                'message' => 'Upload gagal',
                'error' => 'No file uploaded'
            ]));
            return;
        }

        // Validate file upload
        $upload_result = $this->validate_upload();
        if (!$upload_result['success']) {
            $this->output->set_output(json_encode([
                'status' => false,
                'message' => 'Upload gagal',
                'error' => $upload_result['error']
            ]));
            return;
        }

        $file_path = $upload_result['file_path'];

        try {
            // Generate preview data and save to temp table
            $result = $this->generate_preview($file_path);

            // Delete uploaded file after processing
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            $this->output->set_output(json_encode($result));

        } catch (Exception $e) {
            // Delete file on error
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            // Log error for debugging
            log_message('error', 'Product Import Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            $this->output->set_output(json_encode([
                'status' => false,
                'message' => 'Upload gagal',
                'error' => $e->getMessage(),
                'trace' => ENVIRONMENT === 'development' ? $e->getTraceAsString() : null
            ]));
        }
    }

    /**
     * Get preview data from temporary table
     */
    public function get_preview_data()
    {
        // Set response as JSON
        $this->output->set_content_type('application/json');

        $session_id = $this->input->get('session_id');

        if (empty($session_id)) {
            $this->output->set_output(json_encode([
                'status' => false,
                'message' => 'Session invalid'
            ]));
            return;
        }

        try {
            // Get data from temp table
            $this->db->where('session_id', $session_id);
            $this->db->order_by('row_number', 'ASC');
            $data = $this->db->get('product_import_temp')->result();

            $this->output->set_output(json_encode([
                'status' => true,
                'data' => $data
            ]));

        } catch (Exception $e) {
            $this->output->set_output(json_encode([
                'status' => false,
                'message' => 'Gagal mengambil data preview',
                'error' => $e->getMessage()
            ]));
        }
    }

    /**
     * Process confirmed import from temp table
     */
    public function confirm_import()
    {
        // Set response as JSON
        $this->output->set_content_type('application/json');

        // Get session_id and selected IDs
        $session_id = $this->input->post('session_id');
        $selected_ids = $this->input->post('selected_ids'); // Array of temp table IDs

        if (empty($session_id)) {
            $this->output->set_output(json_encode([
                'status' => false,
                'message' => 'Session invalid'
            ]));
            return;
        }

        try {
            $result = $this->process_import_from_temp($session_id, $selected_ids);
            $this->output->set_output(json_encode($result));

        } catch (Exception $e) {
            log_message('error', 'Confirm Import Error: ' . $e->getMessage());

            $this->output->set_output(json_encode([
                'status' => false,
                'message' => 'Import gagal',
                'error' => $e->getMessage()
            ]));
        }
    }

    /**
     * Validate file upload
     *
     * @return array Upload result
     */
    private function validate_upload()
    {
        // Ensure upload directory exists
        $upload_path = FCPATH . 'uploads/import/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        // Configure upload
        $config['upload_path']      = $upload_path;
        $config['allowed_types']    = 'xlsx|xls';
        $config['max_size']         = 10240; // 10MB
        $config['file_name']        = 'import_' . date('YmdHis') . '_' . rand(1000, 9999);
        $config['overwrite']        = false;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('excel_file')) {
            return [
                'success' => false,
                'error' => $this->upload->display_errors('', '')
            ];
        }

        $upload_data = $this->upload->data();

        return [
            'success' => true,
            'file_path' => $upload_data['full_path'],
            'file_name' => $upload_data['file_name']
        ];
    }

    /**
     * Process Excel file and import products
     *
     * @param string $file_path Path to uploaded Excel file
     * @return array Import results
     */
    private function process_import($file_path)
    {
        // Load PHPSpreadsheet
        $vendor_path = FCPATH . 'vendor/autoload.php';
        if (!file_exists($vendor_path)) {
            // Try alternative path
            $vendor_path = dirname(FCPATH) . '/vendor/autoload.php';
        }

        if (!file_exists($vendor_path)) {
            throw new Exception('Composer autoload not found. Please run: composer install');
        }

        require_once $vendor_path;

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        // Initialize counters
        $total_rows = 0;
        $success_count = 0;
        $inserted_count = 0;
        $updated_count = 0;
        $failed_count = 0;
        $errors = [];

        // Track current category
        $current_category = '';
        $current_category_id = null;

        // Start from row 6 (after header at row 5)
        for ($row = 6; $row <= $highestRow; $row++) {
            // Get cell values
            $colB = trim($sheet->getCell('B' . $row)->getCalculatedValue());
            $colC = trim($sheet->getCell('C' . $row)->getCalculatedValue()); // SKU
            $colE = trim($sheet->getCell('E' . $row)->getCalculatedValue()); // Product Name
            $colJ = $sheet->getCell('J' . $row)->getCalculatedValue(); // Price
            $colL = $sheet->getCell('L' . $row)->getCalculatedValue(); // Stock

            // Detect category
            if (!empty($colB) && empty($colC)) {
                $category_name = $colB;

                // Skip header/footer rows
                $skip_keywords = ['ACCURATE', 'Cabang', 'Daftar Barang', 'Halaman',
                                 'Per Tgl', 'Tercetak', 'UP.GADGET', 'Kode Barang', 'UP.GADGET'];

                $should_skip = false;
                foreach ($skip_keywords as $keyword) {
                    if (stripos($category_name, $keyword) !== false) {
                        $should_skip = true;
                        break;
                    }
                }

                if (!$should_skip && strlen($category_name) > 0) {
                    $current_category = $category_name;
                    $current_category_id = $this->Product_import_model->map_category($current_category);
                }

                continue; // Skip to next row
            }

            // Process product row
            if (!empty($colC)) {
                $total_rows++;

                // Prepare product data
                $product_data = [
                    'sku'           => $colC,
                    'product_name'  => $colE,
                    'price'         => is_numeric($colJ) ? floatval($colJ) : 0,
                    'stock'         => is_numeric($colL) ? intval($colL) : 0,
                    'category_id'   => $current_category_id,
                    'brand_id'      => $this->Product_import_model->detect_brand($colE)
                ];

                // Validate data
                $validation = $this->Product_import_model->validate_product_data($product_data, $row);

                if (!$validation['valid']) {
                    $errors[] = $validation['error'];
                    $failed_count++;
                    continue;
                }

                // Check if SKU exists
                $existing = $this->Product_import_model->check_sku_exists($colC);

                if ($existing) {
                    // UPDATE existing product
                    $update_data = [
                        'product_name'  => $product_data['product_name'],
                        'product_slug'  => $this->slug_generator->make_unique($product_data['product_name'], $existing->product_id),
                        'category_id'   => $product_data['category_id'],
                        'brand_id'      => $product_data['brand_id'],
                        'price'         => $product_data['price'],
                        'stock'         => $product_data['stock'],
                        'updated_at'    => date('Y-m-d H:i:s')
                    ];

                    if ($this->Product_import_model->update_product($existing->product_id, $update_data)) {
                        $updated_count++;
                        $success_count++;
                    } else {
                        $errors[] = "Baris {$row}: Gagal update produk {$colC}";
                        $failed_count++;
                    }

                } else {
                    // INSERT new product
                    $insert_data = [
                        'sku'               => $product_data['sku'],
                        'product_name'      => $product_data['product_name'],
                        'product_slug'      => $this->slug_generator->make_unique($product_data['product_name']),
                        'category_id'       => $product_data['category_id'],
                        'brand_id'          => $product_data['brand_id'],
                        'price'             => $product_data['price'],
                        'stock'             => $product_data['stock'],
                        'description'       => null,
                        'specifications'    => null,
                        'discount_price'    => null,
                        'weight'            => 0,
                        'main_image'        => null,
                        'is_featured'       => 0,
                        'is_active'         => 1,
                        'views'             => 0,
                        'created_at'        => date('Y-m-d H:i:s'),
                        'updated_at'        => date('Y-m-d H:i:s')
                    ];

                    if ($this->Product_import_model->insert_product($insert_data)) {
                        $inserted_count++;
                        $success_count++;
                    } else {
                        $errors[] = "Baris {$row}: Gagal insert produk {$colC}";
                        $failed_count++;
                    }
                }
            }
        }

        // Prepare response
        return [
            'status' => true,
            'message' => 'Import selesai!',
            'data' => [
                'total_rows'        => $total_rows,
                'success_count'     => $success_count,
                'failed_count'      => $failed_count,
                'inserted_count'    => $inserted_count,
                'updated_count'     => $updated_count,
                'errors'            => array_slice($errors, 0, 50) // Limit errors to first 50
            ]
        ];
    }

    /**
     * Download sample Excel template
     */
    public function download_template()
    {
        $file_path = FCPATH . 'import_daftar_barang_dan_jasa_upgadget.xlsx';

        if (file_exists($file_path)) {
            // Force download
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="template_import_produk.xlsx"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));

            readfile($file_path);
            exit;
        } else {
            show_404();
        }
    }

    /**
     * Generate preview data from Excel and save to temporary table
     *
     * @param string $file_path Path to Excel file
     * @return array Preview data with session_id
     */
    private function generate_preview($file_path)
    {
        // Load PHPSpreadsheet
        $vendor_path = FCPATH . 'vendor/autoload.php';
        if (!file_exists($vendor_path)) {
            $vendor_path = dirname(FCPATH) . '/vendor/autoload.php';
        }

        if (!file_exists($vendor_path)) {
            throw new Exception('Composer autoload not found. Please run: composer install');
        }

        require_once $vendor_path;

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        // Generate unique session ID
        $session_id = 'import_' . date('YmdHis') . '_' . uniqid();

        // Clean old temp data (older than 1 hour)
        $this->db->where('created_at <', date('Y-m-d H:i:s', strtotime('-1 hour')));
        $this->db->delete('product_import_temp');

        // Initialize counters
        $total_rows = 0;
        $valid_count = 0;
        $invalid_count = 0;
        $new_count = 0;
        $update_count = 0;

        // Track current category
        $current_category = '';
        $current_category_id = null;

        // Start from row 6 (after header at row 5)
        for ($row = 6; $row <= $highestRow; $row++) {
            // Get cell values
            $colB = trim($sheet->getCell('B' . $row)->getCalculatedValue());
            $colC = trim($sheet->getCell('C' . $row)->getCalculatedValue()); // SKU
            $colE = trim($sheet->getCell('E' . $row)->getCalculatedValue()); // Product Name
            $colJ = $sheet->getCell('J' . $row)->getCalculatedValue(); // Price
            $colL = $sheet->getCell('L' . $row)->getCalculatedValue(); // Stock

            // Detect category
            if (!empty($colB) && empty($colC)) {
                $category_name = $colB;

                // Skip header/footer rows
                $skip_keywords = ['ACCURATE', 'Cabang', 'Daftar Barang', 'Halaman',
                                 'Per Tgl', 'Tercetak', 'UP.GADGET', 'Kode Barang', 'UP.GADGET'];

                $should_skip = false;
                foreach ($skip_keywords as $keyword) {
                    if (stripos($category_name, $keyword) !== false) {
                        $should_skip = true;
                        break;
                    }
                }

                if (!$should_skip && strlen($category_name) > 0) {
                    $current_category = $category_name;
                    $current_category_id = $this->Product_import_model->map_category($current_category);
                }

                continue; // Skip to next row
            }

            // Process product row
            if (!empty($colC)) {
                $total_rows++;

                // Prepare product data
                $product_data = [
                    'sku'           => $colC,
                    'product_name'  => $colE,
                    'price'         => is_numeric($colJ) ? floatval($colJ) : 0,
                    'stock'         => is_numeric($colL) ? intval($colL) : 0,
                    'category_id'   => $current_category_id,
                    'brand_id'      => $this->Product_import_model->detect_brand($colE)
                ];

                // Validate data
                $validation = $this->Product_import_model->validate_product_data($product_data, $row);

                // Check if SKU exists
                $existing = $this->Product_import_model->check_sku_exists($colC);
                $status = $existing ? 'UPDATE' : 'NEW';

                if ($status == 'NEW') {
                    $new_count++;
                } else {
                    $update_count++;
                }

                // Generate slug
                $slug = $this->slug_generator->generate($colE);

                // Get brand and category names
                $brand_name = $this->Product_import_model->get_brand_name($product_data['brand_id']);
                $category_name = $current_category;

                // Prepare temp data
                $temp_data = [
                    'session_id'        => $session_id,
                    'row_number'        => $row,
                    'sku'               => $product_data['sku'],
                    'product_name'      => $product_data['product_name'],
                    'category_name'     => $category_name,
                    'category_id'       => $product_data['category_id'],
                    'brand_name'        => $brand_name,
                    'brand_id'          => $product_data['brand_id'],
                    'price'             => $product_data['price'],
                    'stock'             => $product_data['stock'],
                    'product_slug'      => $slug,
                    'status'            => $validation['valid'] ? $status : 'ERROR',
                    'validation_errors' => $validation['error'],
                    'is_valid'          => $validation['valid'] ? 1 : 0,
                    'created_at'        => date('Y-m-d H:i:s')
                ];

                // Insert to temp table
                $this->db->insert('product_import_temp', $temp_data);

                if ($validation['valid']) {
                    $valid_count++;
                } else {
                    $invalid_count++;
                }
            }
        }

        // Prepare response
        return [
            'status' => true,
            'message' => 'Preview berhasil dibuat',
            'data' => [
                'session_id'    => $session_id,
                'total_rows'    => $total_rows,
                'valid_count'   => $valid_count,
                'invalid_count' => $invalid_count,
                'new_count'     => $new_count,
                'update_count'  => $update_count
            ]
        ];
    }

    /**
     * Process import from temporary table
     *
     * @param string $session_id Session ID
     * @param array $selected_ids Selected temp record IDs
     * @return array Import results
     */
    private function process_import_from_temp($session_id, $selected_ids = null)
    {
        // Get data from temp table
        $this->db->where('session_id', $session_id);
        $this->db->where('is_valid', 1);

        if (!empty($selected_ids) && is_array($selected_ids)) {
            $this->db->where_in('id', $selected_ids);
        }

        $temp_data = $this->db->get('product_import_temp')->result();

        if (empty($temp_data)) {
            throw new Exception('No data to import');
        }

        // Initialize counters
        $total_rows = count($temp_data);
        $success_count = 0;
        $inserted_count = 0;
        $updated_count = 0;
        $failed_count = 0;
        $errors = [];

        // Process each record
        foreach ($temp_data as $item) {
            try {
                // Check if SKU exists
                $existing = $this->Product_import_model->check_sku_exists($item->sku);

                if ($existing) {
                    // UPDATE
                    $update_data = [
                        'product_name'  => $item->product_name,
                        'product_slug'  => $this->slug_generator->make_unique($item->product_name, $existing->product_id),
                        'category_id'   => $item->category_id,
                        'brand_id'      => $item->brand_id,
                        'price'         => $item->price,
                        'stock'         => $item->stock,
                        'updated_at'    => date('Y-m-d H:i:s')
                    ];

                    if ($this->Product_import_model->update_product($existing->product_id, $update_data)) {
                        $updated_count++;
                        $success_count++;
                    } else {
                        $errors[] = "Gagal update produk: {$item->sku}";
                        $failed_count++;
                    }

                } else {
                    // INSERT
                    $insert_data = [
                        'sku'               => $item->sku,
                        'product_name'      => $item->product_name,
                        'product_slug'      => $this->slug_generator->make_unique($item->product_name),
                        'category_id'       => $item->category_id,
                        'brand_id'          => $item->brand_id,
                        'price'             => $item->price,
                        'stock'             => $item->stock,
                        'description'       => null,
                        'specifications'    => null,
                        'discount_price'    => null,
                        'weight'            => 0,
                        'main_image'        => null,
                        'is_featured'       => 0,
                        'is_active'         => 1,
                        'views'             => 0,
                        'created_at'        => date('Y-m-d H:i:s'),
                        'updated_at'        => date('Y-m-d H:i:s')
                    ];

                    if ($this->Product_import_model->insert_product($insert_data)) {
                        $inserted_count++;
                        $success_count++;
                    } else {
                        $errors[] = "Gagal insert produk: {$item->sku}";
                        $failed_count++;
                    }
                }

            } catch (Exception $e) {
                $errors[] = "Error pada {$item->sku}: " . $e->getMessage();
                $failed_count++;
            }
        }

        // Clean up temp data for this session
        $this->db->where('session_id', $session_id);
        $this->db->delete('product_import_temp');

        // Prepare response
        return [
            'status' => true,
            'message' => 'Import selesai!',
            'data' => [
                'total_rows'        => $total_rows,
                'success_count'     => $success_count,
                'failed_count'      => $failed_count,
                'inserted_count'    => $inserted_count,
                'updated_count'     => $updated_count,
                'errors'            => array_slice($errors, 0, 50)
            ]
        ];
    }
}
