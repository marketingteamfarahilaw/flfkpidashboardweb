<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<!-- Libs -->
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios@1.6.7/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<style>
  :root{
    --ink:#0b2239; --muted:#607489; --card:#f6f7f9; --line:#e8edf3;
    /* palette used in the legend */
    --c1:#071e37; --c2:#144468; --c3:#1b5f8a; --c4:#217ba9; --c5:#695c4a;
    --c6:#a38776; --c7:#c2aa91; --c8:#b58f44; --c9:#d8ac54; --c10:#e3cf91;
  }
  .dash-wrap{padding:24px}
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
  .btn{border:0;border-radius:10px;padding:8px 12px;font-weight:600}
  .btn-primary{background:#0b2239;color:#fff}
  .btn-ghost{background:#fff;border:1px solid var(--line);color:#0b2239}
  .pill{display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border:1px solid var(--line);border-radius:999px;background:#fff}
  .stack-legend{display:flex;gap:14px;align-items:center}
  .stack-legend span{display:inline-flex;align-items:center;gap:8px;font-size:12px;color:var(--muted)}
  .swatch{width:14px;height:14px;border-radius:3px;display:inline-block}
  .sr{position:absolute;left:-9999px}
  @media (max-width: 1024px){ .grid-2{grid-template-columns: 1fr} }
</style>

<section class="content" id="app">
  <div class="dash-wrap container-fluid">

    <!-- TOP: Donut + KPI panel -->
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

          <!-- Donut Chart -->
          <div class="chart-box">
            <canvas id="donutChart" aria-hidden="true"></canvas>
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

    <!-- MIDDLE: Stacked bars -->
    <div class="panel" style="margin-top:18px">
      <div class="panel-header">
        <h4 class="panel-title">Total Complete vs Incomplete Tasks (ALL)</h4>
      </div>
      <div class="panel-body">
        <div class="chart-box">
          <canvas id="statusBarChart" aria-hidden="true"></canvas>
        </div>
        <div class="divider"></div>
        <div class="stack-legend">
          <span><i class="swatch" style="background:#0b2239"></i> COMPLETE</span>
          <span><i class="swatch" style="background:#1b5f8a"></i> PROGRESS</span>
          <span><i class="swatch" style="background:#6ec1e4"></i> INCOMPLETE</span>
          <span class="pill" title="Filters are already applied">Filters Active</span>
        </div>
      </div>
    </div>

    <!-- BOTTOM: Data Matrix Table -->
    <div class="panel" style="margin-top:18px">
      <div class="panel-header">
        <h4 class="panel-title">Data Matrix Table</h4>
      </div>
      <div class="panel-body">
        <!-- Global search -->
        <div class="grid" style="grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr; gap:12px; margin-bottom:14px">
          <input class="form-control" v-model="filters.search" placeholder="Search anything…">
          <select class="form-control" v-model="filters.report">
            <option value="">All Reports</option>
            <option v-for="r in uniqueOptions.reports" :value="r">{{ r }}</option>
          </select>
          <select class="form-control" v-model="filters.month">
            <option value="">All Months</option>
            <option v-for="m in uniqueOptions.months" :value="m">{{ m }}</option>
          </select>
          <select class="form-control" v-model="filters.brand">
            <option value="">All Brands</option>
            <option v-for="b in uniqueOptions.brands" :value="b">{{ b }}</option>
          </select>
          <select class="form-control" v-model="filters.platform">
            <option value="">All Platforms</option>
            <option v-for="p in uniqueOptions.platforms" :value="p">{{ p }}</option>
          </select>
          <select class="form-control" v-model="filters.department">
            <option value="">All Departments</option>
            <option v-for="d in uniqueOptions.departments" :value="d">{{ d }}</option>
          </select>
        </div>

        <div class="table-responsive">
          <table class="table">
            <thead><tr>
              <th>Performed By</th>
              <th>Task Count</th>
              <th>Toggle View</th>
            </tr></thead>
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

              <!-- Expand row -->
              <tr v-if="selectedRow.report==='__ALL__' && groupedData[selectedRow.person]">
                <td colspan="3">
                  <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                      <thead>
                        <tr><th>Report</th><th>Count</th><th>Target</th><th>Balance</th><th>Achieved %</th></tr>
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

                  <!-- Nested detail list when selecting a report inside the expanded panel -->
                  <div v-if="selectedRow.report !== '__ALL__'">
                    <div class="divider"></div>
                    <div class="grid" style="grid-template-columns: 1fr auto;align-items:center;margin-bottom:8px">
                      <input class="form-control form-control-sm" v-model="detailSearch" placeholder="Search in details…">
                      <div class="pill">Page {{ detailPage }} / {{ detailTotalPages() }}</div>
                    </div>
                    <div class="table-responsive">
                      <table class="table table-bordered table-sm">
                        <thead>
                          <tr><th>Date</th><th>Title</th><th>Link</th><th>Language</th><th>Notes</th></tr>
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
/* === Add these small pieces into your existing Vue instance ===
   1) data(): add two Chart instances + donut colors
*/
const DONUT_COLORS = ['#071e37','#144468','#1b5f8a','#217ba9','#695c4a','#a38776','#c2aa91','#b58f44','#d8ac54','#e3cf91'];

new Vue({
  el:'#app',
  data:{
    /* keep all your existing data ... */
    donutChartInstance:null,
    statusBarInstance:null
  },
  computed:{
    /* REUSE your existing computed. Add this one: */
    overallCompletion(){
      // Sum current counts vs targets across groupedData
      let achieved=0, target=0;
      Object.keys(this.groupedData||{}).forEach(person=>{
        Object.keys(this.groupedData[person]).forEach(report=>{
          const count=this.groupedData[person][report]||0;
          const t=this.getTarget(person,report)||0;
          achieved+=Math.min(count,t); target+=t;
        });
      });
      if(!target){ return {achieved:0, remaining:100}; }
      const pct=Math.round((achieved/target)*100);
      return {achieved:pct, remaining:Math.max(0,100-pct)};
    },
    /* Optional helper to build status buckets per performer */
    statusBuckets(){
      // expects item.status ∈ ['Complete','Progress','Incomplete']; falls back to Complete
      const out={}; // performer -> {Complete,Progress,Incomplete}
      this.filteredData.forEach(it=>{
        const who=it.performed_by||'Unknown';
        const st=(it.status==='Progress'||it.status==='Incomplete')?it.status:'Complete';
        if(!out[who]) out[who]={Complete:0,Progress:0,Incomplete:0};
        out[who][st]++
      });
      return out;
    }
  },
  watch:{
    filteredData:{
      handler(){
        this.groupRecords();
        this.updateDonut();
        this.updateStatusBars();
      }, immediate:true
    }
  },
  methods:{
    /* keep all your existing methods ... */

    // ------- DONUT (by individual total tasks) -------
    donutData(){
      const counts={};
      this.filteredData.forEach(i=>{
        const k=i.performed_by||'Unknown';
        counts[k]=(counts[k]||0)+1;
      });
      const labels=Object.keys(counts);
      const data=Object.values(counts);
      return {labels,data};
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

    // ------- STACKED BAR (Complete/Progress/Incomplete per person) -------
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
          responsive:true,maintainAspectRatio:false,
          plugins:{legend:{display:false}},
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

    // override your toggle to allow “__ALL__”
    toggleDetails(person, report){
      if(this.selectedRow.person===person && this.selectedRow.report===report){
        this.selectedRow={person:'',report:''}; this.detailSearch=''; return;
      }
      this.selectedRow={person,report}; this.detailPage=1; this.detailSearch='';
    }
  },
  mounted(){
    // your existing setKPI() call fetches data then we render
    if(typeof this.setKPI==='function'){ this.setKPI(); }
    this.$nextTick(()=>{ this.renderDonut(); this.renderStatusBars(); });
  }
});
</script>
