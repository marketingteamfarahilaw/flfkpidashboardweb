<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<section class="content" id="app">
  <div class="container-fluid pt-3 pl-md-2 pr-md-2 pl-md-5 pr-md-5">
    <div class="table-responsive">
      <h4 class="fw-300 mb-3">ASANA Task Overview</h4>

      <!-- Filters -->
      <div class="row mb-3">
        <div class="col" v-for="(label, key) in filterLabels" :key="key">
          <label>{{ label }}</label>
          <select class="form-control" v-model="filters[key]">
            <option value="">All</option>
            <option v-for="val in filterOptions[key]" :value="val">{{ val }}</option>
          </select>
        </div>
      </div>

      <!-- Export Button -->
      <button class="btn btn-success mb-3" @click="exportToExcel">Export to Excel</button>

      <!-- Charts -->
      <div class="row">
        <div class="col-md-4 mb-4">
          <h6>Tasks Performed By</h6>
          <canvas id="performedByChart" height="200"></canvas>
        </div>
        <div class="col-md-4 mb-4">
          <h6>Tasks Completed Over Time</h6>
          <canvas id="lineChart"></canvas>
        </div>
        <div class="col-md-4 mb-4">
          <h6>Tasks Due per Month by Performer</h6>
          <canvas id="stackedBarChart"></canvas>
        </div>
      </div>

      <!-- Interactive Tooltip Table -->
      <table class="table table-hover table-bordered table-sm mt-4">
        <thead class="thead-light">
          <tr>
            <th @click="sortBy('performed_by')">Performed By</th>
            <th @click="sortBy('count')">Task Count</th>
            <th>Toggle View</th>
          </tr>
        </thead>
        <tbody>
          <template v-for="(group, performer) in groupedTasks" :key="performer">
            <tr>
              <td>{{ performer }}</td>
              <td>{{ group.length }}</td>
              <td><button class="btn btn-link" @click="toggleDetail(performer)">Toggle</button></td>
            </tr>
            <template v-if="expanded[performer]">
              <tr v-for="task in group" :key="task.id" @mouseover="hoverTask = task.id" @mouseleave="hoverTask = null">
                <td colspan="3">
                  <div>
                    <strong>{{ task.title }}</strong>
                    <span v-if="hoverTask === task.id" class="text-muted float-right">{{ task.parent_name }}</span>
                  </div>
                  <small>Due: {{ task.due_on }} | Completed: {{ task.completed_at }}</small><br>
                  <a :href="task.permalink_url" target="_blank">View Task</a>
                </td>
              </tr>
            </template>
          </template>
        </tbody>
      </table>
    </div>
  </div>
</section>

