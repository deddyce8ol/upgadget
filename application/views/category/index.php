<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Kategori Produk' ?> - Putra Elektronik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/category.css') ?>" rel="stylesheet">
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h3"><?= $title ?></h1>
                <p class="text-muted">Kelola kategori produk elektronik</p>
            </div>
            <div class="col-md-4 text-end">
                <?php if ($user_role === 'Administrator'): ?>
                <a href="<?= base_url('admin/category/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Kategori Baru
                </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="get" action="<?= base_url('admin/category') ?>">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Cari kategori..." value="<?= htmlspecialchars($search ?? '') ?>">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="bi bi-search"></i> Cari
                                </button>
                                <?php if ($search): ?>
                                <a href="<?= base_url('admin/category') ?>" class="btn btn-outline-danger">
                                    <i class="bi bi-x"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        <small class="text-muted">Total: <?= $total_rows ?> kategori</small>
                    </div>
                </div>

                <?php if (empty($categories)): ?>
                <div class="text-center py-5">
                    <p class="text-muted">Tidak ada kategori yang ditemukan</p>
                    <?php if ($search): ?>
                    <a href="<?= base_url('admin/category') ?>" class="btn btn-sm btn-secondary">Reset Pencarian</a>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Icon</th>
                                <th>Nama Kategori</th>
                                <th>Slug</th>
                                <th>Deskripsi</th>
                                <th class="text-center">Jumlah Produk</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Tanggal Dibuat</th>
                                <?php if ($user_role === 'Administrator'): ?>
                                <th class="text-center">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                            <tr>
                                <td>
                                    <?php if ($category['icon_path']): ?>
                                    <img src="<?= base_url($category['icon_path']) ?>" alt="<?= htmlspecialchars($category['name']) ?>" class="category-icon">
                                    <?php else: ?>
                                    <div class="category-icon-placeholder">
                                        <i class="bi bi-image"></i>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= htmlspecialchars($category['name']) ?></strong></td>
                                <td><code><?= htmlspecialchars($category['slug']) ?></code></td>
                                <td>
                                    <?php
                                    $desc = htmlspecialchars($category['description'] ?? '');
                                    echo strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
                                    ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info"><?= $category['product_count'] ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($user_role === 'Administrator'): ?>
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input status-toggle" type="checkbox" role="switch"
                                               <?= $category['status'] == 1 ? 'checked' : '' ?>
                                               data-category-id="<?= $category['id'] ?>">
                                    </div>
                                    <?php endif; ?>
                                    <span class="badge status-badge <?= $category['status'] == 1 ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $category['status'] == 1 ? 'Aktif' : 'Tidak Aktif' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?= date('d/m/Y', strtotime($category['created_at'])) ?>
                                </td>
                                <?php if ($user_role === 'Administrator'): ?>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= base_url('admin/category/edit/' . $category['id']) ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger delete-btn"
                                                data-category-id="<?= $category['id'] ?>"
                                                data-category-name="<?= htmlspecialchars($category['name']) ?>"
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($pagination): ?>
                <div class="d-flex justify-content-center mt-4">
                    <?= $pagination ?>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Set global variables for category.js
        window.BASE_URL = '<?= base_url() ?>';
        window.CSRF_TOKEN_NAME = '<?= $this->security->get_csrf_token_name() ?>';
        window.CSRF_HASH = '<?= $this->security->get_csrf_hash() ?>';
    </script>
    <script src="<?= base_url('assets/js/category.js') ?>"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</body>
</html>
