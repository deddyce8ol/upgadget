<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= getenv('APP_NAME') . ' - ' . $title; ?></title>

    <!-- Favicon -->
    <?php
    // Load site settings to get the favicon
    $CI =& get_instance();
    if (!$CI->load->is_loaded('Site_setting_model')) {
        $CI->load->model('Site_setting_model');
    }

    // Try site_favicon first, then site_logo, then default
    $favicon_setting = $CI->Site_setting_model->get_value('site_favicon');
    if (empty($favicon_setting)) {
        $favicon_setting = $CI->Site_setting_model->get_value('site_logo');
    }

    $favicon_url = !empty($favicon_setting)
        ? base_url('uploads/' . $favicon_setting)
        : base_url('assets/images/icons/favicon.ico');
    ?>
    <link rel="icon" type="image/x-icon" href="<?= $favicon_url ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $favicon_url ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $favicon_url ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $favicon_url ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= $favicon_url ?>">
    <link rel="icon" type="image/png" sizes="512x512" href="<?= $favicon_url ?>">

    <!-- Assets -->
    <link rel="stylesheet" href="<?= base_url(); ?>template/mazer/dist/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= base_url(); ?>template/mazer/dist/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= base_url(); ?>template/mazer/dist/assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="<?= base_url(); ?>template/mazer/dist/assets/compiled/css/auth.css">

    <!-- Sweetalert -->
    <link rel="stylesheet" href="<?= base_url(); ?>template/mazer/dist/assets/extensions/sweetalert2/sweetalert2.min.css">

    <!-- jQuery DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">

    <!-- Date Range Picker -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <!-- Fontawesome -->
    <link rel="stylesheet" href="<?= base_url(); ?>template/mazer/dist/assets/extensions/@fortawesome/fontawesome-free/css/all.min.css">

    <!-- Dripicons -->
    <link rel="stylesheet" href="<?= base_url(); ?>template/mazer/dist/assets/extensions/@icon/dripicons/dripicons.css">
    <link rel="stylesheet" href="<?= base_url(); ?>template/mazer/dist//assets/compiled/css/ui-icons-dripicons.css">

    <!-- Load jQuery in HEAD to prevent $ is not defined errors -->
    <script src="<?= base_url(); ?>assets/js/jquery/jquery-3.7.1.min.js"></script>

    <!-- jQuery DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="<?= base_url(); ?>template/mazer/dist/assets/extensions/sweetalert2/sweetalert2.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Moment.js (required for daterangepicker) -->
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

    <!-- Date Range Picker -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="<?= base_url(); ?>template/mazer/dist/assets/static/js/initTheme.js"></script>
</head>

<body>
    <div id="app">