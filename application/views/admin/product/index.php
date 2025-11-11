<div class="page-content">
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Product Management</h4>
                        <div class="btn-group" role="group">
                            <a href="<?= base_url('admin/product_import'); ?>" class="btn btn-success">
                                <i class="bi bi-file-earmark-arrow-up"></i> Import Products
                            </a>
                            <a href="<?= base_url('admin/product/create'); ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add Product
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?= $this->session->flashdata('message'); ?>

                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="filterName" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" id="filterName" placeholder="Cari nama produk...">
                        </div>
                        <div class="col-md-3">
                            <label for="filterCategory" class="form-label">Kategori</label>
                            <select class="form-select" id="filterCategory">
                                <option value="">Semua Kategori</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filterBrand" class="form-label">Brand</label>
                            <select class="form-select" id="filterBrand">
                                <option value="">Semua Brand</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filterStatus" class="form-label">Status</label>
                            <select class="form-select" id="filterStatus">
                                <option value="">Semua Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="productTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Price</th>
                                    <th>Stock</th>
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

<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#productTable').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": "<?= base_url('admin/product/get_data'); ?>",
                "type": "GET",
                "data": function(d) {
                    d.filter_name = $('#filterName').val();
                    d.filter_category = $('#filterCategory').val();
                    d.filter_brand = $('#filterBrand').val();
                    d.filter_status = $('#filterStatus').val();
                }
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
                    "data": 4
                },
                {
                    "data": 5
                },
                {
                    "data": 6
                },
                {
                    "data": 7
                },
                {
                    "data": 8,
                    "orderable": false
                }
            ],
            "order": [
                [0, 'desc']
            ]
        });

        // Initialize Select2 for filters
        $('#filterCategory').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Kategori',
            allowClear: true,
            width: '100%'
        });

        $('#filterBrand').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Brand',
            allowClear: true,
            width: '100%'
        });

        // Load Categories
        $.ajax({
            url: '<?= base_url('admin/product/get_categories'); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $.each(response.data, function(index, category) {
                        $('#filterCategory').append($('<option>', {
                            value: category.id,
                            text: category.name
                        }));
                    });
                    // Trigger Select2 to update
                    $('#filterCategory').trigger('change.select2');
                }
            }
        });

        // Load Brands
        $.ajax({
            url: '<?= base_url('admin/product/get_brands'); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $.each(response.data, function(index, brand) {
                        $('#filterBrand').append($('<option>', {
                            value: brand.brand_id,
                            text: brand.brand_name
                        }));
                    });
                    // Trigger Select2 to update
                    $('#filterBrand').trigger('change.select2');
                }
            }
        });

        // Filter event handlers
        $('#filterName').on('keyup', function() {
            table.ajax.reload();
        });

        $('#filterCategory, #filterBrand, #filterStatus').on('change', function() {
            table.ajax.reload();
        });

        // Delete Product
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
                        url: '<?= base_url('admin/product/delete/'); ?>' + id,
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
                url: '<?= base_url('admin/product/toggle_status/'); ?>' + id,
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
