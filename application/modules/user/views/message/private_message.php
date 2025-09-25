<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
    <script src="https://unpkg.com/vue-multiselect@2.1.2/dist/vue-multiselect.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/vue-multiselect@2.1.2/dist/vue-multiselect.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/queries.css') ?>">

    <!-- Main content -->
    <section class="bc-lightgray content content-msg __transition pb-3" style="min-height: 100%;">
        <div class="container-fluid pl-2 pr-2 pl-md-5 pr-md-5">
            <div class="row" id="app">
                <div class="col-md-12 __transition" >
                    <input type="hidden" name="action" value="sendEmail">
                    <div class="row">
                        <div class="col-md-8">
                        <div class="__vertical_center left"  style="min-height: 70px;">
                            <ul class="nav nav-pills custom-tab-navs mb-3 mt-3">
                                <li class="nav-item"><a class="nav-link" href="<?= site_url('queries') ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i> Back to Messages</a></li>
                            </ul>    
                        </div>
                        </div>
                        <div class="col-md-4">
                            <div class="__vertical_center right">
                              <!-- <span class="fa fa-search"></span> -->
                            </div>
                        </div>
                    </div>
                    
                    <hr class="mt-0 mb-2">

                    <div class="row">
                        <div class="col">
                            <div class="__vertical_md_center pt-1">
                                <div class="mr-md-2">
                                    <a href="queries.php" class="mx-auto mx-md-0 __transition d-block d-md-inline-block pr-md-3 pl-md-3 mx-auto red-link"><i class="fa fa-times"></i> Discard</a>
                                </div>
                                <div>
                                    <a class="mx-auto mx-md-0 __transition d-block d-md-inline-block pr-md-3 pl-md-3 mx-auto  primary-link" id="sendBtn" @click="sendQuery"><i class="fa fa-envelope-o"></i> Send</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                <div class="row">
                    <div class="col">
                        <div class="box box-primary">
                            <div class="box-header with-border"> </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <div class="form-group">
                                        <multiselect
                                        v-model="connectionSelected"
                                        :options="connection"
                                        track-by="id" 
                                        label="name"
                                        placeholder="Select an
                                        Option"></multiselect>
                                        
                                    <div class="form-group">
                                        <input name="sendSubject" class="form-control" id="subject" placeholder="Subject:" required>
                                    </div>
                                    <div class="form-group">
                                        <textarea name="sendBody" id="message_body" class="form-control" style="height: 300px"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <!-- <div class="btn btn-default btn-file"> -->
                                        <p class="help-block mb-1"><b>Attachment</b> (Max. 32MB)</p>
                                        <input type="file" name="sendAttachment" id="attachment" multiple>
                                        <!-- </div> -->
                                    </div>
                                </div>
                                <!-- /.box-body -->
                              
                            </div>
                            <!-- /. box -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<script type="text/javascript">
    $(document).ready(function() {
        var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
        var url = $(location).attr("href"),
        parts = url.split("/"),
        last_part = parts[parts.length - 1];
        new Vue({
            components: {
                Multiselect: window.VueMultiselect.default
            },
            data: {
                connection: [],
                connectionSelected: '',
            },
            mounted() {
                $(document).ready( () => {
                    this.setLawyers();
                });
            },
            methods: {
                getConnection: async function () {
                    let response = axios.get(endPoints.CONNECTION_CHAT.concat('/'+last_part+'?token='+token), CONFIG.HEADER);

                    return response;
                },
                setLawyers: async function () {
                    try {
                        let result = await this.getConnection();
                        this.connection = result.data.response;
                        this.connectionSelected = result.data.response;
                    } catch (error) {
                        console.log(error);
                    }
                },
                sendQuery: function (e) { 
                    e.preventDefault();   

                    var bodyFormData = new FormData();

                    bodyFormData.set('send_to',  this.connectionSelected[0].id);
                    bodyFormData.set('subject',  $('#subject').val());
                    bodyFormData.set('message_body',  $('#message_body').val());
                    
                    axios.post(endPoints.COMPOSE.concat('?token=' + token), bodyFormData, CONFIG.HEADER)
                        .then( (response) => {
                            this.connectToLawyer();
                        })
                        .catch( (error) => {
                            toastr.error('Error occured');
                        })
                },
                connectToLawyer: async function () {
                    var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));

                    var bodyFormData = new FormData();
                    var composeSelected = this.connectionSelected[0].id;
                    bodyFormData.set("lawyer_connected", composeSelected);
                    axios.post(endPoints.CONNECT.concat('/'+ composeSelected).concat('?token='+token), bodyFormData, CONFIG.HEADER)
                         .then( (response) => {
                            toastr.success('Successfully connected!');
                            setTimeout(function() { 
                                window.location = '<?= site_url("queries") ?>';
                            }, 2000);
                         })
                         .catch( (error) => {
                             toastr.error('AN error occured while connecting on the lawyer');

                         });
                }
            }
        }).$mount('#app')
    });
</script>
<script src="<?= base_url('assets/js/dashboard/queries.js'); ?>"></script>