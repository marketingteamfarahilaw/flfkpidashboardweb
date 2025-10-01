<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<!-- Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<section class="content" id="app">
  <div class="container-fluid pt-3 px-md-5">
    <div class="table-responsive">
      <h2 class="fw-300 mb-3">Campaign Performance</h2>

      <!-- Chart Grid -->
      <div class="row">
              <div class="col-md-3">
                    <div class="dashboard-card"><h5>KPI Summary</h5><canvas id="kpiChart"></canvas></div>
              </div>
              <div class="col-md-3">
                    <div class="dashboard-card"><h5>Campaign Status Distribution</h5><canvas id="pieStatus"></canvas></div>
              </div>
              <div class="col-md-">
                 <div class="dashboard-card"><h5>Top Campaigns by Opens</h5><canvas id="barTopOpens"></canvas></div>
              </div>
          <div class="col-md">
                <div class="dashboard-card"><h5>Engagement per Campaign</h5><canvas id="barEngagement"></canvas></div>
          </div>
          <div class="col-md">
                <div class="dashboard-card"><h5>Trends Over Time</h5><canvas id="kpiLineChart"></canvas></div>
          </div>
      </div>

      <!-- Filters -->
      <div class="my-4">
        <label>Month:
          <select v-model="filters.month"><option value="">All</option><option v-for="m in 12" :key="m" :value="m">{{ m }}</option></select>
        </label>
        <label>Year:
          <select v-model="filters.year"><option value="">All</option><option v-for="y in yearOptions" :key="y" :value="y">{{ y }}</option></select>
        </label>
        <label>Status:
          <select v-model="filters.status"><option value="">All</option><option v-for="s in uniqueStatuses" :key="s" :value="s">{{ s }}</option></select>
        </label>
        <button @click="exportToExcel">📤 Export Filtered Data</button>
      </div>

      <!-- Data Table -->
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Campaign ID</th><th>Name</th><th>Status</th><th>Opens</th><th>Clicks</th><th>Bounces</th><th>Unsubscribes</th><th>Created At</th>
          </tr>
        </thead>
        <tbody>
          <template v-for="(campaign, index) in filteredCampaigns">
            <tr @click="toggleDetails(index)" style="cursor:pointer;">
              <td>{{ campaign.campaign_id }}</td>
              <td>{{ campaign.name }}</td>
              <td>{{ statusLabels[campaign.status] || campaign.status }}</td>
              <td>{{ campaign.opens }}</td>
              <td>{{ campaign.clicks }}</td>
              <td>{{ campaign.bounces }}</td>
              <td>{{ campaign.unsubscribes }}</td>
              <td>{{ formatDate(campaign.created_at) }}</td>
            </tr>
            <tr v-if="selectedRow === index">
              <td colspan="8">
                <strong>Subject:</strong>
                <span v-if="emailReports[campaign.campaign_id]?.[0]?.campaign_name">
                  {{ emailReports[campaign.campaign_id][0].campaign_name }}
                </span>
                <span v-else>—</span>
                <br><br>
                <div v-if="loadingDetails">Loading details...</div>
                <div v-else>
                  <div v-if="emailReports[campaign.campaign_id] && emailReports[campaign.campaign_id].length">
                    <h6>Email Activity Logs</h6>
                    <table class="table table-sm table-bordered">
                      <thead>
                        <tr>
                          <th>Email</th><th>Contact ID</th><th>Event Type</th><th>Timestamp</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="entry in emailReports[campaign.campaign_id]" :key="entry.id">
                          <td>{{ entry.email }}</td>
                          <td>{{ entry.contact_id }}</td>
                          <td>{{ entry.event_type }}</td>
                          <td>{{ formatDate(entry.timestamp) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div v-else>No activity data found.</div>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>
</section>

<script>
new Vue({
  el: '#app',
  data: {
    campaigns: [],
    selectedRow: null,
    filters: { month: "", year: "", status: "" },
    kpiChartInstance: null,
    kpiLineChartInstance: null,
    barTopOpensInstance: null,
    barEngagementInstance: null,
    pieStatusInstance: null,
    emailReports: {},
    loadingDetails: false,
    statusLabels: { 5: 'Active', 0: 'Inactive', 1: 'Draft' }
  },
  computed: {
    filteredCampaigns() {
      return this.campaigns.filter(c => {
        const d = new Date(c.created_at);
        return (!this.filters.month || d.getMonth() + 1 === +this.filters.month) &&
               (!this.filters.year || d.getFullYear() === +this.filters.year) &&
               (!this.filters.status || c.status == this.filters.status);
      });
    },
    yearOptions() {
      return [...new Set(this.campaigns.map(c => new Date(c.created_at).getFullYear()))].sort();
    },
    uniqueStatuses() {
      return [...new Set(this.campaigns.map(c => c.status))];
    }
  },
  methods: {
    fetchData() {
      axios.get('http://31.97.43.196/kpidashboardapi/kpi/fetch_ac', CONFIG.HEADER)
        .then(res => {
          if (res.data.status) {
            this.campaigns = res.data.response.map(c => ({
              id: c.id,
              campaign_id: c.campaign_id,
              name: c.name || 'Unnamed',
              status: c.status,
              opens: +c.opens,
              clicks: +c.clicks,
              bounces: +c.bounces,
              unsubscribes: +c.unsubscribes,
              created_at: c.created_at && c.created_at !== '0000-00-00 00:00:00' ? c.created_at : null
            })).filter(c => c.created_at)
              .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            this.renderAllCharts();
          }
        });
    },
    toggleDetails(i) {
      const campaign = this.filteredCampaigns[i];
      const campaignId = campaign.campaign_id;

      if (this.selectedRow === i) {
        this.selectedRow = null;
        return;
      }

      this.selectedRow = i;

      if (!this.emailReports[campaignId]) {
        this.loadingDetails = true;
        axios.get(`http://31.97.43.196/kpidashboardapi/kpi/fetchemailreports?campaign_id=${campaignId}`, CONFIG.HEADER)
          .then(res => {
            if (res.data.status && Array.isArray(res.data.response)) {
              this.$set(this.emailReports, campaignId, res.data.response);
            } else {
              this.$set(this.emailReports, campaignId, []);
            }
          })
          .catch(() => {
            this.$set(this.emailReports, campaignId, []);
          })
          .finally(() => {
            this.loadingDetails = false;
          });
      }
    },
    formatDate(date) {
      const dt = new Date(date);
      return isNaN(dt) ? '—' : dt.toISOString().replace('T', ' ').split('.')[0];
    },
    exportToExcel() {
      const data = this.filteredCampaigns.map(c => ({
        ID: c.id,
        "Campaign ID": c.campaign_id,
        Name: c.name,
        Subject: this.emailReports[c.campaign_id]?.[0]?.campaign_name || '',
        Status: this.statusLabels[c.status] || c.status,
        Opens: c.opens,
        Clicks: c.clicks,
        Bounces: c.bounces,
        Unsubscribes: c.unsubscribes,
        "Created At": this.formatDate(c.created_at)
      }));
      const ws = XLSX.utils.json_to_sheet(data);
      const wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, ws, "Campaigns");
      XLSX.writeFile(wb, "Campaigns.xlsx");
    },
    renderAllCharts() {
      this.renderKPIChart();
      this.renderLineChart();
      this.renderTopOpensBar();
      this.renderStackedEngagement();
      this.renderPieStatus();
    },
    renderKPIChart() {
      const total = { opens: 0, clicks: 0, bounces: 0, unsubscribes: 0 };
      this.filteredCampaigns.forEach(c => {
        total.opens += c.opens; total.clicks += c.clicks;
        total.bounces += c.bounces; total.unsubscribes += c.unsubscribes;
      });
      if (this.kpiChartInstance) this.kpiChartInstance.destroy();
      this.kpiChartInstance = new Chart(document.getElementById('kpiChart'), {
        type: 'doughnut',
        data: {
          labels: ['Opens', 'Clicks', 'Bounces', 'Unsubscribes'],
          datasets: [{
            data: [total.opens, total.clicks, total.bounces, total.unsubscribes],
            backgroundColor: ['#36a2eb', '#4bc0c0', '#ff6384', '#ffcd56']
          }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
      });
    },
    renderLineChart() {
      const sorted = [...this.filteredCampaigns].sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
      const labels = sorted.map(c => this.formatDate(c.created_at));
      const metrics = ["opens", "clicks", "bounces", "unsubscribes"];
      if (this.kpiLineChartInstance) this.kpiLineChartInstance.destroy();
      this.kpiLineChartInstance = new Chart(document.getElementById('kpiLineChart'), {
        type: 'line',
        data: {
          labels,
          datasets: metrics.map((m, i) => ({
            label: m.charAt(0).toUpperCase() + m.slice(1),
            data: sorted.map(c => c[m]),
            borderColor: ['#36a2eb', '#4bc0c0', '#ff6384', '#ffcd56'][i],
            fill: false
          }))
        },
        options: {
          responsive: true,
          plugins: { legend: { position: 'bottom' } },
          scales: { y: { beginAtZero: true } }
        }
      });
    },
    renderTopOpensBar() {
      const top = [...this.filteredCampaigns].sort((a, b) => b.opens - a.opens).slice(0, 10);
      if (this.barTopOpensInstance) this.barTopOpensInstance.destroy();
      this.barTopOpensInstance = new Chart(document.getElementById('barTopOpens'), {
        type: 'bar',
        data: {
          labels: top.map(c => c.name),
          datasets: [{ label: 'Opens', data: top.map(c => c.opens), backgroundColor: '#36a2eb' }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
      });
    },
    renderStackedEngagement() {
      const c = this.filteredCampaigns;
      const labels = c.map(x => x.name);
      const types = ["opens", "clicks", "bounces", "unsubscribes"];
      const colors = ['#36a2eb', '#4bc0c0', '#ff6384', '#ffcd56'];
      if (this.barEngagementInstance) this.barEngagementInstance.destroy();
      this.barEngagementInstance = new Chart(document.getElementById('barEngagement'), {
        type: 'bar',
        data: {
          labels,
          datasets: types.map((t, i) => ({
            label: t.charAt(0).toUpperCase() + t.slice(1),
            data: c.map(x => x[t]),
            backgroundColor: colors[i]
          }))
        },
        options: {
          responsive: true,
          plugins: { legend: { position: 'bottom' } },
          scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }
        }
      });
    },
    renderPieStatus() {
      const statusCount = {};
      this.filteredCampaigns.forEach(c => statusCount[c.status] = (statusCount[c.status] || 0) + 1);
      if (this.pieStatusInstance) this.pieStatusInstance.destroy();
      this.pieStatusInstance = new Chart(document.getElementById('pieStatus'), {
        type: 'pie',
        data: {
          labels: Object.keys(statusCount).map(s => this.statusLabels[s] || s),
          datasets: [{
            data: Object.values(statusCount),
            backgroundColor: ['#36a2eb', '#4bc0c0', '#ff6384', '#ffcd56', '#9966ff']
          }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
      });
    }
  },
  watch: {
    filters: {
      handler() { this.renderAllCharts(); },
      deep: true
    }
  },
  mounted() {
    this.fetchData();
  }
});
</script>
