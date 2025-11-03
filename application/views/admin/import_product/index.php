<div class="page-content">
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Import Products from CSV</h4>
                        <a href="<?= base_url('admin/product'); ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Products
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?= $this->session->flashdata('message'); ?>

                    <!-- Step 1: Upload CSV -->
                    <div id="uploadSection">
                        <div class="alert alert-info">
                            <h5 class="alert-heading"><i class="bi bi-info-circle"></i> CSV Format Requirements</h5>
                            <p>Your CSV file must have the following columns in order:</p>
                            <ul class="mb-0">
                                <li><strong>sku</strong> - Product SKU (unique identifier)</li>
                                <li><strong>price</strong> - Product price (numeric)</li>
                                <li><strong>stock</strong> - Stock quantity (numeric)</li>
                                <li><strong>product_name</strong> - Product name</li>
                                <li><strong>description</strong> - Product description</li>
                            </ul>
                            <hr>
                            <p class="mb-0"><strong>Note:</strong> Brand and Category will be automatically extracted from product name. Existing SKUs will be skipped.</p>
                        </div>

                        <div class="mb-3">
                            <label for="csv_file" class="form-label">Select CSV File</label>
                            <input type="file" class="form-control" id="csv_file" accept=".csv" required>
                            <small class="text-muted">Maximum file size: 10MB</small>
                        </div>

                        <button type="button" class="btn btn-primary" id="uploadBtn">
                            <i class="bi bi-upload"></i> Upload & Preview
                        </button>
                    </div>

                    <!-- Step 2: Preview Data -->
                    <div id="previewSection" style="display: none;">
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> CSV file uploaded successfully!
                        </div>

                        <div class="mb-3">
                            <h5>Preview (First 50 rows)</h5>
                            <p><strong>Total rows in CSV:</strong> <span id="totalRows">0</span></p>
                        </div>

                        <div class="table-responsive mb-3" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-bordered" id="previewTable">
                                <thead class="table-light sticky-top">
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success" id="startImportBtn">
                                <i class="bi bi-play-fill"></i> Start Import
                            </button>
                            <button type="button" class="btn btn-secondary" id="cancelBtn">
                                <i class="bi bi-x-circle"></i> Cancel
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Processing -->
                    <div id="processingSection" style="display: none;">
                        <div class="alert alert-info">
                            <i class="bi bi-hourglass-split"></i> Import in progress... Please do not close this page.
                        </div>

                        <div class="mb-3">
                            <h5>Progress</h5>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                     role="progressbar"
                                     id="progressBar"
                                     style="width: 0%">
                                    0%
                                </div>
                            </div>
                            <p class="mt-2 mb-0">
                                <strong>Status:</strong> <span id="progressStatus">Initializing...</span>
                            </p>
                            <p class="mb-0">
                                <strong>Processed:</strong> <span id="processedCount">0</span> / <span id="totalCount">0</span>
                            </p>
                        </div>

                        <div id="batchStats" class="mb-3">
                            <h6>Current Statistics:</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check-circle text-success"></i> Success: <strong id="successCount">0</strong></li>
                                <li><i class="bi bi-skip-forward text-warning"></i> Skipped: <strong id="skippedCount">0</strong></li>
                                <li><i class="bi bi-x-circle text-danger"></i> Failed: <strong id="failedCount">0</strong></li>
                                <li><i class="bi bi-plus-circle text-info"></i> New Brands Created: <strong id="brandsCreatedCount">0</strong></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Step 4: Results -->
                    <div id="resultsSection" style="display: none;">
                        <div class="alert alert-success">
                            <h5 class="alert-heading"><i class="bi bi-check-circle"></i> Import Completed!</h5>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body">
                                <h5>Import Summary</h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-light rounded">
                                            <h2 class="text-primary mb-0" id="finalTotal">0</h2>
                                            <small class="text-muted">Total Processed</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-light rounded">
                                            <h2 class="text-success mb-0" id="finalSuccess">0</h2>
                                            <small class="text-muted">Successfully Imported</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-light rounded">
                                            <h2 class="text-warning mb-0" id="finalSkipped">0</h2>
                                            <small class="text-muted">Skipped (Duplicate SKU)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-light rounded">
                                            <h2 class="text-danger mb-0" id="finalFailed">0</h2>
                                            <small class="text-muted">Failed</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="text-center p-3 bg-light rounded">
                                            <h2 class="text-info mb-0" id="finalBrandsCreated">0</h2>
                                            <small class="text-muted">New Brands Auto-Created</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="errorSection" style="display: none;">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong id="errorCount">0</strong> error(s) occurred during import.
                                <a href="<?= base_url('admin/import_product/download_errors'); ?>" class="btn btn-sm btn-warning ms-2">
                                    <i class="bi bi-download"></i> Download Error Log
                                </a>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="<?= base_url('admin/product'); ?>" class="btn btn-primary">
                                <i class="bi bi-list"></i> View Products
                            </a>
                            <a href="<?= base_url('admin/import_product'); ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-repeat"></i> Import Another File
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    let importData = {
        totalRows: 0,
        totalBatches: 0,
        batchSize: 0,
        currentBatch: 0
    };

    // Upload CSV file
    $('#uploadBtn').click(function() {
        const fileInput = $('#csv_file')[0];

        if (!fileInput.files.length) {
            Swal.fire('Error', 'Please select a CSV file', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('csv_file', fileInput.files[0]);

        $(this).prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Uploading...');

        $.ajax({
            url: '<?= base_url("admin/import_product/upload"); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;

                if (data.success) {
                    displayPreview(data.data.preview);
                    $('#uploadSection').hide();
                    $('#previewSection').show();
                } else {
                    Swal.fire('Error', data.message, 'error');
                    $('#uploadBtn').prop('disabled', false).html('<i class="bi bi-upload"></i> Upload & Preview');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to upload file', 'error');
                $('#uploadBtn').prop('disabled', false).html('<i class="bi bi-upload"></i> Upload & Preview');
            }
        });
    });

    // Display preview table
    function displayPreview(preview) {
        $('#totalRows').text(preview.total_rows.toLocaleString());

        // Build table headers
        let headerHtml = '<tr>';
        preview.headers.forEach(header => {
            headerHtml += `<th>${header}</th>`;
        });
        headerHtml += '</tr>';
        $('#previewTable thead').html(headerHtml);

        // Build table rows
        let bodyHtml = '';
        preview.rows.forEach(row => {
            bodyHtml += '<tr>';
            row.forEach(cell => {
                // Truncate long text for display
                const displayText = cell && cell.length > 100 ? cell.substring(0, 100) + '...' : cell;
                bodyHtml += `<td>${displayText || '-'}</td>`;
            });
            bodyHtml += '</tr>';
        });
        $('#previewTable tbody').html(bodyHtml);
    }

    // Start import process
    $('#startImportBtn').click(function() {
        Swal.fire({
            title: 'Start Import?',
            text: 'This will import all products from the CSV file.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Start Import',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                startImport();
            }
        });
    });

    // Initialize and start import
    function startImport() {
        $('#previewSection').hide();
        $('#processingSection').show();
        $('#progressStatus').text('Initializing import...');

        $.ajax({
            url: '<?= base_url("admin/import_product/start_import"); ?>',
            type: 'POST',
            success: function(response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;

                if (data.success) {
                    importData.totalRows = data.data.total_rows;
                    importData.totalBatches = data.data.total_batches;
                    importData.batchSize = data.data.batch_size;
                    importData.currentBatch = 1;

                    $('#totalCount').text(importData.totalRows.toLocaleString());

                    // Start processing first batch
                    processBatch(1);
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to initialize import', 'error');
            }
        });
    }

    // Process single batch
    function processBatch(batchNumber) {
        $('#progressStatus').text(`Processing batch ${batchNumber} of ${importData.totalBatches}...`);

        $.ajax({
            url: '<?= base_url("admin/import_product/process_batch"); ?>',
            type: 'POST',
            data: { batch_number: batchNumber },
            success: function(response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;

                if (data.success) {
                    // Update statistics
                    updateStats(data.data.cumulative_stats);

                    // Update progress bar
                    const progress = Math.round((data.data.cumulative_stats.total / importData.totalRows) * 100);
                    $('#progressBar').css('width', progress + '%').text(progress + '%');
                    $('#processedCount').text(data.data.cumulative_stats.total.toLocaleString());

                    // Process next batch or show results
                    if (data.data.has_more && batchNumber < importData.totalBatches) {
                        importData.currentBatch++;
                        processBatch(importData.currentBatch);
                    } else {
                        showResults();
                    }
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to process batch ' + batchNumber, 'error');
            }
        });
    }

    // Update statistics display
    function updateStats(stats) {
        $('#successCount').text(stats.success.toLocaleString());
        $('#skippedCount').text(stats.skipped.toLocaleString());
        $('#failedCount').text(stats.failed.toLocaleString());
        $('#brandsCreatedCount').text(stats.brands_created.toLocaleString());
    }

    // Show final results
    function showResults() {
        $.ajax({
            url: '<?= base_url("admin/import_product/get_results"); ?>',
            type: 'GET',
            success: function(response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;

                if (data.success) {
                    const stats = data.data;

                    $('#finalTotal').text(stats.total.toLocaleString());
                    $('#finalSuccess').text(stats.success.toLocaleString());
                    $('#finalSkipped').text(stats.skipped.toLocaleString());
                    $('#finalFailed').text(stats.failed.toLocaleString());
                    $('#finalBrandsCreated').text(stats.brands_created.toLocaleString());

                    if (stats.errors.length > 0) {
                        $('#errorCount').text(stats.errors.length);
                        $('#errorSection').show();
                    }

                    $('#processingSection').hide();
                    $('#resultsSection').show();

                    Swal.fire({
                        title: 'Import Complete!',
                        text: `Successfully imported ${stats.success} products`,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                }
            }
        });
    }

    // Cancel button
    $('#cancelBtn').click(function() {
        Swal.fire({
            title: 'Cancel Import?',
            text: 'The uploaded file will be removed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Cancel',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= base_url("admin/import_product/cancel"); ?>';
            }
        });
    });
});
</script>

<style>
.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}
</style>
