<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>


<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/queries.css') ?>">
    <section class="bc-lightgray content content-msg __transition pb-3" style="min-height: 100%;">
        <div class="container-fluid pl-2 pr-2 pl-md-5 pr-md-5">
      <div class="row">
        <div class="col-md-12 __transition" >
          <div class="row">
            <div class="col-md-8">
              <div class="__vertical_center left"  style="min-height: 70px;">
                <ul class="nav nav-pills custom-tab-navs mb-3 mt-3">
                  <li class="nav-item"><a class="nav-link" href="<?= site_url('queries') ?>" ><i class="fa fa-chevron-left" aria-hidden="true"></i> Back to Messagses</a></li>
                </ul>    
              </div>
            </div>
            <div class="col-md-4">
              <div class="__vertical_center right">
                <!-- <span class="fa fa-search"></span> -->
              </div>
            </div>
          </div>
          <hr class="mt-0">
          <div class="row">
            <div class="col">
              <div class="box box-primary">
                <!-- /.box-header -->
                <div class="box-body no-padding">
                  <!-- FROM EMAIL -->
                  <div class="mailbox-read-info">
                    <h3 id="subject_query"></h3>
                    <h5>From: <span id="sender_query"></span></h5>
                    <p class="mailbox-read-time text-md-right" id="date_query"></p>
                  </div>

                  <div class="mailbox-controls with-border">

                    <!-- MESSAGE CONTROLS -->
                    <div class="__vertical_md_center pt-1">
                      <form class="mr-md-2" action="queries.php" method="POST">
                        <button type="submit" class="mx-auto mx-md-0 __transition float-md-right pr-md-3 pl-md-2 red-link"><i class="fa fa-trash-o"></i> Delete</button>
                        <input type="hidden" name="msgids" value="">
                      </form>
                      <a id="composeReply" class="mx-auto mx-md-0 __transition float-md-right pr-md-3 pl-md-2 primary-link"> <i class="fa fa-reply"></i> Reply </a>
                      <!-- <form class="mr-md-2" action="<?= site_url('compose-reply/1') ?>" method="POST">
                        <button type="submit" class="mx-auto mx-md-0 __transition float-md-right pr-md-3 pl-md-2 primary-link"><i class="fa fa-reply"></i> Reply</button>
                        <input type="hidden" name="fromEmail" value="">
                      </form>
                      <form class="mr-md-2" action="compose.php" method="POST">
                        <button type="submit" class="mx-auto mx-md-0 __transition float-md-right pr-md-3 pl-md-3 mw-150 green-link"><i class="fa fa-share"></i> Forward</button>
                      </form> -->
                    </div>

                    <hr>

                    <!-- MESSAGE -->
                    <div class="mailbox-read-message" id="message_body_query"></div>

                  </div>
                  <div class="box-footer">
                    <!-- ATTACHMENTS -->
                    <div class="mb-3"></div>
                    <div class="col">
                      <ul class="mailbox-attachments clearfix">
                          <li>
                            <a href="asd" class="btn btn-default btn-xs">asd <i class="fa fa-cloud-download"></i> </a>
                          </li>  
                      </ul>
                    </div>
                  </div>
                </div>
                <!-- /. box -->
              </div>
            </div>

          </div>
        </div>
        </div>
    </section>

<script src="<?= base_url('assets/js/actionlayer/queryInfo.js') ?>"></script>
<script src="<?= base_url('assets/js/dashboard/queries.js'); ?>"></script>