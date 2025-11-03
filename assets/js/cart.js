/**
 * Cart Page JavaScript
 * Handles cart interactions (update, remove, notifications)
 */

// Wait for jQuery to be loaded
(function checkJQuery() {
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ready(function($) {

            // Update cart quantity
            $('.cart-qty-input').on('change', function() {
                var rowid = $(this).data('rowid');
                var quantity = parseInt($(this).val());
                var stock = parseInt($(this).data('stock'));
                var $row = $('tr[data-rowid="' + rowid + '"]');

                // Validate quantity
                if (quantity < 1) {
                    quantity = 1;
                    $(this).val(1);
                }

                if (quantity > stock) {
                    alert('Jumlah melebihi stok yang tersedia. Stok tersedia: ' + stock);
                    quantity = stock;
                    $(this).val(stock);
                    return;
                }

                // Show loading
                $row.addClass('updating');

                // Send AJAX request
                $.ajax({
                    url: BASE_URL + 'cart/update',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        rowid: rowid,
                        quantity: quantity
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            // Update item subtotal
                            $('.subtotal-' + rowid).html(response.item_subtotal);

                            // Update cart total
                            $('#cart-subtotal').html(response.cart_total);
                            $('#cart-total').html(response.cart_total);

                            // Update header cart count
                            $('.cart-count').html(response.cart_count);

                            // Show success notification
                            showNotification('success', response.message);
                        } else {
                            showNotification('error', response.message);
                        }
                    },
                    error: function() {
                        showNotification('error', 'Terjadi kesalahan saat memperbarui keranjang');
                    },
                    complete: function() {
                        $row.removeClass('updating');
                    }
                });
            });

            // Remove item from cart
            $('.btn-remove').on('click', function(e) {
                e.preventDefault();

                if (!confirm('Yakin ingin menghapus produk ini dari keranjang?')) {
                    return;
                }

                var rowid = $(this).data('rowid');
                var $row = $('tr[data-rowid="' + rowid + '"]');

                // Show loading
                $row.addClass('removing');

                // Send AJAX request
                $.ajax({
                    url: BASE_URL + 'cart/remove',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        rowid: rowid
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            // Remove row with animation
                            $row.fadeOut(400, function() {
                                $(this).remove();

                                // Check if cart is empty
                                if (response.cart_empty) {
                                    location.reload();
                                } else {
                                    // Update cart total
                                    $('#cart-subtotal').html(response.cart_total);
                                    $('#cart-total').html(response.cart_total);

                                    // Update header cart count
                                    $('.cart-count').html(response.cart_count);
                                }
                            });

                            // Show success notification
                            showNotification('success', response.message);
                        } else {
                            $row.removeClass('removing');
                            showNotification('error', response.message);
                        }
                    },
                    error: function() {
                        $row.removeClass('removing');
                        showNotification('error', 'Terjadi kesalahan saat menghapus produk');
                    }
                });
            });

            // Notification function
            function showNotification(type, message) {
                var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                var icon = type === 'success' ? 'icon-check-circle' : 'icon-times-circle';

                var notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert" style="position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">' +
                    '<i class="' + icon + '"></i> ' + message +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                    '</div>');

                $('body').append(notification);

                // Auto dismiss after 3 seconds
                setTimeout(function() {
                    notification.fadeOut(400, function() {
                        $(this).remove();
                    });
                }, 3000);
            }

        });
    } else {
        // jQuery not loaded yet, wait and try again
        setTimeout(checkJQuery, 50);
    }
})();
