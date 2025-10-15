<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<!-- Libs -->
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios@1.6.7/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<script>
$(document).ready(function () {
  new Vue({
    el: '#app',
    data: {
      selectedRow: { person: '', report: '' },
      detailSearch: '',
      detailPage: 1,
      detailPerPage: 10,

      profileData: [],        // unified data from ALL endpoints
      groupedData: {},        // grouped by person -> report
      barChartInstance: null,

      // Filters
      filters: {
        search: '',
        report: '',
        month: '',
        brand: '',
        date: '',             // YYYY-MM-DD
        platform: '',
      },

      // Options for dropdowns
      uniqueOptions: {
        reports: [],
        months: [],
        brands: [],
        platforms: [],
        dates: [],
      },

      // Optional per-report targets (fallback used if key not found)
      targetData: {
        'Blog Optimized': 25,
        'Blog Published': 50,
        'News Published': 20,
        'Web App Developed': 20,
        'Web App Optimized': 20,
        'Landing Page Developed': 10,
        'Landing Page Optimized': 25
      },

      webdevEOW: '',
    },

    computed: {
      // Entire dashboard should always rely on filteredData
      filteredData() {
        const search = (this.filters.search || '').toLowerCase();

        return this.profileData
          .filter(item => {
            const monthName = item.date ? new Date(item.date).toLocaleString('default', { month: 'long' }) : '';
            const matchesSearch = !search || (
              (item.performed_by && item.performed_by.toLowerCase().includes(search)) ||
              (item.report && item.report.toLowerCase().includes(search)) ||
              (item.brand && item.brand.toLowerCase().includes(search)) ||
              (item.platform && item.platform.toLowerCase().includes(search)) ||
              (item.notes && item.notes.toLowerCase().includes(search))
            );

            // Normalize date to YYYY-MM-DD for strict equality against <input type="date">
            const sameDate = !this.filters.date ||
              (String(item.date).slice(0, 10) === String(this.filters.date).slice(0, 10));

            return (
              item.report !== 'TLC' &&              // keep excluding TLC if that’s intended
              matchesSearch &&
              (!this.filters.report || item.report === this.filters.report) &&
              (!this.filters.month || monthName === this.filters.month) &&
              (!this.filters.brand || item.brand === this.filters.brand) &&
              (!this.filters.platform || item.platform === this.filters.platform) &&
              sameDate
            );
          })
          .sort((a, b) => new Date(b.date) - new Date(a.date)); // latest first
      },

      // Weekly summary by person (still respects filters)
      weeklyDataByPerson() {
        const today = new Date();
        const startOfWeek = new Date(today);
        const dayOfWeek = today.getDay(); // Sunday=0
        const diffToMonday = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
        startOfWeek.setDate(today.getDate() - diffToMonday);
        startOfWeek.setHours(0, 0, 0, 0);

        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6);
        endOfWeek.setHours(23, 59, 59, 999);

        const result = {};
        this.filteredData.forEach(item => {
          const d = new Date(item.date);
          if (d >= startOfWeek && d <= endOfWeek) {
            const person = item.performed_by || 'Unknown';
            const report = item.report;
            if (!result[person]) result[person] = { total: 0, reports: {} };
            result[person].total++;
            if (!result[person].reports[report]) result[person].reports[report] = 0;
            result[person].reports[report]++;
          }
        });
        return result;
      },

      // Daily KPI by Team (respects filters)
      dailyKPIByTeam() {
        const out = {};
        this.filteredData.forEach(item => {
          const team = item.team || 'Unassigned';
          const date = (item.date || '').slice(0, 10);
          if (!out[team]) out[team] = {};
          if (!out[team][date]) out[team][date] = 0;
          out[team][date]++;
        });
        return out; // { Team: { '2025-10-14': 5, ... }, ... }
      },

      // MTD summary (respects filters)
      mtdData() {
        const now = new Date();
        const currentMonth = now.getMonth();
        const currentYear = now.getFullYear();

        const filteredMTD = this.filteredData.filter(item => {
          const d = new Date(item.date);
          return d.getMonth() === currentMonth && d.getFullYear() === currentYear;
        });

        const totalReports = filteredMTD.length;
        const uniquePerformers = new Set(filteredMTD.map(i => i.performed_by)).size;

        const reportCounts = {};
        filteredMTD.forEach(i => {
          if (!reportCounts[i.report]) reportCounts[i.report] = 0;
          reportCounts[i.report]++;
        });

        return { totalReports, uniquePerformers, reportCounts };
      }
    },

    watch: {
      filteredData: {
        handler() {
          this.groupRecords();
          this.updateBarChart();
        },
        immediate: true
      }
    },

    methods: {
      // ===== Detail table helpers =====
      toggleDetails(person, report) {
        if (this.selectedRow.person === person && this.selectedRow.report === report) {
          this.selectedRow = { person: '', report: '' };
          this.detailSearch = '';
        } else {
          this.selectedRow = { person, report };
          this.detailPage = 1;
          this.detailSearch = '';
        }
      },
      getDetailList(person, report) {
        return this.filteredData.filter(
          item => item.performed_by === person && item.report === report
        );
      },
      paginatedDetailList() {
        const all = this.getDetailList(this.selectedRow.person, this.selectedRow.report);
        const search = (this.detailSearch || '').toLowerCase();

        const filtered = all.filter(item =>
          !search ||
          (item.date && item.date.toLowerCase().includes(search)) ||
          (item.brand && item.brand.toLowerCase().includes(search)) ||
          (item.title && item.title.toLowerCase().includes(search)) ||
          (item.platform && item.platform.toLowerCase().includes(search)) ||
          (item.notes && item.notes.toLowerCase().includes(search))
        );

        const start = (this.detailPage - 1) * this.detailPerPage;
        return filtered.slice(start, start + this.detailPerPage);
      },
      detailTotalPages() {
        const all = this.getDetailList(this.selectedRow.person, this.selectedRow.report);
        const search = (this.detailSearch || '').toLowerCase();

        const filtered = all.filter(item =>
          !search ||
          (item.date && item.date.toLowerCase().includes(search)) ||
          (item.brand && item.brand.toLowerCase().includes(search)) ||
          (item.title && item.title.toLowerCase().includes(search)) ||
          (item.platform && item.platform.toLowerCase().includes(search)) ||
          (item.notes && item.notes.toLowerCase().includes(search))
        );

        return Math.ceil(filtered.length / this.detailPerPage);
      },

      // ===== Targets/Progress helpers =====
      getTarget(person, report) {
        const key = `${person}-${report}`;
        return this.targetData[key] || this.targetData[report] || 20; // sensible fallback
      },
      getBalance(person, report) {
        const target = this.getTarget(person, report);
        const count = this.groupedData[person][report];
        return target - count;
      },
      getPercentage(person, report) {
        const target = this.getTarget(person, report);
        const count = this.groupedData[person][report];
        return target ? ((count / target) * 100).toFixed(1) + '%' : '0%';
      },

      // ===== Data fetching (COMBINE 3 ENDPOINTS) =====
      async getProfileDetail() {
        try {
          const urls = [
            'http://31.97.43.196/kpidashboardapi/kpi/show',
            'http://31.97.43.196/kpidashboardapi/kpi/getGraphicsTeam',
            'http://31.97.43.196/kpidashboardapi/kpi/content'
          ];
          const responses = await Promise.all(urls.map(url => axios.get(url, CONFIG.HEADER)));
          const mergedData = responses.flatMap(res => res?.data?.response || []);
          // Optional: capture any conclusion text if present on the first endpoint
          this.webdevEOW = responses?.[0]?.data?.conclusionEOW || '';
          return mergedData;
        } catch (error) {
          console.error('Error fetching KPI data:', error);
          return [];
        }
      },

      async setKPI() {
        try {
          const result = await this.getProfileDetail();
          this.profileData = result;
          this.setFilterOptions();
          this.groupRecords();
          this.renderBarChart();  // ensure chart exists once data is in
        } catch (error) {
          console.error(error);
        }
      },

      setFilterOptions() {
        const reports = new Set();
        const months = new Set();
        const brands = new Set();
        const platforms = new Set();
        const dates = new Set();

        this.profileData.forEach(item => {
          if (item.report) reports.add(item.report);
          if (item.date) {
            months.add(new Date(item.date).toLocaleString('default', { month: 'long' }));
            dates.add(String(item.date).slice(0, 10));
          }
          if (item.brand) brands.add(item.brand);
          if (item.platform) platforms.add(item.platform);
        });

        this.uniqueOptions = {
          reports: Array.from(reports),
          months: Array.from(months),
          brands: Array.from(brands),
          platforms: Array.from(platforms),
          dates: Array.from(dates),
        };
      },

      groupRecords() {
        const grouped = {};
        this.filteredData.forEach(item => {
          const person = item.performed_by || 'Unknown';
          const report = item.report || 'Unspecified';

          if (!grouped[person]) grouped[person] = {};
          if (!grouped[person][report]) grouped[person][report] = 0;
          grouped[person][report]++;
        });
        this.groupedData = grouped;
      },

      // ===== Charts =====
      prepareBarChartData() {
        const performerCounts = {};
        this.filteredData.forEach(item => {
          const name = item.performed_by || 'Unknown';
          performerCounts[name] = (performerCounts[name] || 0) + 1;
        });

        return {
          labels: Object.keys(performerCounts),
          datasets: [{
            label: 'Reports',
            data: Object.values(performerCounts)
          }]
        };
      },
      renderBarChart() {
        const ctx = document.getElementById('barChart');
        if (!ctx) return;

        if (this.barChartInstance) this.barChartInstance.destroy();

        this.barChartInstance = new Chart(ctx, {
          type: 'bar',
          data: this.prepareBarChartData(),
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true } },
            scales: { x: { ticks: { autoSkip: false } }, y: { beginAtZero: true } }
          }
        });
      },
      updateBarChart() {
        if (!this.barChartInstance) return this.renderBarChart();
        this.barChartInstance.data = this.prepareBarChartData();
        this.barChartInstance.update();
      },

      // ===== Excel Export (grouped summary) =====
      exportGroupedToExcel() {
        // Build rows: Performed By, Report, Count, Target, Balance, Percentage
        const rows = [['Performed By', 'Report', 'Count', 'Target', 'Balance', 'Achieved %']];
        Object.keys(this.groupedData).forEach(person => {
          Object.keys(this.groupedData[person]).forEach(report => {
            const count = this.groupedData[person][report];
            const target = this.getTarget(person, report);
            const balance = target - count;
            const pct = target ? (count / target * 100).toFixed(1) + '%' : '0%';
            rows.push([person, report, count, target, balance, pct]);
          });
        });

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(rows);
        XLSX.utils.book_append_sheet(wb, ws, 'Grouped Summary');
        XLSX.writeFile(wb, 'kpi_grouped_summary.xlsx');
      }
    },

    mounted() {
      this.setKPI();
    }
  });
});
</script>

