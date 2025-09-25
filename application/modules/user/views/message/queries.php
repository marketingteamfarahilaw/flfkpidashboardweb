<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/queries.css') ?>">
<style type="text/css">
    .mailbox-subject {
        display: block;
        display: -webkit-box;
        height: 43px;
        font-size: 14px;
        line-height: 1;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
<style type="text/css">
    .nav-tabs, .nav-tabs .nav-link:focus, .nav-tabs .nav-link:hover {
        border-bottom: none!important;
    }
</style>
<script>
    $(function(){
        var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
        $.jMsg.newMsg({
            target: '#target-div-list',
            searchData : '#search-id',
            url : endPoints.QUERIES + '?token=' + token,
            columns : {
                subject : {
                    name: 'subject',
                    sortable: true,
                },
                datesend : {
                    name: 'datesend',
                    sortable: true,
                },
                message : {
                    name: 'message',
                    sortable: true,
                },
                isopen : {
                    name: 'isopen',
                    sortable: true,
                },
            },
            sort: 'datesend',
            order: 'desc',
            limit: 20,
            count: false,
            topBar: false,
        });

        $('#query_search').keyup(function(){
            $('#btn_submit').click();
        });
        
        $('#btn_submit').css('visibility', 'hidden');
    });

    $(function(){
        var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
        $.jMsg.newMsg({
            target: '#target-sent-list-connect',
            searchData : '#search-id',
            url : endPoints.SENT_QUERIES_CONNECT + '?token=' + token,
            columns : {
                subject : {
                    name: 'subject',
                    sortable: true,
                },
                datesend : {
                    name: 'datesend',
                    sortable: true,
                },
                message : {
                    name: 'message',
                    sortable: true,
                },
            },
            sort: 'datesend',
            order: 'asc',
            limit: 20,
            count: false,
            topBar: false,
        });
    });

    $(function(){
        var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
        $.jMsg.newMsg({
            target: '#target-sent-list-client',
            searchData : '#search-id',
            url : endPoints.SENT_QUERIES + '?token=' + token,
            columns : {
                subject : {
                    name: 'subject',
                    sortable: true,
                },
                datesend : {
                    name: 'datesend',
                    sortable: true,
                },
                message : {
                    name: 'message',
                    sortable: true,
                },
            },
            sort: 'datesend',
            order: 'asc',
            limit: 20,
            count: false,
            topBar: false,
        });
    });

    $(function(){
        var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
        $.jMsg.newMsg({
            target: '#target-archive-list',
            searchData : '#search-id',
            url : endPoints.ARCHIVE_QUERIES + '?token=' + token,
            columns : {
                subject : {
                    name: 'subject',
                    sortable: true,
                },
                datesend : {
                    name: 'datesend',
                    sortable: true,
                },
                message : {
                    name: 'message',
                    sortable: true,
                },
            },
            sort: 'datesend',
            order: 'asc',
            limit: 20,
            count: false,
            topBar: false,
        });
    });

    $('#query_search').keyup(function(){
        $('#btn_submit').click();
    });
    
    $('#btn_submit').css('visibility', 'hidden');
</script>

<section class="bc-lightgray content h-100 o-hidden content-msg __transition">
    <div class="container-fluid pl-2 pr-2 pl-md-5 pr-md-5 ">
        <div class="tab-content pt-3">
            <div class="tab-pane active" id="connect">
                <div class="row">
                    <div class="col-md-12 __transition" >
                        <div class="row">
                            <div class="col-md-8">
                                <div class="__vertical_center left">
                                    <ul class="nav nav-tabs mb-3 mt-3" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" href="#inboxConnect"><i class="fa fa-inbox" aria-hidden="true"></i> Inbox</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#sentConnect"><i class="fa fa-paper-plane" aria-hidden="true"></i> Sent</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#archivesConnect"><i class="fa fa-archive" aria-hidden="true"></i> Archives</a>
                                        </li>
                                    </ul>   
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="__vertical_center right">
                                    <form id="search-id" class="w-100" method="post">
                                        <input type="text" class="form-control input-sm mt-3 mb-3" id="query_search" name="query_search" placeholder="Search Mail">
                                        <button type="submit" data-jMsgTarget="#target-div-list" id="btn_submit">
                                            <i class="fa fa-search" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="row"></div>
                            <div class="tab-content">
                                <div id="inboxConnect" class="tab-pane active"><br>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tab_1">
                                            <div class="box box-primary">
                                                <div class="box-footer no-padding">
                                                    <div class="mailbox-controls">
                                                        <!-- Check all button -->
                                                        <a href="<?= site_url('compose') ?>" class="btn btn-default btn-sm" ><i class="fa fa-pencil-square-o"></i></a>
                                                        <div class="btn-group">
                                                            <a href="javascript:window.location=window.location.href" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /.box-header -->
                                                <div class="box-body no-padding h600">
                                                    <div class="mailbox-messages">
                                                    <table class="table table-hover">
                                                        <tbody id="target-div-list"></tbody>
                                                    </table>
                                                    <!-- /.table -->
                                                    </div>
                                                  <!-- /.mail-box-messages -->
                                                </div>
                                                <!-- /.box-body -->
                                            </div>
                                        </div>
                                      <!-- /.tab-pane -->
                                    </div>
                                </div>
                                <div id="sentConnect" class="tab-pane"><br>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tab_1">
                                            <div class="box box-primary">
                                                <!-- /.box-header -->
                                                <div class="box-body no-padding h600">
                                                    <div class="mailbox-messages">
                                                    <table class="table table-hover">
                                                        <tbody id="target-sent-list-connect"></tbody>
                                                    </table>
                                                    <!-- /.table -->
                                                    </div>
                                                  <!-- /.mail-box-messages -->
                                                </div>
                                                <!-- /.box-body -->
                                            </div>
                                        </div>
                                      <!-- /.tab-pane -->
                                    </div>
                                </div>
                                <div id="archivesConnect" class="tab-pane"><br>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tab_1">
                                            <div class="box box-primary">
                                                <!-- /.box-header -->
                                                <div class="box-body no-padding h600">
                                                    <div class="mailbox-messages">
                                                    <table class="table table-hover">
                                                        <tbody id="target-archive-list-connect"></tbody>
                                                    </table>
                                                    <!-- /.table -->
                                                    </div>
                                                  <!-- /.mail-box-messages -->
                                                </div>
                                                <!-- /.box-body -->
                                            </div>
                                        </div>
                                      <!-- /.tab-pane -->
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script src="<?= base_url('assets/js/jInbox/jInbox.js'); ?>"></script>