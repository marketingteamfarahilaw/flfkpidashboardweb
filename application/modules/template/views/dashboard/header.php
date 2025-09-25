<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html>
<head>
	<title class="site_title"> <?= $title; ?></title>
	<meta charset="utf-8">
    <link rel="icon" href="<?= base_url('assets/images/icons/lawdger-3.png') ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Lawdger">
    <meta name="developers" content="Raimehn Roger">

    <link href="https://fonts.googleapis.com/css?family=Ubuntu:400,500,700" rel="stylesheet">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets/vendors/adminlte/plugins/font-awesome/css/font-awesome.min.css') ?>">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('assets/vendors/adminlte/dist/css/adminlte.min.css'); ?>">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?= base_url('assets/vendors/adminlte/plugins/iCheck/flat/blue.css'); ?>">
    <!-- Morris chart -->
    <link rel="stylesheet" href="<?= base_url('assets/vendors/adminlte/plugins/morris/morris.css'); ?>">
    <!-- jvectormap -->
    <link rel="stylesheet" href="<?= base_url('assets/vendors/adminlte/plugins/jvectormap/jquery-jvectormap-1.2.2.css'); ?>">
    <!-- Date Picker -->
    <link rel="stylesheet" href="<?= base_url('assets/vendors/adminlte/plugins/datepicker/datepicker3.css'); ?>">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?= base_url('assets/vendors/adminlte/plugins/daterangepicker/daterangepicker-bs3.css'); ?>">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="<?= base_url('assets/vendors/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css '); ?>">

    
    <link rel="stylesheet" href="<?= base_url('assets/css/helper.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    
    <!-- jQuery UI 1.11.4 -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <script src="<?= base_url('assets/js/helpers/util.js') ?>"></script> 
    <script src="<?= base_url('assets/js/connection/config.js') ?>"></script> 


    <!-- toastr -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body class="hold-transition sidebar-mini dashboard-wrapper">
	<div class="body-wrapper home-wrapper">

		<div class="wrapper">
            <div class="content-wrapper bc-white">