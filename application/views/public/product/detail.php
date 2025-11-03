<div class="container">
	<nav aria-label="breadcrumb" class="breadcrumb-nav">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?= base_url() ?>"><i class="icon-home"></i></a></li>
			<li class="breadcrumb-item"><a href="<?= base_url('product') ?>">Produk</a></li>
			<li class="breadcrumb-item"><a href="<?= base_url('product/category/' . $product->category_slug) ?>"><?= $product->category_name ?></a></li>
			<li class="breadcrumb-item active" aria-current="page"><?= $product->product_name ?></li>
		</ol>
	</nav>

	<div class="product-single-container product-single-default">
		<div class="row">
			<div class="col-lg-5 col-md-6 product-single-gallery">
				<div class="product-slider-container">
					<div class="label-group">
						<?php if ($product->is_featured): ?>
							<div class="product-label label-hot">HOT</div>
						<?php endif; ?>
						<?php if (has_discount($product)): ?>
							<div class="product-label label-sale">-<?= calculate_discount_percentage($product->price, $product->discount_price) ?>%</div>
						<?php endif; ?>
					</div>

					<div class="product-single-carousel owl-carousel owl-theme show-nav-hover">
						<div class="product-item">
							<img class="product-single-image" src="<?= get_product_image($product->main_image) ?>" data-zoom-image="<?= get_product_image($product->main_image) ?>" width="468" height="468" alt="<?= $product->product_name ?>" />
						</div>
						<?php if (!empty($product_images)): ?>
							<?php foreach ($product_images as $img): ?>
							<div class="product-item">
								<img class="product-single-image" src="<?= get_product_image($img->image_path) ?>" data-zoom-image="<?= get_product_image($img->image_path) ?>" width="468" height="468" alt="<?= $product->product_name ?>" />
							</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>

				<?php if (!empty($product_images)): ?>
				<div class="prod-thumbnail owl-dots">
					<div class="owl-dot active">
						<img src="<?= get_product_image($product->main_image) ?>" width="110" height="110" alt="<?= $product->product_name ?>" />
					</div>
					<?php foreach ($product_images as $img): ?>
					<div class="owl-dot">
						<img src="<?= get_product_image($img->image_path) ?>" width="110" height="110" alt="<?= $product->product_name ?>" />
					</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
			</div>

			<div class="col-lg-7 col-md-6 product-single-details">
				<h1 class="product-title"><?= $product->product_name ?></h1>

				<div class="product-nav">
					<div class="product-prev">
						<a href="#">
							<span class="product-link"></span>
							<span class="product-popup">
								<span class="box-content">
									<span class="product-popup-title">Previous Product</span>
								</span>
							</span>
						</a>
					</div>

					<div class="product-next">
						<a href="#">
							<span class="product-link"></span>
							<span class="product-popup">
								<span class="box-content">
									<span class="product-popup-title">Next Product</span>
								</span>
							</span>
						</a>
					</div>
				</div>

				<div class="price-box">
					<?php if (has_discount($product)): ?>
						<span class="old-price"><?= format_rupiah($product->price) ?></span>
						<span class="product-price"><?= format_rupiah(get_final_price($product)) ?></span>
					<?php else: ?>
						<span class="product-price"><?= format_rupiah($product->price) ?></span>
					<?php endif; ?>
				</div>

				<div class="product-desc">
					<p><?= nl2br(strip_tags($product->description)) ?></p>
				</div>

				<ul class="single-info-list">
					<li>
						SKU: <strong><?= $product->sku ?? '-' ?></strong>
					</li>
					<li>
						KATEGORI: <strong><a href="<?= base_url('product/category/' . $product->category_slug) ?>"><?= $product->category_name ?></a></strong>
					</li>
					<?php if ($product->brand_name): ?>
					<li>
						BRAND: <strong><?= $product->brand_name ?></strong>
					</li>
					<?php endif; ?>
				</ul>

				<div class="product-action">
					<?php if ($product->stock > 0): ?>
						<div class="product-single-qty">
							<div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
								<span class="input-group-btn input-group-prepend">
									<button class="btn btn-outline btn-down-icon bootstrap-touchspin-down" type="button" onclick="decreaseQty()"></button>
								</span>
								<input class="horizontal-quantity form-control" id="quantity" type="text" value="1" min="1" max="<?= $product->stock ?>">
								<span class="input-group-btn input-group-append">
									<button class="btn btn-outline btn-up-icon bootstrap-touchspin-up" type="button" onclick="increaseQty()"></button>
								</span>
							</div>
						</div>

						<a href="javascript:void(0)" class="btn btn-dark add-cart icon-shopping-cart mr-2" onclick="addToCartDetail()" title="Add to Cart">Tambah ke Keranjang</a>

						<?php if ($customer_logged_in): ?>
							<a href="javascript:void(0)" class="btn btn-gray add-wishlist icon-wishlist-2 mr-2" onclick="addToWishlist(<?= $product->product_id ?>)" title="Add to Wishlist">Tambah ke Wishlist</a>
						<?php else: ?>
							<a href="<?= base_url('customer/login') ?>" class="btn btn-gray add-wishlist icon-wishlist-2 mr-2" title="Login untuk menambahkan ke Wishlist">Tambah ke Wishlist</a>
						<?php endif; ?>

						<a href="javascript:void(0)" class="btn btn-success btn-whatsapp-product" onclick="chatAdminWhatsApp()" title="Chat Admin via WhatsApp">
							<i class="fab fa-whatsapp"></i> Chat Admin
						</a>
					<?php else: ?>
						<div class="alert alert-warning">
							<strong>Stok habis!</strong> Produk ini sedang tidak tersedia.
						</div>

						<a href="javascript:void(0)" class="btn btn-success btn-whatsapp-product mt-2" onclick="chatAdminWhatsApp()" title="Chat Admin via WhatsApp">
							<i class="fab fa-whatsapp"></i> Tanya Ketersediaan
						</a>
					<?php endif; ?>
				</div>

				<hr class="divider mb-0 mt-0">

				<div class="product-single-share mb-3">
					<label class="sr-only">Share:</label>

					<div class="social-icons mr-2">
						<a href="javascript:void(0)" onclick="shareToFacebook()" class="social-icon social-facebook icon-facebook" title="Share to Facebook"></a>
						<a href="javascript:void(0)" onclick="shareToTwitter()" class="social-icon social-twitter icon-twitter" title="Share to Twitter"></a>
						<a href="javascript:void(0)" onclick="copyProductLink()" class="social-icon social-instagram icon-instagram" title="Copy Link"></a>
						<a href="javascript:void(0)" onclick="shareToWhatsApp()" class="social-icon social-whatsapp fab fa-whatsapp" title="Share to WhatsApp"></a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="product-single-tabs">
		<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="product-tab-desc" data-toggle="tab" href="#product-desc-content" role="tab" aria-controls="product-desc-content" aria-selected="true">Deskripsi</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="product-tab-info" data-toggle="tab" href="#product-info-content" role="tab" aria-controls="product-info-content" aria-selected="false">Informasi Produk</a>
			</li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane fade show active" id="product-desc-content" role="tabpanel" aria-labelledby="product-tab-desc">
				<div class="product-desc-content">
					<?= $product->description ?>
				</div>
			</div>

			<div class="tab-pane fade" id="product-info-content" role="tabpanel" aria-labelledby="product-tab-info">
				<div class="product-desc-content">
					<table class="table table-striped">
						<tbody>
							<tr>
								<th>SKU:</th>
								<td><?= $product->sku ?? '-' ?></td>
							</tr>
							<tr>
								<th>Kategori:</th>
								<td><?= $product->category_name ?></td>
							</tr>
							<?php if ($product->brand_name): ?>
							<tr>
								<th>Brand:</th>
								<td><?= $product->brand_name ?></td>
							</tr>
							<?php endif; ?>
							<tr>
								<th>Stok:</th>
								<td><?= $product->stock > 0 ? $product->stock . ' unit' : 'Habis' ?></td>
							</tr>
							<tr>
								<th>Dilihat:</th>
								<td><?= $product->views ?> kali</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<?php if (!empty($related_products)): ?>
	<div class="products-section pt-0">
		<h2 class="section-title">Produk Terkait</h2>

		<div class="products-slider owl-carousel owl-theme dots-top dots-small">
			<?php foreach ($related_products as $related): ?>
			<div class="product-default">
				<figure>
					<a href="<?= base_url('product/detail/' . $related->product_slug) ?>">
						<img src="<?= get_product_image($related->main_image) ?>" width="280" height="280" alt="<?= $related->product_name ?>">
					</a>
					<?php if (has_discount($related)): ?>
					<div class="label-group">
						<div class="product-label label-sale">-<?= calculate_discount_percentage($related->price, $related->discount_price) ?>%</div>
					</div>
					<?php endif; ?>
				</figure>
				<div class="product-details">
					<h3 class="product-title">
						<a href="<?= base_url('product/detail/' . $related->product_slug) ?>"><?= truncate_text($related->product_name, 50) ?></a>
					</h3>
					<div class="price-box">
						<?php if (has_discount($related)): ?>
							<span class="old-price"><?= format_rupiah($related->price) ?></span>
							<span class="product-price"><?= format_rupiah(get_final_price($related)) ?></span>
						<?php else: ?>
							<span class="product-price"><?= format_rupiah($related->price) ?></span>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php endif; ?>
