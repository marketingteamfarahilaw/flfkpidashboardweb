<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<!-- Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<div id="dashboard" class="container-fluid py-4">
  <h4 class="mb-4 font-weight-bold">Marketing & Performance Dashboard</h4>
  <div class="row">
    <div class="col-md-4 mb-4" v-for="card in cards" :key="card.id">
      <div class="card shadow-sm h-100 border-light">
        <div class="card-body">
          <small class="text-muted">{{ card.subtitle }}</small>
          <div class="d-flex justify-content-between align-items-baseline">
            <h5 class="card-title font-weight-bold">{{ card.title }}</h5>
            <h6 class="text-success">{{ card.metric }}</h6>
          </div>
          <canvas :id="card.canvasId" height="100"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
new Vue({
  el: '#dashboard',
  data: {
    cards: [
      { id: 1, title: 'Campaign Reach', metric: '12.1k', canvasId: 'chart1', subtitle: 'Last 30 Days' },
      { id: 2, title: 'Leads Captured', metric: '3.2k', canvasId: 'chart2', subtitle: 'By Platform' },
      { id: 3, title: 'Bounce Rate', metric: '27%', canvasId: 'chart3', subtitle: 'Weekly Average' },
      { id: 4, title: 'CTR Trend', metric: '5.3%', canvasId: 'chart4', subtitle: 'Monthly Trend' },
      { id: 5, title: 'GBP Views', metric: '21.4k', canvasId: 'chart5', subtitle: 'Google Business Profile' },
      { id: 6, title: 'Conversion Funnel', metric: '', canvasId: 'chart6', subtitle: 'Campaign Flow' }
    ]
  },
  mounted() {
    this.initCharts();
  },
  methods: {
    initCharts() {
      // Chart 1: Stacked Bar
      new Chart(document.getElementById('chart1'), {
        type: 'bar',
        data: {
          labels: ['W1', 'W2', 'W3', 'W4'],
          datasets: [
            { label: 'FB Ads', data: [3000, 3200, 3100, 3700], backgroundColor: '#36a2eb' },
            { label: 'Google Ads', data: [2500, 2700, 2600, 2900], backgroundColor: '#4bc0c0' }
          ]
        },
        options: {
          responsive: true,
          plugins: { legend: { position: 'bottom' } },
          scales: {
            y: { beginAtZero: true }
          }
        }
      });

      // Chart 2: Horizontal Bar
      new Chart(document.getElementById('chart2'), {
        type: 'bar',
        data: {
          labels: ['FB', 'Google', 'Instagram', 'Tiktok'],
          datasets: [{
            data: [1000, 900, 800, 500],
            backgroundColor: ['#36a2eb', '#4bc0c0', '#ffcd56', '#ff6384']
          }]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          plugins: { legend: { display: false } },
          scales: {
            x: { beginAtZero: true }
          }
        }
      });

      // Chart 3: Line
      new Chart(document.getElementById('chart3'), {
        type: 'line',
        data: {
          labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
          datasets: [{
            label: 'Bounce Rate',
            data: [25, 28, 26, 30, 27],
            borderColor: '#ff6384',
            fill: false
          }]
        },
        options: {
          responsive: true,
          plugins: { legend: { display: false } },
          scales: {
            y: { beginAtZero: true }
          }
        }
      });

      // Chart 4: Bar
      new Chart(document.getElementById('chart4'), {
        type: 'bar',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
          datasets: [{
            label: 'CTR',
            data: [4.1, 4.5, 5.0, 5.2, 5.3],
            backgroundColor: '#ff9f40'
          }]
        },
        options: {
          responsive: true,
          plugins: { legend: { display: false } },
          scales: {
            y: { beginAtZero: true }
          }
        }
      });

      // Chart 5: Area (Line + Fill)
      new Chart(document.getElementById('chart5'), {
        type: 'line',
        data: {
          labels: ['W1', 'W2', 'W3', 'W4'],
          datasets: [{
            label: 'GBP Views',
            data: [5000, 5200, 6000, 7200],
            borderColor: '#36a2eb',
            backgroundColor: 'rgba(54, 162, 235, 0.3)',
            fill: true
          }]
        },
        options: {
          responsive: true,
          plugins: { legend: { display: false } },
          scales: {
            y: { beginAtZero: true }
          }
        }
      });

      // Chart 6: Funnel (simulated with horizontal bar)
      new Chart(document.getElementById('chart6'), {
        type: 'bar',
        data: {
          labels: ['Reached', 'Clicked', 'Signed Up'],
          datasets: [{
            data: [1000, 500, 120],
            backgroundColor: ['#4bc0c0', '#ffcd56', '#ff6384']
          }]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          plugins: { legend: { display: false } },
          scales: {
            x: { beginAtZero: true }
          }
        }
      });
    }
  }
});
</script>
