<main class="main">
    <div class="container account-container">
        <h2 class="step-title mb-4">Detail Pesanan</h2>

        <div class="row">
            <div class="col-lg-3">
                <div class="dashboard-menu">
                    <ul class="menu">
                        <li><a href="<?= base_url('customer/account') ?>"><i class="icon-user-2"></i> Dashboard</a></li>
                        <li class="active"><a href="<?= base_url('customer/orders') ?>"><i class="icon-shopping-cart"></i> Pesanan Saya</a></li>
                        <li><a href="<?= base_url('customer/wishlist') ?>"><i class="icon-heart-o"></i> Wishlist</a></li>
                        <li><a href="<?= base_url('customer/logout') ?>"><i class="icon-log-out"></i> Logout</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="dashboard-content">
                    <div class="order-header mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>No. Pesanan: <strong><?= $order->order_number ?></strong></h5>
                                <p class="text-muted mb-0">Tanggal: <?= date('d/m/Y H:i', strtotime($order->created_at)) ?></p>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <?= get_order_status_badge($order->status) ?>
                                <?= get_payment_status_badge($order->payment_status) ?>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header"><h6>Informasi Pengiriman</h6></div>
                        <div class="card-body">
                            <p><strong><?= $order->customer_name ?></strong></p>
                            <p><?= $order->customer_phone ?><br><?= $order->customer_email ?></p>
                            <p><?= $order->shipping_address ?><br>
                            <?= $order->shipping_city ?>, <?= $order->shipping_province ?> <?= $order->shipping_postal_code ?></p>
                            <?php if ($order->notes): ?>
                                <p><strong>Catatan:</strong><br><?= nl2br($order->notes) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header"><h6>Produk yang Dipesan</h6></div>
                        <div class="card-body p-0">
                            <table class="table table-cart mb-0">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order_items as $item): ?>
                                        <tr>
                                            <td><?= $item->product_name ?></td>
                                            <td><?= format_rupiah($item->price) ?></td>
                                            <td><?= $item->quantity ?></td>
                                            <td><?= format_rupiah($item->subtotal) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                                        <td><strong><?= format_rupiah($order->subtotal) ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Ongkir:</strong></td>
                                        <td><strong><?= $order->shipping_cost > 0 ? format_rupiah($order->shipping_cost) : 'Gratis' ?></strong></td>
                                    </tr>
                                    <tr class="order-total">
                                        <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                        <td><strong><?= format_rupiah($order->total_amount) ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="text-right">
                        <a href="<?= base_url('customer/orders') ?>" class="btn btn-outline-secondary">
                            <i class="icon-angle-left"></i> Kembali
                        </a>
                        <a href="<?= $whatsapp_url ?>" class="btn btn-success" target="_blank">
                            <i class="fab fa-whatsapp"></i> Hubungi Admin
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-6"></div>
</main>