<script>
$(document).ready(function () {
  new Vue({
    el: '#app',
    data: {
      tasks: [],
      filters: {
        dueMonth: '', dueYear: '', completedMonth: '', completedYear: '', performedBy: ''
      },
      filterLabels: {
        dueMonth: 'Due Month',
        dueYear: 'Due Year',
        completedMonth: 'Completed Month',
        completedYear: 'Completed Year',
        performedBy: 'Performed By'
      },
      filterOptions: {
        dueMonth: ["01","02","03","04","05","06","07","08","09","10","11","12"],
        dueYear: [],
        completedMonth: ["01","02","03","04","05","06","07","08","09","10","11","12"],
        completedYear: [],
        performedBy: []
      },
      expanded: {},
      chartInstance: null,
      lineChartInstance: null,
      stackedBarChartInstance: null,
      hoverTask: null,
      sortKey: '',
      sortAsc: true
    },
    computed: {
      filteredTasks() {
        return this.tasks
          .filter(task => {
            const due = new Date(task.due_on);
            const completed = new Date(task.completed_at);
            return (!this.filters.dueMonth || this.filters.dueMonth === ('0' + (due.getMonth() + 1)).slice(-2)) &&
                   (!this.filters.dueYear || this.filters.dueYear == due.getFullYear()) &&
                   (!this.filters.completedMonth || this.filters.completedMonth === ('0' + (completed.getMonth() + 1)).slice(-2)) &&
                   (!this.filters.completedYear || this.filters.completedYear == completed.getFullYear()) &&
                   (!this.filters.performedBy || task.performed_by === this.filters.performedBy);
          })
          .sort((a, b) => new Date(b.completed_at) - new Date(a.completed_at));
      },
      groupedTasks() {
        const base = this.filteredTasks.reduce((acc, task) => {
          if (!acc[task.performed_by]) acc[task.performed_by] = [];
          acc[task.performed_by].push(task);
          return acc;
        }, {});
        if (!this.sortKey) return base;
        return Object.fromEntries(
          Object.entries(base).sort(([aKey, a], [bKey, b]) => {
            const aVal = this.sortKey === 'performed_by' ? aKey : a.length;
            const bVal = this.sortKey === 'performed_by' ? bKey : b.length;
            return this.sortAsc ? (aVal > bVal ? 1 : -1) : (aVal < bVal ? 1 : -1);
          })
        );
      }
    },
    watch: {
      filteredTasks() {
        this.renderChart();
      }
    },
    methods: {
      sortBy(key) {
        if (this.sortKey === key) this.sortAsc = !this.sortAsc;
        else { this.sortKey = key; this.sortAsc = true; }
      },
      toggleDetail(performer) {
        this.$set(this.expanded, performer, !this.expanded[performer]);
      },
      async fetchData() {
        const res = await fetch("https://lmthrp.com/api/kpi/getGraphicsTeam", CONFIG.HEADER);
        const json = await res.json();
        if (json.status) {
          this.tasks = json.response;
          const years = [...new Set(this.tasks.flatMap(task => [new Date(task.due_on).getFullYear(), new Date(task.completed_at).getFullYear()]))];
          const performers = [...new Set(this.tasks.map(task => task.performed_by))];
          this.filterOptions.dueYear = this.filterOptions.completedYear = years;
          this.filterOptions.performedBy = performers;
          this.renderChart();
        }
      },
      exportToExcel() {
        const data = this.filteredTasks.map(task => ({
          Title: task.title,
          'Due Date': task.due_on,
          'Completed At': task.completed_at,
          'Performed By': task.performed_by,
          'Parent Name': task.parent_name,
          URL: task.permalink_url
        }));
        const worksheet = XLSX.utils.json_to_sheet(data);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Filtered Tasks");
        XLSX.writeFile(workbook, "Graphics_Team_Tasks.xlsx");
      },
      renderChart() {
        const filtered = this.filteredTasks;
        const counts = filtered.reduce((acc, t) => {
          acc[t.performed_by] = (acc[t.performed_by] || 0) + 1;
          return acc;
        }, {});
        const ctx1 = document.getElementById('performedByChart').getContext('2d');
        const ctx2 = document.getElementById('lineChart').getContext('2d');
        const ctx3 = document.getElementById('stackedBarChart').getContext('2d');
        if (this.chartInstance) this.chartInstance.destroy();
        if (this.lineChartInstance) this.lineChartInstance.destroy();
        if (this.stackedBarChartInstance) this.stackedBarChartInstance.destroy();
        this.chartInstance = new Chart(ctx1, {
          type: 'doughnut',
          data: {
            labels: Object.keys(counts),
            datasets: [{ data: Object.values(counts), backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#66bb6a', '#9575cd', '#f06292'] }]
          },
          options: { responsive: true, plugins: { legend: { position: 'right' }, title: { display: true, text: 'Tasks Completed by Performer' } } }
        });
        const dateMap = {};
        filtered.forEach(task => { const date = task.completed_at.slice(0, 10); dateMap[date] = (dateMap[date] || 0) + 1; });
        const sortedDates = Object.keys(dateMap).sort();
        this.lineChartInstance = new Chart(ctx2, {
          type: 'line',
          data: { labels: sortedDates, datasets: [{ label: 'Tasks Completed', data: sortedDates.map(date => dateMap[date]), borderColor: '#36A2EB', tension: 0.1 }] },
          options: { responsive: true, scales: { x: { title: { display: true, text: 'Date' } }, y: { title: { display: true, text: 'Tasks Completed' } } } }
        });
        const performerMonthMap = {};
        filtered.forEach(task => {
          const month = task.due_on.slice(0, 7);
          const person = task.performed_by;
          if (!performerMonthMap[month]) performerMonthMap[month] = {};
          performerMonthMap[month][person] = (performerMonthMap[month][person] || 0) + 1;
        });
        const allMonths = Object.keys(performerMonthMap).sort();
        const allPerformers = Object.keys(counts);
        const datasets = allPerformers.map((p, i) => ({
          label: p,
          backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#66bb6a', '#9575cd', '#f06292'][i % 6],
          data: allMonths.map(month => performerMonthMap[month]?.[p] || 0),
          stack: 'stack1'
        }));
        this.stackedBarChartInstance = new Chart(ctx3, {
          type: 'bar',
          data: { labels: allMonths, datasets },
          options: { responsive: true, plugins: { title: { display: true, text: 'Tasks Due Per Month by Performer' }, legend: { position: 'bottom' } }, scales: { x: { stacked: true }, y: { stacked: true } } }
        });
      }
    },
    mounted() {
      this.fetchData();
    }
  });
});
</script>
