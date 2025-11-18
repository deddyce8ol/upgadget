<!-- Banner Slider - Full Width -->
<?php if (!empty($banners)): ?>
<div class="intro-section bg-lighter pt-5 pb-6">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="intro-slider-container slider-container-ratio slider-container-1">
					<div class="intro-slider owl-carousel owl-simple owl-dark owl-nav-inside" data-toggle="owl" data-owl-options='{
						"nav": true,
						"dots": true,
						"loop": true,
						"autoplay": true,
						"autoplayTimeout": 5000,
						"autoplayHoverPause": true,
						"responsive": {
							"0": {
								"nav": false,
								"dots": true
							},
							"768": {
								"nav": true,
								"dots": true
							}
						}
					}'>
						<?php foreach ($banners as $banner): ?>
						<div class="intro-slide">
							<?php if (!empty($banner->banner_link)): ?>
								<a href="<?= $banner->banner_link ?>" class="banner-link-wrapper" title="<?= $banner->banner_title ?>">
									<figure class="slide-image">
										<img src="<?= get_banner_image($banner->banner_image) ?>" alt="<?= $banner->banner_title ?>">
									</figure>
								</a>
							<?php else: ?>
								<figure class="slide-image">
									<img src="<?= get_banner_image($banner->banner_image) ?>" alt="<?= $banner->banner_title ?>">
								</figure>
							<?php endif; ?>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- Official Brand Slider -->
<?php if (!empty($brands)): ?>
<section class="brand-slider-section">
	<div class="container">
		<h2 class="section-title text-center">Temukan Produk Berdasarkan Brand</h2>

		<div class="brand-slider owl-carousel owl-theme">
			<?php foreach ($brands as $brand): ?>
			<div class="brand-item">
				<?php if (!empty($brand->brand_logo)): ?>
					<a href="<?= base_url('product?brand=' . $brand->brand_slug) ?>" title="<?= $brand->brand_name ?>">
						<img src="<?= base_url('uploads/brands/' . $brand->brand_logo) ?>" alt="<?= $brand->brand_name ?>" class="brand-logo">
					</a>
				<?php else: ?>
					<a href="<?= base_url('product?brand=' . $brand->brand_slug) ?>" title="<?= $brand->brand_name ?>" class="brand-name-link">
						<div class="brand-name-text"><?= $brand->brand_name ?></div>
					</a>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- Budget Finder Section -->
