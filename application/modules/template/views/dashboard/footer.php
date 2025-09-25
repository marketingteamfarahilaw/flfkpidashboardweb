<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>    

        <!-- custom css -->
        <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    
        <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
        <script>
           $.widget.bridge('uibutton', $.ui.button)
        </script>

        <script src="<?= base_url('assets/js/actionlayer/member_center.js') ?>"></script> 
        <script src="<?= base_url('assets/js/actionlayer/logout.js') ?>"></script> 

    	<!-- Bootstrap 4 -->
    	<script src="<?= base_url('assets/vendors/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    	<!-- Morris.js charts -->
    	<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    	<script src="<?= base_url('assets/vendors/adminlte/plugins/morris/morris.js') ?>"></script>
    	<!-- Sparkline -->
    	<script src="<?= base_url('assets/vendors/adminlte/plugins/sparkline/jquery.sparkline.min.js') ?>"></script>
    	<!-- jvectormap -->
    	<script src="<?= base_url('assets/vendors/adminlte/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') ?>"></script>
    	<script src="<?= base_url('assets/vendors/adminlte/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') ?>"></script>
    	<!-- jQuery Knob Chart -->
    	<script src="<?= base_url('assets/vendors/adminlte/plugins/knob/jquery.knob.js') ?>"></script>
    	<!-- daterangepicker -->
    	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
    	<script src="<?= base_url('assets/vendors/adminlte/plugins/daterangepicker/daterangepicker.js') ?>"></script>
    	<!-- datepicker -->
    	<script src="<?= base_url('assets/vendors/adminlte/plugins/datepicker/bootstrap-datepicker.js') ?>"></script>
    	<!-- Bootstrap WYSIHTML5 -->
    	<script src="<?= base_url('assets/vendors/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') ?>"></script>
    	<!-- Slimscroll -->
    	<script src="<?= base_url('assets/vendors/adminlte/plugins/slimScroll/jquery.slimscroll.min.js') ?>"></script>
    	<!-- FastClick -->
    	<script src="<?= base_url('assets/vendors/adminlte/plugins/fastclick/fastclick.js') ?>"></script>
    	<!-- AdminLTE App -->
    	<script src="<?= base_url('assets/vendors/adminlte/dist/js/adminlte.js') ?>"></script>
    	<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    	<script src="<?= base_url('assets/vendors/adminlte/dist/js/pages/dashboard.js') ?>"></script>
    	<!-- AdminLTE for demo purposes -->
    	<script src="<?= base_url('assets/vendors/adminlte/dist/js/demo.js') ?>"></script>
    	<script src="<?= base_url('assets/js/dashboard/user/me.js') ?>"></script>
    	<script src="<?= base_url('assets/js/dashboard/adminsettings.js') ?>"></script>
    </div>
</body>
</html>