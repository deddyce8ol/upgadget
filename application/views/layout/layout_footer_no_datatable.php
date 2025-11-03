</div>
<footer class="main-footer">
    <div class="footer clearfix mb-0 text-muted">
        <div class="float-start">
            <p>&copy; Copyright 2023 - <?= date("Y"); ?> <a href="https://github.com/armandwipangestu/ci3-boilerplate" target="_blank"><?= getenv('APP_NAME'); ?></a>. All rights reserved.</p>
        </div>
    </div>
</footer>
</div>



<script src="<?= base_url(); ?>template/mazer/dist/assets/static/js/components/dark.js"></script>
<script src="<?= base_url(); ?>template/mazer/dist/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>

<script src="<?= base_url(); ?>template/mazer/dist/assets/compiled/js/app.js"></script>

<!-- Sweetalert -->
<script src="<?= base_url(); ?>template/mazer/dist/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
<!-- Note: sweetalert2.js removed to prevent errors on pages without demo elements -->

<!-- jQuery -->
<script src="<?= base_url(); ?>assets/js/jquery/jquery-3.7.1.min.js"></script>

<!-- Custom JS -->
<script src="<?= base_url(); ?>assets/js/script.js"></script>

</body>

</html>
