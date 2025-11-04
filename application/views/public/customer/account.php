<main class="main">
    <div class="container account-container">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <?= $this->session->flashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>

        <!-- Page Title -->
        <div class="page-header mb-3">
            <h2 class="page-title">Dashboard Akun</h2>
            <p class="page-subtitle">Halo <strong><?= $customer->full_name ?></strong> <span class="d-none d-md-inline">(bukan Anda? <a href="<?= base_url('customer/logout') ?>">Logout</a>)</span></p>
        </div>

        <div class="row">
            <!-- Sidebar Menu -->
            <div class="col-lg-3 mb-4 mb-lg-0">
                <div class="dashboard-menu">
                    <ul class="menu">
                        <li class="active">
                            <a href="<?= base_url('customer/account') ?>">
                                <i class="icon-user-2"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('customer/orders') ?>">
                                <i class="icon-shopping-cart"></i>
                                <span>Pesanan Saya</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('customer/wishlist') ?>">
                                <i class="icon-heart-o"></i>
                                <span>Wishlist</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('customer/logout') ?>">
                                <i class="icon-log-out"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Stats Cards -->
                <div class="row stats-row">
                    <div class="col-6 col-md-4 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="icon-shipping"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value"><?= count($recent_orders) ?></div>
                                <div class="stat-label">Total Pesanan</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-4 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="icon-heart-o"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value"><?= $wishlist_count ?></div>
                                <div class="stat-label">Wishlist</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="icon-user-2"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">Member</div>
                                <div class="stat-label">Status Akun</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders Section -->
                <div class="orders-section">
                    <h3 class="section-title">Pesanan Terbaru</h3>

                    <?php if (empty($recent_orders)): ?>
                        <div class="empty-state">
                            <i class="icon-shopping-cart"></i>
                            <p>Belum ada pesanan</p>
                            <a href="<?= base_url('product') ?>" class="btn btn-primary">Mulai Belanja</a>
                        </div>
                    <?php else: ?>
                        <!-- Mobile: Card View -->
                        <div class="orders-mobile d-lg-none">
                            <?php foreach ($recent_orders as $order): ?>
                                <div class="order-card">
                                    <div class="order-header">
                                        <span class="order-number"><?= $order->order_number ?></span>
                                        <?= get_order_status_badge($order->status) ?>
                                    </div>
                                    <div class="order-body">
                                        <div class="order-info">
                                            <span class="info-label">Tanggal</span>
                                            <span class="info-value"><?= date('d/m/Y', strtotime($order->created_at)) ?></span>
                                        </div>
                                        <div class="order-info">
                                            <span class="info-label">Total</span>
                                            <span class="info-value"><?= format_rupiah($order->total_amount) ?></span>
                                        </div>
                                    </div>
                                    <div class="order-footer">
                                        <a href="<?= base_url('customer/order/' . $order->order_id) ?>" class="btn btn-sm btn-outline-primary btn-block">Lihat Detail</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Desktop: Table View -->
                        <div class="orders-desktop d-none d-lg-block">
                            <div class="table-responsive">
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
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <a href="<?= base_url('customer/orders') ?>" class="btn btn-outline-dark">Lihat Semua Pesanan</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-5"></div>
</main>

<style>
/* ===================================
   MOBILE FIRST - BASE STYLES (320px+)
   =================================== */

/* Container */
.account-container {
    padding: 15px 0 30px;
}

/* Page Header */
.page-header {
    margin-bottom: 20px;
}

.page-title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
}

.page-subtitle {
    font-size: 14px;
    color: #666;
    margin: 0;
}

/* Dashboard Menu */
.dashboard-menu {
    background: #fff;
    border: 1px solid #e7e7e7;
    border-radius: 8px;
    overflow: hidden;
}

.dashboard-menu .menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.dashboard-menu .menu li {
    border-bottom: 1px solid #f0f0f0;
}

.dashboard-menu .menu li:last-child {
    border-bottom: none;
}

.dashboard-menu .menu li a {
    display: flex;
    align-items: center;
    padding: 14px 16px;
    color: #333;
    text-decoration: none;
    transition: all 0.3s ease;
}

.dashboard-menu .menu li a i {
    font-size: 18px;
    width: 24px;
    margin-right: 12px;
    color: #666;
    flex-shrink: 0;
}

.dashboard-menu .menu li a span {
    font-size: 14px;
    font-weight: 500;
}

.dashboard-menu .menu li a:hover,
.dashboard-menu .menu li.active a {
    background: #f8f9fa;
    color: #0052CC;
}

