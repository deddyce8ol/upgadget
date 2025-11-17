<div class="page-content">
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Brand Management</h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#brandModal" id="addBrandBtn">
                            <i class="bi bi-plus-circle"></i> Add Brand
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?= $this->session->flashdata('message'); ?>

                    <div class="table-responsive">
                        <table class="table table-hover" id="brandTable">
                            <thead>
                                <tr>
                                    <th style="width: 40px;"><i class="bi bi-grip-vertical"></i></th>
                                    <th>ID</th>
                                    <th>Logo</th>
                                    <th>Brand Name</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-tbody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Load SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<!-- Custom styles for drag and drop -->
<style>
    .sortable-ghost {
        opacity: 0.4;
        background-color: #f8f9fa;
    }

    .drag-handle {
        cursor: move;
        user-select: none;
    }

    .drag-handle:hover i {
        color: #435ebe !important;
    }

    #sortable-tbody tr {
        transition: background-color 0.2s;
    }

    #sortable-tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>

<!-- Brand Modal -->
<div class="modal fade" id="brandModal" tabindex="-1" aria-labelledby="brandModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="brandForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="brandModalLabel">Add Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="brand_id" name="brand_id">

                    <div class="mb-3">
                        <label for="brand_name" class="form-label">Brand Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="brand_name" name="brand_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="brand_logo" class="form-label">Brand Logo</label>
                        <input type="file" class="form-control" id="brand_logo" name="brand_logo" accept="image/*">
                        <small class="text-muted d-block">
                            <i class="bi bi-info-circle"></i> Recommended size: <strong>350 × 122 px</strong>
                        </small>
                        <small class="text-muted">Max file size: 2MB. Formats: JPG, JPEG, PNG, GIF. Image will be auto-resized.</small>
                    </div>

                    <div id="currentLogo" class="mb-3" style="display: none;">
                        <label class="form-label">Current Logo</label><br>
                        <img id="currentLogoImg" src="" style="max-width: 350px; max-height: 122px; border: 1px solid #ddd; padding: 5px;">
                    </div>

                    <div id="previewLogo" class="mb-3" style="display: none;">
                        <label class="form-label">Preview</label><br>
                        <img id="previewLogoImg" src="" style="max-width: 350px; max-height: 122px; border: 1px solid #ddd; padding: 5px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#brandTable').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": "<?= base_url('admin/brand/get_data'); ?>",
                "type": "GET"
            },
            "columns": [{
                    "data": null,
                    "orderable": false,
                    "className": "drag-handle",
                    "render": function() {
                        return '<i class="bi bi-grip-vertical" style="cursor: move; font-size: 1.2em; color: #999;"></i>';
                    }
                },
                {
                    "data": 0
                },
                {
                    "data": 1
                },
                {
                    "data": 2
                },
                {
                    "data": 3
                },
                {
                    "data": 4,
                    "orderable": false
                }
            ],
            "ordering": false, // Disable DataTable sorting to maintain sort_order from backend
            "rowCallback": function(row, data) {
                $(row).attr('data-id', data[0]);
            }
        });

        // Initialize SortableJS for drag and drop
        var sortable = null;
        table.on('draw.dt', function() {
            var tbody = document.getElementById('sortable-tbody');
            if (sortable) {
                sortable.destroy();
            }

            sortable = new Sortable(tbody, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function(evt) {
                    // Get new order
                    var order = [];
                    $('#sortable-tbody tr').each(function() {
                        var brandId = $(this).attr('data-id');
                        if (brandId) {
                            order.push(brandId);
                        }
                    });

                    // Send to server
                    $.ajax({
                        url: '<?= base_url('admin/brand/update_order'); ?>',
                        type: 'POST',
                        data: {
                            order: order
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message,
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                                table.ajax.reload();
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Failed to update order', 'error');
                            table.ajax.reload();
                        }
                    });
                }
            });
        });

        // Add Brand Button
        $('#addBrandBtn').click(function() {
            $('#brandModalLabel').text('Add Brand');
            $('#brandForm')[0].reset();
            $('#brand_id').val('');
            $('#currentLogo').hide();
            $('#previewLogo').hide();
        });

        // Image preview on file select
        $('#brand_logo').on('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                // Check file size (2MB = 2097152 bytes)
                if (file.size > 2097152) {
                    Swal.fire('Error!', 'File size exceeds 2MB. Please select a smaller file.', 'error');
                    $(this).val('');
                    $('#previewLogo').hide();
                    return;
                }

                // Check file type
                var validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    Swal.fire('Error!', 'Invalid file type. Please select JPG, JPEG, PNG, or GIF.', 'error');
                    $(this).val('');
                    $('#previewLogo').hide();
                    return;
                }

                // Show preview
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewLogoImg').attr('src', e.target.result);
                    $('#previewLogo').show();
                };
                reader.readAsDataURL(file);
            } else {
                $('#previewLogo').hide();
            }
        });

        // Edit Brand
        $(document).on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            $.ajax({
                url: '<?= base_url('admin/brand/get_by_id/'); ?>' + id,
                type: 'GET',
                dataType: 'json',
                success: function(brand) {
                    $('#brandModalLabel').text('Edit Brand');
                    $('#brand_id').val(brand.brand_id);
                    $('#brand_name').val(brand.brand_name);

                    if (brand.brand_logo) {
                        $('#currentLogoImg').attr('src', '<?= base_url('uploads/brands/'); ?>' + brand.brand_logo);
                        $('#currentLogo').show();
                    } else {
                        $('#currentLogo').hide();
                    }

                    $('#brandModal').modal('show');
                }
            });
        });

        // Submit Form
        $('#brandForm').submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var id = $('#brand_id').val();
            var url = id ? '<?= base_url('admin/brand/update'); ?>' : '<?= base_url('admin/brand/create'); ?>';

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#brandModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Success!', response.message, 'success');
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Something went wrong!', 'error');
                }
            });
        });

        // Delete Brand
        $(document).on('click', '.delete-btn', function() {
            var id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('admin/brand/delete/'); ?>' + id,
                        type: 'POST',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                table.ajax.reload();
                                Swal.fire('Deleted!', response.message, 'success');
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        }
                    });
                }
            });
        });

        // Toggle Status
        $(document).on('click', '.toggle-status-btn', function() {
            var id = $(this).data('id');

            $.ajax({
                url: '<?= base_url('admin/brand/toggle_status/'); ?>' + id,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload();
                        Swal.fire('Success!', response.message, 'success');
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                }
            });
        });
    });
</script>
