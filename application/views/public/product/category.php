<div class="container">
	<nav aria-label="breadcrumb" class="breadcrumb-nav">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?= base_url() ?>"><i class="icon-home"></i></a></li>
			<li class="breadcrumb-item"><a href="<?= base_url('product') ?>">Produk</a></li>
			<li class="breadcrumb-item active" aria-current="page"><?= $category->name ?></li>
		</ol>
	</nav>

	<div class="category-banner-container bg-gray mb-3">
		<?php
			// Use custom banner if available, otherwise use default
			$banner_url = !empty($category->banner_image)
				? base_url('uploads/categories/' . $category->banner_image)
				: base_url('assets/images/banners/banner-top.jpg');
		?>
		<div class="category-banner banner text-uppercase" style="background: no-repeat 60%/cover url('<?= $banner_url ?>');">
			<div class="container position-relative">
				<div class="row">
					<div class="pl-lg-5 pb-5 pb-md-0 col-sm-5 col-xl-4 col-lg-4 offset-1">
						<h3 class="mb-2 text-white"><?= $category->name ?></h3>
						<p class="text-white mb-0"><?= $category->description ?? '' ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-9 main-content">
			<nav class="toolbox sticky-header" data-sticky-options="{'mobile': true}">
				<div class="toolbox-left">
					<div class="toolbox-item toolbox-sort">
						<label>Urutkan:</label>
						<div class="select-custom">
							<select name="sort" class="form-control" onchange="window.location.href='<?= current_url() ?>?sort='+this.value">
								<option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Terbaru</option>
								<option value="popular" <?= $sort == 'popular' ? 'selected' : '' ?>>Terpopuler</option>
								<option value="price_low" <?= $sort == 'price_low' ? 'selected' : '' ?>>Harga: Rendah ke Tinggi</option>
								<option value="price_high" <?= $sort == 'price_high' ? 'selected' : '' ?>>Harga: Tinggi ke Rendah</option>
							</select>
						</div>
					</div>
				</div>

				<div class="toolbox-right">
					<div class="toolbox-item toolbox-show">
						<label>Tampilkan:</label>
						<div class="select-custom">
							<select name="count" class="form-control">
								<option value="12" selected>12</option>
								<option value="24">24</option>
								<option value="36">36</option>
							</select>
						</div>
					</div>
				</div>
			</nav>

			<div class="row">
				<?php if (!empty($products)): ?>
					<?php foreach ($products as $product): ?>
					<div class="col-6 col-sm-4">
						<div class="product-default">
							<figure class="product-image-container">
								<a href="<?= base_url('product/detail/' . $product->product_slug) ?>">
									<img src="<?= get_product_image($product->main_image) ?>" class="product-image" alt="<?= $product->product_name ?>">
								</a>

								<?php if (has_discount($product) || $product->is_featured): ?>
								<div class="label-group">
									<?php if ($product->is_featured): ?>
										<div class="product-label label-hot">HOT</div>
									<?php endif; ?>
									<?php if (has_discount($product)): ?>
										<div class="product-label label-sale">-<?= calculate_discount_percentage($product->price, $product->discount_price) ?>%</div>
									<?php endif; ?>
								</div>
								<?php endif; ?>

								<a href="javascript:void(0)" onclick="addToWishlist(<?= $product->product_id ?>)" class="btn-quickview" title="Tambah ke Wishlist"><i class="icon-wishlist-2"></i></a>
							</figure>

							<div class="product-details">
								<div class="category-wrap">
									<div class="category-list">
										<a href="<?= base_url('product/category/' . $product->category_name) ?>" class="product-category"><?= $product->category_name ?></a>
									</div>
								</div>

								<h3 class="product-title">
									<a href="<?= base_url('product/detail/' . $product->product_slug) ?>"><?= $product->product_name ?></a>
								</h3>

								<div class="price-box">
									<?php if (has_discount($product)): ?>
										<span class="old-price"><?= format_rupiah($product->price) ?></span>
										<span class="product-price"><?= format_rupiah(get_final_price($product)) ?></span>
									<?php else: ?>
										<span class="product-price"><?= format_rupiah($product->price) ?></span>
									<?php endif; ?>
								</div>

								<div class="product-action">
									<?php if ($product->stock > 0): ?>
									<a href="javascript:void(0)" onclick="addToCart(<?= $product->product_id ?>)" class="btn-add-cart"><i class="icon-shopping-cart"></i><span>TAMBAH KE KERANJANG</span></a>
									<?php else: ?>
									<a href="#" class="btn-add-cart disabled"><i class="icon-shopping-cart"></i><span>STOK HABIS</span></a>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="col-12">
						<div class="alert alert-info">
							<p class="mb-0">Tidak ada produk yang ditemukan.</p>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<?php if (!empty($pagination)): ?>
			<nav class="toolbox toolbox-pagination">
				<div class="toolbox-item toolbox-show">
					<label>Menampilkan <?= count($products) ?> dari <?= $total_products ?> produk</label>
				</div>

				<?= $pagination ?>
			</nav>
			<?php endif; ?>
		</div>

		<aside class="sidebar-shop col-lg-3 order-lg-first mobile-sidebar">
			<div class="sidebar-wrapper">
				<div class="widget">
					<h3 class="widget-title">
						<a data-toggle="collapse" href="#widget-body-2" role="button" aria-expanded="true" aria-controls="widget-body-2">Filter by Category</a>
					</h3>

					<div class="collapse show" id="widget-body-2">
						<div class="widget-body">
							<ul class="cat-list">
								<li><a href="<?= base_url('product') ?>">Semua Kategori</a></li>
								<?php if (!empty($categories)): ?>
									<?php foreach ($categories as $cat): ?>
										<li>
											<a href="<?= base_url('product/category/' . $cat->slug) ?>">
												<?= $cat->name ?>
											</a>
										</li>
									<?php endforeach; ?>
								<?php endif; ?>
							</ul>
						</div>
					</div>
				</div>

				<div class="widget">
					<h3 class="widget-title">
						<a data-toggle="collapse" href="#widget-body-1" role="button" aria-expanded="false" aria-controls="widget-body-1">Filter by Brand</a>
					</h3>

					<div class="collapse" id="widget-body-1">
						<div class="widget-body">
							<ul class="cat-list">
								<li><a href="<?= base_url('product') ?>">Semua Brand</a></li>
								<?php if (!empty($brands)): ?>
									<?php foreach ($brands as $brand): ?>
										<li>
											<a href="<?= base_url('product?brand=' . $brand->brand_id) ?>">
												<?= $brand->brand_name ?>
											</a>
										</li>
									<?php endforeach; ?>
								<?php endif; ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</aside>
	</div>
</div>

<script>
function addToCart(productId) {
	$.ajax({
		url: BASE_URL + 'cart/add',
		type: 'POST',
		data: { product_id: productId, quantity: 1 },
		dataType: 'json',
		success: function(response) {
			if (response.success) {
				alert('Produk berhasil ditambahkan ke keranjang');
				location.reload();
			} else {
				alert(response.message || 'Gagal menambahkan produk ke keranjang');
			}
		},
		error: function() {
			alert('Terjadi kesalahan. Silakan coba lagi.');
		}
	});
}

function addToWishlist(productId) {
	$.ajax({
		url: BASE_URL + 'customer/wishlist/add',
		type: 'POST',
		data: { product_id: productId },
		dataType: 'json',
		success: function(response) {
			if (response.success) {
				alert('Produk berhasil ditambahkan ke wishlist');
				location.reload();
			} else {
				alert(response.message || 'Gagal menambahkan produk ke wishlist');
			}
		},
		error: function() {
			alert('Terjadi kesalahan. Silakan coba lagi.');
		}
	});
}
</script>
