<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<script src="<?= base_url('assets/js/actionlayer/kpi.js') ?>"></script>

<section class="content" id="app">
  <div class="container-fluid pt-3 pl-md-2 pr-md-2 pl-md-5 pr-md-5">

    <div>
      <!-- Title -->
      <h4 class="fw-300 mb-3">WEB DEVELOPMENT TEAM</h4>

      <div class="table-responsive">

        <!-- Global Search -->
        <div class="mb-3">
          <input
            type="text"
            class="form-control"
            v-model="filters.search"
            placeholder="Search anything..."
          >
        </div>

        <!-- Filters -->
        <div class="row mb-4">
          <div class="col">
            <label>Report:</label>
            <select class="form-control" v-model="filters.report">
              <option value="">All</option>
              <option
                v-for="report in uniqueOptions.reports"
                :key="report"
                :value="report"
              >
                {{ report }}
              </option>
            </select>
          </div>
          <div class="col">
            <label>Month:</label>
            <select class="form-control" v-model="filters.month">
              <option value="">All</option>
              <option
                v-for="month in uniqueOptions.months"
                :key="month"
                :value="month"
              >
                {{ month }}
              </option>
            </select>
          </div>
          <div class="col">
            <label>Brand:</label>
            <select class="form-control" v-model="filters.brand">
              <option value="">All</option>
              <option
                v-for="brand in uniqueOptions.brands"
                :key="brand"
                :value="brand"
              >
                {{ brand }}
              </option>
            </select>
          </div>
          <div class="col">
            <label>Platform:</label>
            <select class="form-control" v-model="filters.platform">
              <option value="">All</option>
              <option
                v-for="platform in uniqueOptions.platforms"
                :key="platform"
                :value="platform"
              >
                {{ platform }}
              </option>
            </select>
          </div>
        </div>

        <!-- Actions (must be OUTSIDE <tbody>) -->
        <div class="d-flex justify-content-between align-items-center mb-2">
          <button class="btn btn-success" @click="exportGroupedToExcel">
            Export Summary Table (Excel)
          </button>

          <!-- Detail Search Bar -->
          <input
            type="text"
            class="form-control form-control-sm w-50"
            v-model="detailSearch"
            placeholder="Search in details..."
          >
        </div>

        <!-- Grouped Summary Table -->
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
              <!-- Loop over grouped data -->
              <template v-for="(reports, person) in groupedData" :key="person">
                <template
                  v-for="(count, report, index) in reports"
                  :key="person + '-' + report"
                >
                  <!-- Summary Row -->
                  <tr
                    @click="toggleDetails(person, report)"
                    style="cursor: pointer;"
                  >
                    <!-- Only show 'Performed By' once per person -->
                    <td
                      v-if="index === 0"
                      :rowspan="Object.keys(reports).length"
                    >
                      <strong>{{ person }}</strong>
                    </td>
                    <td>{{ report }}</td>
                    <td>{{ count }}</td>
                    <td>{{ getBalance(person, report) }}</td>
                    <td>{{ getPercentage(person, report) }}</td>
                  </tr>

                  <!-- Nested Detail Row -->
                  <tr
                    v-if="selectedRow.person === person && selectedRow.report === report"
                  >
                    <!-- Table has 5 columns, so colspan=5 -->
                    <td colspan="5">
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
                              <td>
                                <a :href="item.link" target="_blank">
                                  {{ item.link }}
                                </a>
                              </td>
                              <td>{{ item.language }}</td>
                              <td>{{ item.note }}</td>
                            </tr>
                          </tbody>
                        </table>

                        <!-- Pagination Controls -->
                        <div class="d-flex justify-content-between align-items-center mt-2">
                          <button
                            class="btn btn-sm btn-primary"
                            @click="detailPage--"
                            :disabled="detailPage === 1"
                          >
                            Previous
                          </button>
                          <span>Page {{ detailPage }} of {{ detailTotalPages() }}</span>
                          <button
                            class="btn btn-sm btn-primary"
                            @click="detailPage++"
                            :disabled="detailPage === detailTotalPages()"
                          >
                            Next
                          </button>
                        </div>
                      </div>
                    </td>
                  </tr>
                </template>
              </template>

              <!-- Empty state -->
              <tr v-if="Object.keys(groupedData).length === 0">
                <td colspan="5" class="text-center">
                  No data found for the selected filters.
                </td>
              </tr>
            </tbody>

          </table>
        </div>

      </div>
    </div>

  </div>
</section>
