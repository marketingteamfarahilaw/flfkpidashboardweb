<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 24px;
        margin-top: 40px;
    }
    .dashboard-card {
        background: #b4d4f5;
        border-radius: 8px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        display: flex;
        flex-direction: column;
    }
    .dashboard-card h5 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 16px;
    }
    .red {
        border-bottom: red 5px solid;
    }
    .orange {
        border-bottom: orange 5px solid;
    }
    .green {
        border-bottom: green 5px solid;
    }
</style>

<section class="content" id="app">
  <div class="container-fluid pt-3 pl-md-2 pr-md-2 pl-md-5 pr-md-5">

    <!-- Date Range Filter -->
    <div class="row mb-3">
      <div class="col-12 col-md-4">
        <label for="startDate">Start Date</label>
        <input type="date" class="form-control" v-model="startDate" @change="filterByDateRange">
      </div>
      <div class="col-12 col-md-4">
        <label for="endDate">End Date</label>
        <input type="date" class="form-control" v-model="endDate" @change="filterByDateRange">
      </div>
    </div>

    <div class="container text-center">
      <h5 class="fw-300 mb-3 text-center">DIGITAL MARKETING DEPARTMENT</h5>
      <h2 class="mb-3 text-center">MTD MARKETING PERFORMANCE SUMMARY</h2>
      <h4 class="text-center mb-5">{{ displayRange }}</h4>
    </div>
    
  </div>
</section>

<script>
$(document).ready(function () {
  new Vue({
    el: '#app',
        data: {
            
        },
        computed: {
            displayRange() {
                return this.startDate && this.endDate ? `${this.startDate} to ${this.endDate}` : '—';
            },
        },
        mounted() {
        const now = new Date();
        const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
        this.startDate = firstDay.toISOString().slice(0, 10);
        this.endDate = now.toISOString().slice(0, 10);

        fetch('http://31.97.43.196/kpidashboardapi/kpi/show', CONFIG.HEADER)
            .then(res => res.json())
            .then(data => {
                this.entries = data.response || [];
                this.filterByDateRange();
            });
        },
        methods: {
            filterByDateRange() {

            }
        }
    });
});
</script>


