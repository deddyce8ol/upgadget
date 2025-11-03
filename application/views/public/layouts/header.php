<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<title><?= $page_title ?></title>

	<meta name="keywords" content="<?= $meta_keywords ?>" />
	<meta name="description" content="<?= $meta_description ?>">
	<meta name="author" content="Putra Elektronik">

	<!-- Open Graph Meta Tags -->
	<meta property="og:title" content="<?= $og_title ?>" />
	<meta property="og:description" content="<?= $og_description ?>" />
	<meta property="og:image" content="<?= $og_image ?>" />
	<meta property="og:url" content="<?= $og_url ?>" />
	<meta property="og:type" content="website" />
	<meta property="og:site_name" content="<?= $site_settings['site_name'] ?? 'Putra Elektronik' ?>" />

	<!-- Twitter Card Meta Tags -->
	<meta name="twitter:card" content="<?= $twitter_card_type ?? 'summary_large_image' ?>" />
	<meta name="twitter:title" content="<?= $og_title ?>" />
	<meta name="twitter:description" content="<?= $og_description ?>" />
	<meta name="twitter:image" content="<?= $og_image ?>" />

	<!-- Favicon -->
	<?php
	$favicon_url = !empty($site_settings['site_favicon'])
		? base_url('uploads/' . $site_settings['site_favicon'])
		: base_url('assets/images/icons/favicon.ico');
	?>
	<link rel="icon" type="image/x-icon" href="<?= $favicon_url ?>">
	<link rel="icon" type="image/png" sizes="16x16" href="<?= $favicon_url ?>">
	<link rel="icon" type="image/png" sizes="32x32" href="<?= $favicon_url ?>">
	<link rel="apple-touch-icon" sizes="180x180" href="<?= $favicon_url ?>">
	<link rel="icon" type="image/png" sizes="192x192" href="<?= $favicon_url ?>">
	<link rel="icon" type="image/png" sizes="512x512" href="<?= $favicon_url ?>">

	<!-- PWA Manifest -->
	<link rel="manifest" href="<?= base_url('manifest.json') ?>">
	<meta name="theme-color" content="#0052CC">

	<script>
		WebFontConfig = {
			google: { families: ['Open+Sans:300,400,600,700,800', 'Poppins:300,400,500,600,700,800', 'Oswald:300,400,500,600,700,800'] }
		};
		(function (d) {
			var wf = d.createElement('script'), s = d.scripts[0];
			wf.src = '<?= base_url('assets/js/webfont.js') ?>';
			wf.async = true;
			s.parentNode.insertBefore(wf, s);
		})(document);
	</script>

	<!-- Plugins CSS File -->
	<link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">

	<!-- Main CSS File -->
	<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
	<link rel="stylesheet" type="text/css" href="<?= base_url('assets/vendor/fontawesome-free/css/all.min.css') ?>">

	<!-- Custom Theme CSS - Putra Elektronik Brand Colors -->
	<link rel="stylesheet" href="<?= base_url('assets/css/custom-theme.css') ?>">

	<!-- Custom CSS -->
	<?php if (isset($custom_css)): ?>
		<?php foreach ($custom_css as $css): ?>
			<link rel="stylesheet" href="<?= base_url($css) ?>">
		<?php endforeach; ?>
	<?php endif; ?>
</head>

