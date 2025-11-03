<main class="main">
    <div class="container login-container">
        <div class="row">
            <div class="col-lg-6 mx-auto">
                <div class="heading mb-3">
                    <h2 class="title">Daftar Akun Baru</h2>
                </div>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $this->session->flashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('customer/register_process') ?>" method="post">
                    <div class="form-group">
                        <label for="register-name">
                            Nama Lengkap <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" id="register-name" name="full_name" required>
                    </div>

                    <div class="form-group">
                        <label for="register-email">
                            Email <span class="required">*</span>
                        </label>
                        <input type="email" class="form-control" id="register-email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="register-phone">
                            No. HP / WhatsApp <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" id="register-phone" name="phone" placeholder="08xxxxxxxxxx" required>
                    </div>

                    <div class="form-group">
                        <label for="register-password">
                            Password <span class="required">*</span>
                        </label>
                        <input type="password" class="form-control" id="register-password" name="password" required>
                        <small class="form-text text-muted">Minimal 6 karakter</small>
                    </div>

                    <div class="form-group">
                        <label for="register-password-confirm">
                            Konfirmasi Password <span class="required">*</span>
                        </label>
                        <input type="password" class="form-control" id="register-password-confirm" name="password_confirm" required>
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-dark btn-md w-100 mb-3">
                            <span>DAFTAR</span>
                            <i class="icon-long-arrow-right"></i>
                        </button>

                        <div class="text-center">
                            <p class="mb-0">
                                Sudah punya akun?
                                <a href="<?= base_url('customer/login') ?>">Login di sini</a>
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="mb-6"></div>
</main>

<style>
.login-container {
    padding: 60px 0;
}

.heading {
    text-align: center;
    margin-bottom: 30px;
}

.heading .title {
    font-size: 2.4rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.form-group label {
    font-weight: 600;
    margin-bottom: 8px;
}

.required {
    color: #f44336;
}

.form-footer {
    margin-top: 30px;
}
</style>
