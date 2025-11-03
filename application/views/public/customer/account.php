<main class="main">
    <div class="container account-container">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <?= $this->session->flashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>

        <h2 class="step-title mb-4">Dashboard Akun</h2>

        <div class="row">
            <div class="col-lg-3">
                <div class="dashboard-menu">
                    <ul class="menu">
                        <li class="active"><a href="<?= base_url('customer/account') ?>"><i class="icon-user-2"></i> Dashboard</a></li>
                        <li><a href="<?= base_url('customer/orders') ?>"><i class="icon-shopping-cart"></i> Pesanan Saya</a></li>
                        <li><a href="<?= base_url('customer/wishlist') ?>"><i class="icon-heart-o"></i> Wishlist</a></li>
                        <li><a href="<?= base_url('customer/logout') ?>"><i class="icon-log-out"></i> Logout</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="dashboard-content">
                    <p>Halo <strong><?= $customer->full_name ?></strong> (bukan Anda? <a href="<?= base_url('customer/logout') ?>">Logout</a>)</p>

                    <div class="row row-sm">
                        <div class="col-6 col-md-4">
                            <div class="feature-box text-center">
                                <i class="icon-shipping"></i>
                                <div class="feature-box-content">
                                    <h3><?= count($recent_orders) ?></h3>
                                    <h5>Total Pesanan</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-4">
                            <div class="feature-box text-center">
                                <i class="icon-heart-o"></i>
                                <div class="feature-box-content">
                                    <h3><?= $wishlist_count ?></h3>
                                    <h5>Wishlist</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-4">
                            <div class="feature-box text-center">
                                <i class="icon-user-2"></i>
                                <div class="feature-box-content">
                                    <h3>Member</h3>
                                    <h5>Status Akun</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h3 class="mt-5 mb-3">Pesanan Terbaru</h3>
                    <?php if (empty($recent_orders)): ?>
                        <div class="alert alert-info">Belum ada pesanan</div>
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
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td><?= $order->order_number ?></td>
                                        <td><?= date('d/m/Y', strtotime($order->created_at)) ?></td>
                                        <td><?= format_rupiah($order->total_amount) ?></td>
                                        <td><?= get_order_status_badge($order->status) ?></td>
                                        <td><a href="<?= base_url('customer/order/' . $order->order_id) ?>" class="btn btn-sm btn-outline-primary">Detail</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <a href="<?= base_url('customer/orders') ?>" class="btn btn-outline-dark">Lihat Semua Pesanan</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6"></div>
</main>

<style>
.account-container { padding: 40px 0; }
.dashboard-menu { background: #f9f9f9; padding: 20px; border-radius: 5px; }
.dashboard-menu .menu { list-style: none; padding: 0; margin: 0; }
.dashboard-menu .menu li { margin-bottom: 10px; }
.dashboard-menu .menu li a { display: block; padding: 12px 15px; color: #333; text-decoration: none; border-radius: 3px; }
.dashboard-menu .menu li a:hover, .dashboard-menu .menu li.active a { background: #fff; color: #08c; }
.dashboard-menu .menu li a i { margin-right: 10px; width: 20px; }
.dashboard-content { background: #fff; padding: 30px; border: 1px solid #e7e7e7; }
.feature-box { background: #f9f9f9; padding: 30px 20px; margin-bottom: 20px; border-radius: 5px; }
.feature-box i { font-size: 40px; color: #08c; margin-bottom: 15px; }
.feature-box h3 { font-size: 24px; margin-bottom: 5px; }
.feature-box h5 { font-size: 14px; color: #777; margin: 0; }
</style>
