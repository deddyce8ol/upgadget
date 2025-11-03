<div class="page-content">
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><?= isset($product) ? 'Edit Produk' : 'Tambah Produk'; ?></h4>
                </div>
                <div class="card-body">
                    <?= $this->session->flashdata('message'); ?>

                    <form action="<?= isset($product) ? base_url('admin/product/update/' . $product->product_id) : base_url('admin/product/store'); ?>" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Product Name -->
                                <div class="mb-3">
                                    <label for="product_name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="product_name" name="product_name" value="<?= isset($product) ? $product->product_name : ''; ?>" required>
                                </div>

                                <!-- SKU -->
                                <div class="mb-3">
                                    <label for="sku" class="form-label">Kode SKU <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="sku" name="sku" value="<?= isset($product) ? $product->sku : ''; ?>" required>
                                </div>

                                <!-- Category & Brand -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="category_id" class="form-label">Kategori</label>
                                        <select class="form-select" id="category_id" name="category_id">
                                            <option value="">Pilih Kategori</option>
                                            <?php foreach ($categories as $cat) : ?>
                                                <option value="<?= $cat['id']; ?>" <?= isset($product) && $product->category_id == $cat['id'] ? 'selected' : ''; ?>>
                                                    <?= $cat['name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="brand_id" class="form-label">Merek</label>
                                        <select class="form-select" id="brand_id" name="brand_id">
                                            <option value="">Pilih Merek</option>
                                            <?php foreach ($brands as $brand) : ?>
                                                <option value="<?= $brand->brand_id; ?>" <?= isset($product) && $product->brand_id == $brand->brand_id ? 'selected' : ''; ?>>
                                                    <?= $brand->brand_name; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"><?= isset($product) ? $product->description : ''; ?></textarea>
                                </div>

                                <!-- Specifications -->
                                <div class="mb-3">
                                    <label for="specifications" class="form-label">Spesifikasi</label>
                                    <textarea class="form-control" id="specifications" name="specifications" rows="4"><?= isset($product) ? $product->specifications : ''; ?></textarea>
                                    <small class="text-muted">Anda dapat menggunakan format HTML</small>
                                </div>

                                <!-- Price & Discount -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="price" class="form-label">Harga <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="price" name="price" value="<?= isset($product) ? $product->price : ''; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="discount_price" class="form-label">Harga Diskon</label>
                                        <input type="number" class="form-control" id="discount_price" name="discount_price" value="<?= isset($product) ? $product->discount_price : ''; ?>">
                                    </div>
                                </div>

                                <!-- Stock & Weight -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="stock" class="form-label">Stok <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="stock" name="stock" value="<?= isset($product) ? $product->stock : ''; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="weight" class="form-label">Berat (gram)</label>
                                        <input type="number" class="form-control" id="weight" name="weight" value="<?= isset($product) ? $product->weight : ''; ?>">
                                    </div>
                                </div>

                                <!-- Featured -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" <?= isset($product) && $product->is_featured == 1 ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_featured">
                                            Produk Unggulan
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Main Image -->
                                <div class="mb-3">
                                    <label class="form-label">Gambar Utama Produk</label>
                                    <div class="dropzone-wrapper" id="main-image-dropzone">
                                        <div class="dropzone-desc">
                                            <i class="bi bi-cloud-upload" style="font-size: 48px;"></i>
                                            <p>Klik atau seret gambar ke sini</p>
                                            <small class="text-muted">Format: JPG, PNG, GIF (Max: 5MB)<br>Akan dikonversi ke WebP</small>
                                        </div>
                                        <input type="file" id="product_image" name="product_image" class="dropzone-input" accept="image/*">
                                    </div>
                                    <div id="main-image-preview" class="mt-2"></div>

                                    <?php if (isset($product) && $product->main_image) : ?>
                                        <div class="mt-2" id="current-main-image">
                                            <label class="form-label">Gambar Saat Ini:</label>
                                            <img src="<?= base_url('uploads/products/' . $product->main_image); ?>" alt="Current Image" class="img-thumbnail" style="max-width: 200px;">
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Additional Images -->
                                <div class="mb-3">
                                    <label class="form-label">Gambar Tambahan</label>
                                    <div class="dropzone-wrapper" id="additional-images-dropzone">
                                        <div class="dropzone-desc">
                                            <i class="bi bi-images" style="font-size: 48px;"></i>
                                            <p>Klik atau seret beberapa gambar ke sini</p>
                                            <small class="text-muted">Pilih beberapa gambar sekaligus</small>
                                        </div>
                                        <input type="file" id="additional_images" name="additional_images[]" class="dropzone-input" accept="image/*" multiple>
                                    </div>
                                    <div id="additional-images-preview" class="mt-2"></div>

                                    <?php if (isset($product_images) && !empty($product_images)) : ?>
                                        <div class="mt-3">
                                            <label class="form-label">Gambar Tambahan Saat Ini:</label>
                                            <div class="row" id="current-additional-images">
                                                <?php foreach ($product_images as $img) : ?>
                                                    <div class="col-6 mb-2">
                                                        <div class="position-relative">
                                                            <img src="<?= base_url('uploads/products/' . $img->image_path); ?>" class="img-thumbnail" style="width: 100%;">
                                                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 delete-image-btn" data-id="<?= $img->image_id; ?>">
                                                                <i class="bi bi-x"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> <?= isset($product) ? 'Perbarui Produk' : 'Simpan Produk'; ?>
                                </button>
                                <a href="<?= base_url('admin/product'); ?>" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Dropzone CSS -->
<style>
.dropzone-wrapper {
    border: 2px dashed #435ebe;
    border-radius: 8px;
    background-color: #f8f9fa;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.dropzone-wrapper:hover {
    border-color: #2f3c8e;
    background-color: #e9ecef;
}

.dropzone-wrapper.dragover {
    border-color: #28a745;
    background-color: #d4edda;
    transform: scale(1.02);
}

.dropzone-desc {
    color: #6c757d;
}

.dropzone-desc i {
    color: #435ebe;
    margin-bottom: 10px;
}

.dropzone-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.image-preview-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
    margin-top: 10px;
}

