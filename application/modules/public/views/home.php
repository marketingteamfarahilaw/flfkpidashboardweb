<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
    <!-- Section 1 Banner -->
    <div id="home-banner-id" class="carousel slide" data-ride="carousel">
      <!-- Indicators -->
      <ul class="carousel-indicators">
        <li data-target="#home-banner-id" data-slide-to="0" class="active"></li>
        <li data-target="#home-banner-id" data-slide-to="1"></li>
        <li data-target="#home-banner-id" data-slide-to="2"></li>
      </ul>

      <!-- Slides -->
      <div class="carousel-inner">
        <div class="carousel-item active">
          <div class="home-banner bg-cover" style="background-image:url('assets/images/banner_1.png');">
          <div class="home-banner-overlay">
            <div class="container __vertical_center left banner-mobile">
              <div class="home-banner-text">
                <h2 class="fw-300">Opportunities don't happen</h2>
                <h2 class="fw-300">you create them.</h2>
                <h2 class="mt5 mb25">Join the community.</h2>
                <a href="<?= site_url('signup');?>" class="primary-link __transition">SIGN IN</a>
              </div>
            </div>
          </div>
        </div>
        </div>
       <div class="carousel-item">
          <div class="home-banner bg-cover" style="background-image:url('assets/images/banner_8.jpg');">
            <div class="home-banner-overlay">
            <div class="container __vertical_center left banner-mobile">
              <div class="home-banner-text">
                <h2 class="fw-300">Opportunities don't happen</h2>
                <h2 class="fw-300">you create them.</h2>
                <h2 class="mt5 mb25">Join the community.</h2>
                <a href="<?= site_url('signup');?>" class="primary-link __transition">SIGN UP</a>
              </div>
            </div>
          </div>
        </div>
        </div>
        <div class="carousel-item">
          <div class="home-banner bg-cover" style="background-image:url('assets/images/banner_4.jpg');">
            <div class="home-banner-overlay">
            <div class="container __vertical_center left banner-mobile">
              <div class="home-banner-text">
                <h2 class="fw-300">Opportunities don't happen</h2>
                <h2 class="fw-300">you create them.</h2>
                <h2 class="mt5 mb25">Join the community.</h2>
                <a href="<?= site_url('signup');?>" class="primary-link __transition">SIGN UP</a>
              </div>
            </div>
          </div>
        </div>  
        </div>
      </div>

      <!-- Left and right controls -->
      <a class="carousel-control-prev" href="#home-banner-id" data-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </a>
      <a class="carousel-control-next" href="#home-banner-id" data-slide="next">
        <span class="carousel-control-next-icon"></span>
      </a>
    </div>

    <!-- Section 2 Blue -->
    <div class="section-two bc-lightgray p-relative">
      <div class="hbox __fixed home-section-blue-img bg-cover section-left-img" style="background:url('assets/images/news_2.jpg');">
        <div class="h-100 blue-overlay w-100 p-absolute" >
        </div>
      </div>
      <div class="container home-section-blue">
        <div class="row">
          <div class="col-md-6 p0">
          </div>
          <div class="col-md-6">
            <div class="hbox __vertical_center right">
              <div class="home-section-blue-text">
                <h3 class="mb20">Vestibulum ac diam sit amet quam vehicula elementum.</h3>
                <p class="mb30">Vestibulum ac diam sit amet quam vehicula elementum sed sit amet dui. Sed porttitor lectus nibh. Cras ultricies ligula sed magna dictum porta. Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Donec rutrum congue leo eget malesuada.</p>
                <hr class="d-inline-block mb0">
                <div class="d-block">
                  <a href="#" class="m0 c-gray primary-link-transition">LEARN MORE</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    

    <!-- How It Works -->
    <div class="home-hiw hbox">
      <div class="__vertical_center">
        <div class="container text-center">
          <h4 class="d-block mx-auto w-80 fw-300">Nulla porttitor accumsan tincidunt. Vestibulum ac diam sit amet quam vehicula elementum sed sit amet dui.</h4>
          <div class="mb70"></div>
          <div class="row">
            <div class="col-12 col-md">
              <div class="home-hiw-img mb30">
                <img src="<?= base_url('assets/images/icons/hp_chat_3.png') ?>" alt="chat" class="img-fluid">
              </div>
              <h5 class="text-center mb20">Chat</h5>
              <p class="c-gray text-center">
                Vestibulum dapibus nunc ac augue. Ut varius tincidunt libero. Praesent nec nisl a purus blandit viverra.
              </p>
            </div>
            <div class="col-12 col-md-1">
              <hr class="d-md-none container-margin">
            </div>
            <div class="col-12 col-md">
              <div class="home-hiw-img mb30">
                <img src="<?= base_url('assets/images/icons/hp_bill_3.png') ?>" alt="chat" class="img-fluid">
              </div>
              <h5 class="text-center mb20">Bill</h5>
              <p class="c-gray text-center">
                Vestibulum dapibus nunc ac augue. Ut varius tincidunt libero. Praesent nec nisl a purus blandit viverra.
              </p>
            </div>
            <div class="col-12 col-md-1">
              <hr class="d-md-none container-margin">
            </div>
            <div class="col-12 col-md">
              <div class="home-hiw-img mb30">
                <img src="<?= base_url('assets/images/icons/hp_pay_3.png') ?>" alt="chat" class="img-fluid">
              </div>
              <h5 class="text-center mb20">Get Paid</h5>
              <p class="c-gray text-center">
                Vestibulum dapibus nunc ac augue. Ut varius tincidunt libero. Praesent nec nisl a purus blandit viverra.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="section-four bc-lightgray p-relative">
      <div class="hbox __fixed home-section-green-img bg-cover section-right-img" style="background:url('assets/images/news_3.jpg');">
        <div class="h-100 green-overlay w-100 p-absolute" >
        </div>
      </div>
      <div class="container-fluid-sm container home-section-green">
        <div class="row">
          <div class="col-md-6 p0 order-md-2">
          </div>
          <div class="col-md-6 ">
            <div class="hbox __vertical_center left">
              <div class="home-section-green-text">
                <h3 class="mb20">Vestibulum ac diam sit amet quam vehicula elementum.</h3>
                <p class="mb30">Vestibulum ac diam sit amet quam vehicula elementum sed sit amet dui. Sed porttitor lectus nibh. Cras ultricies ligula sed magna dictum porta. Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Donec rutrum congue leo eget malesuada.</p>
                <hr class="d-inline-block mb0">
                <div class="d-block">
                  <a href="#" class="m0 c-gray green-link-transition">LEARN MORE</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <!-- Login -->
    <div class="section-five p-relative">
      <div class="home-login bg-cover blur-bg parallax-fixed"></div>
      <div class="banner-overlay container-padding">
        <div class="container text-center c-white pb30 pt30">
          <h3 class="mb30"><span class="fw-300">Already a </span><span class="fw-400">Member?</span></h3>
          <a href="<?= site_url('signin'); ?>" class="green-link __transition mb-auto d-inline-block w-100 withborder">SIGN IN</a>
        </div>
      </div>
    </div>