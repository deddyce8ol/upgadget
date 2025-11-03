<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= getenv('APP_NAME') . ' - ' . $title; ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/images/icons/favicon.ico') ?>">

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