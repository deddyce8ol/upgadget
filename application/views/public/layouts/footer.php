		</main><!-- End .main -->

		<footer class="footer bg-dark">
			<div class="footer-middle">
				<div class="container">
					<div class="row">
						<div class="col-lg-4 col-sm-6">
							<div class="widget">
								<a href="<?= base_url() ?>" class="footer-logo mb-2 d-inline-block">
									<?php
									$footer_logo_url = !empty($site_settings['site_logo'])
										? base_url('uploads/' . $site_settings['site_logo'])
										: base_url('assets/images/logo-putra-elektronik.png');
									?>
									<img src="<?= $footer_logo_url ?>" alt="<?= $site_settings['site_name'] ?? 'Putra Elektronik' ?>" style="max-height: 60px; width: auto;">
								</a>
								<h4 class="widget-title mt-2"><?= $site_settings['site_name'] ?? 'Putra Elektronik' ?></h4>
								<p><?= $site_settings['site_tagline'] ?? 'Toko Elektronik Terpercaya' ?></p>
								<?php if (!empty($site_settings['site_description'])): ?>
								<p class="mb-2"><?= $site_settings['site_description'] ?></p>
								<?php endif; ?>
								<div class="widget-about-info">
									<div class="row">
										<div class="col-12">
											<p><i class="icon-location"></i> <?= $site_settings['contact_address'] ?? $site_settings['site_address'] ?? 'Jakarta, Indonesia' ?></p>
										</div>
									</div>
								</div><!-- End .widget-about-info -->
							</div><!-- End .widget -->
						</div><!-- End .col-lg-4 -->

						<div class="col-lg-2 col-sm-6">
							<div class="widget">
								<h4 class="widget-title">Informasi</h4>
								<ul class="links">
									<li><a href="<?= base_url() ?>">Home</a></li>
									<li><a href="<?= base_url('product') ?>">Produk</a></li>
									<li><a href="<?= base_url('page/about') ?>">Tentang Kami</a></li>
									<li><a href="<?= base_url('page/contact') ?>">Kontak</a></li>
								</ul>
							</div><!-- End .widget -->
						</div><!-- End .col-lg-2 -->

						<div class="col-lg-2 col-sm-6">
							<div class="widget">
								<h4 class="widget-title">Akun Saya</h4>
								<ul class="links">
									<?php if ($customer_logged_in): ?>
										<li><a href="<?= base_url('customer/account') ?>">Dashboard</a></li>
										<li><a href="<?= base_url('customer/orders') ?>">Pesanan Saya</a></li>
										<li><a href="<?= base_url('customer/wishlist') ?>">Wishlist</a></li>
										<li><a href="<?= base_url('cart') ?>">Keranjang</a></li>
									<?php else: ?>
										<li><a href="<?= base_url('customer/login') ?>">Login</a></li>
										<li><a href="<?= base_url('customer/register') ?>">Register</a></li>
									<?php endif; ?>
								</ul>
							</div><!-- End .widget -->
						</div><!-- End .col-lg-2 -->

						<div class="col-lg-4 col-sm-6">
							<div class="widget">
								<h4 class="widget-title">Hubungi Kami</h4>
								<ul class="contact-info">
									<li>
										<span class="contact-info-label">Phone:</span>
										<a href="tel:<?= $site_settings['contact_phone'] ?? $site_settings['site_phone'] ?? '' ?>"><?= $site_settings['contact_phone'] ?? $site_settings['site_phone'] ?? '' ?></a>
									</li>
									<?php if (!empty($site_settings['contact_whatsapp']) || !empty($site_settings['whatsapp_number'])): ?>
									<li>
										<span class="contact-info-label">WhatsApp:</span>
										<a href="https://wa.me/<?= $site_settings['contact_whatsapp'] ?? $site_settings['whatsapp_number'] ?? '' ?>" target="_blank"><?= $site_settings['contact_whatsapp'] ?? $site_settings['whatsapp_number'] ?? '' ?></a>
									</li>
									<?php endif; ?>
									<li>
										<span class="contact-info-label">Email:</span>
										<a href="mailto:<?= $site_settings['contact_email'] ?? $site_settings['site_email'] ?? '' ?>"><?= $site_settings['contact_email'] ?? $site_settings['site_email'] ?? '' ?></a>
									</li>
								</ul>
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
									<?php if (!empty($site_settings['tokopedia_url'])): ?>
										<a href="<?= $site_settings['tokopedia_url'] ?>" class="social-icon social-tokopedia" target="_blank" title="Tokopedia" style="color: #42b549;">
											<i class="fas fa-store"></i>
										</a>
									<?php endif; ?>
								</div><!-- End .social-icons -->
							</div><!-- End .widget -->
						</div><!-- End .col-lg-4 -->
					</div><!-- End .row -->
				</div><!-- End .container -->
			</div><!-- End .footer-middle -->

			<div class="container">
				<div class="footer-bottom">
					<p class="footer-copyright">© <?= $site_settings['site_name'] ?? 'Putra Elektronik' ?> <?= date('Y') ?>. All Rights Reserved</p>
				</div><!-- End .footer-bottom -->
			</div><!-- End .container -->
		</footer><!-- End .footer -->
	</div><!-- End .page-wrapper -->

	<div class="mobile-menu-overlay"></div><!-- End .mobil-menu-overlay -->

	<div class="mobile-menu-container">
		<div class="mobile-menu-wrapper">
			<span class="mobile-menu-close"><i class="fa fa-times"></i></span>
			<nav class="mobile-nav">
				<ul class="mobile-menu">
					<li class="active"><a href="<?= base_url() ?>">Home</a></li>
					<li><a href="<?= base_url('product') ?>">Produk</a></li>
					<?php if (!empty($categories)): ?>
						<?php foreach ($categories as $cat): ?>
						<li><a href="<?= base_url('product/category/' . $cat->slug) ?>"><?= $cat->name ?></a></li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul><!-- End .mobile-menu -->

				<ul class="mobile-menu">
					<?php if ($customer_logged_in): ?>
						<li><a href="<?= base_url('customer/account') ?>">Akun Saya</a></li>
						<li><a href="<?= base_url('customer/orders') ?>">Pesanan Saya</a></li>
						<li><a href="<?= base_url('customer/wishlist') ?>">Wishlist</a></li>
						<li><a href="<?= base_url('cart') ?>">Keranjang</a></li>
						<li><a href="<?= base_url('customer/logout') ?>" class="login-link">Logout</a></li>
					<?php else: ?>
						<li><a href="<?= base_url('customer/wishlist') ?>">Wishlist</a></li>
						<li><a href="<?= base_url('cart') ?>">Keranjang</a></li>
						<li><a href="<?= base_url('customer/login') ?>" class="login-link">Login</a></li>
					<?php endif; ?>
				</ul><!-- End .mobile-menu -->
			</nav><!-- End .mobile-nav -->

			<form class="search-wrapper mb-2" action="<?= base_url('product/search') ?>" method="get">
				<input type="search" class="form-control mb-0" name="q" placeholder="Cari produk..." required />
				<button class="btn icon-search text-white bg-transparent p-0" type="submit"></button>
			</form>

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
			</div>
		</div><!-- End .mobile-menu-wrapper -->
	</div><!-- End .mobile-menu-container -->

	<a id="scroll-top" href="#top" title="Top" role="button"><i class="icon-angle-up"></i></a>

	<!-- Plugins JS File -->
	<script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
	<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
	<script src="<?= base_url('assets/js/plugins.min.js') ?>"></script>
	<script src="<?= base_url('assets/js/optional/isotope.pkgd.min.js') ?>"></script>

	<!-- Main JS File -->
	<script src="<?= base_url('assets/js/main.min.js') ?>"></script>

	<!-- Custom JS -->
	<script>
		const BASE_URL = '<?= base_url() ?>';

		// Override cart toggle behavior - remove dropdown functionality
		$(document).ready(function() {
			// Remove cart toggle event handlers to prevent dropdown from showing
			$('.header-icon[title="Cart"]').off('click');
			$('.cart-toggle').off('click');
			$('.cart-overlay').off('click');
			$('.btn-close').off('click');

			// Ensure cart icon links directly to cart page
			$('.header-icon[title="Cart"]').on('click', function(e) {
				// Let the default link behavior work (go to cart page)
				return true;
			});
		});
	</script>

	<!-- Cart Functions JS -->
	<script src="<?= base_url('assets/js/cart-functions.js') ?>"></script>

	<?php if ($this->session->flashdata('success')): ?>
	<script>
		$(document).ready(function() {
			alert('<?= $this->session->flashdata('success') ?>');
		});
	</script>
	<?php endif; ?>

	<?php if ($this->session->flashdata('error')): ?>
	<script>
		$(document).ready(function() {
			alert('<?= $this->session->flashdata('error') ?>');
		});
	</script>
	<?php endif; ?>

	<script type="module" defer>
		import Chatbot from "https://cdn.n8nchatui.com/v1/embed.js";
		Chatbot.init({
		"n8nChatUrl": "https://n8n-qqupawp1hvly.stroberi.sumopod.my.id/webhook/78212c2d-dd21-4918-8f44-889e7217b914/chat",
		"metadata": {}, // Include any custom data to send with each message to your n8n workflow
		"theme": {
		"button": {
			"backgroundColor": "#ff7300",
			"right": 20,
			"bottom": 20,
			"size": 50,
			"iconColor": "#6d3131",
			"customIconSrc": "https://www.svgrepo.com/show/339963/chat-bot.svg",
			"customIconSize": 53,
			"customIconBorderRadius": 15,
			"autoWindowOpen": {
			"autoOpen": false,
			"openDelay": 2
			},
			"borderRadius": "circle"
		},
		"tooltip": {
			"showTooltip": false,
			"tooltipMessage": "Hello 👋 customize & connect me to n8n",
			"tooltipBackgroundColor": "#fff9f6",
			"tooltipTextColor": "#1c1c1c",
			"tooltipFontSize": 15
		},
		"chatWindow": {
			"borderRadiusStyle": "rounded",
			"avatarBorderRadius": 25,
			"messageBorderRadius": 6,
			"showTitle": true,
			"title": "UpGadget ChatBot",
			"titleAvatarSrc": "https://www.svgrepo.com/show/339963/chat-bot.svg",
			"avatarSize": 40,
			"welcomeMessage": "Halo, bingung cari produk. Tanya di sini aja",
			"errorMessage": "Please connect me to n8n first",
			"backgroundColor": "#ffffff",
			"height": 600,
			"width": 400,
			"fontSize": 16,
			"starterPrompts": [
			"Produk Promo",
			"Rekomendasi Produk"
			],
			"starterPromptFontSize": 15,
			"renderHTML": true,
			"clearChatOnReload": false,
			"showScrollbar": false,
			"botMessage": {
			"backgroundColor": "#f26507",
			"textColor": "#fafafa",
			"showAvatar": true,
			"avatarSrc": "https://www.svgrepo.com/show/334455/bot.svg",
			"showCopyToClipboardIcon": false
			},
			"userMessage": {
			"backgroundColor": "#fff6f3",
			"textColor": "#050505",
			"showAvatar": false,
			"avatarSrc": "https://www.svgrepo.com/show/532363/user-alt-1.svg"
			},
			"textInput": {
			"placeholder": "Ketikan pertanyaan Anda",
			"backgroundColor": "#ffffff",
			"textColor": "#1e1e1f",
			"sendButtonColor": "#f36539",
			"maxChars": 100,
			"maxCharsWarningMessage": "Silahkan input kurang dari 100 karakter",
			"autoFocus": false,
			"borderRadius": 6,
			"sendButtonBorderRadius": 50
			}
		}
		}
		});
	</script>
</body>
</html>
