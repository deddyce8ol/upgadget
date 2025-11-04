<div class="page-content">
    <section class="row">
        <!-- Summary Cards -->
        <div class="col-12 mb-3">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-4 d-flex justify-content-start">
                                    <div class="stats-icon blue mb-2">
                                        <i class="bi bi-cash-stack"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-8">
                                    <h6 class="text-muted font-semibold">Total Revenue</h6>
                                    <h6 class="font-extrabold mb-0" id="totalRevenue"><?= format_rupiah($summary['total_revenue']); ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-4 d-flex justify-content-start">
                                    <div class="stats-icon green mb-2">
                                        <i class="bi bi-receipt"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-8">
                                    <h6 class="text-muted font-semibold">Total Transaksi</h6>
                                    <h6 class="font-extrabold mb-0" id="totalTransactions"><?= number_format($summary['total_transactions']); ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-4 d-flex justify-content-start">
                                    <div class="stats-icon purple mb-2">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-8">
                                    <h6 class="text-muted font-semibold">Total Items Terjual</h6>
                                    <h6 class="font-extrabold mb-0" id="totalItems"><?= number_format($summary['total_items_sold']); ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Grafik Penjualan</h4>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Laporan Penjualan</h4>
                        <button type="button" id="exportExcel" class="btn btn-success">
                            <i class="bi bi-file-earmark-excel"></i> Export Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?= $this->session->flashdata('message'); ?>

                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Periode Tanggal</label>
                            <input type="text" id="dateRangeFilter" class="form-control" placeholder="Pilih periode tanggal...">
                            <input type="hidden" id="startDate" value="<?= $start_date; ?>">
                            <input type="hidden" id="endDate" value="<?= $end_date; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" id="applyFilter" class="btn btn-primary w-100">
                                <i class="bi bi-funnel"></i> Terapkan Filter
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="salesTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>No. Order</th>
                                    <th>Tanggal</th>
                                    <th>Customer</th>
                                    <th>Jumlah Item</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
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

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize date range with default values
        var startDate = moment('<?= $start_date; ?>');
        var endDate = moment('<?= $end_date; ?>');

        // Initialize Date Range Picker
        $('#dateRangeFilter').daterangepicker({
            startDate: startDate,
            endDate: endDate,
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

        // Set initial display
        $('#dateRangeFilter').val(startDate.format('DD/MM/YYYY') + ' - ' + endDate.format('DD/MM/YYYY'));

        // Update hidden inputs when date is selected
        $('#dateRangeFilter').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            $('#startDate').val(picker.startDate.format('YYYY-MM-DD'));
            $('#endDate').val(picker.endDate.format('YYYY-MM-DD'));
        });

        // Initialize DataTable
        var table = $('#salesTable').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": "<?= base_url('admin/report/get_sales_data'); ?>",
                "type": "POST",
                "data": function(d) {
                    d.start_date = $('#startDate').val();
                    d.end_date = $('#endDate').val();
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
                    "data": 6
                },
                {
                    "data": 7,
                    "orderable": false
                }
            ],
            "order": [
                [2, 'desc']
            ],
            "pageLength": 25
        });

        // Initialize Chart
        var salesChart;
        var ctx = document.getElementById('salesChart').getContext('2d');

        function initChart(chartData) {
            if (salesChart) {
                salesChart.destroy();
            }

            salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Revenue (Rp)',
                        data: chartData.revenues,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        yAxisID: 'y'
                    }, {
                        label: 'Jumlah Order',
                        data: chartData.orders,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.1,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Revenue (Rp)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Jumlah Order'
                            },
                            grid: {
                                drawOnChartArea: false,
                            }
                        }
                    }
                }
            });
        }

        // Load initial chart data
        loadChartData();

        function loadChartData() {
            $.ajax({
                url: '<?= base_url('admin/report/get_chart_data'); ?>',
                type: 'POST',
                data: {
                    start_date: $('#startDate').val(),
                    end_date: $('#endDate').val()
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        initChart(response.data);
                    }
                }
            });
        }

        function updateSummary() {
            $.ajax({
                url: '<?= base_url('admin/report/get_summary_data'); ?>',
                type: 'POST',
                data: {
                    start_date: $('#startDate').val(),
                    end_date: $('#endDate').val()
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#totalRevenue').text('Rp ' + new Intl.NumberFormat('id-ID').format(response.data.total_revenue));
                        $('#totalTransactions').text(new Intl.NumberFormat('id-ID').format(response.data.total_transactions));
                        $('#totalItems').text(new Intl.NumberFormat('id-ID').format(response.data.total_items_sold));
                    }
                }
            });
        }

        // Apply filter button
        $('#applyFilter').on('click', function() {
            table.ajax.reload();
            loadChartData();
            updateSummary();
        });

        // Export Excel button
        $('#exportExcel').on('click', function() {
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();
            window.location.href = '<?= base_url('admin/report/export_excel'); ?>?start_date=' + startDate + '&end_date=' + endDate;
        });
    });
</script>