<section class="content" id="app">
  <div class="container-fluid pt-3 pl-md-2 pr-md-2 pl-md-5 pr-md-5">
    <div class="table-responsive">
      <h4 class="fw-300 mb-3">Department Marketing KPI</h4>

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
        <div class="col">
          <label>Date:</label>
          <!-- This controls the global date filter; affects ALL tables & summaries -->
          <input type="date" class="form-control" v-model="filters.date">
        </div>
      </div>

      <!-- Summary Chart -->
      <div class="card mb-4">
        <div class="card-body" style="height: 300px;">
          <canvas id="barChart"></canvas>
        </div>
      </div>

      <!-- Grouped Table -->
      <div class="table-responsive">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-2">Grouped by Person & Report</h5>
          <button class="btn btn-success mb-3" @click="exportGroupedToExcel">Export Summary (Excel)</button>
        </div>

        <!-- Detail Search Bar -->
        <div class="mb-2">
          <input type="text" class="form-control form-control-sm" v-model="detailSearch" placeholder="Search in details...">
        </div>

        <table class="table">
          <thead>
            <tr>
              <th>Performed By</th>
              <th>Report</th>
              <th>Count</th>
              <th>Target</th>
              <th>Balance</th>
              <th>Achieved %</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="(reports, person) in groupedData">
              <template v-for="(count, report, index) in reports">
                <!-- Summary Row -->
                <tr @click="toggleDetails(person, report)" style="cursor: pointer;">
                  <td v-if="index === 0" :rowspan="Object.keys(reports).length"><strong>{{ person }}</strong></td>
                  <td>{{ report }}</td>
                  <td>{{ count }}</td>
                  <td>{{ getTarget(person, report) }}</td>
                  <td>{{ getBalance(person, report) }}</td>
                  <td>{{ getPercentage(person, report) }}</td>
                </tr>

                <!-- Nested Detail Row -->
                <tr v-if="selectedRow.person === person && selectedRow.report === report">
                  <td colspan="6">
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
                            <td><a :href="item.link" target="_blank">{{ item.link }}</a></td>
                            <td>{{ item.language }}</td>
                            <td>{{ item.note }}</td>
                          </tr>
                        </tbody>
                      </table>

                      <!-- Pagination -->
                      <div class="d-flex justify-content-between align-items-center mt-2">
                        <button class="btn btn-sm btn-primary" @click="detailPage--" :disabled="detailPage === 1">Previous</button>
                        <span>Page {{ detailPage }} of {{ detailTotalPages() }}</span>
                        <button class="btn btn-sm btn-primary" @click="detailPage++" :disabled="detailPage === detailTotalPages()">Next</button>
                      </div>
                    </div>
                  </td>
                </tr>
              </template>
            </template>
          </tbody>
        </table>
      </div>

      <!-- Daily KPI by Team -->
      <div class="table-responsive mt-4">
        <h5>Daily KPI by Team</h5>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Team</th>
              <th>Date</th>
              <th>Total KPI</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="(dates, team) in dailyKPIByTeam" :key="team">
              <tr v-for="(count, date) in dates" :key="team + '-' + date">
                <td>{{ team }}</td>
                <td>{{ date }}</td>
                <td>{{ count }}</td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</section>
