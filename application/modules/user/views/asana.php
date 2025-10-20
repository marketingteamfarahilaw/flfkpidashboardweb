<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<!-- Vue + Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<style>
  /* ====== LOOK & FEEL TO MATCH THE MOCKUP ====== */
  :root{
    --ink:#0b2239;           /* deep navy for headings */
    --muted:#607489;         /* muted gray-blue */
    --card:#f6f7f9;          /* soft card bg */
    --line:#e8edf3;          /* separators */
    --accent:#0b2239;
    /* donut palette: blues to taupes like the mock */
    --c1:#071e37; --c2:#144468; --c3:#1b5f8a; --c4:#217ba9;
    --c5:#695c4a; --c6:#a38776; --c7:#c2aa91; --c8:#b58f44;
    --c9:#d8ac54; --c10:#e3cf91;
  }
  body {background:#ffffff; color:var(--ink);}
  h4,h5,h6{font-weight:800; letter-spacing:.4px; color:var(--ink);}
  .content-title{ text-align:center; text-transform:uppercase; letter-spacing:1px; }

  .card-xl{
    background:#fff; border:1px solid var(--line); border-radius:14px;
    padding:28px; box-shadow:0 2px 10px rgba(11,34,57,.06);
  }
  .card-muted{
    background:var(--card); border:1px solid var(--line); border-radius:14px; padding:24px;
  }
  .legend-list{
    list-style:none; margin:0; padding:0;
  }
  .legend-item{
    display:flex; align-items:center; margin:8px 0;
    font-size:13px; color:var(--muted); font-weight:700;
  }
  .swatch{
    width:22px; height:8px; border-radius:2px; margin-right:10px;
  }

  .kpi-wrap{
    display:flex; flex-direction:column; gap:22px; max-width:340px;
  }
  .kpi-block{
    background:#fff; border:1px solid var(--line); border-radius:12px; padding:18px 18px 16px;
  }
  .kpi-label{ font-size:13px; color:var(--muted); margin:0 0 4px; }
  .kpi-value{ font-size:44px; font-weight:900; color:var(--ink); line-height:1; }

  .section-title{
    text-align:center; font-weight:900; text-transform:uppercase; margin:8px 0 18px;
  }

  /* table styling like the mock */
  .matrix-title{ text-align:center; margin:18px 0 10px; text-transform:uppercase; font-weight:900; }
  table.table-sm thead th{ background:#f3f6fa; color:var(--ink); border-bottom:2px solid var(--line); }
  table.table-sm td, table.table-sm th{ border-color:var(--line); }
  .btn-link.p-0{ font-weight:700; }

  /* layout helpers */
  .grid-3{
    display:grid; grid-template-columns:260px 1fr 360px; gap:26px; align-items:center;
  }
  .chart-box{ position:relative; height:320px; }
  .bar-box{ height:340px; }
  @media(max-width: 992px){
    .grid-3{ grid-template-columns:1fr; }
    .kpi-wrap{ max-width:100%; }
  }
</style>

<section class="content" id="app">
  <div class="container-fluid pt-3 pl-md-2 pr-md-2 pl-md-5 pr-md-5">

    <h4 class="content-title mb-3">ASANA Task Overview</h4>

    <!-- Filters (kept but compact) -->
    <div class="card-muted mb-3">
      <div class="row align-items-end">
        <div class="col-md-2 col-6 mb-2">
          <label class="mb-1">Date Start</label>
          <input type="date" class="form-control form-control-sm" v-model="filters.startDate">
        </div>
        <div class="col-md-2 col-6 mb-2">
          <label class="mb-1">Date End</label>
          <input type="date" class="form-control form-control-sm" v-model="filters.endDate">
        </div>
        <div class="col-md-2 col-6 mb-2">
          <label class="mb-1">Performed By</label>
          <select class="form-control form-control-sm" v-model="filters.performedBy">
            <option value="">All</option>
            <option v-for="val in filterOptions.performedBy" :key="val" :value="val">{{ val }}</option>
          </select>
        </div>
        <div class="col-md-3 col-12 mb-2">
          <button class="btn btn-outline-secondary btn-sm mr-2" @click="clearDateRange">Clear</button>
          <button class="btn btn-outline-primary btn-sm mr-2" @click="setMTD()">MTD</button>
          <button class="btn btn-success btn-sm" @click="exportToExcel">Export to Excel</button>
        </div>
      </div>
    </div>

    <!-- ===== Section 1: TASKS PERFORMED BY (INDIVIDUAL) ===== -->
    <div class="card-xl mb-4">
      <h5 class="section-title">Tasks Performed By (Individual)</h5>
      <div class="grid-3">
        <!-- LEFT: Legend list -->
        <div>
          <div class="mb-2" style="font-size:12px; color:var(--muted);">Digital Marketing Members</div>
          <ul class="legend-list">
            <li class="legend-item" v-for="(n,i) in donutLabels" :key="i">
              <span class="swatch" :style="{background: donutColors[i % donutColors.length]}"></span>
              {{ n }}
            </li>
          </ul>
        </div>

        <!-- CENTER: Donut -->
        <div class="chart-box">
          <canvas id="performedByChart"></canvas>
          <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; pointer-events:none;">
            <div style="text-align:center;">
              <div style="font-weight:800; font-size:14px; color:#2d3b4a;">Tasks</div>
              <div style="font-size:12px; color:var(--muted);">Completed<br/>By Performer</div>
            </div>
          </div>
        </div>

        <!-- RIGHT: KPIs -->
        <div class="kpi-wrap">
          <div class="kpi-block">
            <p class="kpi-label">Results As To Date (ALL)</p>
            <div class="kpi-value">{{ overallCompletion }}%</div>
            <!-- <p style="font-size:12px; color:var(--muted); margin:8px 0 0;">
              Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor.
            </p> -->
          </div>
          <div class="kpi-block">
            <p class="kpi-label">Target/Goal (ALL)</p>
            <div class="kpi-value">{{ targetGoal }}%</div>
            <!-- <p style="font-size:12px; color:var(--muted); margin:8px 0 0;">
              Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor.
            </p> -->
          </div>
        </div>
      </div>
    </div>

    <!-- ===== Section 2: TOTAL COMPLETE VS INCOMPLETE TASKS (ALL) ===== -->
    <div class="card-xl mb-4">
      <h5 class="section-title">Total Complete vs Incomplete Tasks (All)</h5>
      <div class="bar-box">
        <canvas id="overallBar"></canvas>
      </div>
      <div class="d-flex justify-content-center mt-2" style="gap:18px;">
        <div class="legend-item"><span class="swatch" style="background:#0b2239"></span> COMPLETE</div>
        <div class="legend-item"><span class="swatch" style="background:#1b5f8a"></span> PROGRESS</div>
        <div class="legend-item"><span class="swatch" style="background:#217ba9"></span> INCOMPLETE</div>
      </div>
    </div>

    <!-- ===== Section 3: DATA MATRIX TABLE ===== -->
    <h5 class="matrix-title">Data Matrix Table</h5>
    <div class="table-responsive">
      <table class="table table-hover table-bordered table-sm">
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
              <tr v-for="task in group" :key="task.id"
                  @mouseover="hoverTask = task.id" @mouseleave="hoverTask = null">
                <td colspan="3">
                  <div>
                    <strong>{{ task.title }}</strong>
                    <span v-if="hoverTask === task.id" class="text-muted float-right">{{ task.parent_name }}</span>
                  </div>
                  <small>Due: {{ task.due_on || '—' }} | Completed: {{ task.completed_at || '—' }}</small><br>
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
      filters: { startDate:'', endDate:'', performedBy:'' },
      filterOptions: { performedBy:[] },
      expanded: {},
      hoverTask: null, sortKey:'', sortAsc:true,

      // styling + target
      donutColors: ['#071e37','#144468','#1b5f8a','#217ba9','#695c4a','#a38776','#c2aa91','#b58f44','#d8ac54','#e3cf91'],
      donutLabels: [],
      targetGoal: 20,

      // chart instances
      donut: null,
      bar: null
    },
    computed:{
      filteredTasks(){
        const s=this.filters.startDate, e=this.filters.endDate, p=this.filters.performedBy;
        const inRange=(d)=> {
          if(!s && !e) return true;
          if(!d) return false;
          const t=new Date(d).getTime();
          const ss=s? new Date(s+'T00:00:00').getTime(): -Infinity;
          const ee=e? new Date(e+'T23:59:59').getTime(): Infinity;
          return t>=ss && t<=ee;
        };
        return this.tasks.filter(t=>{
          const dateToCheck = t.completed_at || t.due_on || null;
          const perfOk = !p || (t.performed_by||'Unassigned')===p;
          return inRange(dateToCheck) && perfOk;
        });
      },
      groupedTasks(){
        const base = this.filteredTasks.reduce((acc,t)=>{
          const k=t.performed_by||'Unassigned';
          (acc[k]=acc[k]||[]).push(t);
          return acc;
        },{});
        if(!this.sortKey) return base;
        return Object.fromEntries(
          Object.entries(base).sort(([ka,a],[kb,b])=>{
            const av = this.sortKey==='performed_by' ? ka : a.length;
            const bv = this.sortKey==='performed_by' ? kb : b.length;
            if(av===bv) return String(ka).localeCompare(String(kb));
            return this.sortAsc ? (av>bv?1:-1) : (av<bv?1:-1);
          })
        );
      },
      overallCompletion(){
        const f=this.filteredTasks;
        if(!f.length) return 0;
        const done=f.filter(t=>t.completed_at && String(t.completed_at).trim()!=='').length;
        return Math.round((done/f.length)*100);
      }
    },
    watch:{
      filteredTasks(){ this.renderAll(); }
    },
    methods:{
      fmt(d){ const y=d.getFullYear(), m=('0'+(d.getMonth()+1)).slice(-2), day=('0'+d.getDate()).slice(-2); return `${y}-${m}-${day}`; },
      setMTD(){ const t=new Date(), first=new Date(t.getFullYear(), t.getMonth(), 1); this.filters.startDate=this.fmt(first); this.filters.endDate=this.fmt(t); },
      clearDateRange(){ this.filters.startDate=''; this.filters.endDate=''; },
      sortBy(key){ if(this.sortKey===key) this.sortAsc=!this.sortAsc; else { this.sortKey=key; this.sortAsc=true; } },
      toggleDetail(p){ this.$set(this.expanded,p,!this.expanded[p]); },
      async fetchData(){
        const res = await fetch("http://31.97.43.196/kpidashboardapi/kpi/getGraphicsTeam", CONFIG.HEADER);
        const json = await res.json();
        if(json.status){
          this.tasks = json.response;

          // performer list
          const performers = Array.from(new Set(this.tasks.map(t=>t.performed_by||'Unassigned'))).sort();
          this.filterOptions.performedBy = performers;
          this.donutLabels = performers;

          // default MTD
          if(!this.filters.startDate && !this.filters.endDate){ this.setMTD(); }
          this.$nextTick(this.renderAll);
        }
      },
      exportToExcel(){
        const data = this.filteredTasks.map(t=>({
          Title: t.title,
          'Due Date': t.due_on||'',
          'Completed At': t.completed_at||'',
          'Performed By': t.performed_by||'Unassigned',
          'Parent Name': t.parent_name||'',
          URL: t.permalink_url||''
        }));
        const ws=XLSX.utils.json_to_sheet(data);
        const wb=XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb,ws,"Filtered Tasks");
        XLSX.writeFile(wb,"ASANA_Tasks.xlsx");
      },

      /* ================== CHARTS ================== */
      buildDonut(){
        // counts per performer
        const counts = this.filteredTasks.reduce((acc,t)=>{
          const k=t.performed_by||'Unassigned';
          acc[k]=(acc[k]||0)+1; return acc;
        },{});
        const labels = this.donutLabels;
        const data = labels.map(l=>counts[l]||0);

        const ctx = document.getElementById('performedByChart').getContext('2d');
        if(this.donut) this.donut.destroy();
        this.donut = new Chart(ctx,{
          type:'doughnut',
          data:{ labels, datasets:[{ data, backgroundColor:this.donutColors, borderWidth:0 }] },
          options:{
            cutout:'58%',
            plugins:{ legend:{ display:false } }
          }
        });
      },

      buildBar(){
        // Group by month (YYYY-MM) across ALL performers
        const buckets = {};
        const today = new Date();

        this.filteredTasks.forEach(t=>{
          // month derived from completed_at (if present) else due_on
          const basis = t.completed_at || t.due_on;
          if(!basis) return;
          const key = basis.slice(0,7); // YYYY-MM

          if(!buckets[key]) buckets[key] = { complete:0, progress:0, incomplete:0 };

          if(t.completed_at && String(t.completed_at).trim()!==''){
            buckets[key].complete++;
          } else {
            // classify as progress vs incomplete
            const due = t.due_on ? new Date(t.due_on) : null;
            if(due && due.getTime() >= today.getTime()){
              buckets[key].progress++;
            } else {
              buckets[key].incomplete++;
            }
          }
        });

        const months = Object.keys(buckets).sort();
        const complete = months.map(m=>buckets[m].complete);
        const progress = months.map(m=>buckets[m].progress);
        const incomplete = months.map(m=>buckets[m].incomplete);

        const ctx = document.getElementById('overallBar').getContext('2d');
        if(this.bar) this.bar.destroy();
        this.bar = new Chart(ctx,{
          type:'bar',
          data:{
            labels: months,
            datasets:[
              { label:'Complete',   data:complete,   backgroundColor:'#0b2239', borderWidth:0 },
              { label:'Progress',   data:progress,   backgroundColor:'#1b5f8a', borderWidth:0 },
              { label:'Incomplete', data:incomplete, backgroundColor:'#217ba9', borderWidth:0 }
            ]
          },
          options:{
            responsive:true,
            plugins:{ legend:{ display:false } },
            scales:{
              x:{ grid:{ display:false } },
              y:{ beginAtZero:true, grid:{ color:'#e9eef4' }, title:{ display:true, text:'Task (count, in numbers)' } }
            }
          }
        });
      },

      renderAll(){
        this.buildDonut();
        this.buildBar();
      }
    },
    mounted(){ this.fetchData(); }
  });
});
</script>
