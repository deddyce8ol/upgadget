</div>
<footer class="main-footer">
    <div class="footer clearfix mb-0 text-muted">
        <div class="float-start">
            <p>&copy; Copyright 2023 - <?= date("Y"); ?> <a href="https://github.com/armandwipangestu/ci3-boilerplate" target="_blank"><?= getenv('APP_NAME'); ?></a>. All rights reserved.</p>
        </div>
    </div>
</footer>
</div>

<!-- Mazer Theme Scripts (jQuery, DataTables, SweetAlert already loaded in header) -->
<script src="<?= base_url(); ?>template/mazer/dist/assets/static/js/components/dark.js"></script>
<script src="<?= base_url(); ?>template/mazer/dist/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>

<!-- Patch untuk mencegah error getBoundingClientRect pada app.js -->
<script>
(function() {
    // Patch untuk mencegah error pada elemen null
    // Error ini terjadi karena template Mazer mencoba mengakses elemen yang tidak ada
    window.addEventListener('error', function(e) {
        // Tangkap error getBoundingClientRect
        if (e.message && e.message.includes('getBoundingClientRect')) {
            e.preventDefault();
            console.warn('Prevented getBoundingClientRect error on null element');
            return true;
        }
    }, true);

    // Tambahkan safeguard untuk getBoundingClientRect
    const originalGetBoundingClientRect = Element.prototype.getBoundingClientRect;
    Element.prototype.getBoundingClientRect = function() {
        try {
            return originalGetBoundingClientRect.call(this);
        } catch (e) {
            console.warn('getBoundingClientRect called on invalid element', e);
            // Return default rect
            return {
                top: 0,
                left: 0,
                bottom: 0,
                right: 0,
                width: 0,
                height: 0,
                x: 0,
                y: 0
            };
        }
    };
})();
</script>

<script src="<?= base_url(); ?>template/mazer/dist/assets/compiled/js/app.js"></script>

<!-- Custom JS -->
<script src="<?= base_url(); ?>assets/js/script.js"></script>

</body>

</html>