<section class="budget-finder-section bg-lighter pt-5 pb-5">
	<div class="container">
		<h2 class="section-title text-center mb-4">Temukan Produk Terbaik Berdasarkan Budget Mu</h2>
		<div class="row">
			<div class="col-md-12">
				<div class="budget-slider owl-carousel owl-theme">
					<!-- 1 Jutaan -->
					<div class="budget-card">
						<a href="<?= base_url('product?price_min=1000000&price_max=1999999') ?>" title="Produk 1 Jutaan">
							<div class="budget-label">1 Jutaan</div>
							<div class="budget-range">Rp 1 - 2 Juta</div>
						</a>
					</div>

					<!-- 2 Jutaan -->
					<div class="budget-card">
						<a href="<?= base_url('product?price_min=2000000&price_max=2999999') ?>" title="Produk 2 Jutaan">
							<div class="budget-label">2 Jutaan</div>
							<div class="budget-range">Rp 2 - 3 Juta</div>
						</a>
					</div>

					<!-- 3 Jutaan -->
					<div class="budget-card">
						<a href="<?= base_url('product?price_min=3000000&price_max=3999999') ?>" title="Produk 3 Jutaan">
							<div class="budget-label">3 Jutaan</div>
							<div class="budget-range">Rp 3 - 4 Juta</div>
						</a>
					</div>

					<!-- 4 Jutaan -->
					<div class="budget-card">
						<a href="<?= base_url('product?price_min=4000000&price_max=4999999') ?>" title="Produk 4 Jutaan">
							<div class="budget-label">4 Jutaan</div>
							<div class="budget-range">Rp 4 - 5 Juta</div>
						</a>
					</div>

					<!-- 5 Jutaan -->
					<div class="budget-card">
						<a href="<?= base_url('product?price_min=5000000&price_max=5999999') ?>" title="Produk 5 Jutaan">
							<div class="budget-label">5 Jutaan</div>
							<div class="budget-range">Rp 5 - 6 Juta</div>
						</a>
					</div>

					<!-- 6 Jutaan -->
					<div class="budget-card">
						<a href="<?= base_url('product?price_min=6000000&price_max=6999999') ?>" title="Produk 6 Jutaan">
							<div class="budget-label">6 Jutaan</div>
							<div class="budget-range">Rp 6 - 7 Juta</div>
						</a>
					</div>

					<!-- 7 Jutaan -->
					<div class="budget-card">
						<a href="<?= base_url('product?price_min=7000000&price_max=7999999') ?>" title="Produk 7 Jutaan">
							<div class="budget-label">7 Jutaan</div>
							<div class="budget-range">Rp 7 - 8 Juta</div>
						</a>
					</div>

					<!-- 8 Jutaan -->
					<div class="budget-card">
						<a href="<?= base_url('product?price_min=8000000&price_max=8999999') ?>" title="Produk 8 Jutaan">
							<div class="budget-label">8 Jutaan</div>
							<div class="budget-range">Rp 8 - 9 Juta</div>
						</a>
					</div>

					<!-- 9 Jutaan -->
					<div class="budget-card">
						<a href="<?= base_url('product?price_min=9000000&price_max=9999999') ?>" title="Produk 9 Jutaan">
							<div class="budget-label">9 Jutaan</div>
							<div class="budget-range">Rp 9 - 10 Juta</div>
						</a>
					</div>

					<!-- Flagship (≥10 Juta) -->
					<div class="budget-card budget-card-flagship">
						<a href="<?= base_url('product?price_min=10000000') ?>" title="Produk Flagship">
							<div class="budget-label">Flagship</div>
							<div class="budget-range">≥ Rp 10 Juta</div>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Featured Products - Demo4 Style -->
<?php if (!empty($featured_products)): ?>
<section class="featured-products-section">
	<div class="container">
		<h2 class="section-title heading-border ls-20 border-0">Produk Unggulan</h2>

		<div class="products-slider custom-products owl-carousel owl-theme nav-outer show-nav-hover nav-image-center" data-toggle="owl" data-owl-options='{
			"dots": false,
			"nav": true,
			"margin": 20,
			"loop": false,
			"responsive": {
				"0": {
					"items": 2
				},
				"480": {
					"items": 2
				},
				"768": {
					"items": 3
				},
				"992": {
					"items": 4
				},
				"1200": {
					"items": 5
				}
			}
		}'>
			<?php foreach ($featured_products as $product): ?>
			<div class="product-default appear-animate" data-animation-name="fadeInRightShorter">
				<figure>
					<a href="<?= base_url('product/detail/' . $product->product_slug) ?>">
						<img src="<?= get_product_image($product->main_image) ?>" width="280" height="280" alt="<?= $product->product_name ?>">
					</a>
					<div class="label-group">
						<?php if ($product->is_featured): ?>
							<div class="product-label label-hot">HOT</div>
						<?php endif; ?>
						<?php if (has_discount($product)): ?>
							<div class="product-label label-sale">-<?= calculate_discount_percentage($product->price, $product->discount_price) ?>%</div>
						<?php endif; ?>
						<?php if ($product->stock <= 0): ?>
							<div class="product-label label-out">Habis</div>
						<?php endif; ?>
					</div>
				</figure>
				<div class="product-details">
					<div class="category-list">
						<a href="<?= base_url('product/category/' . $product->category_name) ?>" class="product-category"><?= $product->category_name ?></a>
					</div>
					<h3 class="product-title">
						<a href="<?= base_url('product/detail/' . $product->product_slug) ?>"><?= truncate_text($product->product_name, 50) ?></a>
					</h3>
					<div class="price-box">
						<?php if (has_discount($product)): ?>
							<del class="old-price"><?= format_rupiah($product->price) ?></del>
							<span class="product-price"><?= format_rupiah(get_final_price($product)) ?></span>
						<?php else: ?>
							<span class="product-price"><?= format_rupiah($product->price) ?></span>
						<?php endif; ?>
					</div>
					<div class="product-action">
						<?php if ($customer_logged_in): ?>
							<a href="javascript:void(0)" class="btn-icon-wish" title="Wishlist" onclick="addToWishlist(<?= $product->product_id ?>)"><i class="icon-heart"></i></a>
						<?php endif; ?>
						<?php if ($product->stock > 0): ?>
							<a href="javascript:void(0)" class="btn-icon btn-add-cart" onclick="addToCart(<?= $product->product_id ?>)"><i class="fa fa-arrow-right"></i><span>TAMBAH</span></a>
						<?php else: ?>
							<a href="#" class="btn-icon btn-add-cart disabled"><i class="fa fa-times"></i><span>HABIS</span></a>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>

		<div class="more-container text-center mt-4 mb-2">
			<a href="<?= base_url('product') ?>" class="btn btn-outline-primary btn-more"><span>Lihat Semua Produk</span><i class="icon-long-arrow-right ml-1"></i></a>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- New Products - Demo4 Style -->