.dashboard-menu .menu li a:hover i,
.dashboard-menu .menu li.active a i {
    color: #0052CC;
}

/* Stats Cards */
.stats-row {
    margin-bottom: 25px;
}

.stat-card {
    background: linear-gradient(135deg, #0052CC 0%, #0066FF 100%);
    border-radius: 12px;
    padding: 16px;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 8px rgba(0, 82, 204, 0.15);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    min-height: 100px;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 16px rgba(0, 82, 204, 0.25);
}

.stat-icon {
    flex-shrink: 0;
    margin-right: 12px;
}

.stat-icon i {
    font-size: 32px;
    color: rgba(255, 255, 255, 0.9);
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 22px;
    font-weight: 700;
    color: #fff;
    line-height: 1.2;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.85);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Orders Section */
.orders-section {
    background: #fff;
    border: 1px solid #e7e7e7;
    border-radius: 8px;
    padding: 16px;
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 16px;
    color: #333;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px 20px;
}

.empty-state i {
    font-size: 64px;
    color: #ccc;
    margin-bottom: 16px;
}

.empty-state p {
    font-size: 16px;
    color: #666;
    margin-bottom: 20px;
}

/* Mobile: Order Cards */
.order-card {
    background: #fff;
    border: 1px solid #e7e7e7;
    border-radius: 8px;
    margin-bottom: 12px;
    overflow: hidden;
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: #f8f9fa;
    border-bottom: 1px solid #e7e7e7;
}

.order-number {
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

.order-body {
    padding: 12px 16px;
}

.order-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.order-info:last-child {
    margin-bottom: 0;
}

.info-label {
    font-size: 13px;
    color: #666;
}

.info-value {
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

.order-footer {
    padding: 12px 16px;
    border-top: 1px solid #f0f0f0;
}

/* ===================================
   TABLET STYLES (768px+)
   =================================== */
@media (min-width: 768px) {
    .account-container {
        padding: 30px 0 40px;
    }

    .page-title {
        font-size: 26px;
    }

    .page-subtitle {
        font-size: 15px;
    }

    .dashboard-menu {
        background: #f8f9fa;
        border: none;
    }

    .dashboard-menu .menu li a {
        padding: 16px 20px;
        border-radius: 6px;
        margin-bottom: 4px;
    }

    .dashboard-menu .menu li {
        border-bottom: none;
    }

    .dashboard-menu .menu li.active a {
        background: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    }

    .stat-icon i {
        font-size: 36px;
    }

    .stat-value {
        font-size: 26px;
    }

    .stat-label {
        font-size: 13px;
    }

    .orders-section {
        padding: 24px;
    }

    .section-title {
        font-size: 20px;
        margin-bottom: 20px;
    }
}

/* ===================================
   DESKTOP STYLES (992px+)
   =================================== */
@media (min-width: 992px) {
    .account-container {
        padding: 40px 0 60px;
    }

    .page-title {
        font-size: 28px;
    }

    .dashboard-menu {
        position: sticky;
        top: 20px;
    }

    .dashboard-menu .menu li a {
        padding: 16px 24px;
    }

    .stat-card {
        padding: 20px;
        min-height: 120px;
    }

    .stat-icon i {
        font-size: 42px;
    }

    .stat-value {
        font-size: 28px;
    }

    .orders-section {
        padding: 30px;
    }

    .section-title {
        font-size: 22px;
    }
}

/* ===================================
   LARGE DESKTOP STYLES (1200px+)
   =================================== */
@media (min-width: 1200px) {
    .stat-card {
        padding: 24px;
    }

    .orders-section {
        padding: 32px;
    }
}

/* ===================================
   UTILITY & ADJUSTMENTS
   =================================== */

/* Badge adjustments for mobile */
.badge {
    font-size: 11px;
    padding: 4px 8px;
}

@media (min-width: 768px) {
    .badge {
        font-size: 12px;
        padding: 5px 10px;
    }
}

/* Button adjustments */
.btn-sm {
    font-size: 13px;
    padding: 6px 12px;
}

@media (min-width: 768px) {
    .btn-sm {
        font-size: 14px;
        padding: 8px 16px;
    }
}

/* Table responsive improvements */
.table-responsive {
    border-radius: 8px;
    overflow: hidden;
}

.table-cart {
    margin-bottom: 0;
}

.table-cart th {
    font-size: 14px;
    font-weight: 600;
    background: #f8f9fa;
    border-bottom: 2px solid #e7e7e7;
}

.table-cart td {
    font-size: 14px;
    vertical-align: middle;
}
</style>
