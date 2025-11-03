<main class="main">
    <div class="container account-container">
        <h2 class="step-title mb-4">Pesanan Saya</h2>

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
                    <?php if (empty($orders)): ?>
                        <div class="alert alert-info">Belum ada pesanan</div>
                        <a href="<?= base_url('product') ?>" class="btn btn-outline-primary">Mulai Belanja</a>
                    <?php else: ?>
                        <table class="table table-cart">
                            <thead>
                                <tr>
                                    <th>No. Pesanan</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?= $order->order_number ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($order->created_at)) ?></td>
                                        <td><?= format_rupiah($order->total_amount) ?></td>
                                        <td><?= get_order_status_badge($order->status) ?></td>
                                        <td><a href="<?= base_url('customer/order/' . $order->order_id) ?>" class="btn btn-sm btn-outline-primary">Detail</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-6"></div>
</main>
