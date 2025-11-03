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
            <li>
                <a href="<?= base_url('cart') ?>">Keranjang Belanja</a>
            </li>
            <li class="active">
                <a href="<?= base_url('checkout') ?>">Pembayaran</a>
            </li>
            <li class="disabled">
                <a href="#">Pesanan Selesai</a>
            </li>
        </ul>

        <form action="<?= base_url('checkout/process') ?>" method="post" id="checkout-form">
            <div class="row">
                <div class="col-lg-8">
                    <h2 class="step-title mb-2">Informasi Pengiriman</h2>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Lengkap <span class="required">*</span></label>
                                <input type="text" class="form-control" name="customer_name"
                                       value="<?= isset($customer) ? $customer->full_name : '' ?>"
                                       placeholder="Masukkan nama lengkap" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email <span class="required">*</span></label>
                                <input type="email" class="form-control" name="customer_email"
                                       value="<?= isset($customer) ? $customer->email : '' ?>"
                                       placeholder="Masukkan email" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>No. Handphone / WhatsApp <span class="required">*</span></label>
                        <input type="text" class="form-control" name="customer_phone"
                               value="<?= isset($customer) ? $customer->phone : '' ?>"
                               placeholder="08xxxxxxxxxx" required>
                        <small class="form-text text-muted">Nomor ini akan digunakan untuk konfirmasi pesanan via WhatsApp</small>
                    </div>

                    <div class="form-group">
                        <label>Alamat Lengkap <span class="required">*</span></label>
                        <textarea class="form-control" name="shipping_address" rows="3"
                                  placeholder="Jalan, Nomor Rumah, RT/RW, Kelurahan, Kecamatan"
                                  required><?= isset($addresses[0]) ? $addresses[0]->address : '' ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kota/Kabupaten <span class="required">*</span></label>
                                <input type="text" class="form-control" name="shipping_city"
                                       value="<?= isset($addresses[0]) ? $addresses[0]->city : '' ?>"
                                       placeholder="Contoh: Jakarta Selatan" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Provinsi <span class="required">*</span></label>
                                <input type="text" class="form-control" name="shipping_province"
                                       value="<?= isset($addresses[0]) ? $addresses[0]->province : '' ?>"
                                       placeholder="Contoh: DKI Jakarta" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Kode Pos <span class="required">*</span></label>
                        <input type="text" class="form-control" name="shipping_postal_code"
                               value="<?= isset($addresses[0]) ? $addresses[0]->postal_code : '' ?>"
                               placeholder="12345" required>
                    </div>

                    <div class="form-group">
                        <label>Catatan Pesanan (Opsional)</label>
                        <textarea class="form-control" name="order_notes" rows="3"
                                  placeholder="Catatan tambahan untuk pesanan Anda (opsional)"></textarea>
                        <small class="form-text text-muted">Misalnya: warna khusus, instruksi pengiriman, dll.</small>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="order-summary">
                        <h3>RINGKASAN PESANAN</h3>

                        <table class="table table-mini-cart">
                            <thead>
                                <tr>
                                    <th colspan="2">Produk</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item): ?>
                                    <tr>
                                        <td class="product-col">
                                            <h3 class="product-title">
                                                <?= $item['name'] ?> × <span class="product-qty"><?= $item['qty'] ?></span>
                                            </h3>
                                        </td>
                                        <td class="price-col">
                                            <span><?= format_rupiah($item['price'] * $item['qty']) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="cart-subtotal">
                                    <td>
                                        <strong>Subtotal</strong>
                                    </td>
                                    <td>
                                        <strong><?= format_rupiah($cart_total) ?></strong>
                                    </td>
                                </tr>

                                <tr class="order-shipping">
                                    <td>
                                        <strong>Ongkir</strong>
                                    </td>
                                    <td>
                                        <strong>-</strong>
                                    </td>
                                </tr>

                                <tr class="order-total">
                                    <td>
                                        <strong>Total</strong>
                                    </td>
                                    <td>
                                        <strong class="total-price"><?= format_rupiah($cart_total) ?></strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="alert alert-icon alert-info mb-4">
                            <i class="icon-info-circle"></i>
                            <strong>Informasi Pembayaran</strong><br>
                            Setelah klik tombol "Proses Pesanan", Anda akan diarahkan ke WhatsApp untuk konfirmasi dan informasi pembayaran dari admin kami.
                        </div>

                        <button type="submit" class="btn btn-dark btn-place-order btn-block">
                            Proses Pesanan via WhatsApp
                            <i class="fa fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="mb-6"></div>
</main>

<script>
// Form validation
document.getElementById('checkout-form').addEventListener('submit', function(e) {
    var phone = document.querySelector('input[name="customer_phone"]').value;

    // Validate phone number (must start with 08 or +62)
    if (!phone.match(/^(08|\+62)\d{8,13}$/)) {
        e.preventDefault();
        alert('Format nomor handphone tidak valid. Gunakan format: 08xxxxxxxxxx atau +62xxxxxxxxxx');
        return false;
    }

    // Show loading state
    var submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';
});
</script>
