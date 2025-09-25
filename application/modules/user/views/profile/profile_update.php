<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>    
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard/profile.css') ?>">

<link rel="stylesheet" href="<?= base_url('assets/vendors/croppr/croppr.min.css') ?>">

<link rel="stylesheet" href="<?= base_url('assets/css/dashboard/user/me.css') ?>">

<section class="content bc-white">    
    <div class="container-fluid pt-3 pl-md-2 pr-md-2 pl-md-5 pr-md-5">
        <div>
          <!-- User Info -->
            <div class="row profile-text">
                <h4>Update Profile</h4>   
                <div class="col-12 col-lg-7 col-xl-5 ml-auto">
                    <div class="row"></div>
                </div>
            </div>
            <hr>
            <?= form_open('', '', 'class="form-signin"', 'id="login-form-id" class="c-gray"') ?>
                <div class="form-group">
                    <p class="mb-2">Profile Image</p>

                    <input id="file_profileimage" name="file_profileimage" type="file" class="d-none">
                    <input id="profileimage" name="profileimage" type="hidden">


                    <div class="upload_pp_btns_cotnainer mb-2 d-inline-block">
                        <img src="" id="resultimg" alt="" class="w200p p-3 img-circle d-none">
                        <div class="text-center">
                            <a href="" class="fs-16 o-7 upload_pp_btn_class btn-link-primary __transition"><i class="fa fa-upload" aria-hidden="true"></i> Upload</a>
                            <a href="" class="fs-16 o-7 ml-2 browse_pp_btn_class btn-link-secondary __transition" data-toggle="modal" data-target="#image_file_gallery"><i class="fa fa-folder-open" aria-hidden="true"></i> Browse</a>
                        </div>
                    </div>

                    <div class="previewimg_container d-none">
                        <div class="mw-400p d-block img-fluid">
                            <img src="" id="previewimg" alt="" >
                        </div>
                        <div class="text-center">
                            <a href="" class="btn fs-16 o-7 crop_pp_btn_class btn-link-primary __transition"><i class="fa fa-check" aria-hidden="true"></i> Crop</a>
                            <a href="" class="btn fs-16 o-7 ml-2 cancel_crop_pp_btn_class btn-link-secondary __transition"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
                        </div>

                    </div>
                </div>
                <div class="form-group">
                    <label>Firstname:</label>
                    <?= form_input('firstname', '', 'id="customer_first_name" placeholder="Firstname" class="form-control mb20 customer_first_name"') ?>
                </div>
                <div class="form-group">
                    <label>Lastname:</label>
                    <?= form_input('lastname', '', 'id="customer_last_name" placeholder="Lastname" class="form-control mb20 customer_last_name"') ?>
                </div>
                <div class="form-group">
                    <label>Middlename:</label>
                    <?= form_input('middlename', '', 'id="customer_middle_name" placeholder="Middlename" class="form-control mb20 customer_middle_name"') ?>
                </div>
                <div class="form-group">
                    <label>Gender:</label>
                    <select name="gender" id="gender" class="form-control mb20">
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <?= form_input('email', '', 'id="cust_email" placeholder="Email" class="form-control mb20 cust_email"') ?>
                </div>
                <div class="form-group">
                    <label>Address:</label>
                    <?= form_input('Address', '', 'id="cust_address" placeholder="Address" class="form-control mb20 cust_address"') ?>
                </div>
                <div class="form-group">
                    <label>Birthday:</label>
                    <?= form_input('customer_birthday', '', 'id="customer_birthday" placeholder="Birthday" class="form-control mb20 customer_birthday"') ?>
                </div>
                <div class="form-group">
                    <label>About:</label>
                    <textarea class="form-control" id="about" name="about"></textarea>
                </div>
                <div class="form-group">
                    <label>Education:</label>
                    <textarea class="form-control" id="education" name="educ"></textarea>
                </div>
                <div class="form-group">
                    <label>Skills:</label>
                    <textarea class="form-control" id="skill" name="skill"></textarea>
                </div>
                <div class="text-right">
                    <?= form_submit('submit', 'Update', 'class="d-inline-block green-link __transition mb20" id="update_profile"') ?>
                    <a href="<?= site_url('profile') ?>">Cancel</a>
                </div>
            <?= form_close() ?>
       </div>
    </div>
</section>

<!-- Croppr -->
<script src="<?= base_url('assets/vendors/croppr/croppr.min.js') ?>"></script>