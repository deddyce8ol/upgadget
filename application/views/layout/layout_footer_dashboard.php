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
<!-- Note: sweetalert2.js demo removed to prevent errors -->

<!-- ApexCharts (only loaded for dashboard) -->
<script src="<?= base_url(); ?>template/mazer/dist/assets/extensions/apexcharts/apexcharts.min.js"></script>

<!-- jQuery -->
<script src="<?= base_url(); ?>assets/js/jquery/jquery-3.7.1.min.js"></script>

<!-- Custom JS -->
<script src="<?= base_url(); ?>assets/js/script.js"></script>

<!-- Dashboard Charts Initialization -->
<script>
    // Wait for ApexCharts to be loaded and check if data exists
    if (typeof ApexCharts !== 'undefined' && typeof getUserRegistration !== 'undefined' && typeof getUserGender !== 'undefined') {

        // User Registration Chart
        const optionsUserRegistration = {
            annotations: {
                position: "back",
            },
            dataLabels: {
                enabled: false,
            },
            chart: {
                type: "bar",
                height: 300,
            },
            fill: {
                opacity: 1,
            },
            plotOptions: {},
            series: [{
                name: "User Registration",
                data: getUserRegistration.map((item) => {
                    return Number(item.total)
                }),
            }],
            colors: "#435ebe",
            xaxis: {
                categories: getUserRegistration.map((item) => item.month),
            },
        };

        const chartUserRegistration = new ApexCharts(
            document.querySelector("#chart-user-registration"),
            optionsUserRegistration
        );

        chartUserRegistration.render();

        // User Gender Chart
        const seriesDataUserGender = getUserGender.map((item) => {
            return parseInt(item.total);
        });

        const optionsUserGender = {
            series: seriesDataUserGender,
            labels: ['Male', 'Female'],
            colors: ["#435ebe", "#55c6e8"],
            chart: {
                type: "donut",
                width: "100%",
                height: "350px",
            },
            legend: {
                position: "bottom",
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: "30%",
                    },
                },
            },
        };

        const chartUserGender = new ApexCharts(
            document.querySelector("#chart-user-gender"),
            optionsUserGender
        );

        chartUserGender.render();
    }
</script>

</body>

</html>
