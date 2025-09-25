<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard/profile.css') ?>">
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>

    <!-- Main content -->
    <section class="content bc-white" id="app">
        <div class="card card-widget widget-user">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header text-white">
                <div class="widget-user-image">
                    <div class="container-fluid pl-md-2 pr-md-2 pl-md-5 pr-md-5">
                        <div class="user-panel">
                            <div class="va-bottom image pl-0">
                                <img v-bind:src="'http://localhost/lawdger-service/uploads/'+profileInfo.customer_image_url + '.png'" class="img-circle w-170p"alt="User Image">
                            </div>
                            <div class="va-bottom info mb-lg-5">
                                <h4 class="c-white fw-300" v-text="profileInfo.name"></h4>
                                <p class="fs-14 c-white" v-text="profileInfo.email"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid pt-3 pl-md-2 pr-md-2 pl-md-5 pr-md-5">
  
            <div>
                <div class="row profile-text">
                    <div class="col-12 col-lg-7 col-xl-5 ml-auto"></div>
                    <div class="col-12 col-lg-3 col-xl-5">
                        <div class="text-lg-right">
                            <a :href="'<?= base_url("compose/") ?>' + profileInfo.id" class="mb-3 __transition green-link d-inline-block mr-md-1">Message</a>
                            <a @click="connect(profileInfo.id)" class="mb-3 __transition primary-link d-inline-block mr-md-1">Connect</a>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
            <div class="pt-4 pb-4">
                <h4 class="mb-3 fw-300">About</h4>
                <p class="mb-3 fw-300 c-gray" v-text="profileInfo.customer_about"></p>
            </div>
            <div class="pt-4 pb-4">
                <h4 class="mb-3 fw-300">Skills</h4>
                <p class="mb-3 fw-300 c-gray" v-if="profileInfo.customer_skills != null"></p>
                <p class="mb-3 fw-300 c-gray" v-else>-</p>
            </div>
            <div class="pt-4 pb-4">
                <h4 class="mb-3 fw-300">Blogs</h4>
                <div class="content_blogs pl30">
                    <p class="mb-3 fw-300 c-gray">asdasd</p>
                    <p class="mb-3 fw-300 c-gray">asd</p>
                    <p class="mb-3 fw-300 c-gray">asd</p>
                </div>
            </div>
        </div>
    </section>

<script type="text/javascript">
    $(document).ready(function() {
        var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
        new Vue({
            data: {
                profileInfo: '',
            },
            mounted() {
                $(document).ready(() => {
                    this.setlawyerInfo();
                });
            },
            methods: {
                setlawyerInfo: function () {
                    var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
                    var url = $(location).attr("href"),
                    parts = url.split("/"),
                    last_part = parts[parts.length - 1];

                    axios.get(endPoints.LAWYER_INFO.concat('/'+ last_part + '?token=' + token), CONFIG.HEADER)
                        .then( (response)  => {
                            console.log(response.data.response);
                            this.profileInfo = response.data.response;
                        })
                        .catch( (error)  => {
                            console.log(error);
                            toastr.error('Error communicating on server');
                        });
                },
                connect: async function (id) {
                    var token = JSON.parse(LocalStorage.get(STORAGE_ITEM.TOKEN));
                    var bodyFormData = new FormData();
                    bodyFormData.set("lawyer_connected", id);
                    axios.post(endPoints.CONNECT.concat('/'+ id).concat('?token='+token), bodyFormData, CONFIG.HEADER)
                                            .then( (response) => {
                                                toastr.success('Successfully connected!');
                                                setTimeout(function() { 
                                                    window.location = '<?= base_url("compose/") ?>'+id;
                                                }, 2000);
                                            })
                                            .catch( (error) => {
                                                toastr.error(error.response);
                                                console.log(error.response);
                                            });
                }
            },
        }).$mount('#app')
    });
</script>