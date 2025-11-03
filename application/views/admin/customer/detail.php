<div class="page-content">
    <section class="row">
        <div class="col-12">
            <!-- Back Button -->
            <div class="mb-3">
                <a href="<?= base_url('admin/customer'); ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Customer List
                </a>
            </div>

            <!-- Customer Information Card -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Customer Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Customer ID:</th>
                                    <td><?= $customer->customer_id; ?></td>
                                </tr>
                                <tr>
                                    <th>Full Name:</th>
                                    <td><?= $customer->full_name; ?></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td><?= $customer->email; ?></td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td><?= $customer->phone ?? '-'; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Address:</th>
                                    <td><?= $customer->address ?? '-'; ?></td>
                                </tr>
                                <tr>
                                    <th>Joined Date:</th>
                                    <td><?= date('d M Y H:i', strtotime($customer->created_at)); ?></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <?php if ($customer->is_active == 1) : ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else : ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Orders</h6>
                                    <h3 class="mb-0"><?= $total_orders; ?></h3>
                                </div>
                                <div class="text-primary">
                                    <i class="bi bi-cart-check" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Spent</h6>
                                    <h3 class="mb-0"><?= format_rupiah($total_spent); ?></h3>
                                </div>
                                <div class="text-success">
                                    <i class="bi bi-cash-stack" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order History Card -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Order History</h4>
                </div>
                <div class="card-body">
                    <?php if (count($orders) > 0) : ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="orderHistoryTable">
                                <thead>
                                    <tr>
                                        <th>Order Number</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Payment Status</th>
                                        <th>Order Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order) : ?>
                                        <tr>
                                            <td><?= $order->order_number; ?></td>
                                            <td><?= date('d M Y', strtotime($order->created_at)); ?></td>
                                            <td><?= format_rupiah($order->total_amount); ?></td>
                                            <td>
                                                <?php
                                                $payment_badge = '';
                                                switch ($order->payment_status) {
                                                    case 'paid':
                                                        $payment_badge = '<span class="badge bg-success">Paid</span>';
                                                        break;
                                                    case 'pending':
                                                        $payment_badge = '<span class="badge bg-warning">Pending</span>';
                                                        break;
                                                    case 'failed':
                                                        $payment_badge = '<span class="badge bg-danger">Failed</span>';
                                                        break;
                                                    case 'cancelled':
                                                        $payment_badge = '<span class="badge bg-secondary">Cancelled</span>';
                                                        break;
                                                    default:
                                                        $payment_badge = '<span class="badge bg-secondary">' . ucfirst($order->payment_status) . '</span>';
                                                }
                                                echo $payment_badge;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $status_badge = '';
                                                switch ($order->status) {
                                                    case 'pending':
                                                        $status_badge = '<span class="badge bg-warning">Pending</span>';
                                                        break;
                                                    case 'processing':
                                                        $status_badge = '<span class="badge bg-info">Processing</span>';
                                                        break;
                                                    case 'shipped':
                                                        $status_badge = '<span class="badge bg-primary">Shipped</span>';
                                                        break;
                                                    case 'delivered':
                                                        $status_badge = '<span class="badge bg-success">Delivered</span>';
                                                        break;
                                                    case 'cancelled':
                                                        $status_badge = '<span class="badge bg-danger">Cancelled</span>';
                                                        break;
                                                    default:
                                                        $status_badge = '<span class="badge bg-secondary">' . ucfirst($order->status) . '</span>';
                                                }
                                                echo $status_badge;
                                                ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('admin/order/detail/' . $order->order_id); ?>" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No orders found for this customer.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        // Initialize DataTable for order history
        <?php if (count($orders) > 0) : ?>
            $('#orderHistoryTable').DataTable({
                "order": [
                    [1, 'desc']
                ],
                "pageLength": 10
            });
        <?php endif; ?>
    });
</script>