<body>
	<div class="page-wrapper">
		<div class="top-notice bg-primary text-white">
			<div class="container text-center">
				<h5 class="d-inline-block">Promo <b>Diskon hingga 40%</b> Untuk Produk Elektronik Pilihan</h5>
				<small class="ml-2">* Berlaku untuk periode tertentu.</small>
				<button title="Close (Esc)" type="button" class="mfp-close">×</button>
			</div><!-- End .container -->
		</div><!-- End .top-notice -->

		<header class="header">
			<div class="header-top">
				<div class="container">
					<div class="header-left d-none d-sm-block">
						<p class="top-message text-uppercase"><?= $site_settings['site_tagline'] ?? 'Toko Elektronik Terpercaya' ?></p>
					</div><!-- End .header-left -->

					<div class="header-right header-dropdowns ml-0 ml-sm-auto w-sm-100">
						<div class="header-dropdown dropdown-expanded d-none d-lg-block">
							<a href="#">Links</a>
							<div class="header-menu">
								<ul>
									<?php if ($customer_logged_in): ?>
										<li><a href="<?= base_url('customer/account') ?>">Akun Saya</a></li>
										<li><a href="<?= base_url('customer/orders') ?>">Pesanan Saya</a></li>
										<li><a href="<?= base_url('customer/wishlist') ?>">Wishlist</a></li>
										<li><a href="<?= base_url('cart') ?>">Keranjang</a></li>
										<li><a href="<?= base_url('customer/logout') ?>">Logout</a></li>
									<?php else: ?>
										<li><a href="<?= base_url('page/about') ?>">Tentang Kami</a></li>
										<li><a href="<?= base_url('customer/wishlist') ?>">Wishlist</a></li>
										<li><a href="<?= base_url('cart') ?>">Keranjang</a></li>
										<li><a href="<?= base_url('customer/login') ?>" class="login-link">Login</a></li>
									<?php endif; ?>
								</ul>
							</div><!-- End .header-menu -->
						</div><!-- End .header-dropown -->

						<span class="separator"></span>

						<div class="header-dropdown mr-auto mr-sm-3 mr-md-0">
							<a href="#"><?= $site_settings['currency_code'] ?? 'IDR' ?></a>
							<div class="header-menu">
								<ul>
									<li><a href="#"><?= $site_settings['currency_code'] ?? 'IDR' ?></a></li>
								</ul>
							</div><!-- End .header-menu -->
						</div><!-- End .header-dropown -->

						<span class="separator"></span>

						<div class="social-icons">
							<?php if (!empty($site_settings['facebook_url'])): ?>
								<a href="<?= $site_settings['facebook_url'] ?>" class="social-icon social-facebook icon-facebook" target="_blank" title="Facebook"></a>
							<?php endif; ?>
							<?php if (!empty($site_settings['instagram_url'])): ?>
								<a href="<?= $site_settings['instagram_url'] ?>" class="social-icon social-instagram icon-instagram" target="_blank" title="Instagram"></a>
							<?php endif; ?>
							<?php if (!empty($site_settings['tiktok_url'])): ?>
								<a href="<?= $site_settings['tiktok_url'] ?>" class="social-icon social-tiktok" target="_blank" title="TikTok">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="width: 14px; height: 14px; fill: currentColor;"><path d="M448 209.9a210.1 210.1 0 0 1 -122.8-39.3V349.4A162.6 162.6 0 1 1 185 188.3V278.2a74.6 74.6 0 1 0 52.2 71.2V0l88 0a121.2 121.2 0 0 0 1.9 22.2h0A122.2 122.2 0 0 0 381 102.4a121.4 121.4 0 0 0 67 20.1z"/></svg>
								</a>
							<?php endif; ?>
							<?php if (!empty($site_settings['shopee_url'])): ?>
								<a href="<?= $site_settings['shopee_url'] ?>" class="social-icon social-shopee" target="_blank" title="Shopee" style="color: #ee4d2d;">
									<i class="fas fa-shopping-bag"></i>
								</a>
							<?php endif; ?>
						</div><!-- End .social-icons -->
					</div><!-- End .header-right -->
				</div><!-- End .container -->
			</div><!-- End .header-top -->

			<div class="header-middle sticky-header" data-sticky-options="{'mobile': true}">
				<div class="container">
					<div class="header-left col-lg-2 w-auto pl-0">
						<button class="mobile-menu-toggler text-primary mr-2" type="button">
							<i class="fas fa-bars"></i>
						</button>
						<a href="<?= base_url() ?>" class="logo d-flex align-items-center">
							<?php
							$logo_url = !empty($site_settings['site_logo'])
								? base_url('uploads/' . $site_settings['site_logo'])
								: base_url('assets/images/logo-putra-elektronik.png');
							$site_name = $site_settings['site_name'] ?? 'Putra Elektronik';
							$logo_text_parts = explode(' ', $site_name, 2);
							?>
							<img src="<?= $logo_url ?>" width="auto" height="45" alt="<?= $site_name ?>" style="max-height: 45px; width: auto;">
							<div class="logo-text">
								<?php if (count($logo_text_parts) == 2): ?>
									<div><?= strtoupper($logo_text_parts[0]) ?></div>
									<div><?= strtoupper($logo_text_parts[1]) ?></div>
								<?php else: ?>
									<div><?= strtoupper($site_name) ?></div>
								<?php endif; ?>
							</div>
						</a>
					</div><!-- End .header-left -->

					<div class="header-right w-lg-max">
						<div class="header-icon header-search header-search-inline header-search-category w-lg-max text-right mt-0">
							<a href="#" class="search-toggle" role="button"><i class="icon-search-3"></i></a>
							<form action="<?= base_url('product/search') ?>" method="get">
								<div class="header-search-wrapper">
									<input type="search" class="form-control" name="q" id="q" placeholder="Cari produk..." required>
									<div class="select-custom">
										<select id="cat" name="cat">
											<option value="">Semua Kategori</option>
											<?php if (!empty($categories)): ?>
												<?php foreach ($categories as $cat): ?>
													<option value="<?= $cat->id ?>"><?= $cat->name ?></option>
												<?php endforeach; ?>
											<?php endif; ?>
										</select>
									</div><!-- End .select-custom -->
									<button class="btn icon-magnifier p-0" title="search" type="submit"></button>
								</div><!-- End .header-search-wrapper -->
							</form>
						</div><!-- End .header-search -->

						<div class="header-contact d-none d-lg-flex pl-4 pr-4">
							<img alt="phone" src="<?= base_url('assets/images/phone.png') ?>" width="30" height="30" class="pb-1">
							<h6><span>Hubungi Kami</span><a href="tel:<?= $site_settings['contact_phone'] ?? $site_settings['site_phone'] ?? '' ?>" class="text-dark font1"><?= $site_settings['contact_phone'] ?? $site_settings['site_phone'] ?? '+62 812 3456 789' ?></a></h6>
						</div>

						<a href="<?= base_url('customer/login') ?>" class="header-icon" title="login"><i class="icon-user-2"></i></a>

						<a href="<?= base_url('customer/wishlist') ?>" class="header-icon" title="wishlist">
							<i class="icon-wishlist-2"></i>
							<?php if ($wishlist_count > 0): ?>
								<span class="wishlist-count badge-circle"><?= $wishlist_count ?></span>
							<?php endif; ?>
						</a>

						<a href="<?= base_url('cart') ?>" class="header-icon" title="Cart">
							<i class="minicart-icon"></i>
							<span class="cart-count badge-circle"><?= $cart_count ?></span>
						</a>
					</div><!-- End .header-right -->
				</div><!-- End .container -->
			</div><!-- End .header-middle -->

			<div class="header-bottom sticky-header d-none d-lg-block" data-sticky-options="{'mobile': false}">
				<div class="container">
					<nav class="main-nav w-100">
						<ul class="menu">
							<li class="active">
								<a href="<?= base_url() ?>">Home</a>
							</li>
							<li>
								<a href="<?= base_url('product') ?>">Produk</a>
							</li>
							<?php if (!empty($categories)): ?>
								<?php foreach (array_slice($categories, 0, 6) as $cat): ?>
								<li>
									<a href="<?= base_url('product/category/' . $cat->slug) ?>"><?= $cat->name ?></a>
								</li>
								<?php endforeach; ?>
							<?php endif; ?>
						</ul>
					</nav>
				</div><!-- End .container -->
			</div><!-- End .header-bottom -->
		</header><!-- End .header -->

		<main class="main">
