<div class="page-content">
    <section class="row">
        <div class="col-12">
            <!-- Page Heading -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3><i class="bi bi-file-earmark-arrow-up"></i> Import Produk</h3>
                    <p class="text-muted">Import produk dari file Excel POS Accurate</p>
                </div>
                <a href="<?php echo base_url('admin/product_import/download_template'); ?>"
                   class="btn btn-info">
                    <i class="bi bi-download"></i> Download Template Excel
                </a>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card shadow-sm border-start border-primary border-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Produk</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800"><?php echo number_format($stats['total_products']); ?></div>
                                </div>
                                <div class="ms-3"><i class="bi bi-box-seam text-gray-300" style="font-size: 2rem;"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card shadow-sm border-start border-success border-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-xs fw-bold text-success text-uppercase mb-1">Produk dengan SKU</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800"><?php echo number_format($stats['products_with_sku']); ?></div>
                                </div>
                                <div class="ms-3"><i class="bi bi-check-circle text-gray-300" style="font-size: 2rem;"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card shadow-sm border-start border-warning border-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-xs fw-bold text-warning text-uppercase mb-1">Produk tanpa SKU</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800"><?php echo number_format($stats['products_without_sku']); ?></div>
                                </div>
                                <div class="ms-3"><i class="bi bi-exclamation-triangle text-gray-300" style="font-size: 2rem;"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card shadow-sm border-start border-info border-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-xs fw-bold text-info text-uppercase mb-1">Total Kategori</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800"><?php echo number_format($stats['total_categories']); ?></div>
                                </div>
                                <div class="ms-3"><i class="bi bi-tags text-gray-300" style="font-size: 2rem;"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import Form -->
            <div class="row" id="upload-section">
                <div class="col-lg-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="bi bi-upload"></i> Upload File Excel</h5>
                        </div>
                        <div class="card-body">
                            <form id="import-form" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="excel_file" class="form-label">Pilih File Excel (.xlsx / .xls)</label>
                                    <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
                                    <small class="form-text text-muted">Format file: .xlsx atau .xls | Maksimal: 10MB</small>
                                </div>

                                <button type="submit" class="btn btn-primary" id="btn-import">
                                    <i class="bi bi-file-earmark-arrow-up"></i> Upload & Preview
                                </button>
                            </form>

                            <!-- Progress bar -->
                            <div id="progress-container" class="mt-3" style="display: none;">
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%">
                                         Memproses file...
                                    </div>
                                </div>
                            </div>

                            <!-- Instructions -->
                            <div class="alert alert-info mt-4">
                                <h5 class="alert-heading"><i class="bi bi-info-circle"></i> Instruksi Import</h5>
                                <hr>
                                <ol class="mb-0">
                                    <li>Export data produk dari sistem POS Accurate dalam format Excel (.xlsx)</li>
                                    <li>Pastikan format file sesuai dengan template (bisa download template di atas)</li>
                                    <li>Upload file Excel menggunakan form di atas</li>
                                    <li>Review data di preview table</li>
                                    <li>Pilih produk yang ingin diimport (uncheck yang tidak ingin diimport)</li>
                                    <li>Klik "Konfirmasi Import" untuk memproses</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Section (Hidden by default) -->
            <div class="row mt-4" id="preview-section" style="display: none;">
                <div class="col-lg-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0"><i class="bi bi-eye"></i> Preview Data Import</h5>
                                <button type="button" class="btn btn-sm btn-light" onclick="cancelPreview()">
                                    <i class="bi bi-x-circle"></i> Batal
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Summary -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="alert alert-info mb-0">
                                        <strong id="preview-total">0</strong> Total Baris
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="alert alert-success mb-0">
                                        <strong id="preview-new">0</strong> Produk Baru
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="alert alert-warning mb-0">
                                        <strong id="preview-update">0</strong> Produk Update
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="alert alert-danger mb-0">
                                        <strong id="preview-error">0</strong> Error
                                    </div>
                                </div>
                            </div>

                            <!-- Checkbox Controls -->
                            <div class="mb-3">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                                    <i class="bi bi-check-square"></i> Pilih Semua
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                                    <i class="bi bi-square"></i> Batal Pilih Semua
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="selectValid()">
                                    <i class="bi bi-check2-square"></i> Pilih Valid Saja
                                </button>
                            </div>

                            <!-- Preview Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-sm" id="preview-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40"><input type="checkbox" id="check-all" onchange="toggleCheckAll(this)"></th>
                                            <th width="60">#</th>
                                            <th width="150">SKU</th>
                                            <th>Nama Produk</th>
                                            <th width="120">Kategori</th>
                                            <th width="100">Brand</th>
                                            <th width="120">Harga</th>
                                            <th width="80">Stock</th>
                                            <th width="100">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="preview-tbody">
                                        <!-- Data will be loaded here -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Import Button -->
                            <div class="mt-3">
                                <button type="button" class="btn btn-success btn-lg" id="btn-confirm-import" onclick="confirmImport()">
                                    <i class="bi bi-check-circle"></i> Konfirmasi Import (<span id="selected-count">0</span> dipilih)
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="cancelPreview()">
                                    <i class="bi bi-x-circle"></i> Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Import Results Modal -->
