<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <!-- HEADER -->
<div class="header __transition">
    <div class="container h-100">
        <div class="row h-100">
            <div class="col-8 col-md-4 h-100">
                <div class="__vertical_center">
                    <a href="index.html">
                        <img src="<?= base_url('assets/images/JFJ-Logo.png') ?>" alt="logo" class="img-fluid header-logo">
                    </a>
                </div>
            </div>
            <div class="col-6 col-md-8 d-none d-md-block h-100">
                <ul class="list-inline __vertical_center right">
                    <li class="list-inline-item current-menu-item"><a href="<?= site_url('/'); ?>" >HOME</a></li>
                    <li class="list-inline-item"><a id="button_checker" class="sign-link"></a></li>
                    <!-- <li class="list-inline-item"><a id="register" class="primary-link"></a></li> -->
                </ul>
            </div>
            <div class="col-4 d-md-none">
                <div class="__vertical_center">
                    <a href="#mobile-menu" class="d-block fs-24">
                        <i class="fas fa-bars"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

  <!-- Mobile Menu -->
<div id="mobile-menu">
    <ul>
        <li class="current-menu-item"><a href="<?= site_url('/'); ?>" >HOME</a></li>
        <li><a id="button_checker"></a></li>
    </ul>
</div>
<div class="main-preloader __vertical_center">
    <img src="<?= base_url('assets/images/icons/lawdger-3.gif') ?>" alt="preloader">
</div>