.image-preview-item {
    position: relative;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    margin-top: 10px;
}

#main-image-preview .image-preview-item {
    display: block;
    margin-bottom: 10px;
}

#additional-images-preview {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
}

#additional-images-preview .image-preview-item {
    margin-bottom: 0;
}

.image-preview-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.image-preview-item .remove-preview {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    line-height: 1;
}

.image-preview-item .image-info {
    padding: 5px;
    font-size: 11px;
    background: #f8f9fa;
    text-align: center;
}

.image-preview-item .compression-badge {
    position: absolute;
    top: 5px;
    left: 5px;
    background: rgba(40, 167, 69, 0.9);
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: bold;
}

.progress-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}
</style>

<!-- Include browser-image-compression library -->
<script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>

<script>
    $(document).ready(function() {
        // Compression options
        const compressionOptions = {
            maxSizeMB: 1,
            maxWidthOrHeight: 1920,
            useWebWorker: true,
            fileType: 'image/webp'
        };

        // Main Image Upload Handler
        initDropzone('#main-image-dropzone', '#product_image', '#main-image-preview', false);

        // Additional Images Upload Handler
        initDropzone('#additional-images-dropzone', '#additional_images', '#additional-images-preview', true);

        function initDropzone(wrapperSelector, inputSelector, previewSelector, multiple) {
            const wrapper = $(wrapperSelector);
            const input = $(inputSelector);
            const preview = $(previewSelector);

            // Click to upload - only if not clicking the input itself
            wrapper.on('click', function(e) {
                // Prevent infinite loop: only trigger if not clicking on input or remove button
                if (!$(e.target).is('input[type="file"]') && !$(e.target).hasClass('remove-preview') && !$(e.target).closest('.remove-preview').length) {
                    e.preventDefault();
                    e.stopPropagation();
                    input.trigger('click');
                }
            });

            // Prevent click event on input from bubbling to wrapper
            input.on('click', function(e) {
                e.stopPropagation();
            });

            // Drag over effect
            wrapper.on('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('dragover');
            });

            wrapper.on('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
            });

            // Drop handler
            wrapper.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');

                const files = e.originalEvent.dataTransfer.files;
                handleFiles(files, input, preview, multiple);
            });

            // File input change
            input.on('change', function(e) {
                const files = this.files;
                handleFiles(files, input, preview, multiple);
            });
        }

        async function handleFiles(files, input, preview, multiple) {
            console.log('handleFiles called with:', files.length, 'files');

            if (!multiple) {
                preview.empty();
            }

            const fileArray = Array.from(files);
            const dataTransfer = new DataTransfer();

            for (let i = 0; i < fileArray.length; i++) {
                const file = fileArray[i];
                console.log('Processing file:', file.name, file.type, file.size);

                // Validate file type
                if (!file.type.match('image.*')) {
                    Swal.fire('Error!', 'File harus berupa gambar', 'error');
                    continue;
                }

                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire('Error!', 'Ukuran file maksimal 5MB', 'error');
                    continue;
                }

                // Create preview container
                const previewItem = $(`
                    <div class="image-preview-item">
                        <div class="progress-overlay">
                            <span>Mengompresi...</span>
                        </div>
                        <img src="" alt="Preview">
                        <button type="button" class="remove-preview" data-index="${i}">×</button>
                        <div class="image-info">
                            <div class="filename">${file.name}</div>
                            <div class="filesize">Ukuran asli: ${formatBytes(file.size)}</div>
                        </div>
                    </div>
                `);

                if (multiple) {
                    preview.append(previewItem);
                } else {
                    preview.html(previewItem);
                }

                // Compress image
                try {
                    console.log('Starting compression for:', file.name);
                    const compressedFile = await imageCompression(file, compressionOptions);
                    console.log('Compression successful. Original:', file.size, 'Compressed:', compressedFile.size);

                    // Update preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        console.log('Image loaded for preview');
                        previewItem.find('img').attr('src', e.target.result);
                        previewItem.find('.progress-overlay').remove();

                        // Add compression badge
                        const compressionRatio = ((1 - compressedFile.size / file.size) * 100).toFixed(0);
                        previewItem.prepend(`<span class="compression-badge">-${compressionRatio}%</span>`);

                        // Update file info
                        previewItem.find('.filesize').html(`
                            Ukuran asli: ${formatBytes(file.size)}<br>
                            Setelah kompresi: ${formatBytes(compressedFile.size)}
                        `);
                    };
                    reader.readAsDataURL(compressedFile);

                    // Add compressed file to DataTransfer
                    const newFileName = file.name.replace(/\.[^/.]+$/, ".webp");
                    const newFile = new File([compressedFile], newFileName, {
                        type: 'image/webp'
                    });
                    console.log('Adding file to DataTransfer:', newFileName);
                    dataTransfer.items.add(newFile);

                } catch (error) {
                    console.error('Compression error:', error);
                    console.warn('Fallback: Using original file without compression');

                    // Fallback: Use original file if compression fails
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        console.log('Using original image without compression');
                        previewItem.find('img').attr('src', e.target.result);
                        previewItem.find('.progress-overlay').html('<span>Tanpa kompresi</span>');
                        setTimeout(() => {
                            previewItem.find('.progress-overlay').remove();
                        }, 1000);

                        previewItem.find('.filesize').html(`
                            Ukuran: ${formatBytes(file.size)}<br>
                            <small class="text-warning">Kompresi gagal, menggunakan file asli</small>
                        `);
                    };
                    reader.readAsDataURL(file);

                    // Add original file to DataTransfer
                    dataTransfer.items.add(file);
                }
            }

            // Update file input with compressed files
            console.log('Total compressed files:', dataTransfer.files.length);
            input[0].files = dataTransfer.files;
            console.log('File input updated, files:', input[0].files.length);

            // Hide current image indicator when new image selected
            if (!multiple) {
                $('#current-main-image').hide();
            }
        }

        // Remove preview
        $(document).on('click', '.remove-preview', function(e) {
            e.stopPropagation();
            const container = $(this).closest('.image-preview-item');
            const previewContainer = container.parent();

            container.remove();

            // If no more images, clear input
            if (previewContainer.children().length === 0) {
                const inputId = previewContainer.attr('id').replace('-preview', '');
                $(`#${inputId}`).val('');

                // Show current image again if exists
                if (inputId === 'main-image') {
                    $('#current-main-image').show();
                }
            }
        });

        // Format bytes to human readable
        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        // Form submit validation
        $('form').on('submit', function(e) {
            const mainImageInput = $('#product_image')[0];
            const additionalImagesInput = $('#additional_images')[0];

            console.log('=== FORM SUBMITTING ===');
            console.log('Main image files count:', mainImageInput.files.length);
            console.log('Additional images files count:', additionalImagesInput.files.length);

            // Check if files are properly set
            if (mainImageInput.files.length > 0) {
                const file = mainImageInput.files[0];
                console.log('Main image file details:');
                console.log('  - Name:', file.name);
                console.log('  - Type:', file.type);
                console.log('  - Size:', file.size, 'bytes');
                console.log('  - Last modified:', new Date(file.lastModified));
            }

            if (additionalImagesInput.files.length > 0) {
                console.log('Additional images:');
                for (let i = 0; i < additionalImagesInput.files.length; i++) {
                    const file = additionalImagesInput.files[i];
                    console.log(`  [${i}] ${file.name} (${file.type}, ${file.size} bytes)`);
                }
            }

            console.log('=== FORM DATA READY TO SUBMIT ===');
            return true; // Allow form submission
        });

        // Delete additional image (existing functionality)
        $('.delete-image-btn').click(function() {
            var imageId = $(this).data('id');
            var btn = $(this);

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Gambar yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('admin/product/delete_image/'); ?>' + imageId,
                        type: 'POST',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                btn.closest('.col-6').remove();
                                Swal.fire('Terhapus!', response.message, 'success');
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        }
                    });
                }
            });
        });
    });
</script>
