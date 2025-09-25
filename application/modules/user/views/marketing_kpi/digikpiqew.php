<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>


<script type="text/javascript">
    $(document).ready(function () {
      new Vue({
        el: '#app',
        data: {
          selectedRow: {
            person: '',
            report: '',
          },
          detailPage: 1,
          detailPerPage: 10,
          profileData: [],
          groupedData: {},
          filters: {
            report: '',
            month: '',
            brand: '',
            date: '',
            platform: '',
          },
          uniqueOptions: {
            reports: [],
            months: [],
            brands: [],
            dates: [],
            platforms: [],
          },
        },
        computed: {
          filteredData() {
            return this.profileData.filter(item => {
              const monthName = new Date(item.date).toLocaleString('default', { month: 'long' });

              return (
                (!this.filters.report || item.report === this.filters.report) &&
                (!this.filters.month || monthName === this.filters.month) &&
                (!this.filters.brand || item.brand === this.filters.brand) &&
                (!this.filters.date || item.date === this.filters.date) &&
                (!this.filters.platform || item.platform === this.filters.platform)
              );
            });
          }
        },
        watch: {
          filteredData: {
            handler() {
              this.groupRecords();
            },
            immediate: true
          }
        },
        methods: {
          exportGroupedToExcel() {
          // 1. Build Summary Sheet (Group Table)
          const summarySheet = [['Performed By', 'Report', 'Count']];
          for (const person in this.groupedData) {
            const reports = this.groupedData[person];
            for (const report in reports) {
              const count = reports[report];
              summarySheet.push([person, report, count]);
            }
          }

          // 2. Build Detail Sheet (Only Expanded Row)
          const detailSheet = [['Performed By', 'Report', 'Date', 'Brand', 'Platform', 'Notes']];
          if (this.selectedRow.person && this.selectedRow.report) {
            const details = this.getDetailList(this.selectedRow.person, this.selectedRow.report);
            details.forEach(item => {
              detailSheet.push([
                item.performed_by,
                item.report,
                item.date,
                item.brand,
                item.platform,
                item.notes || '', // fallback if "notes" not present
              ]);
            });
          }

          // 3. Create and export workbook
          const wb = XLSX.utils.book_new();
          const summaryWS = XLSX.utils.aoa_to_sheet(summarySheet);
          XLSX.utils.book_append_sheet(wb, summaryWS, 'Grouped Summary');

          if (detailSheet.length > 1) {
            const detailWS = XLSX.utils.aoa_to_sheet(detailSheet);
            XLSX.utils.book_append_sheet(wb, detailWS, 'Selected Details');
          }

          const filename = `Report_${new Date().toISOString().slice(0, 10)}.xlsx`;
          XLSX.writeFile(wb, filename);
        },

          async getProfileDetail() {
            try {
              let response = await axios.get('http://localhost:8899/flfdashboard/api/kpi', CONFIG.HEADER);
              return response;
            } catch (error) {
              console.log(error);
            }
          },
          toggleDetails(person, report) {
            if (this.selectedRow.person === person && this.selectedRow.report === report) {
              // Collapse same row
              this.selectedRow = { person: '', report: '' };
            } else {
              // Open new row and reset pagination
              this.selectedRow = { person, report };
              this.detailPage = 1; // ✅ Reset to first page
            }
          },
          getDetailList(person, report) {
            return this.filteredData.filter(
              item => item.performed_by === person && item.report === report
            );
          },
          paginatedDetailList() {
            const all = this.getDetailList(this.selectedRow.person, this.selectedRow.report);
            const start = (this.detailPage - 1) * this.detailPerPage;
            return all.slice(start, start + this.detailPerPage);
          },
          detailTotalPages() {
            const all = this.getDetailList(this.selectedRow.person, this.selectedRow.report);
            return Math.ceil(all.length / this.detailPerPage);
          },
          async setLawyers() {
            try {
              let result = await this.getProfileDetail();
              this.profileData = result.data.response;
              this.setFilterOptions();
              this.groupRecords();
            } catch (error) {
              console.log(error);
            }
          },
          setFilterOptions() {
            const reports = new Set();
            const months = new Set();
            const brands = new Set();
            const dates = new Set();
            const platforms = new Set();

            this.profileData.forEach(item => {
              reports.add(item.report);
              months.add(new Date(item.date).toLocaleString('default', { month: 'long' }));
              brands.add(item.brand);
              dates.add(item.date);
              platforms.add(item.platform);
            });

            this.uniqueOptions = {
              reports: Array.from(reports),
              months: Array.from(months),
              brands: Array.from(brands),
              dates: Array.from(dates),
              platforms: Array.from(platforms),
            };
          },
          groupRecords() {
            const grouped = {};

            this.filteredData.forEach(item => {
              const person = item.performed_by;
              const report = item.report;

              if (!grouped[person]) {
                grouped[person] = {};
              }

              if (!grouped[person][report]) {
                grouped[person][report] = 0;
              }

              grouped[person][report]++;
            });

            this.groupedData = grouped;
          }
        },
        mounted() {
          this.setLawyers();
        }
      });
    });