<div class="modal fade" id="resultsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-check-circle"></i> Hasil Import</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modal-content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise"></i> Import Lagi
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
var currentSessionId = null;

$(document).ready(function() {
    // Handle form submission
    $('#import-form').on('submit', function(e) {
        e.preventDefault();

        var fileInput = $('#excel_file')[0];
        if (fileInput.files.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'File Tidak Dipilih',
                text: 'Silakan pilih file Excel terlebih dahulu'
            });
            return;
        }

        var file = fileInput.files[0];
        var fileSize = file.size / 1024 / 1024; // in MB

        if (fileSize > 10) {
            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar',
                text: 'Ukuran file maksimal 10MB'
            });
            return;
        }

        processUpload();
    });
});

function processUpload() {
    var formData = new FormData($('#import-form')[0]);

    // Show progress
    $('#progress-container').show();
    $('#btn-import').prop('disabled', true);
    $('#excel_file').prop('disabled', true);

    $.ajax({
        url: '<?php echo base_url("admin/product_import/upload"); ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            $('#progress-container').hide();
            $('#btn-import').prop('disabled', false);
            $('#excel_file').prop('disabled', false);

            if (response.status) {
                // Success - Show preview
                currentSessionId = response.data.session_id;
                showPreviewSummary(response.data);
                loadPreviewData(currentSessionId);
            } else {
                // Error
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Gagal',
                    text: response.error || response.message
                });
            }
        },
        error: function(xhr, status, error) {
            $('#progress-container').hide();
            $('#btn-import').prop('disabled', false);
            $('#excel_file').prop('disabled', false);

            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: 'Gagal melakukan upload. Silakan coba lagi.'
            });
        }
    });
}

function showPreviewSummary(data) {
    $('#preview-total').text(data.total_rows);
    $('#preview-new').text(data.new_count);
    $('#preview-update').text(data.update_count);
    $('#preview-error').text(data.invalid_count);

    $('#upload-section').hide();
    $('#preview-section').show();
}

function loadPreviewData(sessionId) {
    $.ajax({
        url: '<?php echo base_url("admin/product_import/get_preview_data"); ?>',
        type: 'GET',
        data: { session_id: sessionId },
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                renderPreviewTable(response.data);
                updateSelectedCount();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal memuat data preview'
            });
        }
    });
}

function renderPreviewTable(data) {
    var tbody = $('#preview-tbody');
    tbody.empty();

    $.each(data, function(index, item) {
        var statusBadge = '';
        var rowClass = '';
        var checked = item.is_valid == 1 ? 'checked' : '';
        var disabled = item.is_valid == 0 ? 'disabled' : '';

        if (item.status == 'NEW') {
            statusBadge = '<span class="badge bg-success">NEW</span>';
        } else if (item.status == 'UPDATE') {
            statusBadge = '<span class="badge bg-warning">UPDATE</span>';
        } else if (item.status == 'ERROR') {
            statusBadge = '<span class="badge bg-danger">ERROR</span>';
            rowClass = 'table-danger';
        }

        var errorHtml = item.validation_errors ? '<br><small class="text-danger">' + item.validation_errors + '</small>' : '';

        var row = '<tr class="' + rowClass + '">' +
            '<td><input type="checkbox" class="preview-checkbox" data-id="' + item.id + '" ' + checked + ' ' + disabled + '></td>' +
            '<td>' + item.row_number + '</td>' +
            '<td><small>' + (item.sku || '-') + '</small></td>' +
            '<td>' + (item.product_name || '-') + errorHtml + '</td>' +
            '<td><small>' + (item.category_name || '-') + '</small></td>' +
            '<td><small>' + (item.brand_name || '-') + '</small></td>' +
            '<td class="text-end"><small>Rp ' + formatNumber(item.price) + '</small></td>' +
            '<td class="text-center">' + item.stock + '</td>' +
            '<td class="text-center">' + statusBadge + '</td>' +
            '</tr>';

        tbody.append(row);
    });

    // Add change event to checkboxes
    $('.preview-checkbox').on('change', updateSelectedCount);
}

