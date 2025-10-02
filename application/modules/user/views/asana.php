<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
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
        <!-- NEW: Date Range -->
        <div class="col">
          <label>Date Start</label>
          <input type="date" class="form-control" v-model="filters.startDate">
        </div>
        <div class="col">
          <label>Date End</label>
          <input type="date" class="form-control" v-model="filters.endDate">
        </div>
        <div class="col d-flex align-items-end">
          <div>
            <button class="btn btn-outline-secondary mr-2" @click="clearDateRange">Clear</button>
            <button class="btn btn-outline-primary" @click="setMTD()">MTD</button>
          </div>
        </div>

        <!-- Existing dropdown filters -->
        <div class="col" v-for="(label, key) in filterLabels" :key="key">
          <label>{{ label }}</label>
          <select class="form-control" v-model="filters[key]">
            <option value="">All</option>
            <option v-for="val in filterOptions[key]" :key="val" :value="val">{{ val }}</option>
          </select>
        </div>
      </div>

      <!-- Export Button -->
      <button class="btn btn-success mb-3" @click="exportToExcel">Export to Excel</button>

      <!-- Existing Charts -->
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

      <!-- NEW: Two bar charts like the screenshot -->
      <div class="row">
        <div class="col-md-6 mb-4">
          <h6>Total completed tasks by assignee</h6>
          <canvas id="completedByAssignee"></canvas>
        </div>
        <div class="col-md-6 mb-4">
          <h6>Total incomplete tasks by assignee</h6>
          <canvas id="incompleteByAssignee"></canvas>
        </div>
      </div>

      <!-- Interactive Tooltip Table -->
      <table class="table table-hover table-bordered table-sm mt-4">
        <thead class="thead-light">
          <tr>
            <th @click="sortBy('performed_by')" style="cursor:pointer">Performed By</th>
            <th @click="sortBy('count')" style="cursor:pointer">Task Count</th>
            <th>Toggle View</th>
          </tr>
        </thead>
        <tbody>
          <template v-for="(group, performer) in groupedTasks" :key="performer">
            <tr>
              <td>{{ performer }}</td>
              <td>{{ group.length }}</td>
              <td><button class="btn btn-link p-0" @click="toggleDetail(performer)">Toggle</button></td>
            </tr>
            <template v-if="expanded[performer]">
              <tr v-for="task in group" :key="task.id" @mouseover="hoverTask = task.id" @mouseleave="hoverTask = null">
                <td colspan="3">
                  <div>
                    <strong>{{ task.title }}</strong>
                    <span v-if="hoverTask === task.id" class="text-muted float-right">{{ task.parent_name }}</span>
                  </div>
                  <small>Due: {{ task.due_on }} | Completed: {{ task.completed_at || '—' }}</small><br>
                  <a :href="task.permalink_url" target="_blank" rel="noopener">View Task</a>
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
        // NEW date range fields
        startDate: '',
        endDate: '',

        // existing dropdown filters
        dueMonth: '', dueYear: '',
        completedMonth: '', completedYear: '',
        performedBy: ''
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
      // NEW instances
      completedBarInstance: null,
      incompleteBarInstance: null,

      hoverTask: null,
      sortKey: '',
      sortAsc: true
    },
    computed: {
      filteredTasks() {
        const s = this.filters.startDate;
        const e = this.filters.endDate;

        return this.tasks
          .filter(task => {
            const due = task.due_on ? new Date(task.due_on) : null;
            const completed = task.completed_at ? new Date(task.completed_at) : null;

            // Date-range logic (inclusive). If completed_at exists, use it; else use due_on.
            let dateOk = true;
            if (s || e) {
              if (completed) {
                dateOk = this.inRange(completed, s, e);
              } else if (due) {
                dateOk = this.inRange(due, s, e);
              } else {
                dateOk = false;
              }
            }

            // Existing dropdown filters
            const dueMonthOk = !this.filters.dueMonth || (this.isValidDate(due) && this.filters.dueMonth === ('0' + (due.getMonth() + 1)).slice(-2));
            const dueYearOk  = !this.filters.dueYear  || (this.isValidDate(due) && String(this.filters.dueYear) == String(due.getFullYear()));
            const compMonthOk = !this.filters.completedMonth || (this.isValidDate(completed) && this.filters.completedMonth === ('0' + (completed.getMonth() + 1)).slice(-2));
            const compYearOk  = !this.filters.completedYear  || (this.isValidDate(completed) && String(this.filters.completedYear) == String(completed.getFullYear()));
            const performerOk = !this.filters.performedBy || task.performed_by === this.filters.performedBy;

            return dateOk && dueMonthOk && dueYearOk && compMonthOk && compYearOk && performerOk;
          })
          .sort((a, b) => {
            const aT = a.completed_at ? Date.parse(a.completed_at) : -Infinity;
            const bT = b.completed_at ? Date.parse(b.completed_at) : -Infinity;
            return bT - aT;
          });
      },
      groupedTasks() {
        const base = this.filteredTasks.reduce((acc, task) => {
          const key = task.performed_by || 'Unassigned';
          if (!acc[key]) acc[key] = [];
          acc[key].push(task);
          return acc;
        }, {});
        if (!this.sortKey) return base;
        return Object.fromEntries(
          Object.entries(base).sort(([aKey, a], [bKey, b]) => {
            const aVal = this.sortKey === 'performed_by' ? aKey : a.length;
            const bVal = this.sortKey === 'performed_by' ? bKey : b.length;
            if (aVal === bVal) return String(aKey).localeCompare(String(bKey));
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
      // ----- NEW helpers -----
      // Return YYYY-MM-DD
      fmt(d) {
        const y = d.getFullYear();
        const m = ('0' + (d.getMonth() + 1)).slice(-2);
        const day = ('0' + d.getDate()).slice(-2);
        return `${y}-${m}-${day}`;
      },
      setMTD() {
        const today = new Date();
        const first = new Date(today.getFullYear(), today.getMonth(), 1);
        this.filters.startDate = this.fmt(first);
        this.filters.endDate   = this.fmt(today);
      },
      clearDateRange() {
        this.filters.startDate = '';
        this.filters.endDate = '';
      },
      isValidDate(d) {
        return d instanceof Date && !isNaN(d);
      },
      // Inclusive range: [start, end]
      inRange(dateObj, startStr, endStr) {
        if (!this.isValidDate(dateObj)) return false;
        if (!startStr && !endStr) return true;
        const t = dateObj.getTime();
        const s = startStr ? new Date(startStr + 'T00:00:00').getTime() : -Infinity;
        const e = endStr   ? new Date(endStr   + 'T23:59:59').getTime() : Infinity;
        return t >= s && t <= e;
      },
      // -----------------------

      sortBy(key) {
        if (this.sortKey === key) this.sortAsc = !this.sortAsc;
        else { this.sortKey = key; this.sortAsc = true; }
      },
      toggleDetail(performer) {
        this.$set(this.expanded, performer, !this.expanded[performer]);
      },
      async fetchData() {
        const res = await fetch("http://31.97.43.196/kpidashboardapi/kpi/getGraphicsTeam", CONFIG.HEADER);
        const json = await res.json();
        if (json.status) {
          this.tasks = json.response;

          // Build year options safely and sorted
          const yearSet = new Set();
          this.tasks.forEach(t => {
            const dy = Date.parse(t.due_on);
            const cy = t.completed_at ? Date.parse(t.completed_at) : NaN;
            if (!isNaN(dy)) yearSet.add(new Date(dy).getFullYear());
            if (!isNaN(cy)) yearSet.add(new Date(cy).getFullYear());
          });
          const years = Array.from(yearSet).sort((a,b)=>a-b);

          // Performer options
          const performers = Array.from(new Set(this.tasks.map(t => t.performed_by || 'Unassigned'))).sort();

          this.filterOptions.dueYear = years;
          this.filterOptions.completedYear = years;
          this.filterOptions.performedBy = performers;

          // Default to current MTD on first load
          if (!this.filters.startDate && !this.filters.endDate) {
            this.setMTD();
          }

          this.renderChart();
        }
      },
      exportToExcel() {
        const data = this.filteredTasks.map(task => ({
          Title: task.title,
          'Due Date': task.due_on,
          'Completed At': task.completed_at || '',
          'Performed By': task.performed_by || 'Unassigned',
          'Parent Name': task.parent_name || '',
          URL: task.permalink_url
        }));
        const worksheet = XLSX.utils.json_to_sheet(data);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Filtered Tasks");
        XLSX.writeFile(workbook, "Graphics_Team_Tasks.xlsx");
      },
      renderChart() {
        const filtered = this.filteredTasks;

        // ===== Doughnut: tasks per performer (completed+incomplete) =====
        const counts = filtered.reduce((acc, t) => {
          const k = t.performed_by || 'Unassigned';
          acc[k] = (acc[k] || 0) + 1;
          return acc;
        }, {});
        const ctx1 = document.getElementById('performedByChart').getContext('2d');
        if (this.chartInstance) this.chartInstance.destroy();
        this.chartInstance = new Chart(ctx1, {
          type: 'doughnut',
          data: {
            labels: Object.keys(counts),
            datasets: [{
              data: Object.values(counts),
              backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#66bb6a', '#9575cd', '#f06292', '#4dd0e1', '#b39ddb']
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { position: 'right' }, title: { display: true, text: 'Tasks Completed by Performer' } }
          }
        });

        // ===== Line: tasks completed over time =====
        const dateMap = {};
        filtered.forEach(task => {
          if (task.completed_at) {
            const date = task.completed_at.slice(0, 10);
            dateMap[date] = (dateMap[date] || 0) + 1;
          }
        });
        const ctx2 = document.getElementById('lineChart').getContext('2d');
        if (this.lineChartInstance) this.lineChartInstance.destroy();
        const sortedDates = Object.keys(dateMap).sort();
        this.lineChartInstance = new Chart(ctx2, {
          type: 'line',
          data: {
            labels: sortedDates,
            datasets: [{ label: 'Tasks Completed', data: sortedDates.map(d => dateMap[d]), borderColor: '#36A2EB', tension: 0.1 }]
          },
          options: {
            responsive: true,
            scales: {
              x: { title: { display: true, text: 'Date' } },
              y: { title: { display: true, text: 'Tasks Completed' }, beginAtZero: true }
            }
          }
        });

        // ===== Stacked bar: tasks due per month by performer =====
        const performerMonthMap = {};
        filtered.forEach(task => {
          const month = (task.due_on || '').slice(0, 7);
          if (!month) return;
          const person = task.performed_by || 'Unassigned';
          if (!performerMonthMap[month]) performerMonthMap[month] = {};
          performerMonthMap[month][person] = (performerMonthMap[month][person] || 0) + 1;
        });
        const ctx3 = document.getElementById('stackedBarChart').getContext('2d');
        if (this.stackedBarChartInstance) this.stackedBarChartInstance.destroy();
        const allMonths = Object.keys(performerMonthMap).sort();
        const allPerformers = Object.keys(counts);
        const colorPool = ['#FF6384', '#36A2EB', '#FFCE56', '#66bb6a', '#9575cd', '#f06292', '#4dd0e1', '#b39ddb', '#ffab91', '#80cbc4'];
        const datasets = allPerformers.map((p, i) => ({
          label: p,
          backgroundColor: colorPool[i % colorPool.length],
          data: allMonths.map(month => performerMonthMap[month]?.[p] || 0),
          stack: 'stack1'
        }));
        this.stackedBarChartInstance = new Chart(ctx3, {
          type: 'bar',
          data: { labels: allMonths, datasets },
          options: {
            responsive: true,
            plugins: { title: { display: true, text: 'Tasks Due Per Month by Performer' }, legend: { position: 'bottom' } },
            scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }
          }
        });

        // ===== NEW: Completed vs Incomplete per assignee (two bar charts) =====
        const byAssignee = {};
        filtered.forEach(t => {
          const p = t.performed_by || 'Unassigned';
          if (!byAssignee[p]) byAssignee[p] = { complete: 0, incomplete: 0 };
          if (t.completed_at && String(t.completed_at).trim() !== '') byAssignee[p].complete++;
          else byAssignee[p].incomplete++;
        });

        const assignees = Object.keys(byAssignee)
          .sort((a, b) => {
            const db = byAssignee[b].complete, da = byAssignee[a].complete;
            if (db !== da) return db - da;
            return a.localeCompare(b);
          });

        const completedCounts  = assignees.map(a => byAssignee[a].complete);
        const incompleteCounts = assignees.map(a => byAssignee[a].incomplete);

        if (this.completedBarInstance)  this.completedBarInstance.destroy();
        if (this.incompleteBarInstance) this.incompleteBarInstance.destroy();

        const c1 = document.getElementById('completedByAssignee').getContext('2d');
        const c2 = document.getElementById('incompleteByAssignee').getContext('2d');

        const valueLabelPlugin = {
          id: 'valueLabelPlugin',
          afterDatasetsDraw(chart) {
            const { ctx } = chart;
            chart.data.datasets.forEach((dataset, i) => {
              const meta = chart.getDatasetMeta(i);
              meta.data.forEach((bar, index) => {
                const val = dataset.data[index];
                if (!val) return;
                ctx.save();
                ctx.font = '12px sans-serif';
                ctx.fillStyle = '#cfd8dc';
                ctx.textAlign = 'center';
                ctx.fillText(val, bar.x, bar.y - 6);
                ctx.restore();
              });
            });
          }
        };

        const commonBarOptions = (title) => ({
          responsive: true,
          plugins: {
            title: { display: true, text: title },
            legend: { display: false }
          },
          scales: {
            x: { grid: { display: false }, ticks: { autoSkip: false, maxRotation: 0 } },
            y: { beginAtZero: true, grid: { color:'#26323855' }, title: { display: true, text: 'Task (count, in numbers)' } }
          }
        });

        this.completedBarInstance = new Chart(c1, {
          type: 'bar',
          data: {
            labels: assignees,
            datasets: [{ label: 'Completed', data: completedCounts, backgroundColor: '#a78bfa' }]
          },
          options: commonBarOptions('Total completed tasks by assignee'),
          plugins: [valueLabelPlugin]
        });

        this.incompleteBarInstance = new Chart(c2, {
          type: 'bar',
          data: {
            labels: assignees,
            datasets: [{ label: 'Incomplete', data: incompleteCounts, backgroundColor: '#81d4fa' }]
          },
          options: commonBarOptions('Total incomplete tasks by assignee'),
          plugins: [valueLabelPlugin]
        });
      }
    },
    mounted() {
      this.fetchData();
    }
  });
});
</script>