</script>

    <section class="content" id="app">
      <div class="container-fluid pt-3 pl-md-2 pr-md-2 pl-md-5 pr-md-5">
          
          <div>
              <!-- User Info -->
              <div class="row profile-text">
                  <div class="col-12 col-md-12 ml-auto">
                    <div class="row">
                      <div class="col-12 col-lg-3 ml-auto">
                        <p class="fw-300">Target Leads</p>
                        <h4 class="fw-300 mb-3">250</h4>
                      </div>
                      <div class="col-12 col-lg-3">
                        <p class="fw-300">Target Signed</p>
                        <h4 class="fw-300 mb-3">40</h4>
                      </div>
                      <div class="col-12 col-lg-6">
                        <p class="fw-300">Target Acquisition Rate</p>
                        <h4 class="fw-300 mb-3">{{(40/250) * 100}}%</h4>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12 col-lg-3 ml-auto">
                        <p class="fw-300">MTD Leads</p>
                        <h4 class="fw-300 mb-3">215</h4>
                      </div>
                      <div class="col-12 col-lg-3">
                        <p class="fw-300">MTD Signed</p>
                        <h4 class="fw-300 mb-3">15</h4>
                      </div>
                      <div class="col-12 col-lg-6">
                        <p class="fw-300">target Acquisition Rate</p>
                        <h4 class="fw-300 mb-3">{{(15/215) * 100}}%</h4>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12 col-lg-3 ml-auto">
                        <p class="fw-300">% to Goal (Leads)</p>
                        <h4 class="fw-300 mb-3">{{(215/250) * 100}}%</h4>
                      </div>
                      <div class="col-12 col-lg-3">
                        <p class="fw-300">% to Goal (Signed)</p>
                        <h4 class="fw-300 mb-3">{{(15/40) * 100}}%</h4>
                      </div>
                      <div class="col-12 col-lg-6">
                        <p class="fw-300">% to Goal (AR)</p>
                        <h4 class="fw-300 mb-3">{{(6.98/16) * 100}}%</h4>
                      </div>
                    </div>
                  </div>
                  <div class="col-12 col-lg-3 col-xl-5">
                      <div class="text-lg-right">
                          <!-- <a href="<?= base_url('update-profile') ?>" class="mb-3 __transition primary-link d-inline-block mr-md-1">Edit Profile</a> -->
                      </div>
                  </div>
              </div>
              <hr>
          <!-- User Data -->
          <div class="table-responsive">
            <h4>WEB DEVELOPMENT TEAM</h4>
            <!-- Filters -->
            <div class="row mb-4">
              <div class="col">
                <label>Report:</label>
                <select class="form-control" v-model="filters.report">
                  <option value="">All</option>
                  <option v-for="report in uniqueOptions.reports" :key="report" :value="report">{{ report }}</option>
                </select>
              </div>
              <div class="col">
                <label>Month:</label>
                <select class="form-control" v-model="filters.month">
                  <option value="">All</option>
                  <option v-for="month in uniqueOptions.months" :key="month" :value="month">{{ month }}</option>
                </select>
              </div>
              <div class="col">
                <label>Brand:</label>
                <select class="form-control" v-model="filters.brand">
                  <option value="">All</option>
                  <option v-for="brand in uniqueOptions.brands" :key="brand" :value="brand">{{ brand }}</option>
                </select>
              </div>
              <div class="col">
                <label>Platform:</label>
                <select class="form-control" v-model="filters.platform">
                  <option value="">All</option>
                  <option v-for="platform in uniqueOptions.platforms" :key="platform" :value="platform">{{ platform }}</option>
                </select>
              </div>
            </div>

            <!-- Grouped Table -->
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Performed By</th>
                    <th>Report</th>
                    <th>Count</th>
                  </tr>
                </thead>
                <tbody>
                <button class="btn btn-success mb-3" @click="exportGroupedToExcel">
                  Export Summary Table (Excel)
                </button>
                  <template v-for="(reports, person) in groupedData">
                    <template v-for="(count, report, index) in reports" :key="person + '-' + report">
                      <!-- Summary Row -->
                      <tr @click="toggleDetails(person, report)" style="cursor: pointer;">
                        <td v-if="index === 0" :rowspan="Object.keys(reports).length"><strong>{{ person }}</strong></td>
                        <td>{{ report }}</td>
                        <td>{{ count }}</td>
                      </tr>

                      <!-- Nested Table Row -->
                      <tr v-if="selectedRow.person === person && selectedRow.report === report">
                        <td colspan="5">
                          <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                              <thead>
                                <tr>
                                  <th>Date</th>
                                  <th>Brand</th>
                                  <th>Title</th>
                                  <th>Link</th>
                                  <th>Platform</th>
                                  <th>Notes</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr v-for="item in paginatedDetailList()" :key="item.id">
                                  <td>{{ item.date }}</td>
                                  <td>{{ item.brand }}</td>
                                  <td>{{ item.title }}</td>
                                  <td>{{ item.link }}</td>
                                  <td>{{ item.platform }}</td>
                                  <td>{{ item.notes }}</td>
                                </tr>
                              </tbody>
                            </table>

                            <!-- Pagination Controls -->
                            <div class="d-flex justify-content-between align-items-center mt-2">
                              <button class="btn btn-sm btn-primary"
                                      @click="detailPage--"
                                      :disabled="detailPage === 1">
                                Previous
                              </button>
                              <span>Page {{ detailPage }} of {{ detailTotalPages() }}</span>
                              <button class="btn btn-sm btn-primary"
                                      @click="detailPage++"
                                      :disabled="detailPage === detailTotalPages()">
                                Next
                              </button>
                            </div>
                          </div>
                        </td>
                      </tr>
                    </template>
                  </template>
                </tbody>

              </table>
            </div>
          </div>
              <hr>
          </div>
      </div>
  </section>
  

<script src="<?= base_url('assets/js/jCardInvoice/jCardInvoice.js'); ?>"></script>