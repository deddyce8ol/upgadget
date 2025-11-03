<main class="main">
    <div class="container">
        <h2 class="step-title mb-4">Wishlist Saya</h2>

        <?php if (!$customer_logged_in): ?>
            <div class="alert alert-info text-center">
                <p>Silakan <a href="<?= base_url('customer/login') ?>">login</a> untuk melihat wishlist Anda</p>
            </div>
        <?php elseif (empty($wishlist_items)): ?>
            <div class="text-center" style="padding: 80px 0;">
                <i class="icon-heart-o" style="font-size: 120px; color: #ccc;"></i>
                <h3 class="mt-3">Wishlist Kosong</h3>
                <p class="text-muted">Anda belum menambahkan produk ke wishlist</p>
                <a href="<?= base_url('product') ?>" class="btn btn-outline-primary-2 btn-lg mt-3">
                    <span>Mulai Belanja</span>
                    <i class="icon-long-arrow-right"></i>
                </a>
            </div>
        <?php else: ?>
            <table class="table table-cart table-wishlist">
                <thead>
                    <tr>
                        <th class="thumbnail-col"></th>
                        <th class="product-col">Produk</th>
                        <th class="price-col">Harga</th>
                        <th>Status</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($wishlist_items as $item): ?>
                        <tr>
                            <td>
                                <figure class="product-image-container">
                                    <a href="<?= base_url('product/detail/' . $item->product_slug) ?>" class="product-image">
                                        <img src="<?= get_product_image($item->main_image) ?>" alt="<?= $item->product_name ?>">
                                    </a>
                                </figure>
                            </td>
                            <td class="product-col">
                                <h5 class="product-title">
                                    <a href="<?= base_url('product/detail/' . $item->product_slug) ?>">
                                        <?= $item->product_name ?>
                                    </a>
                                </h5>
                            </td>
                            <td class="price-col">
                                <?php if ($item->discount_price > 0): ?>
                                    <span class="old-price"><?= format_rupiah($item->price) ?></span>
                                    <span class="new-price"><?= format_rupiah($item->discount_price) ?></span>
                                <?php else: ?>
                                    <?= format_rupiah($item->price) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($item->stock > 0 && $item->is_active == 1): ?>
                                    <span class="badge badge-success">Tersedia</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Habis</span>
                                <?php endif; ?>
                            </td>
                            <td class="action-col">
                                <?php if ($item->stock > 0 && $item->is_active == 1): ?>
                                    <button class="btn btn-dark btn-sm add-to-cart" data-product-id="<?= $item->product_id ?>">
                                        <i class="icon-shopping-cart"></i> Add to Cart
                                    </button>
                                <?php endif; ?>
                                <button class="btn btn-outline-danger btn-sm remove-wishlist" data-product-id="<?= $item->product_id ?>">
                                    <i class="icon-cancel"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <div class="mb-6"></div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wait for jQuery to be available
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }

    // Add to cart from wishlist
    $('.add-to-cart').on('click', function() {
        var productId = $(this).data('product-id');
        var $btn = $(this);

        $.ajax({
            url: BASE_URL + 'cart/add',
            type: 'POST',
            data: { product_id: productId, quantity: 1 },
            dataType: 'json',
            beforeSend: function() {
                $btn.prop('disabled', true).html('<i class="icon-spinner spinner"></i> Loading...');
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Update cart count
                    if (response.cart_count && $('.cart-count').length) {
                        $('.cart-count').text(response.cart_count);
                    }

                    // Show notification
                    if (typeof showNotification === 'function') {
                        showNotification('success', response.message);
                    } else {
                        alert(response.message);
                    }

                    // Restore button after delay
                    setTimeout(function() {
                        $btn.prop('disabled', false).html('<i class="icon-shopping-cart"></i> Add to Cart');
                    }, 1000);
                } else {
                    if (typeof showNotification === 'function') {
                        showNotification('error', response.message);
                    } else {
                        alert(response.message);
                    }
                    $btn.prop('disabled', false).html('<i class="icon-shopping-cart"></i> Add to Cart');
                }
            },
            error: function(xhr) {
                var message = 'Terjadi kesalahan. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                if (typeof showNotification === 'function') {
                    showNotification('error', message);
                } else {
                    alert(message);
                }
                $btn.prop('disabled', false).html('<i class="icon-shopping-cart"></i> Add to Cart');
            }
        });
    });

    // Remove from wishlist
    $('.remove-wishlist').on('click', function() {
        if (!confirm('Hapus dari wishlist?')) return;

        var productId = $(this).data('product-id');
        var $row = $(this).closest('tr');
        var $btn = $(this);

        $.ajax({
            url: BASE_URL + 'customer/wishlist_remove',
            type: 'POST',
            data: { product_id: productId },
            dataType: 'json',
            beforeSend: function() {
                $btn.prop('disabled', true).html('<i class="icon-spinner spinner"></i>');
            },
            success: function(response) {
                if (response.success) {
                    $row.fadeOut(400, function() {
                        $(this).remove();

                        // Reload page if no items left
                        if ($('.table-wishlist tbody tr:visible').length === 0) {
                            location.reload();
                        }
                    });

                    // Update wishlist count
                    if (response.wishlist_count !== undefined) {
                        $('.wishlist-count').text(response.wishlist_count);
                    }

                    // Show notification
                    if (typeof showNotification === 'function') {
                        showNotification('success', response.message);
                    }
                } else {
                    if (typeof showNotification === 'function') {
                        showNotification('error', response.message);
                    } else {
                        alert(response.message);
                    }
                    $btn.prop('disabled', false).html('<i class="icon-cancel"></i>');
                }
            },
            error: function(xhr) {
                var message = 'Terjadi kesalahan. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                if (typeof showNotification === 'function') {
                    showNotification('error', message);
                } else {
                    alert(message);
                }
                $btn.prop('disabled', false).html('<i class="icon-cancel"></i>');
            }
        });
    });
});
</script>