<?php if (!empty($new_products)): ?>
<section class="featured-products-section">
	<div class="container">
		<h2 class="section-title heading-border ls-20 border-0">Produk Terbaru</h2>

		<div class="products-slider custom-products owl-carousel owl-theme nav-outer show-nav-hover nav-image-center" data-toggle="owl" data-owl-options='{
			"dots": false,
			"nav": true,
			"margin": 20,
			"loop": false,
			"responsive": {
				"0": {
					"items": 2
				},
				"480": {
					"items": 2
				},
				"768": {
					"items": 3
				},
				"992": {
					"items": 4
				},
				"1200": {
					"items": 5
				}
			}
		}'>
			<?php foreach ($new_products as $product): ?>
			<div class="product-default appear-animate" data-animation-name="fadeInRightShorter">
				<figure>
					<a href="<?= base_url('product/detail/' . $product->product_slug) ?>">
						<img src="<?= get_product_image($product->main_image) ?>" width="280" height="280" alt="<?= $product->product_name ?>">
					</a>
					<div class="label-group">
						<?php if (has_discount($product)): ?>
							<div class="product-label label-sale">-<?= calculate_discount_percentage($product->price, $product->discount_price) ?>%</div>
						<?php endif; ?>
						<?php if ($product->stock <= 0): ?>
							<div class="product-label label-out">Habis</div>
						<?php endif; ?>
					</div>
				</figure>
				<div class="product-details">
					<div class="category-list">
						<a href="<?= base_url('product/category/' . $product->category_name) ?>" class="product-category"><?= $product->category_name ?></a>
					</div>
					<h3 class="product-title">
						<a href="<?= base_url('product/detail/' . $product->product_slug) ?>"><?= truncate_text($product->product_name, 50) ?></a>
					</h3>
					<div class="price-box">
						<?php if (has_discount($product)): ?>
							<del class="old-price"><?= format_rupiah($product->price) ?></del>
							<span class="product-price"><?= format_rupiah(get_final_price($product)) ?></span>
						<?php else: ?>
							<span class="product-price"><?= format_rupiah($product->price) ?></span>
						<?php endif; ?>
					</div>
					<div class="product-action">
						<?php if ($customer_logged_in): ?>
							<a href="javascript:void(0)" class="btn-icon-wish" title="Wishlist" onclick="addToWishlist(<?= $product->product_id ?>)"><i class="icon-heart"></i></a>
						<?php endif; ?>
						<?php if ($product->stock > 0): ?>
							<a href="javascript:void(0)" class="btn-icon btn-add-cart" onclick="addToCart(<?= $product->product_id ?>)"><i class="fa fa-arrow-right"></i><span>TAMBAH</span></a>
						<?php else: ?>
							<a href="#" class="btn-icon btn-add-cart disabled"><i class="fa fa-times"></i><span>HABIS</span></a>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>