</div>

<script>
function increaseQty() {
	var qty = parseInt($('#quantity').val());
	var max = parseInt($('#quantity').attr('max'));
	if (qty < max) {
		$('#quantity').val(qty + 1);
	}
}

function decreaseQty() {
	var qty = parseInt($('#quantity').val());
	if (qty > 1) {
		$('#quantity').val(qty - 1);
	}
}

function addToCartDetail() {
	var productId = <?= $product->product_id ?>;
	var quantity = parseInt($('#quantity').val());

	$.ajax({
		url: BASE_URL + 'cart/add',
		type: 'POST',
		data: { product_id: productId, quantity: quantity },
		dataType: 'json',
		beforeSend: function() {
			$('.add-cart').prop('disabled', true).html('<i class="icon-spinner spinner"></i> Loading...');
		},
		success: function(response) {
			if (response.status === 'success') {
				// Update cart count in header
				if (response.cart_count && $('.cart-count').length) {
					$('.cart-count').text(response.cart_count);
				}

				// Show success notification
				if (typeof showNotification === 'function') {
					showNotification('success', response.message);
				} else {
					alert(response.message);
				}

				// Reset quantity to 1
				$('#quantity').val(1);
			} else {
				if (typeof showNotification === 'function') {
					showNotification('error', response.message);
				} else {
					alert(response.message || 'Gagal menambahkan produk ke keranjang');
				}
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
		},
		complete: function() {
			$('.add-cart').prop('disabled', false).html('<i class="icon-shopping-cart"></i> Tambah ke Keranjang');
		}
	});
}

function addToWishlist(productId) {
	$.ajax({
		url: BASE_URL + 'customer/wishlist_add',
		type: 'POST',
		data: { product_id: productId },
		dataType: 'json',
		beforeSend: function() {
			$('.add-wishlist').prop('disabled', true).html('<i class="icon-spinner spinner"></i> Loading...');
		},
		success: function(response) {
			if (response.success) {
				// Update wishlist count in header if element exists
				if (response.wishlist_count && $('.wishlist-count').length) {
					$('.wishlist-count').text(response.wishlist_count);
				}

				// Show success notification
				if (typeof showNotification === 'function') {
					showNotification('success', response.message);
				} else {
					alert(response.message);
				}

				// Update button state
				$('.add-wishlist').addClass('added').html('<i class="icon-heart"></i> Ditambahkan');
			} else {
				if (typeof showNotification === 'function') {
					showNotification('error', response.message);
				} else {
					alert(response.message || 'Gagal menambahkan produk ke wishlist');
				}
			}
		},
		error: function(xhr, status, error) {
			console.error('Error:', error);
			var message = 'Terjadi kesalahan. Silakan coba lagi.';
			if (xhr.responseJSON && xhr.responseJSON.message) {
				message = xhr.responseJSON.message;
			}
			if (typeof showNotification === 'function') {
				showNotification('error', message);
			} else {
				alert(message);
			}
		},
		complete: function() {
			setTimeout(function() {
				if (!$('.add-wishlist').hasClass('added')) {
					$('.add-wishlist').prop('disabled', false).html('<i class="icon-wishlist-2"></i> Tambah ke Wishlist');
				}
			}, 500);
		}
	});
}

// Chat Admin WhatsApp Function
function chatAdminWhatsApp() {
	var productName = '<?= addslashes($product->product_name) ?>';
	var productUrl = window.location.href;
	var productPrice = '<?= format_rupiah(get_final_price($product)) ?>';
	var productStock = <?= $product->stock ?>;
	var whatsappNumber = '<?= $site_settings['contact_whatsapp'] ?? $site_settings['whatsapp_number'] ?? $site_settings['site_whatsapp'] ?? '' ?>';

	// Validate WhatsApp number
	if (!whatsappNumber || whatsappNumber.trim() === '') {
		alert('Nomor WhatsApp admin belum tersedia. Silakan hubungi melalui kontak lain.');
		return false;
	}

	// Create message based on stock availability
	var message = '';
	if (productStock > 0) {
		message = 'Halo Admin Putra Elektronik,\n\n';
		message += 'Saya tertarik dengan produk berikut:\n';
		message += '*' + productName + '*\n';
		message += 'Harga: ' + productPrice + '\n';
		message += 'Stok: Tersedia\n\n';
		message += 'Link Produk: ' + productUrl + '\n\n';
		message += 'Saya ingin bertanya lebih lanjut tentang produk ini.';
	} else {
		message = 'Halo Admin Putra Elektronik,\n\n';
		message += 'Saya ingin menanyakan ketersediaan produk berikut:\n';
		message += '*' + productName + '*\n';
		message += 'Harga: ' + productPrice + '\n';
		message += 'Status: Stok Habis\n\n';
		message += 'Link Produk: ' + productUrl + '\n\n';
		message += 'Apakah produk ini akan segera tersedia kembali?';
	}

	var encodedMessage = encodeURIComponent(message);

	// Use wa.me format (works for both mobile and desktop)
	var whatsappUrl = 'https://wa.me/' + whatsappNumber + '?text=' + encodedMessage;

	window.open(whatsappUrl, '_blank');
	return false;
}

// Social Media Share Functions
function shareToFacebook() {
	var productUrl = encodeURIComponent(window.location.href);
	var shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + productUrl;
	window.open(shareUrl, 'facebook-share-dialog', 'width=800,height=600');
	return false;
}

function shareToTwitter() {
	var productName = '<?= addslashes($product->product_name) ?>';
	var productUrl = encodeURIComponent(window.location.href);
	var shareText = encodeURIComponent('Check out this product: ' + productName);
	var shareUrl = 'https://twitter.com/intent/tweet?text=' + shareText + '&url=' + productUrl;
	window.open(shareUrl, 'twitter-share-dialog', 'width=800,height=600');
	return false;
}

function shareToWhatsApp() {
	var productName = '<?= addslashes($product->product_name) ?>';
	var productUrl = window.location.href;
	var shareText = encodeURIComponent('Halo, saya ingin berbagi produk ini dengan Anda:\n\n*' + productName + '*\n\n' + productUrl);

	// Use wa.me format (works for both mobile and desktop)
	var shareUrl = 'https://wa.me/?text=' + shareText;

	window.open(shareUrl, '_blank');
	return false;
}

function copyProductLink() {
	var productUrl = window.location.href;

	// Create temporary input element
	var tempInput = document.createElement('input');
	tempInput.value = productUrl;
	document.body.appendChild(tempInput);
	tempInput.select();
	tempInput.setSelectionRange(0, 99999); // For mobile devices

	try {
		// Copy to clipboard
		document.execCommand('copy');
		alert('Link produk berhasil disalin ke clipboard!');
	} catch (err) {
		// Fallback for modern browsers
		navigator.clipboard.writeText(productUrl).then(function() {
			alert('Link produk berhasil disalin ke clipboard!');
		}, function() {
			alert('Gagal menyalin link. Silakan salin manual: ' + productUrl);
		});
	}

	// Remove temporary input
	document.body.removeChild(tempInput);
	return false;
}
</script>
