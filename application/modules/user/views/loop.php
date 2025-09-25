<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/loop.css') ?>">

<script>
    $(function(){
        var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
        $.jCard.newCard({
            target: '.div-card',
            searchData : '.search-id',
            url : endPoints.LAWYERS + '?token=' + token,
            columns : {
                id : {
                    name: 'id',
                    sortable: true,
                },
                name : {
                    name: 'name',
                    sortable: true,
                },
                email : {
                    name: 'email',
                    sortable: true,
                },
                customer_image_url : {
                    name: 'customer_image_url',
                    sortable: true,
                },
            },
            sort: 'email',
            order: 'acs',
            limit: 6,
            topBar: false,
        });
        $('#search-id').keyup(function(){
            $('#btn_submit').click();
        });
    });
</script>

<!-- Main content -->
    <section class="content" id="app">
        <div class="card-search card-widget widget-user">
          <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header text-white loop-head">
                <div class="__vertical_center text-center">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-8 mx-auto p-relative">
                                <form method="post" id="search-id" class="search-id w-100">
                                    <input type="text" name="search_all" placeholder="Search">
                                    <button type="submit" data-jCardTarget='.div-card' id="btn_submit">
                                        <i class="fa fa-search" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid pl-2 pr-2 pl-md-5 pr-md-5 pb-5 pt-5"> 
              <div class="div-card">
              </div>
        </div>
    </section>

<script src="<?= base_url('assets/js/jCard/jCard.js'); ?>"></script>