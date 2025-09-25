<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?= base_url('assets/css/invoice.css') ?>">

<script>
    $(function(){
        var url = $(location).attr("href"),
        parts = url.split("/"),
        last_part = parts[parts.length - 1];

        $.jListInvoice.newListInvoice({
            target: '#target-div-list',
            searchData : '#search-portfolio-id',
            url : endPoints.PORTFOLIO_INFO_LIST + '/'+ last_part + '?token=' + token,
            columns : {
                name : {
                    name: 'Name',
                    sortable: true,
                },
                portfolio_legal_title : {
                    name: 'Legal Concern',
                    sortable: true,
                },
            },
            sort: '',
            order: 'desc',
            limit: 20,
            count: false,
            topBar: false,
        });

        $('#query_search').keyup(function(){
            $('#btn_submit').click();
        });

        $('#filter').on('change', function() {
            $('#btn_submit').click();
        });

        
        $('#btn_submit').css('visibility', 'hidden');
    });
</script>
<!-- Main content -->
<section class="content">
    <div class="container-fluid pl-2 pr-2 pl-md-5 pr-md-5 pb-5 pt-5"> 
        <div class="row mb-2">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h2 class="page-header">
                    <b>Billing #<span id="invoice_id"></span></b><br>
                </h2>
            </div>
            <div class="col-sm-6">
                <!-- Actions will only be available if status is not archived -->
                <div class="float-lg-right mr-lg-2">
                    <form method="POST">
                        <input type="hidden" id="id" name="id" value="a1sdf34fdgvd">
                        <input type="hidden" id="action" name="action" value="updatestatus">
                        <input type="submit" class="pl-2 pl-sm-3 pr-2 pr-sm-3 __transition primary-link d-lg-inline-block invoice-link" value="Mark as Paid">
                    </form>
                </div>
                <div class="float-lg-right mr-lg-2">
                      <form method="POST">
                        <input type="hidden" id="id" name="id" value="a1sdf34fdgvd">
                        <input type="hidden" id="action" name="action" value="resendinvoice">
                        <input type="submit" class="pl-2 pl-sm-3 pr-2 pr-sm-3 __transition green-link d-lg-inline-block invoice-link" value="Resend Invoice">
                      </form>
                </div>
            </div>
        </div>
        <hr>

        <!-- info row -->
        <div class="row invoice-info">
            <div class="col-sm-6 invoice-col">
                <span id="invoice_made"></span><br>
                <span id="invoice_address"></span><br>
                Email: <span id="invoice_made_email"></span><br>
                Phone: <span id="invoice_made_number"></span><br><br>
                www.lawdger.com
            </div>
          <!-- /.col -->
            <div class="col-sm-6 invoice-col">
                <b>Billing Date:</b> <span id="invoice_date"></span><br>
                <b>Billing Status:</b><span class="badge badgeType ml-1 pr-3 pl-3"><span id="invoice_status"></span></span><br>
                <b>Due Date:</b> 01/27/2019<br>
            </div>
          <!-- /.col -->
        </div>
        <hr>

        <div class="row">
            <div class="col">
                <p><strong>Bill To:</strong><br>
                <b><span id="invoice_name"></span></b><br>
                valenzuela <br>
                Email: <span id="invoice_email"></span><br>
                Phone: 0912365465765<br><br>
            </div>
        </div>

        <div class="row">
            <div class="col"> 
                <div class="card-body table-responsive p-0 table-container">
                    <table class="table table-hover">
                        <tbody id="target-div-list"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <hr>

        <!-- Table row -->
        <div class="row">
          <!-- /.col -->
            <div class="ml-auto col-sm-6">
                <p class="lead mb-3 mt-3">Billing Summary</p>
                <div class="table-responsive text-right">
                  <table class="table">
                    <tbody><tr>
                      <th width="50%">Subtotal:</th>
                      <td class="subtotal">₱ <span id="portfolio_price"> </td>
                    </tr>
                    <tr>
                      <th class="bc-lightgray">Total:</th>
                      <td class="bc-lightgray totalamount">₱<span id="portfolio_total"></td>
                    </tr>
                  </tbody></table>
                </div>
            </div>
        <!-- /.row -->
        </div>
    </div>    
</section>
<!-- /.content -->

<script src="<?= base_url('assets/js/actionlayer/portfolioInfo.js') ?>"></script>
<script src="<?= base_url('assets/js/jListInvoice/jListInvoice.js'); ?>"></script>