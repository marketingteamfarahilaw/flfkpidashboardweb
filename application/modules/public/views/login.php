<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="<?= base_url('assets/images/icons/lawdger-3.png') ?>">
    <title><?= $title ?></title>

    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Styleshets -->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/vendors/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/vendors/font-awesome/css/fontawesome-all.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/helper.css') ?>">

    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/form.css') ?>">
</head>

<body>
    <div class="body-wrapper form-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 order-lg-2 p0">
                    <div class="bg-cover" style="background-image:url('assets/images/banner_9.jpg');">
                        <div class="h-100 blue-overlay" >
                            <div class="form-img">
                                <div class="float-lg-right">
                                    <a href="<?= site_url('/'); ?>">
                                        <img src="<?= base_url('assets/images/JFJ-Logo.png') ?>" alt="logo" class="form-logo d-block img-fluid">
                                    </a>
                                </div>  
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-form">
                        <div class="__vertical_center">
                            <div class="form-container w-100">
                                <h3 class="mb40 text-center fw-300 title_login">Sign In to Your Account</h3>
                                <form id="login-form-id" class="c-gray">
                                    <?= form_input('user_name', '', 'id="login_username" placeholder="Username" class="mb20" autofocus') ?>
                                    <?= form_password('password', '', 'id="login_password" placeholder="Password" class="mb20"') ?>
                                    <a href="#" class="c-gray mb40 ml15 d-block">Reset Password</a>
                                    <div class="text-right">
                                        <a href="#" class="d-inline-block green-link __transition mb20" id="login">SIGN IN</a>
                                        <p class="mb30 d-block float-none ">Not a Member? <a href="<?= site_url('registration'); ?>" class="c-gray">Create an Account</a></p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= base_url('assets/js/jquery-3.3.1.min.js') ?>"></script> 
    <script src="<?= base_url('assets/vendors/bootstrap/bootstrap.min.js') ?>"></script>

    <script src="<?= base_url('assets/js/helpers/util.js') ?>"></script> 
    <script src="<?= base_url('assets/js/connection/config.js') ?>"></script>   
    
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="<?= base_url('assets/js/actionlayer/login.js') ?>"></script> 
</body>
</html>