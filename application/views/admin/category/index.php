<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">

<div class="page-content">
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Category Management</h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal" id="addCategoryBtn">
                            <i class="bi bi-plus-circle"></i> Add Category
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?= $this->session->flashdata('message'); ?>

                    <div class="table-responsive">
                        <table class="table table-hover" id="categoryTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Icon</th>
                                    <th>Category Name</th>
                                    <th>Slug</th>
                                    <th>Description</th>
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

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="categoryForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="category_id" name="category_id">

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            <small class="text-muted">Displayed on category banner page</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="icon_path" class="form-label">Category Icon</label>
                            <input type="file" class="form-control" id="icon_path" name="icon_path" accept="image/*">
                            <small class="text-muted">Recommended: 200×200px. Max: 2MB</small>

                            <div id="currentIcon" class="mt-2" style="display: none;">
                                <label class="form-label text-muted">Current Icon:</label><br>
                                <img id="currentIconImg" src="" style="max-width: 80px; max-height: 80px;" class="img-thumbnail">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="banner_image" class="form-label">Category Banner</label>
                            <input type="file" class="form-control" id="banner_image" name="banner_image" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <small class="text-muted">Recommended: 1920×400px (4.8:1). Max: 5MB</small>

                            <div id="currentBanner" class="mt-2" style="display: none;">
                                <label class="form-label text-muted">Current Banner:</label><br>
                                <img id="currentBannerImg" src="" style="max-width: 200px; max-height: 100px; object-fit: cover;" class="img-thumbnail">
                                <p class="text-muted mt-1"><small>Leave empty to keep current banner</small></p>
                            </div>
                        </div>
                    </div>

                    <!-- Banner Crop Area -->
                    <div id="bannerPreviewContainer" class="mb-3" style="display: none;">
                        <label class="form-label">Crop Banner (Ratio 4.8:1 - 1920×400px)</label>
                        <div style="max-height: 450px; overflow: hidden; border: 1px solid #dee2e6; border-radius: 4px;">
                            <img id="bannerPreview" style="max-width: 100%; display: block;">
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="zoomIn">
                                <i class="bi bi-zoom-in"></i> Zoom In
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="zoomOut">
                                <i class="bi bi-zoom-out"></i> Zoom Out
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="rotateLeft">
                                <i class="bi bi-arrow-counterclockwise"></i> Rotate Left
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="rotateRight">
                                <i class="bi bi-arrow-clockwise"></i> Rotate Right
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="resetCrop">
                                <i class="bi bi-arrow-repeat"></i> Reset
                            </button>
                        </div>
                        <div class="alert alert-info mt-2" role="alert">
                            <small>
                                <i class="bi bi-info-circle"></i>
                                Banner akan di-resize ke 1920×400px dan dikompres ke < 400KB secara otomatis.
                                Drag untuk posisi, scroll untuk zoom.
                            </small>
                        </div>
                    </div>

                    <div class="alert alert-primary" role="alert">
                        <strong><i class="bi bi-info-circle"></i> Tips:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>Icon</strong>: Small square image for category display (e.g., 200×200px)</li>
                            <li><strong>Banner</strong>: Large banner for category page header (1920×400px, auto-resized & compressed to ~400KB)</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="bi bi-save"></i> Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<script>
    $(document).ready(function() {
        let cropper = null;

        // Initialize DataTable
        var table = $('#categoryTable').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": "<?= base_url('admin/category/get_data'); ?>",
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
                    "data": 4
                },
                {
                    "data": 5
                },
                {
                    "data": 6,
                    "orderable": false
                }
            ],
            "order": [
                [0, 'desc']
            ]
        });

        // Add Category Button
        $('#addCategoryBtn').click(function() {
            $('#categoryModalLabel').text('Add Category');
            $('#categoryForm')[0].reset();
            $('#category_id').val('');
            $('#currentIcon').hide();
            $('#currentBanner').hide();
            $('#bannerPreviewContainer').hide();
            $('#banner_image').prop('required', false);
            destroyCropper();
        });

        // Initialize Cropper when banner image is selected
        $('#banner_image').change(function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire('Error!', 'File size must be less than 5MB!', 'error');
                    $(this).val('');
                    return;
                }

                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    Swal.fire('Error!', 'Only JPG, PNG, and WebP formats are allowed!', 'error');
                    $(this).val('');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {
                    $('#bannerPreview').attr('src', event.target.result);
                    $('#bannerPreviewContainer').show();
                    $('#currentBanner').hide();

                    // Destroy previous cropper instance
                    destroyCropper();

                    // Initialize new cropper
                    const image = document.getElementById('bannerPreview');
                    cropper = new Cropper(image, {
                        aspectRatio: 4.8, // 1920:400 = 4.8:1
                        viewMode: 2,
                        dragMode: 'move',
                        autoCropArea: 1,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: false,
                        toggleDragModeOnDblclick: false,
                        minContainerWidth: 200,
                        minContainerHeight: 200,
                        ready: function() {
                            // Set initial zoom to fit
                            this.cropper.reset();
                        }
                    });
                };
                reader.readAsDataURL(file);
            }
        });

        // Cropper control buttons
        $('#zoomIn').click(function() {
            if (cropper) cropper.zoom(0.1);
        });

        $('#zoomOut').click(function() {
            if (cropper) cropper.zoom(-0.1);
        });

        $('#rotateLeft').click(function() {
            if (cropper) cropper.rotate(-90);
        });

        $('#rotateRight').click(function() {
            if (cropper) cropper.rotate(90);
        });

        $('#resetCrop').click(function() {
            if (cropper) cropper.reset();
        });

        // Destroy cropper function
        function destroyCropper() {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        }

        // Edit Category
        $(document).on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            $.ajax({
                url: '<?= base_url('admin/category/get_by_id/'); ?>' + id,
                type: 'GET',
                dataType: 'json',
                success: function(category) {
                    $('#categoryModalLabel').text('Edit Category');
                    $('#category_id').val(category.id);
                    $('#name').val(category.name);
                    $('#description').val(category.description);

                    // Make banner field optional when editing
                    $('#banner_image').prop('required', false);
                    $('#bannerPreviewContainer').hide();
                    destroyCropper();

                    // Show current icon if exists
                    if (category.icon_path) {
                        $('#currentIconImg').attr('src', '<?= base_url('uploads/categories/'); ?>' + category.icon_path);
                        $('#currentIcon').show();
                    } else {
                        $('#currentIcon').hide();
                    }

                    // Show current banner if exists
                    if (category.banner_image) {
                        $('#currentBannerImg').attr('src', '<?= base_url('uploads/categories/'); ?>' + category.banner_image);
                        $('#currentBanner').show();
                    } else {
                        $('#currentBanner').hide();
                    }

                    $('#categoryModal').modal('show');
                }
            });
        });

        // Submit Form
        $('#categoryForm').submit(function(e) {
            e.preventDefault();

            // If cropper is active, get cropped image data
            if (cropper) {
                const canvas = cropper.getCroppedCanvas({
                    width: 1920,
                    height: 400,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });

                // Convert to blob with compression
                canvas.toBlob(function(blob) {
                    submitFormWithBanner(blob);
                }, 'image/jpeg', 0.85); // 85% quality for compression
            } else {
                // No cropper, submit normally (for edit without new banner)
                submitFormWithBanner(null);
            }
        });

        // Submit form with or without cropped banner
        function submitFormWithBanner(croppedBlob) {
            var formData = new FormData($('#categoryForm')[0]);
            var id = $('#category_id').val();
            var url = id ? '<?= base_url('admin/category/update'); ?>' : '<?= base_url('admin/category/create'); ?>';

            // If we have a cropped image, replace the banner_image file
            if (croppedBlob) {
                formData.delete('banner_image');
                formData.append('banner_image', croppedBlob, 'category_banner.jpg');
            }

            // Disable submit button
            $('#submitBtn').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Processing...');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    $('#submitBtn').prop('disabled', false).html('<i class="bi bi-save"></i> Save Category');

                    if (response.success) {
                        $('#categoryModal').modal('hide');
                        table.ajax.reload();
                        destroyCropper();
                        Swal.fire('Success!', response.message, 'success');
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    $('#submitBtn').prop('disabled', false).html('<i class="bi bi-save"></i> Save Category');
                    Swal.fire('Error!', 'Something went wrong: ' + error, 'error');
                }
            });
        }

        // Delete Category
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
                        url: '<?= base_url('admin/category/delete/'); ?>' + id,
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
                url: '<?= base_url('admin/category/toggle_status/'); ?>' + id,
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

        // Clean up cropper when modal is closed
        $('#categoryModal').on('hidden.bs.modal', function() {
            destroyCropper();
            $('#bannerPreviewContainer').hide();
        });
    });
</script>
