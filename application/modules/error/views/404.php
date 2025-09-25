<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $title ?></title>

    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Styleshets -->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/vendors/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/vendors/font-awesome/css/fontawesome-all.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/helper.css') ?>">

    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/form.css') ?>">

    <style>
        body { text-align: center; padding: 150px; }
        h1 { font-size: 50px; }
        body { font: 20px Helvetica, sans-serif; color: #333; }
        article { display: block; text-align: left; width: 650px; margin: 0 auto; }
        a { color: #dc8100; text-decoration: none; }
        a:hover { color: #333; text-decoration: none; }
    </style>
</head>

<body>
    <div class="body-wrapper form-wrapper">
        <div class="container-fluid">
            <div class="row">
                <article>
                    <h1>We'll be back soon!</h1>
                    <div>
                        <p>Sorry for the inconvenience but we're performing some maintenance at the moment. If you need to you can always <a href="mailto:#">contact us</a>, otherwise we'll be back online shortly!</p>
                        <p>&mdash; The Team</p>
                    </div>
                </article>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= base_url('assets/js/jquery-3.3.1.min.js') ?>"></script> 
    <script src="<?= base_url('assets/vendors/bootstrap/bootstrap.min.js') ?>"></script> 

    <script src="<?= base_url('assets/js/helpers/util.js') ?>"></script> 
    <script src="<?= base_url('assets/js/connection/config.js') ?>"></script>   
    
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="<?= base_url('assets/js/actionlayer/register_account.js') ?>"></script> 
</body>
</html>