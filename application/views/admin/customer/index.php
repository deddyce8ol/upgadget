<div class="page-content">
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Customer Management</h4>
                    </div>
                </div>
                <div class="card-body">
                    <?= $this->session->flashdata('message'); ?>

                    <div class="table-responsive">
                        <table class="table table-hover" id="customerTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Total Orders</th>
                                    <th>Status</th>
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
        // Initialize DataTable
        var table = $('#customerTable').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": "<?= base_url('admin/customer/get_data'); ?>",
                "type": "GET"
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
                [0, 'desc']
            ]
        });

        // Toggle Status
        $(document).on('click', '.toggle-status-btn', function() {
            var id = $(this).data('id');

            Swal.fire({
                title: 'Toggle Customer Status?',
                text: "This will activate/deactivate the customer account.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, toggle it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('admin/customer/toggle_status/'); ?>' + id,
                        type: 'POST',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                table.ajax.reload();
                                Swal.fire('Success!', response.message, 'success');
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Something went wrong!', 'error');
                        }
                    });
                }
            });
        });

        // Delete Customer
        $(document).on('click', '.delete-btn', function() {
            var id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this! Customers with orders cannot be deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('admin/customer/delete/'); ?>' + id,
                        type: 'POST',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                table.ajax.reload();
                                Swal.fire('Deleted!', response.message, 'success');
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Something went wrong!', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
