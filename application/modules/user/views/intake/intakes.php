<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        new Vue({
            data: {
                profileData: [],
            },
            mounted() {
                this.setLawyers();
            },
            methods: {
                getProfileDetail: async function () {
                    let response = axios.get(endPoints.INTAKE, CONFIG.HEADER);

                    return response;
                },
                setLawyers: async function () {
                try {
                    console.log('yeah');
                    let result = await this.getProfileDetail();
                    this.profileData = result.data.response;
                    console.log(this.profileData);
                } catch (error) {
                    console.log(error);
                }
            },
            }
        }).$mount('#app')
    });
</script>

    <section class="content" id="app">
      <div class="container-fluid pt-3 pl-md-2 pr-md-2 pl-md-5 pr-md-5">
          
          <div>
          <!-- User Data -->
              <div class="pt-4 pb-4" v-for="item in profileData">
                  <h4 class="mb-3 fw-300">{{item.date}}</h4>
                  <div class="table-responsive">
                    <table class="table">
                      <tr v-for="data in item.records">

                        {{item.date}}
                        <td>
                          {{data.type}}
                        </td>
                        <td>
                          {{data.data}}
                        </td>
                      </tr>
                    </table>
                  </div>
              </div>
              <hr>
          </div>
      </div>
  </section>

<script src="<?= base_url('assets/js/dashboard/portfolio.js') ?>"></script>

<script src="<?= base_url('assets/js/jCardInvoice/jCardInvoice.js'); ?>"></script>