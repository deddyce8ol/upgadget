<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">

<div class="page-content">
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Banner Management</h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bannerModal" id="addBannerBtn">
                            <i class="bi bi-plus-circle"></i> Add Banner
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?= $this->session->flashdata('message'); ?>

                    <div class="table-responsive">
                        <table class="table table-hover" id="bannerTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Link</th>
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

<!-- Banner Modal -->
<div class="modal fade" id="bannerModal" tabindex="-1" aria-labelledby="bannerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="bannerForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="bannerModalLabel">Add Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="banner_id" name="banner_id">
                    <input type="hidden" id="cropped_image_data" name="cropped_image_data">

                    <div class="mb-3">
                        <label for="banner_title" class="form-label">Banner Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="banner_title" name="banner_title" required>
                    </div>

                    <div class="mb-3">
                        <label for="banner_link" class="form-label">Banner Link</label>
                        <input type="text" class="form-control" id="banner_link" name="banner_link" placeholder="https://example.com">
                        <small class="text-muted">Optional: URL to redirect when banner is clicked</small>
                    </div>

                    <div class="mb-3">
                        <label for="banner_image" class="form-label">Banner Image <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="banner_image" name="banner_image" accept="image/jpeg,image/png,image/jpg,image/webp">
                        <small class="text-muted">Recommended: 1920×600px (ratio 3.2:1). Max: 5MB. Format: JPG, PNG, WebP</small>
                    </div>

                    <div id="currentImage" class="mb-3" style="display: none;">
                        <label class="form-label">Current Image</label><br>
                        <img id="currentImageImg" src="" style="max-width: 200px; max-height: 100px; object-fit: cover;" class="img-thumbnail">
                        <p class="text-muted mt-2"><small>Leave image field empty to keep current image</small></p>
                    </div>

                    <!-- Image Preview and Crop Area -->
                    <div id="imagePreviewContainer" class="mb-3" style="display: none;">
                        <label class="form-label">Crop Image (Ratio 3.2:1 - 1920×600px)</label>
                        <div style="max-height: 400px; overflow: hidden;">
                            <img id="imagePreview" style="max-width: 100%; display: block;">
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
                                Image akan di-resize ke 1920×600px dan dikompres ke < 400KB secara otomatis.
                                Drag untuk posisi, scroll untuk zoom.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Banner</button>
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
        var table = $('#bannerTable').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": "<?= base_url('admin/banner/get_data'); ?>",
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
                    "data": 5,
                    "orderable": false
                }
            ],
            "order": [
                [0, 'desc']
            ]
        });

        // Add Banner Button
        $('#addBannerBtn').click(function() {
            $('#bannerModalLabel').text('Add Banner');
            $('#bannerForm')[0].reset();
            $('#banner_id').val('');
            $('#currentImage').hide();
            $('#imagePreviewContainer').hide();
            $('#banner_image').prop('required', true);
            destroyCropper();
        });

        // Initialize Cropper when image is selected
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
                    $('#imagePreview').attr('src', event.target.result);
                    $('#imagePreviewContainer').show();
                    $('#currentImage').hide();

                    // Destroy previous cropper instance
                    destroyCropper();

                    // Initialize new cropper
                    const image = document.getElementById('imagePreview');
                    cropper = new Cropper(image, {
                        aspectRatio: 3.2, // 1920:600 = 3.2:1
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

        // Edit Banner
        $(document).on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            $.ajax({
                url: '<?= base_url('admin/banner/get_by_id/'); ?>' + id,
                type: 'GET',
                dataType: 'json',
                success: function(banner) {
                    $('#bannerModalLabel').text('Edit Banner');
                    $('#banner_id').val(banner.banner_id);
                    $('#banner_title').val(banner.banner_title);
                    $('#banner_link').val(banner.banner_link);

                    // Make image field optional when editing
                    $('#banner_image').prop('required', false);
                    $('#imagePreviewContainer').hide();
                    destroyCropper();

                    if (banner.banner_image) {
                        $('#currentImageImg').attr('src', '<?= base_url('uploads/banners/'); ?>' + banner.banner_image);
                        $('#currentImage').show();
                    } else {
                        $('#currentImage').hide();
                    }

                    $('#bannerModal').modal('show');
                }
            });
        });

        // Submit Form
        $('#bannerForm').submit(function(e) {
            e.preventDefault();

            // If cropper is active, get cropped image data
            if (cropper) {
                const canvas = cropper.getCroppedCanvas({
                    width: 1920,
                    height: 600,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });

                // Convert to blob with compression
                canvas.toBlob(function(blob) {
                    submitFormWithImage(blob);
                }, 'image/jpeg', 0.85); // 85% quality for compression
            } else {
                // No cropper, submit normally (for edit without new image)
                submitFormWithImage(null);
            }
        });

        function submitFormWithImage(imageBlob) {
            var formData = new FormData($('#bannerForm')[0]);
            var id = $('#banner_id').val();
            var url = id ? '<?= base_url('admin/banner/update'); ?>' : '<?= base_url('admin/banner/create'); ?>';

            // If we have a cropped image, replace the file input
            if (imageBlob) {
                formData.delete('banner_image');
                formData.append('banner_image', imageBlob, 'banner_' + Date.now() + '.jpg');
            }

            // Disable submit button to prevent double submission
            $('#submitBtn').prop('disabled', true).text('Processing...');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    $('#submitBtn').prop('disabled', false).text('Save Banner');

                    if (response.success) {
                        $('#bannerModal').modal('hide');
                        table.ajax.reload();
                        destroyCropper();
                        Swal.fire('Success!', response.message, 'success');
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    $('#submitBtn').prop('disabled', false).text('Save Banner');
                    Swal.fire('Error!', 'Something went wrong: ' + error, 'error');
                }
            });
        }

        // Clean up cropper when modal is closed
        $('#bannerModal').on('hidden.bs.modal', function() {
            destroyCropper();
            $('#imagePreviewContainer').hide();
        });

        // Delete Banner
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
                        url: '<?= base_url('admin/banner/delete/'); ?>' + id,
                        type: 'POST',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                table.ajax.reload();
                                Swal.fire('Deleted!', response.message, 'success');
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Failed to delete banner!', 'error');
                        }
                    });
                }
            });
        });

        // Toggle Status
        $(document).on('click', '.toggle-status-btn', function() {
            var id = $(this).data('id');

            $.ajax({
                url: '<?= base_url('admin/banner/toggle_status/'); ?>' + id,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload();
                        Swal.fire('Success!', response.message, 'success');
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Failed to toggle status!', 'error');
                }
            });
        });
    });
</script>
