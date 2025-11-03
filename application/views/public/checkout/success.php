<main class="main">
    <div class="container">
        <ul class="checkout-progress-bar d-flex justify-content-center flex-wrap">
            <li>
                <a href="<?= base_url('cart') ?>">Keranjang Belanja</a>
            </li>
            <li>
                <a href="<?= base_url('checkout') ?>">Pembayaran</a>
            </li>
            <li class="active">
                <a href="#">Pesanan Selesai</a>
            </li>
        </ul>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="order-success-section text-center">
                    <i class="icon-check-circle" style="font-size: 100px; color: #28a745;"></i>
                    <h2 class="step-title mt-4 mb-2">Pesanan Berhasil Dibuat!</h2>
                    <p class="mb-4">Terima kasih telah berbelanja di <?= $site_settings['site_name'] ?? 'Putra Elektronik' ?></p>

                    <div class="order-info-box">
                        <div class="alert alert-success mb-4">
                            <h4 class="alert-heading">
                                <i class="icon-info-circle"></i>
                                Nomor Pesanan: <strong><?= $order->order_number ?></strong>
                            </h4>
                            <p class="mb-0">Silakan klik tombol di bawah untuk melanjutkan konfirmasi pesanan via WhatsApp</p>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Detail Pesanan</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Nama:</strong></div>
                                    <div class="col-sm-8"><?= $order->customer_name ?></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Email:</strong></div>
                                    <div class="col-sm-8"><?= $order->customer_email ?></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>No. HP:</strong></div>
                                    <div class="col-sm-8"><?= $order->customer_phone ?></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Alamat Pengiriman:</strong></div>
                                    <div class="col-sm-8">
                                        <?= $order->shipping_address ?><br>
                                        <?= $order->shipping_city ?>, <?= $order->shipping_province ?> <?= $order->shipping_postal_code ?>
                                    </div>
                                </div>
                                <?php if ($order->notes): ?>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Catatan:</strong></div>
                                        <div class="col-sm-8"><?= nl2br($order->notes) ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Produk yang Dipesan</h5>
                            </div>
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
                                            <td><strong class="total-price"><?= format_rupiah($order->total_amount) ?></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="alert alert-info mb-4">
                            <h5 class="alert-heading">
                                <i class="fab fa-whatsapp"></i>
                                Langkah Selanjutnya
                            </h5>
                            <p class="mb-2">Untuk menyelesaikan pesanan Anda, silakan:</p>
                            <ol class="mb-0 pl-4">
                                <li>Klik tombol "Konfirmasi via WhatsApp" di bawah</li>
                                <li>Anda akan diarahkan ke WhatsApp dengan pesan otomatis berisi detail pesanan</li>
                                <li>Kirim pesan tersebut ke admin kami</li>
                                <li>Admin kami akan membalas dengan informasi pembayaran dan konfirmasi pesanan</li>
                            </ol>
                        </div>

                        <a href="<?= $whatsapp_url ?>" class="btn btn-success btn-lg btn-block mb-3" target="_blank">
                            <i class="fab fa-whatsapp"></i>
                            Konfirmasi via WhatsApp
                        </a>

                        <a href="<?= base_url('product') ?>" class="btn btn-outline-dark btn-block">
                            <i class="icon-angle-left"></i>
                            Lanjut Belanja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6"></div>
</main>

<style>
.order-success-section {
    padding: 40px 0;
}

.order-info-box {
    text-align: left;
    margin-top: 30px;
}

.order-info-box .card-header {
    background-color: #f9f9f9;
    border-bottom: 2px solid #e7e7e7;
}

.order-info-box .row {
    margin-bottom: 0;
}

.order-info-box .row + .row {
    padding-top: 10px;
}

.btn-success {
    background-color: #25d366;
    border-color: #25d366;
    font-size: 16px;
    padding: 15px 30px;
}

.btn-success:hover {
    background-color: #20ba5a;
    border-color: #20ba5a;
}

.btn-success i {
    font-size: 20px;
    margin-right: 8px;
}

.alert-heading i {
    margin-right: 8px;
}

.table-cart thead th {
    background-color: #f9f9f9;
    font-weight: 600;
}

.order-total td {
    font-size: 18px;
    padding-top: 15px;
}
</style>
