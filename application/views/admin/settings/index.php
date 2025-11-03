<div class="page-content">
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Site Settings</h4>
                </div>
                <div class="card-body">
                    <?= $this->session->flashdata('message'); ?>

                    <form action="<?= base_url('admin/settings/update'); ?>" method="post" enctype="multipart/form-data">
                        <!-- Bootstrap Tabs -->
                        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                                    <i class="bi bi-gear"></i> General Settings
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">
                                    <i class="bi bi-telephone"></i> Contact Settings
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab" aria-controls="social" aria-selected="false">
                                    <i class="bi bi-share"></i> Social Media
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="ecommerce-tab" data-bs-toggle="tab" data-bs-target="#ecommerce" type="button" role="tab" aria-controls="ecommerce" aria-selected="false">
                                    <i class="bi bi-cart"></i> E-commerce Settings
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content mt-4" id="settingsTabsContent">
                            <!-- Tab 1: General Settings -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="site_name" class="form-label">Site Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="site_name" name="site_name" value="<?= isset($settings['site_name']) ? htmlspecialchars($settings['site_name']) : ''; ?>" required>
                                            <small class="text-muted">The name of your website</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="site_tagline" class="form-label">Site Tagline</label>
                                            <input type="text" class="form-control" id="site_tagline" name="site_tagline" value="<?= isset($settings['site_tagline']) ? htmlspecialchars($settings['site_tagline']) : ''; ?>">
                                            <small class="text-muted">A short tagline for your website</small>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="site_description" class="form-label">Site Description</label>
                                            <textarea class="form-control" id="site_description" name="site_description" rows="4"><?= isset($settings['site_description']) ? htmlspecialchars($settings['site_description']) : ''; ?></textarea>
                                            <small class="text-muted">A brief description of your website for SEO</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="site_logo" class="form-label">Site Logo</label>
                                            <input type="file" class="form-control" id="site_logo" name="site_logo" accept="image/*">
                                            <small class="text-muted">Max size: 2MB. Supported formats: JPG, JPEG, PNG, GIF</small>

                                            <?php if (!empty($settings['site_logo'])): ?>
                                                <div class="mt-2">
                                                    <label class="form-label">Current Logo:</label><br>
                                                    <img src="<?= base_url('uploads/' . $settings['site_logo']); ?>" alt="Site Logo" style="max-width: 200px; max-height: 100px; border: 1px solid #ddd; padding: 5px;">
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="site_favicon" class="form-label">Site Favicon</label>
                                            <input type="file" class="form-control" id="site_favicon" name="site_favicon" accept="image/*">
                                            <small class="text-muted">Max size: 2MB. Supported formats: JPG, JPEG, PNG, GIF</small>

                                            <?php if (!empty($settings['site_favicon'])): ?>
                                                <div class="mt-2">
                                                    <label class="form-label">Current Favicon:</label><br>
                                                    <img src="<?= base_url('uploads/' . $settings['site_favicon']); ?>" alt="Site Favicon" style="max-width: 50px; max-height: 50px; border: 1px solid #ddd; padding: 5px;">
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 2: Contact Settings -->
                            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contact_email" class="form-label">Contact Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?= isset($settings['contact_email']) ? htmlspecialchars($settings['contact_email']) : ''; ?>" required>
                                            <small class="text-muted">Primary email for customer inquiries</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contact_phone" class="form-label">Contact Phone</label>
                                            <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="<?= isset($settings['contact_phone']) ? htmlspecialchars($settings['contact_phone']) : ''; ?>">
                                            <small class="text-muted">Primary phone number</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contact_whatsapp" class="form-label">WhatsApp Number</label>
                                            <input type="text" class="form-control" id="contact_whatsapp" name="contact_whatsapp" value="<?= isset($settings['contact_whatsapp']) ? htmlspecialchars($settings['contact_whatsapp']) : ''; ?>" placeholder="628123456789">
                                            <small class="text-muted">WhatsApp number with country code (e.g., 628123456789)</small>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="contact_address" class="form-label">Contact Address</label>
                                            <textarea class="form-control" id="contact_address" name="contact_address" rows="4"><?= isset($settings['contact_address']) ? htmlspecialchars($settings['contact_address']) : ''; ?></textarea>
                                            <small class="text-muted">Your business address</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 3: Social Media -->
                            <div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="social-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="facebook_url" class="form-label">
                                                <i class="bi bi-facebook text-primary"></i> Facebook URL
                                            </label>
                                            <input type="url" class="form-control" id="facebook_url" name="facebook_url" value="<?= isset($settings['facebook_url']) ? htmlspecialchars($settings['facebook_url']) : ''; ?>" placeholder="https://facebook.com/yourpage">
                                            <small class="text-muted">Full URL to your Facebook page</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="instagram_url" class="form-label">
                                                <i class="bi bi-instagram text-danger"></i> Instagram URL
                                            </label>
                                            <input type="url" class="form-control" id="instagram_url" name="instagram_url" value="<?= isset($settings['instagram_url']) ? htmlspecialchars($settings['instagram_url']) : ''; ?>" placeholder="https://instagram.com/yourprofile">
                                            <small class="text-muted">Full URL to your Instagram profile</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="shopee_url" class="form-label">
                                                <i class="bi bi-shop text-warning"></i> Shopee URL
                                            </label>
                                            <input type="url" class="form-control" id="shopee_url" name="shopee_url" value="<?= isset($settings['shopee_url']) ? htmlspecialchars($settings['shopee_url']) : ''; ?>" placeholder="https://shopee.co.id/yourshop">
                                            <small class="text-muted">Full URL to your Shopee shop</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tiktok_url" class="form-label">
                                                <i class="bi bi-tiktok text-dark"></i> TikTok URL
                                            </label>
                                            <input type="url" class="form-control" id="tiktok_url" name="tiktok_url" value="<?= isset($settings['tiktok_url']) ? htmlspecialchars($settings['tiktok_url']) : ''; ?>" placeholder="https://tiktok.com/@yourprofile">
                                            <small class="text-muted">Full URL to your TikTok profile</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tokopedia_url" class="form-label">
                                                <i class="bi bi-bag text-success"></i> Tokopedia URL
                                            </label>
                                            <input type="url" class="form-control" id="tokopedia_url" name="tokopedia_url" value="<?= isset($settings['tokopedia_url']) ? htmlspecialchars($settings['tokopedia_url']) : ''; ?>" placeholder="https://tokopedia.com/yourshop">
                                            <small class="text-muted">Full URL to your Tokopedia shop</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 4: E-commerce Settings -->
                            <div class="tab-pane fade" id="ecommerce" role="tabpanel" aria-labelledby="ecommerce-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="currency" class="form-label">Currency</label>
                                            <input type="text" class="form-control" id="currency" name="currency" value="<?= isset($settings['currency']) ? htmlspecialchars($settings['currency']) : 'IDR'; ?>" placeholder="IDR">
                                            <small class="text-muted">Currency code (e.g., IDR, USD, EUR)</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="products_per_page" class="form-label">Products Per Page</label>
                                            <input type="number" class="form-control" id="products_per_page" name="products_per_page" value="<?= isset($settings['products_per_page']) ? htmlspecialchars($settings['products_per_page']) : '12'; ?>" min="1">
                                            <small class="text-muted">Number of products to display per page</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="featured_products_limit" class="form-label">Featured Products Limit</label>
                                            <input type="number" class="form-control" id="featured_products_limit" name="featured_products_limit" value="<?= isset($settings['featured_products_limit']) ? htmlspecialchars($settings['featured_products_limit']) : '8'; ?>" min="1">
                                            <small class="text-muted">Number of featured products to display on homepage</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="new_products_limit" class="form-label">New Products Limit</label>
                                            <input type="number" class="form-control" id="new_products_limit" name="new_products_limit" value="<?= isset($settings['new_products_limit']) ? htmlspecialchars($settings['new_products_limit']) : '8'; ?>" min="1">
                                            <small class="text-muted">Number of new products to display on homepage</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Settings
                            </button>
                            <a href="<?= base_url('admin/dashboard'); ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        // Form submission confirmation
        $('form').on('submit', function(e) {
            var form = $(this);

            // Check if file inputs have valid files
            var logoFile = $('#site_logo')[0].files[0];
            var faviconFile = $('#site_favicon')[0].files[0];

            if (logoFile && logoFile.size > 2097152) {
                e.preventDefault();
                Swal.fire('Error!', 'Logo file size must not exceed 2MB', 'error');
                return false;
            }

            if (faviconFile && faviconFile.size > 2097152) {
                e.preventDefault();
                Swal.fire('Error!', 'Favicon file size must not exceed 2MB', 'error');
                return false;
            }

            // Show loading state
            var submitBtn = form.find('button[type="submit"]');
            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="bi bi-hourglass-split"></i> Saving...');
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
