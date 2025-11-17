/**
 * Home Page JavaScript
 * Handles brand slider initialization and product interactions
 */

$(document).ready(function() {
	// Initialize Brand Slider
	if ($('.brand-slider').length) {
		$('.brand-slider').owlCarousel({
			dots: true,
			nav: false,
			margin: 30,
			loop: true,
			autoplay: true,
			autoplayTimeout: 3000,
			autoplayHoverPause: true,
			autoWidth: true,
			items: 5,
			responsive: {
				0: {
					nav: false,
					dots: true
				},
				768: {
					nav: false,
					dots: true
				}
			}
		});
	}
});

/**
 * Add product to cart
 * @param {number} productId - The product ID to add
 */
function addToCart(productId) {
	$.ajax({
		url: base_url + 'cart/add',
		type: 'POST',
		data: {
			product_id: productId,
			quantity: 1
		},
		dataType: 'json',
		beforeSend: function() {
			// Show loading indicator
			$('.btn-product-icon[onclick*="' + productId + '"]')
				.prop('disabled', true)
				.html('<i class="icon-spinner spinner"></i>');
		},
		success: function(response) {
			if (response.status === 'success') {
				// Update cart count in header
				$('.cart-count').html(response.cart_count);

				// Show success notification
				showNotification('success', response.message);
			} else {
				showNotification('error', response.message);
			}
		},
		error: function() {
			showNotification('error', 'Terjadi kesalahan. Silakan coba lagi.');
		},
		complete: function() {
			// Restore button
			setTimeout(function() {
				$('.btn-product-icon[onclick*="' + productId + '"]')
					.prop('disabled', false)
					.html('<i class="icon-shopping-cart"></i>');
			}, 1000);
		}
	});
}

/**
 * Add product to wishlist
 * @param {number} productId - The product ID to add
 */
function addToWishlist(productId) {
	$.ajax({
		url: base_url + 'customer/wishlist/add',
		type: 'POST',
		data: {
			product_id: productId
		},
		dataType: 'json',
		beforeSend: function() {
			// Show loading indicator
			$('.btn-product-icon[onclick*="addToWishlist(' + productId + ')"]')
				.prop('disabled', true)
				.html('<i class="icon-spinner spinner"></i>');
		},
		success: function(response) {
			if (response.status === 'success') {
				// Update wishlist count in header
				$('.wishlist-count').html(response.wishlist_count);

				// Show success notification
				showNotification('success', response.message);
			} else {
				showNotification('error', response.message);
			}
		},
		error: function() {
			showNotification('error', 'Terjadi kesalahan. Silakan coba lagi.');
		},
		complete: function() {
			// Restore button
			setTimeout(function() {
				$('.btn-product-icon[onclick*="addToWishlist(' + productId + ')"]')
					.prop('disabled', false)
					.html('<i class="icon-heart-o"></i>');
			}, 1000);
		}
	});
}

/**
 * Show notification popup
 * @param {string} type - Notification type ('success' or 'error')
 * @param {string} message - The message to display
 */
function showNotification(type, message) {
	var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
	var icon = type === 'success' ? 'icon-check-circle' : 'icon-times-circle';

	var notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show notification-popup" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">' +
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
