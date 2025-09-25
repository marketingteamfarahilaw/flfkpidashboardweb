<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/locale/af.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
<script src="https://unpkg.com/vue-multiselect@2.1.2/dist/vue-multiselect.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://unpkg.com/vue-multiselect@2.1.2/dist/vue-multiselect.min.css">

<script type="text/javascript">
    $(document).ready(function() {
        var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
        new Vue({
            components: {
                Multiselect: window.VueMultiselect.default
            },
            data: {
                connection: [],
                connectionSelected: '',
            },
            mounted() {
                this.setLawyers();  
                $(document).ready( function() {

                    $('.userDetail').hide();
                    $('.clientDetail').hide();
                    $('#add_invoice').hide();
                    $('#add_invoice_client').hide();
                    if($('#switchDetail').is(':checked')){
                        $('.clientDetail').show();
                        $('.userDetail').hide();
                        $('#add_invoice').hide();
                        $('#add_invoice_client').show();
                    } else {
                        $('.clientDetail').hide();
                        $('.userDetail').show();
                        $('#add_invoice').show();
                        $('#add_invoice_client').hide();
                    }
                    $('#switchDetail').click(() => {
                        if($('#switchDetail').is(':checked')){
                            $('.clientDetail').show();
                            $('.userDetail').hide();
                            $('#add_invoice').hide();
                            $('#add_invoice_client').show();
                        } else {
                            $('.clientDetail').hide();
                            $('.userDetail').show();
                            $('#add_invoice').show();
                            $('#add_invoice_client').hide();
                        }
                    });
                });
            },
            methods: {
                getConnection: async function () {
                    let response = axios.get(endPoints.CONNECTION.concat('?token='+token), CONFIG.HEADER);

                    return response;
                },
                setLawyers: async function () {
                    try {
                        let result = await this.getConnection();
                        this.connection = result.data.response;
                    } catch (error) {
                        console.log(error);
                    }
                },
                createInvoiceLawlist: function (e) {
                    e.preventDefault();
                    var bodyFormData = new FormData();
                    bodyFormData.set("transaction_no", $('#invoicenumber').val());
                    bodyFormData.set("portfolio_lawlist_user", this.connectionSelected.id);
                    bodyFormData.set("portfolio_firstname", this.connectionSelected.firstname);
                    bodyFormData.set("portfolio_lastname", this.connectionSelected.lastname);
                    bodyFormData.set("portfolio_email", this.connectionSelected.email);
                    bodyFormData.set("portfolio_legal_title", $('#civilTerms').val());
                    bodyFormData.set("portfolio_legal_desc", $('#clientNotes').val());
                    bodyFormData.set("portfolio_price", $('#amountDue').val());
                    bodyFormData.set("portfolio_total", $('.inputtotalamount').val());
                    bodyFormData.set("portfolio_date", $('#invoice_date').val());
                    axios.post(endPoints.PORTFOLIO_CLIENT_ADD.concat('/'+ last_part + '?token=' + token), bodyFormData, CONFIG.HEADER)
                        .then( (response) => {
                            toastr.success('Invoice successfully sent!');
                            setTimeout(function() { 
                                window.location = 'portfolio';
                            }, 2000);
                        })
                        .catch( (error) => {
                            toastr.error(error.response);
                            console.log(error.response);
                        });
                },  

                createPortfolioInvoice : function (e) {
                    e.preventDefault();
                    var bodyFormData = new FormData();
                    bodyFormData.set("transaction_no", $('#invoicenumber').val());
                    bodyFormData.set("portfolio_firstname", $('#invoiceFname').val());
                    bodyFormData.set("portfolio_lastname", $('#invoiceLname').val());
                    bodyFormData.set("portfolio_email", $('#invoiceEmail').val());
                    bodyFormData.set("portfolio_legal_title", $('#civilTerms').val());
                    bodyFormData.set("portfolio_legal_desc", $('#clientNotes').val());
                    bodyFormData.set("portfolio_price", $('#amountDue').val());
                    bodyFormData.set("portfolio_total", $('.inputtotalamount').val());
                    bodyFormData.set("portfolio_date", $('#invoice_date').val());

                    axios.post(endPoints.PORTFOLIO_ADD.concat('/'+ last_part + '?token=' + token), bodyFormData, CONFIG.HEADER)
                            .then( (response) => {
                                toastr.success('Invoice successfully sent!');
                                setTimeout(function() { 
                                    window.location = 'portfolio';
                                }, 2000);
                            })
                            .catch( (error) => {
                                toastr.error(error.response);
                                console.log(error.response);
                            });
                }
            }
        }).$mount('#app')
    });
</script>
<style type="text/css">
    .switch {
      position: relative;
      display: inline-block;
      width: 60px;
      height: 34px;
    }

    .switch input { 
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      -webkit-transition: .4s;
      transition: .4s;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      -webkit-transition: .4s;
      transition: .4s;
    }

    input:checked + .slider {
      background-color: #2196F3;
    }

    input:focus + .slider {
      box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
      -webkit-transform: translateX(26px);
      -ms-transform: translateX(26px);
      transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
      border-radius: 34px;
    }

    .slider.round:before {
      border-radius: 50%;
    }
