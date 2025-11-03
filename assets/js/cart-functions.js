/**
 * Cart Functions
 * Global JavaScript functions for cart operations
 */

/**
 * Add product to cart
 * @param {number} productId - Product ID
 * @param {number} quantity - Quantity (default: 1)
 */
function addToCart(productId, quantity) {
    quantity = quantity || 1;

    $.ajax({
        url: BASE_URL + 'cart/add',
        type: 'POST',
        data: {
            product_id: productId,
            quantity: quantity
        },
        dataType: 'json',
        beforeSend: function() {
            // Show loading indicator
            var $btn = $('.btn-cart[data-product-id="' + productId + '"]');
            if ($btn.length) {
                $btn.prop('disabled', true).data('original-html', $btn.html()).html('<i class="icon-spinner spinner"></i> Loading...');
            }
        },
        success: function(response) {
            if (response.status === 'success') {
                // Update cart count in header
                updateCartCount(response.cart_count);

                // Show success notification
                showNotification('success', response.message);

                // Trigger cart update event
                $(document).trigger('cart:updated', [response]);
            } else {
                showNotification('error', response.message);
            }
        },
        error: function(xhr) {
            var message = 'Terjadi kesalahan. Silakan coba lagi.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showNotification('error', message);
        },
        complete: function() {
            // Restore button
            setTimeout(function() {
                var $btn = $('.btn-cart[data-product-id="' + productId + '"]');
                if ($btn.length) {
                    var originalHtml = $btn.data('original-html') || '<i class="icon-shopping-cart"></i><span>Add to Cart</span>';
                    $btn.prop('disabled', false).html(originalHtml);
                }
            }, 500);
        }
    });
}

/**
 * Update cart item quantity
 * @param {string} rowid - Cart row ID
 * @param {number} quantity - New quantity
 * @param {function} callback - Callback function
 */
function updateCartQuantity(rowid, quantity, callback) {
    $.ajax({
        url: BASE_URL + 'cart/update',
        type: 'POST',
        data: {
            rowid: rowid,
            quantity: quantity
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Update cart count in header
                updateCartCount(response.cart_count);

                // Trigger cart update event
                $(document).trigger('cart:updated', [response]);

                if (typeof callback === 'function') {
                    callback(response);
                }
            } else {
                showNotification('error', response.message);
            }
        },
        error: function() {
            showNotification('error', 'Terjadi kesalahan saat memperbarui keranjang');
        }
    });
}

/**
 * Remove item from cart
 * @param {string} rowid - Cart row ID
 * @param {function} callback - Callback function
 */
function removeFromCart(rowid, callback) {
    if (!confirm('Yakin ingin menghapus produk ini dari keranjang?')) {
        return;
    }

    $.ajax({
        url: BASE_URL + 'cart/remove',
        type: 'POST',
        data: { rowid: rowid },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Update cart count in header
                updateCartCount(response.cart_count);

                // Show success notification
                showNotification('success', response.message);

                // Trigger cart update event
                $(document).trigger('cart:updated', [response]);

                if (typeof callback === 'function') {
                    callback(response);
                }
            } else {
                showNotification('error', response.message);
            }
        },
        error: function() {
            showNotification('error', 'Terjadi kesalahan saat menghapus produk');
        }
    });
}

/**
 * Update cart count in header
 * @param {number} count - Cart count
 */
function updateCartCount(count) {
    $('.cart-count').html(count);

    // Update cart dropdown if exists
    if (count == 0) {
        $('.dropdown-cart-products').html('<p class="text-center mb-0">Keranjang kosong</p>');
        $('.dropdown-cart-total').hide();
        $('.dropdown-cart-action').hide();
    }
}

/**
 * Add product to wishlist
 * @param {number} productId - Product ID
 */
function addToWishlist(productId) {
    $.ajax({
        url: BASE_URL + 'customer/wishlist_add',
        type: 'POST',
        data: { product_id: productId },
        dataType: 'json',
        beforeSend: function() {
            // Show loading indicator
            var $btn = $('.btn-wishlist[data-product-id="' + productId + '"]');
            if ($btn.length) {
                $btn.prop('disabled', true).html('<i class="icon-spinner spinner"></i>');
            }
        },
        success: function(response) {
            if (response.success) {
                // Update wishlist count in header
                if (response.wishlist_count) {
                    $('.wishlist-count').html(response.wishlist_count);
                }

                // Show success notification
                showNotification('success', response.message);

                // Update button to "added" state
                var $btn = $('.btn-wishlist[data-product-id="' + productId + '"]');
                if ($btn.length) {
                    $btn.addClass('added').html('<i class="icon-heart"></i>');
                }
            } else {
                showNotification('error', response.message);
            }
        },
        error: function(xhr) {
            var message = 'Terjadi kesalahan. Silakan coba lagi.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showNotification('error', message);
        },
        complete: function() {
            // Restore button
            setTimeout(function() {
                var $btn = $('.btn-wishlist[data-product-id="' + productId + '"]');
                if ($btn.length && !$btn.hasClass('added')) {
                    $btn.prop('disabled', false).html('<i class="icon-heart-o"></i>');
                }
            }, 500);
        }
    });
}

/**
 * Show notification popup
 * @param {string} type - Notification type (success, error, warning, info)
 * @param {string} message - Notification message
 * @param {number} duration - Duration in milliseconds (default: 3000)
 */
function showNotification(type, message, duration) {
    duration = duration || 3000;

    var alertClass = 'alert-success';
    var icon = 'icon-check-circle';

    switch(type) {
        case 'error':
        case 'danger':
            alertClass = 'alert-danger';
            icon = 'icon-times-circle';
            break;
        case 'warning':
            alertClass = 'alert-warning';
            icon = 'icon-exclamation-circle';
            break;
        case 'info':
            alertClass = 'alert-info';
            icon = 'icon-info-circle';
            break;
    }

    var notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show notification-popup" role="alert" style="position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); animation: slideInRight 0.3s ease-out;">' +
        '<i class="' + icon + '"></i> ' + message +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true">&times;</span>' +
        '</button>' +
        '</div>');

    $('body').append(notification);

    // Auto dismiss
    setTimeout(function() {
        notification.fadeOut(400, function() {
            $(this).remove();
        });
    }, duration);
}

/**
 * Load cart count from server
 * Useful for refreshing cart count on page load
 */
function loadCartCount() {
    $.ajax({
        url: BASE_URL + 'cart/count',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                updateCartCount(response.count);
            }
        },
        error: function() {
            // Silently fail - cart count will show server-rendered value
            console.log('Failed to load cart count');
        }
    });
}

// Add animation styles
if (!$('#cart-notification-styles').length) {
    $('head').append('<style id="cart-notification-styles">' +
        '@keyframes slideInRight {' +
        '  from { transform: translateX(100%); opacity: 0; }' +
        '  to { transform: translateX(0); opacity: 1; }' +
        '}' +
        '.notification-popup { animation: slideInRight 0.3s ease-out; }' +
        '.notification-popup i { margin-right: 8px; font-size: 16px; }' +
        '.spinner { animation: spin 1s linear infinite; display: inline-block; }' +
        '@keyframes spin {' +
        '  0% { transform: rotate(0deg); }' +
        '  100% { transform: rotate(360deg); }' +
        '}' +
        '</style>');
}

// Load cart count on page ready
$(document).ready(function() {
    // Refresh cart count on page load to ensure it's accurate
    loadCartCount();
});
