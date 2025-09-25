<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>  
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/loop.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard/directory.css') ?>">
<style type="text/css">
    #search-id input[type = "text"] {
        width: 15%!important; 
        border: 2px solid #ddd!important;
        border-radius: 0px!important;
        color: #000!important;
        margin: 0!important;
        margin-bottom: 40px;
    }
    #search-id button {
        margin: 0!important;
        color: #296bf7!important;
        position: relative!important;  
        border: 2px solid #ddd!important;
        font-size: 19px;
        padding: 7px 15px 10px;
        right: 4px!important;
        top: 0px!important;
    }

    .widget-user-header.loop-head {
         background-color: #fff!important; 
        border-radius: 0!important;
        height: 130px!important;
        border-top: 1px solid #ddd!important;
        border-bottom: 1px solid #ddd!important;
        margin-bottom: 40px!important;
    }

    .select_box:after{
      width: 0; 
      height: 0; 
      border-left: 6px solid transparent;
      border-right: 6px solid transparent;
      border-top: 6px solid #296bf7;
      position: absolute;
      top: 40%;
      right: 12px;
      content: "";
      z-index: 98;
     }
</style>
<script>
    $(function(){
        $.jDirTable.newDirGrid({
            target: '#target-div-list',
            searchData : '.search_id',
            url : endPoints.CONTACT,
            columns : {
                name : {
                    name: 'Name',
                    sortable: true,
                },
                location : {
                    name: 'Location',
                    sortable: true,
                },
                contact : {
                    name: 'Contact',
                    sortable: true,
                }
            },
            sort: 'name',
            order: 'asc',
            limit: 10,
            count: false,
            topBar: false,
        });

        $('#search_form').keyup(function(){
            $('#btn_submit').click();
        });
    });
</script>

<section class="content">
    <div class="card card-widget widget-user">
      <!-- Add the bg color to the header using any of the bg-* classes -->
        <div class="widget-user-header text-white loop-head">
            <div class="__vertical_center text-center">
                <div class="container-fluid pl-2 pr-2 pl-md-5 pr-md-5">
                    <div class="row">
                        <div class="col-md-5 mx-auto p-relative select_box">
                            <select class="form-control" name="fieldRegion" style="border-radius: 0px;">
                                <option selected="selected" value="">Select Region</option>
                                <script type="text/javascript">
                                    if (LocalStorage.get(STORAGE_ITEM.TOKEN)) {

                                        axios.get(endPoints.LOGIN.concat('?token=').concat(token), CONFIG.HEADER)
                                             .then( (response) => {
                                                // handle success
                                                // console.log(response);
                                                window.location.replace(viewRoutes.MEMBERSCENTER);
                                             })
                                             .catch( (error) => {
                                                // handle error
                                                LocalStorage.delete(STORAGE_ITEM.TOKEN);
                                             })
                                    }
                                </script>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <form id="search-id" class="w-100 search_id mb20" method="post">
                <input type="text" id="search_form" name="search_directory" placeholder="Search Sector">
                <button type="submit" data-jDirTableTarget="#target-div-list" id="btn_submit">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </div>
    <div class="pb-5 pt-4 pl-2 pr-2 pl-md-5 pr-md-5"> 
        <div id="target-div-list"></div>      
    </div>
</section>

<script src="<?= base_url('assets/js/jDirTable/jDirTable.js'); ?>"></script>