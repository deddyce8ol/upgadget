<div class="page-content">
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Order Detail - #<?= $order->order_number; ?></h4>
                        <a href="<?= base_url('admin/order'); ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?= $this->session->flashdata('message'); ?>

                    <div class="row">
                        <!-- Customer Information -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Customer Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Name:</th>
                                            <td><?= $order->customer_name; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td><?= $order->customer_email; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Phone:</th>
                                            <td><?= $order->customer_phone; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Address:</th>
                                            <td><?= $order->shipping_address; ?></td>
                                        </tr>
                                        <?php if ($order->notes) : ?>
                                            <tr>
                                                <th>Notes:</th>
                                                <td><?= $order->notes; ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Order Information -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Order Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Order Number:</th>
                                            <td><?= $order->order_number; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Order Date:</th>
                                            <td><?= date('d M Y H:i', strtotime($order->created_at)); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Order Status:</th>
                                            <td>
                                                <?= get_order_status_badge($order->status); ?>
                                                <button type="button" class="btn btn-sm btn-warning ms-2" data-bs-toggle="modal" data-bs-target="#statusModal">
                                                    Change
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Payment Status:</th>
                                            <td>
                                                <?= get_payment_status_badge($order->payment_status); ?>
                                                <button type="button" class="btn btn-sm btn-warning ms-2" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                                    Change
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Total Amount:</th>
                                            <td class="font-bold"><?= format_rupiah($order->total_amount); ?></td>
                                        </tr>
                                    </table>
                                    <div class="mt-3">
                                        <a href="<?= generate_whatsapp_url($order->customer_phone, generate_whatsapp_message($order, $order_items)); ?>" target="_blank" class="btn btn-success w-100">
                                            <i class="bi bi-whatsapp"></i> Send to WhatsApp
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Order Items</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>SKU</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order_items as $item) : ?>
                                            <tr>
                                                <td><?= $item->product_name; ?></td>
                                                <td><?= $item->sku; ?></td>
                                                <td><?= format_rupiah($item->price); ?></td>
                                                <td><?= $item->quantity; ?></td>
                                                <td class="font-bold"><?= format_rupiah($item->subtotal); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-end">Total:</th>
                                            <th><?= format_rupiah($order->total_amount); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    <input type="hidden" name="order_id" value="<?= $order->order_id; ?>">
                    <div class="mb-3">
                        <label class="form-label">Order Status</label>
                        <select name="status" class="form-select" required>
                            <option value="pending" <?= $order->status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?= $order->status == 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="shipped" <?= $order->status == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="completed" <?= $order->status == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?= $order->status == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Payment Status Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Payment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <input type="hidden" name="order_id" value="<?= $order->order_id; ?>">
                    <div class="mb-3">
                        <label class="form-label">Payment Status</label>
                        <select name="payment_status" class="form-select" required>
                            <option value="pending" <?= $order->payment_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="paid" <?= $order->payment_status == 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="failed" <?= $order->payment_status == 'failed' ? 'selected' : ''; ?>>Failed</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Payment Status</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Update Order Status
        $('#statusForm').submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: '<?= base_url('admin/order/update_status'); ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#statusModal').modal('hide');
                        Swal.fire('Success!', response.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                }
            });
        });

        // Update Payment Status
        $('#paymentForm').submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: '<?= base_url('admin/order/update_payment_status'); ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#paymentModal').modal('hide');
                        Swal.fire('Success!', response.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                }
            });
        });
    });
</script>