function formatNumber(num) {
    return parseFloat(num).toLocaleString('id-ID');
}

function toggleCheckAll(checkbox) {
    $('.preview-checkbox:not(:disabled)').prop('checked', checkbox.checked);
    updateSelectedCount();
}

function selectAll() {
    $('.preview-checkbox:not(:disabled)').prop('checked', true);
    $('#check-all').prop('checked', true);
    updateSelectedCount();
}

function deselectAll() {
    $('.preview-checkbox').prop('checked', false);
    $('#check-all').prop('checked', false);
    updateSelectedCount();
}

function selectValid() {
    $('.preview-checkbox:not(:disabled)').prop('checked', true);
    updateSelectedCount();
}

function updateSelectedCount() {
    var count = $('.preview-checkbox:checked').length;
    $('#selected-count').text(count);

    if (count > 0) {
        $('#btn-confirm-import').prop('disabled', false);
    } else {
        $('#btn-confirm-import').prop('disabled', true);
    }
}

function cancelPreview() {
    $('#preview-section').hide();
    $('#upload-section').show();
    $('#import-form')[0].reset();
    currentSessionId = null;
}

function confirmImport() {
    var selectedIds = [];
    $('.preview-checkbox:checked').each(function() {
        selectedIds.push($(this).data('id'));
    });

    if (selectedIds.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Ada Data Dipilih',
            text: 'Pilih minimal 1 produk untuk diimport'
        });
        return;
    }

    Swal.fire({
        title: 'Konfirmasi Import',
        text: 'Import ' + selectedIds.length + ' produk?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Import!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            processConfirmImport(selectedIds);
        }
    });
}

function processConfirmImport(selectedIds) {
    // Show loading
    Swal.fire({
        title: 'Memproses Import...',
        html: 'Mohon tunggu',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '<?php echo base_url("admin/product_import/confirm_import"); ?>',
        type: 'POST',
        data: {
            session_id: currentSessionId,
            selected_ids: selectedIds
        },
        dataType: 'json',
        success: function(response) {
            Swal.close();

            if (response.status) {
                showResults(response);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Import Gagal',
                    text: response.error || response.message
                });
            }
        },
        error: function() {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: 'Gagal melakukan import'
            });
        }
    });
}

function showResults(response) {
    var data = response.data;

    var html = '<div class="row">';
    html += '<div class="col-md-12">';
    html += '<h5><i class="bi bi-pie-chart"></i> Ringkasan Import</h5>';
    html += '<table class="table table-bordered">';
    html += '<tr><th width="200">Total Baris Diproses</th><td>' + data.total_rows + '</td></tr>';
    html += '<tr class="table-success"><th>Berhasil</th><td><strong>' + data.success_count + '</strong></td></tr>';
    html += '<tr class="table-info"><th>Produk Baru (Insert)</th><td>' + data.inserted_count + '</td></tr>';
    html += '<tr class="table-warning"><th>Produk Update</th><td>' + data.updated_count + '</td></tr>';
    html += '<tr class="table-danger"><th>Gagal</th><td>' + data.failed_count + '</td></tr>';
    html += '</table>';

    if (data.errors && data.errors.length > 0) {
        html += '<h5 class="mt-3"><i class="bi bi-exclamation-triangle text-danger"></i> Error Details</h5>';
        html += '<div class="alert alert-danger">';
        html += '<ul class="mb-0">';
        data.errors.forEach(function(error) {
            html += '<li>' + error + '</li>';
        });
        html += '</ul>';
        if (data.failed_count > 50) {
            html += '<p class="mt-2 mb-0"><em>Menampilkan 50 error pertama dari total ' + data.failed_count + ' error</em></p>';
        }
        html += '</div>';
    }

    html += '</div>';
    html += '</div>';

    $('#modal-content').html(html);

    // Use Bootstrap 5 modal
    var myModal = new bootstrap.Modal(document.getElementById('resultsModal'));
    myModal.show();

    // Reset
    $('#preview-section').hide();
    $('#upload-section').show();
    $('#import-form')[0].reset();
    currentSessionId = null;
}
</script>
