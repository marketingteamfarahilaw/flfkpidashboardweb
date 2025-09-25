<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
    <div class="footer pb30">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mt30">
                    © 2018 FLF. All Rights Reserved.
                </div>
                <div class="col-md-6 mt30">
                    <div class="float-md-right">
                        <ul class="list-inline m0">
                            <li class="list-inline-item"><a href="<?= site_url('/') ?>" >Home</a></li>
                            <li class="list-inline-item"><a href="<?= site_url('about-us') ?>" >About</a></li>
                            <li class="list-inline-item"><a href="<?= site_url('feature') ?>" >Features</a></li>
                            <?php if(@$_SESSION['is_logged_in'] != true) : ?>
                              <li class="list-inline-item"><a href="<?= site_url('signin'); ?>">Sign In</a></li>
                              <li class="list-inline-item"><a href="<?= site_url('signup');?>">Sign Up</a></li>
                            <?php else : ?>
                              <li class="list-inline-item"><a href="<?= site_url('loop');?>"><?= $_SESSION['first_name'] ?></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="<?= base_url('assets/js/actionlayer/main.js') ?>"></script> 
    <!-- Scripts -->
    <script src="<?= base_url('assets/js/jquery-3.3.1.min.js') ?>"></script> 

    <!-- toastr -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <script src="<?= base_url('assets/vendors/bootstrap/bootstrap.min.js') ?>"></script> 
    <script src="<?= base_url('assets/vendors/mmenu/jquery.mmenu.js') ?>"></script> 
    <script src="<?= base_url('assets/vendors/owl/owl.carousel.min.js') ?>"></script> 
    <script src="<?= base_url('assets/js/default.js') ?>"></script> 
</body>
</html>