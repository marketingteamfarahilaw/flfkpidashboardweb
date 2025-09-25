<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>


<script src="<?= base_url('assets/js/actionlayer/kpi.js') ?>"></script>

<section class="content" id="app">
    <div class="container-fluid pt-3 pl-md-2 pr-md-2 pl-md-5 pr-md-5">
        
        <div>
        <!-- User Data -->
        <div class="table-responsive">
            
            <h4 class="fw-300 mb-3">WEB DEVELOPMENT TEAM</h4>

            <!-- Filters -->
            <div class="mb-3">
                <input type="text" class="form-control" v-model="filters.search" placeholder="Search anything...">
            </div>
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
                            <th>Target</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <button class="btn btn-success mb-3" @click="exportGroupedToExcel">
                            Export Summary Table (Excel)
                        </button>
                    
                        <!-- Detail Search Bar -->
                        <div class="mb-2">
                            <input type="text" class="form-control form-control-sm" v-model="detailSearch" placeholder="Search in details...">
                        </div>
                        <template v-for="(reports, person) in groupedData">
                            <template v-for="(count, report, index) in reports">
                            <!-- Summary Row -->
                            <tr @click="toggleDetails(person, report)" style="cursor: pointer;">
                                <td v-if="index === 0" :rowspan="Object.keys(reports).length"><strong>{{ person }}</strong></td>
                                <td>{{ report }}</td>
                                <td>{{ count }}</td>
                                <td>{{ getBalance(person, report) }}</td>
                                <td>{{ getPercentage(person, report) }}</td>
                            </tr>
                            <!-- Nested Table Row -->
                            <!-- Inner Detail Table -->
                            <tr v-if="selectedRow.person === person && selectedRow.report === report">
                              <td colspan="6"> <!-- Update colspan if you added new columns -->
                            
                                <div class="table-responsive">
                                  <table class="table table-bordered table-sm">
                                    <thead>
                                      <tr>
                                        <th>Date</th>
                                        <th>Title</th>
                                        <th>Link</th>
                                        <th>Language</th>
                                        <th>Notes</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <tr v-for="item in paginatedDetailList()" :key="item.id">
                                        <td>{{ item.date }}</td>
                                        <td>{{ item.title }}</td>
                                        <td><a :href="item.link" target="_blank"> {{ item.link }}  </a></td>
                                        <td>{{ item.language }}</td>
                                        <td>{{ item.note }}</td>
                                      </tr>
                                    </tbody>
                                  </table>
                            
                                  <!-- Pagination Controls -->
                                  <div class="d-flex justify-content-between align-items-center mt-2">
                                    <button class="btn btn-sm btn-primary" @click="detailPage--" :disabled="detailPage === 1">
                                      Previous
                                    </button>
                                    <span>Page {{ detailPage }} of {{ detailTotalPages() }}</span>
                                    <button class="btn btn-sm btn-primary" @click="detailPage++" :disabled="detailPage === detailTotalPages()">
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
    </div>
</section>