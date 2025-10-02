<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<!-- Chart & Vue Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<!-- Flatpickr Calendar -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
let engagementChart, pageviewsChart, sessionDurationChart;

$(document).ready(function () {
  new Vue({
    el: '#app',
    data: {
      gadata: [],
      rawData: [],
      loading: true,
      error: null,
      dateRange: {
        from: null,
        to: null
      }
    },
    computed: {
      cards() {
        if (!this.gadata.length) return [];
        const total = (field) => this.gadata.reduce((sum, item) => sum + parseFloat(item[field] || 0), 0);
        const avg = (field) => (total(field) / this.gadata.length).toFixed(2);
        return [
          { title: 'Total Pageviews', value: this.formatNumber(total('screen_pageviews')) },
          { title: 'Engaged Sessions', value: this.formatNumber(total('engaged_sessions')) },
          { title: 'Total Users', value: this.formatNumber(total('total_users')) },
          { title: 'Avg. Engagement Duration (sec)', value: avg('engagement_duration') },
          { title: 'Avg. ESP/User', value: avg('esp_user') }
        ];
      }
    },
    methods: {
      formatNumber(num) {
        return Number(num).toLocaleString();
      },
      async fetchData() {
        try {
          this.loading = true;
          const res = await fetch("http://31.97.43.196/kpidashboardapi/kpi/fetchga", CONFIG.HEADER);
          const json = await res.json();
          if (json.status) {
            let cleanedData = json.response.map(item => ({
              ...item,
              screen_pageviews: parseInt(item.screen_pageviews || 0),
              engaged_sessions: parseInt(item.engaged_sessions || 0),
              engagement_duration: parseInt(item.user_engagement_duration || 0),
              avg_session_duration: parseFloat(item.avg_session_duration || 0),
              total_users: parseInt(item.total_users || 0),
              esp_user: parseFloat(item.engaged_sessions_per_user || 0),
              fetched_at: item.fetched_at,
              report_month: item.report_month
            }));
            this.rawData = cleanedData;
            this.applyFilter();
          } else {
            this.error = 'Failed to load data';
          }
        } catch (err) {
          console.error(err);
          this.error = 'An error occurred while fetching data';
        } finally {
          this.loading = false;
        }
      },
      applyFilter() {
        if (!this.dateRange.from || !this.dateRange.to) return;
        const from = new Date(this.dateRange.from);
        const to = new Date(this.dateRange.to);
        to.setHours(23, 59, 59);

        const filtered = this.rawData.filter(item => {
          const created = new Date(item.fetched_at);
          return created >= from && created <= to;
        });

        const uniquePages = {};
        filtered.forEach(item => {
          if (!item.page_title || item.page_title.toLowerCase() === '(not set)') return;
          if (!uniquePages[item.page_title]) {
            uniquePages[item.page_title] = { ...item };
          } else {
            const existing = uniquePages[item.page_title];
            existing.screen_pageviews += item.screen_pageviews;
            existing.engaged_sessions += item.engaged_sessions;
            existing.engagement_duration += item.engagement_duration;
            existing.total_users += item.total_users;
            existing.esp_user += item.esp_user;
            existing.avg_session_duration = (existing.avg_session_duration + item.avg_session_duration) / 2;
          }
        });

        this.gadata = Object.values(uniquePages)
          .sort((a, b) => b.screen_pageviews - a.screen_pageviews)
          .slice(0, 10);

        this.renderCharts();
      },
      renderCharts() {
        const labels = this.gadata.map(item => item.page_title);
        const pageviews = this.gadata.map(item => item.screen_pageviews);
        const engagedSessions = this.gadata.map(item => item.engaged_sessions);
        const engagementDuration = this.gadata.map(item => item.engagement_duration);
        const totalUsers = this.gadata.map(item => item.total_users);
        const espUser = this.gadata.map(item => item.esp_user);
        const avgSessionDurations = this.gadata.map(item => item.avg_session_duration);

        if (engagementChart) engagementChart.destroy();
        if (pageviewsChart) pageviewsChart.destroy();
        if (sessionDurationChart) sessionDurationChart.destroy();

        engagementChart = new Chart(document.getElementById('engagementOverviewChart'), {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [
              { label: 'Pageviews', data: pageviews, backgroundColor: 'rgba(54, 162, 235, 0.6)' },
              { label: 'Engaged Sessions', data: engagedSessions, backgroundColor: 'rgba(255, 206, 86, 0.6)' },
              { label: 'Engagement Duration (sec)', data: engagementDuration, backgroundColor: 'rgba(75, 192, 192, 0.6)' },
              { label: 'Total Users', data: totalUsers, backgroundColor: 'rgba(153, 102, 255, 0.6)' },
              { label: 'ESP/User', data: espUser, backgroundColor: 'rgba(255, 99, 132, 0.6)' }
            ]
          },
          options: {
            responsive: true,
            scales: {
              x: { stacked: false },
              y: { beginAtZero: true, title: { display: true, text: 'Metric Values' } }
            },
            plugins: {
              legend: { position: 'top' },
              tooltip: { mode: 'index', intersect: false }
            }
          }
        });

        pageviewsChart = new Chart(document.getElementById('pageviewsChart'), {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [
              { label: 'Screen Pageviews', data: pageviews, backgroundColor: 'rgba(54, 162, 235, 0.6)', borderColor: 'rgba(54, 162, 235, 1)', borderWidth: 1 }
            ]
          },
          options: {
            responsive: true,
            indexAxis: 'y',
            scales: { x: { beginAtZero: true } }
          }
        });

        sessionDurationChart = new Chart(document.getElementById('sessionDurationChart'), {
          type: 'line',
          data: {
            labels: labels,
            datasets: [
              { label: 'Avg. Session Duration (seconds)', data: avgSessionDurations, borderColor: 'rgba(255, 99, 132, 1)', backgroundColor: 'rgba(255, 99, 132, 0.2)', tension: 0.3, fill: true }
            ]
          },
          options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
          }
        });
      }
    },
    mounted() {
      this.fetchData();
      flatpickr("#dateRangePicker", {
        mode: "range",
        dateFormat: "Y-m-d",
        onChange: (selectedDates) => {
          if (selectedDates.length === 2) {
            this.dateRange.from = selectedDates[0];
            this.dateRange.to = selectedDates[1];
            this.applyFilter();
          }
        }
      });
    }
  });
});
</script>

