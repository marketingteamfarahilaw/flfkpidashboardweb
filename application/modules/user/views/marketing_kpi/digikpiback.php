<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>


<script src="<?= base_url('assets/js/actionlayer/kpi.js') ?>"></script>

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
            
            <h4 class="fw-300 mb-3">WEB DEVELOPMENT TEAM</h4>
            <div class="row">
                <div class="col-md">
                    <!-- 📊 MTD Summary Section -->
                    <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="fw-300 mb-3">Summary Report: Month-to-Date (MTD)</h5>
                        <p class="mb-1">Total Performed Reports: <strong>{{ mtdData.totalReports }}</strong></p>
                        <p class="mb-1">Unique Team Members: <strong>{{ mtdData.uniquePerformers }}</strong></p>
                        
                        <div>
                        <p class="fw-bold mt-3">Counts per Report Type:</p>
                        <ul class="mb-0">
                            <li v-for="(count, report) in mtdData.reportCounts" :key="report">
                            {{ report }}: <strong>{{ count }}</strong>
                            </li>
                        </ul>
                        </div>
                    </div>
                    </div>
                </div>
                
                <div class="col-md">
                    <!-- 📅 Weekly Summary Report by Person -->
                    <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="fw-300 mb-3">Summary Report: This Week (Per Individual)</h5>
                        <div v-for="(personData, personName) in weeklyDataByPerson" :key="personName" class="mb-3">
                        <h5 class="fw-300">{{ personName }}</h5>
                        <p class="mb-1">Total Reports: <strong>{{ personData.total }}</strong></p>
                    
                        <div>
                            <p class="fw-bold mb-1">Report Breakdown:</p>
                            <ul class="mb-0">
                            <li v-for="(count, report) in personData.reports" :key="report">
                                {{ report }}: <strong>{{ count }}</strong>
                            </li>
                            </ul>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-body">
                        <h5 class="fw-300 mb-3">Data Visualization: Reports This Month</h5>
                        <canvas id="barChart" height="150"></canvas>
                    </div>
                </div>
            </div>

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
                                <th>Notes</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="item in paginatedDetailList()" :key="item.id">
                                <td>{{ item.date }}</td>
                                <td>{{ item.title }}</td>
                                <td><a :href="item.link" target="_blank"> {{ item.link }}  </a></td>
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
            
            
            <hr>
            <!-- 📣 Social Media Reports Section -->
            
            <h3 class="fw-300 mb-3">Social Media Reports</h3>
            <div class="row">
                <div class="col-md">
                    <!-- 📣 SocMed MTD Summary -->
                    <div class="card mb-4">
                      <div class="card-body">
                        <h5 class="fw-300 mb-3">Socmed Summary Report: Month-to-Date (MTD)</h5>
                        <p class="mb-1">Total Reports: <strong>{{ socmedMtdData.totalReports }}</strong></p>
                        <p class="mb-1">Unique Contributors: <strong>{{ socmedMtdData.uniquePerformers }}</strong></p>
                    
                        <div>
                          <p class="fw-bold mt-3">Brand Breakdown:</p>
                          <ul class="mb-0">
                            <li v-for="(count, brand) in socmedMtdData.brandCounts" :key="brand">
                              {{ brand }}: <strong>{{ count }}</strong>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                </div>
                <div class="col-md">
                    <!-- 📅 SocMed Weekly Report Per Person -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="fw-300 mb-3">SocMed Summary Report: This Week (Per Individual)</h5>
                            <div v-for="(personData, personName) in socmedWeeklyByPerson" :key="personName" class="mb-3">
                                <h6 class="fw-bold">{{ personName }}</h6>
                                <p class="mb-1">Total Reports: <strong>{{ personData.total }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-body">
                        <h5 class="fw-300 mb-3">SocMed Reports Performed By (Bar Chart)</h5>
                        <canvas id="socmedBarChart" height="150"></canvas>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
              <div class="card-body">
                <!-- 🔍 SocMed Filters -->
                <div class="row mb-4">
                  <div class="col-12 mb-2">
                    <input type="text" class="form-control" v-model="socmedFilters.search" placeholder="Search SocMed records...">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Month</label>
                    <select class="form-control" v-model="socmedFilters.month">
                      <option value="">All</option>
                      <option v-for="month in uniqueMonths" :key="month" :value="month">{{ month }}</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Brand</label>
                    <select class="form-control" v-model="socmedFilters.brand">
                      <option value="">All</option>
                      <option v-for="brand in uniqueBrands" :key="brand" :value="brand">{{ brand }}</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label">Performed By</label>
                      <select class="form-control" v-model="socmedFilters.performed_by">
                        <option value="">All</option>
                        <option v-for="name in uniquePerformedBy" :key="name" :value="name">{{ name }}</option>
                      </select>
                    </div>
                </div>

                <button class="btn btn-success mb-3" @click="exportSocmedToExcel">
                  Export SocMed Reports to Excel
                </button>

                <div class="table-responsive">
                  <table class="table table-bordered table-sm">
                    <thead class="table-light">
                      <tr>
                        <th>Performed By</th>
                        <th>Total Reports</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <template v-for="(items, person) in groupedSocmedByPerson">
                        <!-- Summary Row -->
                        <tr>
                          <td>{{ person }}</td>
                          <td>{{ items.length }}</td>
                          <td>
                            <button class="btn btn-sm btn-outline-primary" @click="toggleSocmedDetails(person)">
                              {{ expandedSocmedPerson === person ? 'Hide' : 'View' }}
                            </button>
                          </td>
                        </tr>
                
                        <!-- Expanded Inner Row with Pagination -->
                        <tr v-if="expandedSocmedPerson === person">
                          <td colspan="3">
                            <div class="table-responsive">
                              <table class="table table-striped table-sm mb-0">
                                <thead>
                                  <tr>
                                    <th>Date</th>
                                    <th>Brand</th>
                                    <th>Title</th>
                                    <th>Link</th>
                                    <th>Notes</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr v-for="item in paginatedSocmedDetails(person)" :key="item.id">
                                    <td>{{ item.date }}</td>
                                    <td>{{ item.brand }}</td>
                                    <td>{{ item.title }}</td>
                                    <td><a :href="item.link" target="_blank"> {{ item.link }}  </a></td>
                                    <td>{{ item.note }}</td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                
                            <!-- Pagination Controls -->
                            <div class="d-flex justify-content-between align-items-center mt-2">
                              <button class="btn btn-sm btn-outline-secondary"
                                      @click="socmedDetailPage[person]--"
                                      :disabled="socmedDetailPage[person] <= 1">
                                Previous
                              </button>
                              <span>Page {{ socmedDetailPage[person] }} of {{ socmedTotalPages(person) }}</span>
                              <button class="btn btn-sm btn-outline-secondary"
                                      @click="socmedDetailPage[person]++"
                                      :disabled="socmedDetailPage[person] >= socmedTotalPages(person)">
                                Next
                              </button>
                            </div>
                          </td>
                        </tr>
                      </template>
                    </tbody>
                  </table>
                </div>

              </div>
            </div>
            
            
            <h3 class="fw-300 mb-3">Graphics Team Reports</h3>
            <!-- 🔍 Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                      <div class="col-md-12">
                        <label>Search</label>
                        <input type="text" class="form-control" v-model="filters.search" placeholder="Task name, status, assignee">
                      </div>
                      <div class="col-md-3">
                        <label>Due Month</label>
                        <select class="form-control" v-model="filters.dueMonth">
                          <option value="">All</option>
                          <option v-for="month in uniqueDueMonths" :key="month">{{ month }}</option>
                        </select>
                      </div>
                      <div class="col-md-3">
                        <label>Completed Month</label>
                        <select class="form-control" v-model="filters.completedMonth">
                          <option value="">All</option>
                          <option v-for="month in uniqueCompletedMonths" :key="month">{{ month }}</option>
                        </select>
                      </div>
                      <div class="col-md-3">
                        <label>Status</label>
                        <select class="form-control" v-model="filters.status">
                          <option value="">All</option>
                          <option>Completed</option>
                          <option>Not Completed</option>
                        </select>
                      </div>
                      <div class="col-md-3">
                        <label>Assignee</label>
                        <select class="form-control" v-model="filters.assignee">
                          <option value="">All</option>
                          <option v-for="name in uniqueAssignees" :key="name">{{ name }}</option>
                        </select>
                      </div>
                    </div>
                </div>
            </div>

            <!-- 🗂️ Grouped Asana Tasks Table -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Assignee</th>
                                <th># of Tasks</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                            <tbody>
                                <template v-for="(tasks, assignee) in groupedAsanaByAssignee" :key="assignee">
                                    <!-- Top-level row -->
                                    <tr>
                                        <td>{{ assignee }}</td>
                                        <td>{{ tasks.length }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" @click="toggleAssignee(assignee)">
                                              {{ expandedAssignee === assignee ? 'Hide' : 'View' }}
                                            </button>
                                        </td>
                                    </tr>
                        
                                    <!-- Detail expanded row -->
                                    <tr v-if="expandedAssignee === assignee">
                                        <td colspan="3">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-sm mb-0">
                                                    <thead>
                                                      <tr>
                                                        <th>Project</th>
                                                        <th>Task Name</th>
                                                        <th>Due Date</th>
                                                        <th>Completed At</th>
                                                        <th>Completed</th>
                                                        <th>Permalink</th>
                                                      </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="task in paginatedTasksByAssignee(assignee)" :key="task.id">
                                                            <td>{{ task.project }}</td>
                                                            <td>{{ task.name }}</td>
                                                            <td>{{ task.due_on }}</td>
                                                            <td>{{ task.completed_at }}</td>
                                                            <td>{{ task.completed }}</td>
                                                            <td><a :href="task.permalink_url" target="_blank">{{ task.permalink_url }}</a></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!-- Pagination Controls -->
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <button class="btn btn-sm btn-outline-secondary"
                                                      @click="assigneeDetailPage[assignee]--"
                                                      :disabled="assigneeDetailPage[assignee] <= 1">
                                                    Previous
                                                </button>
                                                <span>Page {{ assigneeDetailPage[assignee] }} of {{ totalPagesByAssignee(assignee) }}</span>
                                                <button class="btn btn-sm btn-outline-secondary"
                                                      @click="assigneeDetailPage[assignee]++"
                                                      :disabled="assigneeDetailPage[assignee] >= totalPagesByAssignee(assignee)">
                                                    Next
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        </div>
    </div>
</section>

<script src="<?= base_url('assets/js/jCardInvoice/jCardInvoice.js'); ?>"></script>