</style>
  <!-- Main content -->
<section class="content" id="app">
      <div class="container-fluid pl-2 pr-2 pl-md-5 pr-md-5 pb-5 pt-5">  
        <div class="row mb-2">
          <div class="col-sm-6">
            <h2 class="page-header">
              <b>Create Billing</b><br>
            </h2>
          </div>
          <div class="col-sm-6" id="add_invoice">
            <div class="float-lg-right">
              <input type="submit" class="pl-2 pl-sm-3 pr-2 pr-sm-3 __transition green-link d-inline-block invoice-link" value="Send Invoice" @click="createPortfolioInvoice">
            </div>
          </div>
          <div class="col-sm-6" id="add_invoice_client">
            <div class="float-lg-right">
              <input type="submit" class="pl-2 pl-sm-3 pr-2 pr-sm-3 __transition green-link d-inline-block invoice-link" value="Send Invoice" @click="createInvoiceLawlist">
            </div>
          </div>
        </div>

        <hr>

        <!-- title row -->
        <div class="row mb-2">
          <div class="col-xs-12">
            <h2 class="page-header">
              
            </h2>
          </div>
          <!-- /.col -->
        </div>

        <!-- info row -->
        <div class="row invoice-info">
          <div class="col-sm-6 invoice-col">
              <span id="invoice_made"></span><br>
              <span id="invoice_address"></span><br>
              Email: <span id="invoice_made_email"></span><br>
              Phone: <span id="invoice_made_number"></span><br>
              billing number: <input type="number" name="invoicenumber" id="invoicenumber" placeholder="000-00000-0000" requried>
          </div>
          
          <!-- /.col -->
          <div class="col-sm-6 invoice-col">
            <b>billing Date:</b> <?= date('m/d/Y') ?><br>
            <input type="hidden" name="invoice_date" id="invoice_date" value="<?= date('Y-m-d') ?>">
          </div>
          <!-- /.col -->
        </div>

        <hr>

        <!-- Table row -->
        <div class="row">
          <div class="col-sm-6">
            <div class="mt-3"></div>
            <strong>Billing details:</strong><br>

            <label class="switch">
                <input type="checkbox" id="switchDetail" checked>
                <span class="slider round"></span>
            </label>

            <div class="userDetail">
                <div class="row">
                  <div class="col-md-6">
                    <input type="text" name="invoiceFname" id="invoiceFname" class="form-control mb-3" placeholder="First Name" required>
                  </div>
                  <div class="col-md-6">
                    <input type="text" name="invoiceLname" id="invoiceLname" class="form-control mb-3" placeholder="Last Name" required>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <input type="email" name="invoiceEmail" id="invoiceEmail" class="form-control mb-3" placeholder="Email" required>
                  </div>
                  <div class="col-md-6">
                    <input type="text" name="invoicePhone" id="invoicePhone" class="form-control mb-3" placeholder="Phone" required>
                  </div>
                </div>
            </div>
            <div class="clientDetail mb15">
                <div>
                    <multiselect
                    v-model="connectionSelected"
                    :options="connection"
                    track-by="id" 
                    label="name"
                    placeholder="Select an Option"></multiselect>
                </div>
            </div>
            <input type="text" name="invoiceAddress" id="invoiceAddress" class="form-control mb-3" placeholder="Address">
            <strong>Legal Concern:</strong><br>
            <input type="text" name="civilTerms" id="civilTerms" class="form-control mb-3" placeholder="Civil Terms" required>
            <strong>Amount Due:</strong><br>
            <input type="number" name="amountDue" id="amountDue" min="1.00" step="0.01" class="form-control mb-3" placeholder="0.00" required>
            <strong>Payment Due:</strong><br>
            <input id="datepicker" name="paymentDue" id="paymentDue" type="text" class="form-control mb-3" placeholder="MM/DD/YYYY" required>            
            <strong>Description:</strong><br>
            <textarea name="clientNotes" id="clientNotes" class="form-control mb-3"></textarea>


            <input type="hidden" name="totalAmount" id="totalamount" class="inputtotalamount" value="0">
          </div>
          <!-- /.col -->
          <div class="col-sm-6">
            <p class="lead mb-3 mt-3">Billing Summary</p>
            <div class="table-responsive text-right">
              <table class="table">
                <tbody><tr>
                  <th width="50%">Subtotal:</th>
                  <td class="subtotal">₱0.00</td>
                </tr>
                <tr>
                  <th>Lawdger Fee (5%)</th>
                  <td class="lawdgerfee">₱0.00</td>
                </tr>
                <tr>
                  <th class="bc-lightgray">Total:</th>
                  <td class="bc-lightgray totalamount">₱0.00</td>
                </tr>
              </tbody></table>
            </div>
        </div>
        <!-- /.row -->

        <hr class="mt-0">

      </div>
</section>

<script src=" <?= base_url('assets/js/dashboard/invoice.js') ?>"></script>
<script src=" <?= base_url('assets/js/actionlayer/createInvoice.js') ?>"></script>

