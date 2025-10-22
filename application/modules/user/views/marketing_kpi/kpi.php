<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<!-- Libraries -->
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios@1.6.7/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<style>
  :root{
    --ink:#0b2239; --muted:#607489; --card:#f6f7f9; --line:#e8edf3;
    --c1:#071e37; --c2:#144468; --c3:#1b5f8a; --c4:#217ba9; --c5:#695c4a;
    --c6:#a38776; --c7:#c2aa91; --c8:#b58f44; --c9:#d8ac54; --c10:#e3cf91;
  }
  .container-fluid{padding:24px}
  .panel{background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:0 1px 2px rgba(0,0,0,.04)}
  .panel-header{padding:18px 20px;border-bottom:1px solid var(--line)}
  .panel-title{margin:0;font-weight:800;letter-spacing:.02em;color:var(--ink)}
  .panel-body{padding:20px}
  .grid{display:grid;gap:18px}
  .grid-2{grid-template-columns: 2fr 1.2fr}
  .legend{display:grid;grid-template-columns: 16px 1fr;gap:10px 12px;font-size:12px;color:var(--ink)}
  .legend > i{width:16px;height:16px;border-radius:4px;display:inline-block}
  .muted{color:var(--muted);font-size:12px;line-height:1.4}
  .kpi-bubble{font-size:56px;font-weight:900;color:#1b2a41;margin:0}
  .subkpi{font-weight:800;font-size:20px;color:#1b2a41;margin:.5rem 0 0}
  .note{font-size:12px;color:var(--muted)}
  .chart-box{height:320px}
  .divider{height:1px;background:var(--line);margin:10px 0 18px}
  .table{width:100%}
  .table th{font-size:12px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);border-bottom:1px solid var(--line)}
  .table td{border-bottom:1px solid var(--line);font-size:14px;color:#223}
  .btn{border:0;border-radius:10px;padding:8px 12px;font-weight:600;cursor:pointer}
  .btn-primary{background:#0b2239;color:#fff}
  .btn-ghost{background:#fff;border:1px solid var(--line);color:#0b2239}
  .pill{display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border:1px solid var(--line);border-radius:999px;background:#fff}
  .stack-legend{display:flex;gap:14px;align-items:center}
  .stack-legend span{display:inline-flex;align-items:center;gap:8px;font-size:12px;color:var(--muted)}
  .swatch{width:14px;height:14px;border-radius:3px;display:inline-block}
  .form-control{width:100%;padding:8px 10px;border:1px solid var(--line);border-radius:10px}
  .row{display:flex;gap:12px;flex-wrap:wrap}
  .col{flex:1 1 150px}
  @media (max-width:1024px){ .grid-2{grid-template-columns:1fr} }
</style>

<section class="content" id="app">
  <div class="container-fluid">

    <h3 class="section-title">Department Marketing KPI</h3>

    <!-- FILTERS -->
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
        <input type="date" class="form-control" v-model="filters.date">
      </div>
      <div class="col">
        <label>Department:</label>
        <select class="form-control" v-model="filters.department">
          <option value="">All</option>
          <option v-for="dept in uniqueOptions.departments" :key="dept" :value="dept">{{ dept }}</option>
        </select>
      </div>
    </div>

    <!-- TOP: DONUT + KPI PANEL -->
    <div class="panel grid grid-2">
      <div class="panel-body">
        <h4 class="panel-title">Tasks Performed by (Individual)</h4>
        <div class="grid" style="grid-template-columns: 220px 1fr;">
          <!-- Legend -->
          <div>
            <div class="muted" style="margin:6px 0 10px">Digital Marketing Members</div>
            <div class="legend">
              <i style="background:var(--c1)"></i><span>071e37</span>
              <i style="background:var(--c2)"></i><span>144468</span>
              <i style="background:var(--c3)"></i><span>1b5f8a</span>
              <i style="background:var(--c4)"></i><span>217ba9</span>
              <i style="background:var(--c5)"></i><span>695c4a</span>
              <i style="background:var(--c6)"></i><span>a38776</span>
              <i style="background:var(--c7)"></i><span>c2aa91</span>
              <i style="background:var(--c8)"></i><span>b58f44</span>
              <i style="background:var(--c9)"></i><span>d8ac54</span>
              <i style="background:var(--c10)"></i><span>e3cf91</span>
            </div>
          </div>

          <!-- Donut -->
          <div class="chart-box">
            <canvas id="donutChart"></canvas>
          </div>
        </div>
      </div>

      <!-- KPI right panel -->
      <div class="panel-body" style="display:flex;flex-direction:column;justify-content:center">
        <div class="muted">Results As To Date (ALL)</div>
        <p class="kpi-bubble">{{ overallCompletion.achieved }}%</p>
        <p class="note" style="max-width:34ch">
          Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
        </p>
        <p class="subkpi">Target/Goal (ALL)</p>
        <p class="kpi-bubble" style="font-size:40px">{{ overallCompletion.remaining }}%</p>
        <p class="note" style="max-width:34ch">
          Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
        </p>
      </div>
    </div>

    <!-- MIDDLE: STACKED BAR -->
    <div class="panel" style="margin-top:18px">
      <div class="panel-header">
        <h4 class="panel-title">Total Complete vs Incomplete Tasks (ALL)</h4>
      </div>
      <div class="panel-body">
        <div class="chart-box">
          <canvas id="statusBarChart"></canvas>
        </div>
        <div class="divider"></div>
        <div class="stack-legend">
          <span><i class="swatch" style="background:#0b2239"></i> COMPLETE</span>
          <span><i class="swatch" style="background:#1b5f8a"></i> PROGRESS</span>
          <span><i class="swatch" style="background:#6ec1e4"></i> INCOMPLETE</span>
          <span class="pill">Filters Active</span>
        </div>
      </div>
    </div>

    <!-- BOTTOM: DATA MATRIX TABLE -->
    <div class="panel" style="margin-top:18px">
      <div class="panel-header">
        <h4 class="panel-title">Data Matrix Table</h4>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Performed By</th>
                <th>Task Count</th>
                <th>Toggle View</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(reports, person) in groupedData" :key="person">
                <td><strong>{{ person }}</strong></td>
                <td>{{ Object.values(reports).reduce((t,c)=>t+c,0) }}</td>
                <td>
                  <button class="btn btn-ghost" @click="toggleDetails(person, '__ALL__')">
                    {{ (selectedRow.person===person && selectedRow.report==='__ALL__') ? 'Hide' : 'View' }}
                  </button>
                </td>
              </tr>

              <!-- Expanded person view -->
              <tr v-if="selectedRow.report==='__ALL__' && groupedData[selectedRow.person]">
                <td colspan="3">
                  <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                      <thead>
                        <tr>
                          <th>Report</th>
                          <th>Count</th>
                          <th>Target</th>
                          <th>Balance</th>
                          <th>Achieved %</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(count, report) in groupedData[selectedRow.person]" :key="report" @click="toggleDetails(selectedRow.person, report)" style="cursor:pointer">
                          <td>{{ report }}</td>
                          <td>{{ count }}</td>
                          <td>{{ getTarget(selectedRow.person, report) }}</td>
                          <td>{{ getBalance(selectedRow.person, report) }}</td>
                          <td>{{ getPercentage(selectedRow.person, report) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <!-- Nested detail list (when a report is clicked) -->
                  <div v-if="selectedRow.report !== '__ALL__'">
                    <div class="divider"></div>
                    <div class="grid" style="grid-template-columns: 1fr auto;align-items:center;margin-bottom:8px">
                      <input class="form-control form-control-sm" v-model="detailSearch" placeholder="Search in details…">
                      <div class="pill">Page {{ detailPage }} / {{ detailTotalPages() }}</div>
                    </div>
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
                            <td><a :href="item.link" target="_blank" rel="noopener">Open</a></td>
                            <td>{{ item.language }}</td>
                            <td>{{ item.note }}</td>
                          </tr>
                        </tbody>
                      </table>
                      <div class="grid" style="grid-template-columns: auto auto; justify-content:space-between; margin-top:10px">
                        <button class="btn btn-primary" @click="detailPage--" :disabled="detailPage===1">Previous</button>
                        <button class="btn btn-primary" @click="detailPage++" :disabled="detailPage===detailTotalPages()">Next</button>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="divider"></div>

        <!-- Daily KPI by Department -->
        <h5 class="panel-title" style="margin-bottom:10px">Daily KPI by Department</h5>
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead><tr><th>Department</th><th>Date</th><th>Total KPI</th></tr></thead>
            <tbody>
              <template v-for="(dates, dept) in dailyKPIByDepartment" :key="dept">
                <tr v-for="(count, date) in dates" :key="dept+'-'+date">
                  <td>{{ dept }}</td>
                  <td>{{ date }}</td>
                  <td>{{ count }}</td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>

        <div class="grid" style="grid-template-columns:auto auto;justify-content:space-between;margin-top:14px">
          <button class="btn btn-primary" @click="exportGroupedToExcel">Export Summary (Excel)</button>
          <span class="note">* All charts & tables respect the filters above.</span>
        </div>
      </div>
    </div>

  </div>
</section>

<script>
/* ---- Axios header stub (edit/remove as needed) ---- */
const API_HEADERS = {
  headers: {
    Authorization: 'Basic ' + btoa('FLF:FLF@P!')
  }
}; // e.g., { headers: { Authorization: 'Bearer …' } };
const DONUT_COLORS = ['#071e37','#144468','#1b5f8a','#217ba9','#695c4a','#a38776','#c2aa91','#b58f44','#d8ac54','#e3cf91'];

new Vue({
  el: '#app',
  data: {
    selectedRow: { person: '', report: '' },
    detailSearch: '',
    detailPage: 1,
    detailPerPage: 10,

    profileData: [],
    usersDirectory: [],
    firstNameDeptIndex: {},

    groupedData: {},

    // Filters
    filters: {
      search: '',
      report: '',
      month: '',
      brand: '',
      date: '',
      platform: '',
      department: '',
    },

    // Dropdown options
    uniqueOptions: {
      reports: [],
      months: [],
      brands: [],
      platforms: [],
      dates: [],
      departments: [],
    },

    // Per-report targets (fallback used if key not found)
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

    // Chart instances
    donutChartInstance: null,
    statusBarInstance: null
  },

  computed: {
    // Entire dashboard should rely on filteredData
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
            (item.notes && item.notes.toLowerCase().includes(search)) ||
            (item.department && item.department.toLowerCase().includes(search))
          );

          const sameDate = !this.filters.date ||
            (String(item.date).slice(0, 10) === String(this.filters.date).slice(0, 10));

          const matchesDept = !this.filters.department ||
            (item.department === this.filters.department);

          return (
            item.report !== 'TLC' &&
            matchesSearch &&
            (!this.filters.report || item.report === this.filters.report) &&
            (!this.filters.month || monthName === this.filters.month) &&
            (!this.filters.brand || item.brand === this.filters.brand) &&
            (!this.filters.platform || item.platform === this.filters.platform) &&
            sameDate &&
            matchesDept
          );
        })
        .sort((a, b) => new Date(b.date) - new Date(a.date));
    },

    // Weekly summary by person (kept in case you need it elsewhere)
    weeklyDataByPerson() {
      const today = new Date();
      const startOfWeek = new Date(today);
      const dayOfWeek = today.getDay();
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

    // Daily KPI by Department
    dailyKPIByDepartment() {
      const out = {};
      this.filteredData.forEach(item => {
        const department = item.department || 'Unassigned';
        const date = (item.date || '').slice(0, 10);
        if (!out[department]) out[department] = {};
        if (!out[department][date]) out[department][date] = 0;
        out[department][date]++;
      });
      return out;
    },

    // Month-to-date summary (unused in UI but available)
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
    },

    // KPI % bubbles at the right of donut
    overallCompletion(){
      let achieved=0, target=0;
      Object.keys(this.groupedData || {}).forEach(person=>{
        Object.keys(this.groupedData[person]).forEach(report=>{
          const count=this.groupedData[person][report]||0;
          const t=this.getTarget(person,report)||0;
          achieved+=Math.min(count,t);
          target+=t;
        });
      });
      if(!target) return {achieved:0, remaining:100};
      const pct=Math.round((achieved/target)*100);
      return {achieved:pct, remaining:Math.max(0,100-pct)};
    },

    // Buckets for stacked bar
    statusBuckets(){
      const out={};
      this.filteredData.forEach(it=>{
        const who=it.performed_by || 'Unknown';
        const st=(it.status==='Progress' || it.status==='Incomplete') ? it.status : 'Complete';
        if(!out[who]) out[who]={Complete:0,Progress:0,Incomplete:0};
        out[who][st]++;
      });
      return out;
    }
  },

  watch: {
    filteredData: {
      handler() {
        this.groupRecords();
        this.updateDonut();
        this.updateStatusBars();
      },
      immediate: true
    }
  },

  methods: {
    // -------- Helpers ----------
    normalize(val) { return String(val || '').trim().toLowerCase(); },
    firstWord(name) { return this.normalize(name).split(/\s+/)[0] || ''; },

    attachDepartmentsToKPI(kpiArray) {
      return kpiArray.map(item => {
        const candidate = this.firstWord(item.performed_by) || this.firstWord(item.created_by);
        const dept = this.firstNameDeptIndex[candidate] || 'Unassigned';
        return { ...item, department: dept };
      });
    },

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
      if (report === '__ALL__') return [];
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

      return Math.ceil(filtered.length / this.detailPerPage) || 1;
    },

    // Targets/Progress
    getTarget(person, report) {
      const key = `${person}-${report}`;
      return this.targetData[key] || this.targetData[report] || 20;
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

    // -------- Data fetching ----------
    async fetchUsers() {
      try {
        const res = await axios.get('http://31.97.43.196/kpidashboardapi/customer/users', API_HEADERS);
        const users = res?.data?.response || res?.data || [];
        this.usersDirectory = users;

        const index = {};
        users.forEach(u => {
          const first = this.normalize(u.first_name || u.firstname || u.given_name);
          const dept  = u.department || u.team || u.role || 'Unassigned';
          if (first) index[first] = dept;
        });
        this.firstNameDeptIndex = index;
      } catch (e) {
        console.error('Users fetch failed:', e);
        this.usersDirectory = [];
        this.firstNameDeptIndex = {};
      }
    },

    async fetchKPI() {
      try {
        const urls = [
          'http://31.97.43.196/kpidashboardapi/kpi/show',
          'http://31.97.43.196/kpidashboardapi/kpi/getGraphicsTeam',
          'http://31.97.43.196/kpidashboardapi/kpi/content'
        ];
        const responses = await Promise.all(urls.map(url => axios.get(url, API_HEADERS)));
        const mergedData = responses.flatMap(res => res?.data?.response || []);
        this.webdevEOW = responses?.[0]?.data?.conclusionEOW || '';
        return mergedData;
      } catch (error) {
        console.error('Error fetching KPI data:', error);
        return [];
      }
    },

    async setKPI() {
      try {
        await this.fetchUsers();
        const rawKPI = await this.fetchKPI();

        const enriched = this.attachDepartmentsToKPI(rawKPI);
        this.profileData = enriched;

        this.setFilterOptions();
        this.groupRecords();
        this.$nextTick(() => {
          this.renderDonut();
          this.renderStatusBars();
        });
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
      const departments = new Set();

      this.profileData.forEach(item => {
        if (item.report) reports.add(item.report);
        if (item.date) {
          months.add(new Date(item.date).toLocaleString('default', { month: 'long' }));
          dates.add(String(item.date).slice(0, 10));
        }
        if (item.brand) brands.add(item.brand);
        if (item.platform) platforms.add(item.platform);
        if (item.department) departments.add(item.department);
      });

      this.uniqueOptions = {
        reports: Array.from(reports),
        months: Array.from(months),
        brands: Array.from(brands),
        platforms: Array.from(platforms),
        dates: Array.from(dates),
        departments: Array.from(departments)
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

    // -------- DONUT (Totals by individual) ----------
    donutData(){
      const counts={};
      this.filteredData.forEach(i=>{
        const k=i.performed_by||'Unknown';
        counts[k]=(counts[k]||0)+1;
      });
      return {labels:Object.keys(counts), data:Object.values(counts)};
    },
    renderDonut(){
      const ctx=document.getElementById('donutChart');
      if(!ctx) return;
      if(this.donutChartInstance) this.donutChartInstance.destroy();
      const {labels,data}=this.donutData();
      this.donutChartInstance=new Chart(ctx,{
        type:'doughnut',
        data:{labels,datasets:[{data,backgroundColor:DONUT_COLORS,cutout:'55%'}]},
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}}}
      });
    },
    updateDonut(){
      if(!this.donutChartInstance) return this.renderDonut();
      const {labels,data}=this.donutData();
      this.donutChartInstance.data.labels=labels;
      this.donutChartInstance.data.datasets[0].data=data;
      this.donutChartInstance.update();
    },

    // -------- STACKED BAR (Complete/Progress/Incomplete per person) ----------
    statusBarData(){
      const labels=Object.keys(this.statusBuckets);
      const complete=labels.map(l=>this.statusBuckets[l]?.Complete||0);
      const progress=labels.map(l=>this.statusBuckets[l]?.Progress||0);
      const incomplete=labels.map(l=>this.statusBuckets[l]?.Incomplete||0);
      return {labels,complete,progress,incomplete};
    },
    renderStatusBars(){
      const ctx=document.getElementById('statusBarChart');
      if(!ctx) return;
      if(this.statusBarInstance) this.statusBarInstance.destroy();
      const {labels,complete,progress,incomplete}=this.statusBarData();
      this.statusBarInstance=new Chart(ctx,{
        type:'bar',
        data:{
          labels,
          datasets:[
            {label:'Complete', data:complete, backgroundColor:'#0b2239', stack:'s'},
            {label:'Progress', data:progress, backgroundColor:'#1b5f8a', stack:'s'},
            {label:'Incomplete', data:incomplete, backgroundColor:'#6ec1e4', stack:'s'}
          ]
        },
        options:{
          responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},
          scales:{x:{stacked:true},y:{stacked:true,beginAtZero:true}}
        }
      });
    },
    updateStatusBars(){
      if(!this.statusBarInstance) return this.renderStatusBars();
      const {labels,complete,progress,incomplete}=this.statusBarData();
      const ds=this.statusBarInstance.data.datasets;
      this.statusBarInstance.data.labels=labels;
      ds[0].data=complete; ds[1].data=progress; ds[2].data=incomplete;
      this.statusBarInstance.update();
    },

    // -------- Excel Export (grouped summary) ----------
    exportGroupedToExcel() {
      const rows = [['Performed By', 'Report', 'Count', 'Target', 'Balance', 'Achieved %', 'Department']];
      Object.keys(this.groupedData).forEach(person => {
        Object.keys(this.groupedData[person]).forEach(report => {
          const count = this.groupedData[person][report];
          const target = this.getTarget(person, report);
          const balance = target - count;
          const pct = target ? (count / target * 100).toFixed(1) + '%' : '0%';

          const sample = this.filteredData.find(r => r.performed_by === person && r.report === report);
          const dept = sample?.department || '';

          rows.push([person, report, count, target, balance, pct, dept]);
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
    this.$nextTick(()=>{ this.renderDonut(); this.renderStatusBars(); });
  }
});
</script>
