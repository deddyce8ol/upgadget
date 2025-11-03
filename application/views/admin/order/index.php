<div class="page-content">
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Order Management</h4>
                </div>
                <div class="card-body">
                    <?= $this->session->flashdata('message'); ?>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Periode Tanggal</label>
                            <input type="text" id="dateRangeFilter" class="form-control" placeholder="Pilih periode...">
                            <input type="hidden" id="startDate">
                            <input type="hidden" id="endDate">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Order Status</label>
                            <select id="filterStatus" class="form-select">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Payment Status</label>
                            <select id="filterPaymentStatus" class="form-select">
                                <option value="">All Payment Status</option>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" id="resetFilter" class="btn btn-secondary w-100">
                                <i class="bi bi-arrow-clockwise"></i> Reset Filter
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="orderTable">
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        // Initialize DataTable with AJAX
        var table = $('#orderTable').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": "<?= base_url('admin/order/get_data'); ?>",
                "type": "POST",
                "data": function(d) {
                    d.start_date = $('#startDate').val();
                    d.end_date = $('#endDate').val();
                    d.status = $('#filterStatus').val();
                    d.payment_status = $('#filterPaymentStatus').val();
                }
            },
            "columns": [{
                    "data": 0
                },
                {
                    "data": 1
                },
                {
                    "data": 2
                },
                {
                    "data": 3
                },
                {
                    "data": 4
                },
                {
                    "data": 5
                },
                {
                    "data": 6,
                    "orderable": false
                }
            ],
            "order": [
                [5, 'desc']
            ]
        });

        // Initialize Date Range Picker
        $('#dateRangeFilter').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear',
                applyLabel: 'Terapkan',
                format: 'DD/MM/YYYY',
                separator: ' - ',
                daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                firstDay: 1
            },
            ranges: {
                'Hari Ini': [moment(), moment()],
                'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });

        // Update input when date is selected
        $('#dateRangeFilter').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            $('#startDate').val(picker.startDate.format('YYYY-MM-DD'));
            $('#endDate').val(picker.endDate.format('YYYY-MM-DD'));
            table.ajax.reload();
        });

        // Clear input when cancelled
        $('#dateRangeFilter').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#startDate').val('');
            $('#endDate').val('');
            table.ajax.reload();
        });

        // Filter event handlers
        $('#filterStatus, #filterPaymentStatus').on('change', function() {
            table.ajax.reload();
        });

        // Reset filter button
        $('#resetFilter').on('click', function() {
            $('#dateRangeFilter').val('');
            $('#startDate').val('');
            $('#endDate').val('');
            $('#filterStatus').val('');
            $('#filterPaymentStatus').val('');
            table.ajax.reload();
        });

        // Delete Order
        $(document).on('click', '.delete-btn', function() {
            var id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('admin/order/delete/'); ?>' + id,
                        type: 'POST',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                table.ajax.reload();
                                Swal.fire('Deleted!', response.message, 'success');
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        }
                    });
                }
            });
        });
    });
</script>
