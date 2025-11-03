<main class="main">
    <div class="container">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <?= $this->session->flashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <?= $this->session->flashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <ul class="checkout-progress-bar d-flex justify-content-center flex-wrap">
            <li class="active">
                <a href="<?= base_url('cart') ?>">Keranjang Belanja</a>
            </li>
            <li>
                <a href="<?= base_url('checkout') ?>">Pembayaran</a>
            </li>
            <li class="disabled">
                <a href="#">Pesanan Selesai</a>
            </li>
        </ul>

        <?php if (empty($cart_items)): ?>
            <!-- Empty Cart -->
            <div class="text-center" style="padding: 80px 0;">
                <i class="icon-shopping-cart" style="font-size: 120px; color: #ccc;"></i>
                <h3 class="mt-3">Keranjang Belanja Kosong</h3>
                <p class="text-muted">Anda belum menambahkan produk ke keranjang belanja</p>
                <a href="<?= base_url('product') ?>" class="btn btn-outline-primary-2 btn-lg mt-3">
                    <span>Mulai Belanja</span>
                    <i class="icon-long-arrow-right"></i>
                </a>
            </div>
        <?php else: ?>
            <!-- Cart Table -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="cart-table-container">
                        <table class="table table-cart">
                            <thead>
                                <tr>
                                    <th class="thumbnail-col"></th>
                                    <th class="product-col">Produk</th>
                                    <th class="price-col">Harga</th>
                                    <th class="qty-col">Jumlah</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item): ?>
                                    <tr class="product-row" data-rowid="<?= $item['rowid'] ?>">
                                        <td>
                                            <figure class="product-image-container">
                                                <a href="<?= base_url('product/detail/' . $item['options']['slug']) ?>" class="product-image">
                                                    <img src="<?= get_product_image($item['options']['image']) ?>" alt="<?= $item['name'] ?>">
                                                </a>

                                                <a href="#" class="btn-remove icon-cancel" title="Remove Product" data-rowid="<?= $item['rowid'] ?>"></a>
                                            </figure>
                                        </td>
                                        <td class="product-col">
                                            <h5 class="product-title">
                                                <a href="<?= base_url('product/detail/' . $item['options']['slug']) ?>">
                                                    <?= $item['name'] ?>
                                                </a>
                                            </h5>
                                        </td>
                                        <td class="price-col">
                                            <?php if ($item['options']['discount_price'] > 0): ?>
                                                <span class="old-price"><?= format_rupiah($item['options']['original_price']) ?></span>
                                                <span class="new-price"><?= format_rupiah($item['price']) ?></span>
                                            <?php else: ?>
                                                <?= format_rupiah($item['price']) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="product-single-qty">
                                                <input class="horizontal-quantity form-control cart-qty-input"
                                                       type="number"
                                                       value="<?= $item['qty'] ?>"
                                                       min="1"
                                                       max="<?= $item['options']['stock'] ?>"
                                                       data-rowid="<?= $item['rowid'] ?>"
                                                       data-stock="<?= $item['options']['stock'] ?>">
                                            </div>
                                            <small class="text-muted d-block mt-1">Stok: <?= $item['options']['stock'] ?></small>
                                        </td>
                                        <td class="text-right">
                                            <span class="subtotal-price subtotal-<?= $item['rowid'] ?>">
                                                <?= format_rupiah($item['price'] * $item['qty']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>

                            <tfoot>
                                <tr>
                                    <td colspan="5" class="clearfix">
                                        <div class="float-left">
                                            <a href="<?= base_url('product') ?>" class="btn btn-outline-secondary">
                                                <i class="icon-angle-left"></i> Lanjut Belanja
                                            </a>
                                        </div>

                                        <div class="float-right">
                                            <a href="<?= base_url('cart/clear') ?>"
                                               class="btn btn-outline-secondary btn-clear-cart"
                                               onclick="return confirm('Yakin ingin mengosongkan keranjang?')">
                                                <i class="icon-refresh"></i> Kosongkan Keranjang
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div><!-- End .cart-table-container -->
                </div><!-- End .col-lg-8 -->

                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h3>TOTAL KERANJANG</h3>

                        <table class="table table-totals">
                            <tbody>
                                <tr>
                                    <td>Subtotal</td>
                                    <td id="cart-subtotal"><?= format_rupiah($cart_total) ?></td>
                                </tr>
                            </tbody>

                            <tfoot>
                                <tr>
                                    <td>Total</td>
                                    <td id="cart-total"><?= format_rupiah($cart_total) ?></td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="checkout-methods">
                            <a href="<?= base_url('checkout') ?>" class="btn btn-block btn-dark">
                                Lanjut ke Pembayaran <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="icon-info-circle"></i>
                            Ongkos kirim akan dihitung saat checkout berdasarkan alamat tujuan Anda.
                        </div>
                    </div><!-- End .cart-summary -->
                </div><!-- End .col-lg-4 -->
            </div><!-- End .row -->
        <?php endif; ?>
    </div><!-- End .container -->

    <div class="mb-6"></div><!-- margin -->
</main><!-- End .main -->

<!-- Cart Page JavaScript -->
<script src="<?= base_url('assets/js/cart.js') ?>"></script>
