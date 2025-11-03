<div class="page-content">
    <section class="row">
        <div class="col-12">
            <!-- E-commerce Statistics -->
            <div class="row">
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon blue mb-2">
                                        <i class="iconly-boldBuy"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Orders</h6>
                                    <h6 class="font-extrabold mb-0"><?= $total_orders; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon red mb-2">
                                        <i class="iconly-boldTime-Circle"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Pending Orders</h6>
                                    <h6 class="font-extrabold mb-0"><?= $pending_orders; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon green mb-2">
                                        <i class="iconly-boldDocument"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Products</h6>
                                    <h6 class="font-extrabold mb-0"><?= $total_products; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon purple mb-2">
                                        <i class="iconly-boldUser1"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Customers</h6>
                                    <h6 class="font-extrabold mb-0"><?= $total_customers; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row align-items-center">
                                <div class="col-md-2 col-lg-1 d-flex justify-content-center">
                                    <div class="stats-icon green mb-2" style="width: 80px; height: 80px;">
                                        <i class="iconly-boldWallet" style="font-size: 2.5rem;"></i>
                                    </div>
                                </div>
                                <div class="col-md-10 col-lg-11">
                                    <h6 class="text-muted font-semibold mb-2">Total Revenue</h6>
                                    <h3 class="font-extrabold mb-0"><?= format_rupiah($total_revenue); ?></h3>
                                    <p class="text-muted mb-0">Total revenue from all orders</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Recent Orders</h4>
                            <a href="<?= base_url('admin/order'); ?>" class="btn btn-sm btn-primary">View All Orders</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-lg">
                                    <thead>
                                        <tr>
                                            <th>Order Number</th>
                                            <th>Customer</th>
                                            <th>Total Amount</th>
                                            <th>Status</th>
                                            <th>Payment</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recent_orders)) : ?>
                                            <?php foreach ($recent_orders as $order) : ?>
                                                <tr>
                                                    <td class="font-bold"><?= $order->order_number; ?></td>
                                                    <td>
                                                        <p class="font-bold mb-0"><?= $order->customer_name; ?></p>
                                                        <small class="text-muted"><?= $order->customer_phone; ?></small>
                                                    </td>
                                                    <td class="font-bold"><?= format_rupiah($order->total_amount); ?></td>
                                                    <td><?= get_order_status_badge($order->status); ?></td>
                                                    <td><?= get_payment_status_badge($order->payment_status); ?></td>
                                                    <td><?= date('d M Y', strtotime($order->created_at)); ?></td>
                                                    <td>
                                                        <a href="<?= base_url('admin/order/detail/' . $order->order_id); ?>" class="btn btn-sm btn-info">
                                                            <i class="bi bi-eye"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="7" class="text-center">No orders yet</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Top Products (by views)</h4>
                            <a href="<?= base_url('admin/product'); ?>" class="btn btn-sm btn-primary">View All Products</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-lg">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Product Name</th>
                                            <th>Category</th>
                                            <th>Brand</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Views</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($top_products)) : ?>
                                            <?php foreach ($top_products as $product) : ?>
                                                <tr>
                                                    <td>
                                                        <img src="<?= get_product_image($product->main_image ?? null); ?>" alt="<?= $product->product_name; ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                                    </td>
                                                    <td>
                                                        <p class="font-bold mb-0"><?= $product->product_name; ?></p>
                                                        <small class="text-muted"><?= $product->sku; ?></small>
                                                    </td>
                                                    <td><?= $product->category_name ?? '-'; ?></td>
                                                    <td><?= $product->brand_name ?? '-'; ?></td>
                                                    <td class="font-bold"><?= format_rupiah($product->price); ?></td>
                                                    <td>
                                                        <?php if ($product->stock > 0) : ?>
                                                            <span class="badge bg-success"><?= $product->stock; ?></span>
                                                        <?php else : ?>
                                                            <span class="badge bg-danger">Out of Stock</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><span class="badge bg-info"><?= number_format($product->views); ?> views</span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="7" class="text-center">No products yet</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Statistics (existing) -->
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="mb-3">Admin User Statistics</h5>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon purple mb-2">
                                        <i class="iconly-boldUser1"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Account</h6>
                                    <h6 class="font-extrabold mb-0"><?= $total_account; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon blue mb-2">
                                        <i class="iconly-boldPassword"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Admin</h6>
                                    <h6 class="font-extrabold mb-0"><?= $total_admin; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon green mb-2">
                                        <i class="iconly-boldAdd-User"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total User</h6>
                                    <h6 class="font-extrabold mb-0"><?= $total_user; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon red mb-2">
                                        <i class="iconly-boldWork"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Role</h6>
                                    <h6 class="font-extrabold mb-0"><?= $total_role; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
