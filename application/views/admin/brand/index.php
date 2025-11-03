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
                                    <th>ID</th>
                                    <th>Logo</th>
                                    <th>Brand Name</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

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
                        <small class="text-muted">Max size: 2MB. Supported formats: JPG, JPEG, PNG, GIF</small>
                    </div>

                    <div id="currentLogo" class="mb-3" style="display: none;">
                        <label class="form-label">Current Logo</label><br>
                        <img id="currentLogoImg" src="" style="max-width: 100px; max-height: 100px;">
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
            "order": [
                [0, 'desc']
            ]
        });

        // Add Brand Button
        $('#addBrandBtn').click(function() {
            $('#brandModalLabel').text('Add Brand');
            $('#brandForm')[0].reset();
            $('#brand_id').val('');
            $('#currentLogo').hide();
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