<style>
.card {
  border-radius: 0.5rem;
  border-left: 0.25rem solid #4e73df;
  background-color: #fff;
}
.font-weight-bold { font-weight: 600; }
.text-uppercase { text-transform: uppercase; font-size: 0.75rem; }
.text-gray-800 { color: #5a5c69; }
</style>

<section class="content" id="app">
  <div class="container-fluid pt-3 pl-md-2 pr-md-2 pl-md-5 pr-md-5">
    <div class="mb-4">
      <h2>Google Analytics Engagement</h2>
      <div v-if="loading">Loading data...</div>
      <div v-if="error" class="alert alert-danger">{{ error }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-4">
        <label for="dateRangePicker">Select Date Range:</label>
        <input type="text" id="dateRangePicker" class="form-control" placeholder="Pick a date range">
      </div>
    </div>

    <div class="row mb-4" v-if="gadata.length">
      <div class="col-md-2 col-sm-6 mb-3" v-for="card in cards" :key="card.title">
        <div class="card shadow-sm border-left-primary h-100 py-2 px-3">
          <div class="card-body p-0">
            <div class="text-xs font-weight-bold text-uppercase mb-1">{{ card.title }}</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ card.value }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 mb-5">
        <h5 class="mb-3">Top 10 Pages – Engagement Overview</h5>
        <canvas id="engagementOverviewChart" height="400"></canvas>
      </div>

      <div class="col-md-6 mb-5">
        <h5 class="mb-3">Top 10 Pages by Pageviews</h5>
        <canvas id="pageviewsChart" height="300"></canvas>
      </div>

      <div class="col-md-12 mb-5">
        <h5 class="mb-3">Avg. Session Duration per Page</h5>
        <canvas id="sessionDurationChart" height="300"></canvas>
      </div>
    </div>
  </div>
</section>
