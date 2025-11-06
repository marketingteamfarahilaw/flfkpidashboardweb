<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<!-- Vue + Chart.js + SheetJS -->
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
    /* donut palette */
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
  .legend-list{ list-style:none; margin:0; padding:0; }
  .legend-item{
    display:flex; align-items:center; margin:8px 0;
    font-size:13px; color:var(--muted); font-weight:700;
  }
  .swatch{ width:22px; height:8px; border-radius:2px; margin-right:10px; }

  .kpi-wrap{ display:flex; flex-direction:column; gap:22px; max-width:340px; }
  .kpi-block{ background:#fff; border:1px solid var(--line); border-radius:12px; padding:18px 18px 16px; }
  .kpi-label{ font-size:13px; color:var(--muted); margin:0 0 4px; }
  .kpi-value{ font-size:44px; font-weight:900; color:var(--ink); line-height:1; }

  .section-title{ text-align:center; font-weight:900; text-transform:uppercase; margin:8px 0 18px; }

  /* ===== Matrix table (matches screenshot) ===== */
  .matrix-title{ text-align:center; margin:18px 0 10px; text-transform:uppercase; font-weight:900; }
  .matrix-table thead th{
    background:#f3f6fa; color:var(--ink); border-bottom:2px solid var(--line);
    vertical-align:middle; text-transform:uppercase; font-size:12px;
  }
  .matrix-table td, .matrix-table th{ border-color:var(--line); font-size:13px; }
  .week-header td{
    background:#e9edf4; color:#233447; font-weight:800; text-transform:uppercase;
  }
  .t-center{ text-align:center; }
  .t-right{ text-align:right; }
  .task-link{ font-weight:600; color:#0b2239; text-decoration:none; }
  .task-link:hover{ text-decoration:underline; }
  .check{ color:#26a269; font-weight:900; margin-right:6px; }
  .pill{
    display:inline-block; padding:2px 8px; border-radius:999px; background:#eef2f6;
    font-size:12px; font-weight:700; color:#516173;
  }
  .status-badge{
    display:inline-block; padding:2px 10px; border-radius:999px; font-size:12px; font-weight:800;
  }
  .status--completed{ background:#e6f6eb; color:#1e7b36; }
  .status--progress{ background:#eaf2fb; color:#195c97; }
  .status--incomplete{ background:#fdeeee; color:#a83b3b; }

  /* layout helpers */
  .grid-3{
    display:grid; grid-template-columns:260px 1fr 360px; gap:26px; align-items:center;
  }
  #overallBar{ width:800px!important; margin:0 auto!important; }
  .chart-box{ position:relative; height:300px; }
  .bar-box{ height:340px; }
  #performedByChart { margin: 0 auto; }
  @media(max-width: 992px){
    .grid-3{ grid-template-columns:1fr; }
    .kpi-wrap{ max-width:100%; }
    #overallBar{ width:100%!important; }
  }
</style>

<section class="content" id="app">
  <div class="container-fluid pt-3 pl-md-2 pr-md-2 pl-md-5 pr-md-5">

    <h2 class="content-title mb-3">ASANA Task Overview</h2>

    <!-- Filters -->
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
        </div>

        <!-- RIGHT: KPIs -->
        <div class="kpi-wrap">
          <div class="kpi-block">
            <p class="kpi-label">Results As To Date (ALL)</p>
            <div class="kpi-value">{{ overallCompletion }}%</div>
          </div>
          <div class="kpi-block">
            <p class="kpi-label">Target/Goal (ALL)</p>
            <div class="kpi-value">{{ targetGoal }}%</div>
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

    <!-- ===== Section 3: DATA MATRIX TABLE (WEEK-GROUPED) ===== -->
    <h5 class="matrix-title">Design-Related Tasks</h5>

    <div class="table-responsive">
      <table class="table table-hover table-bordered table-sm matrix-table">
        <thead class="thead-light">
          <tr>
            <th style="min-width:380px">Design-Related Tasks</th>
            <th style="width:90px; text-align:center">OUTPUT<br>COUNT</th>
            <th style="width:120px">BRAND</th>
            <th style="width:140px">TASK TYPE</th>
            <th style="width:150px">POC</th>
            <th style="width:120px">STATUS</th>
            <th style="width:120px">DUE DATE</th>
            <th style="width:140px">DATE SUBMITTED</th>
            <th style="width:120px">Time<br>(Minutes)</th>
          </tr>
        </thead>

        <tbody>
          <template v-for="(rows, weekKey) in weekGroups" :key="weekKey">
            <tr class="week-header">
              <td colspan="9">
                <strong>{{ weekKey }}</strong>
              </td>
            </tr>

            <tr v-for="r in rows" :key="r.id">
              <td>
                <a :href="r.permalink_url" target="_blank" rel="noopener" class="task-link">
                  <span v-if="r.completed_at" class="check">✓</span>
                  {{ r.title || '—' }}
                </a>
              </td>
              <td class="t-center">{{ r.output_count ?? 0 }}</td>
              <td>{{ r.brand || '—' }}</td>
              <td><span class="pill">{{ r.task_type || '—' }}</span></td>
              <td>{{ r.performed_by || '—' }}</td>
              <td>
                <span :class="['status-badge', statusClass(r.status, r.completed_at, r.due_on)]">
                  {{ prettyStatus(r.status, r.completed_at, r.due_on) }}
                </span>
              </td>
              <td>{{ fmtDate(r.due_on) }}</td>
              <td>{{ fmtDate(r.completed_at || r.date_submitted) }}</td>
              <td class="t-right">{{ r.time_minutes ?? '' }}</td>
            </tr>
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

      donutColors: ['#071e37','#144468','#1b5f8a','#217ba9','#695c4a','#a38776','#c2aa91','#b58f44','#d8ac54','#e3cf91'],
      donutLabels: [],
      targetGoal: 20,

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
          const dateToCheck = t.completed_at || t.date_submitted || t.due_on || null;
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
      },

      /* ===== Week-grouped rows for the matrix table ===== */
      weekGroups(){
        const bucket = {};
        this.filteredTasks.forEach(t=>{
          const basisStr = t.completed_at || t.date_submitted || t.due_on;
          const basis = basisStr ? new Date(basisStr.length<=10 ? basisStr+'T00:00:00' : basisStr) : new Date();
          const key = this.weekLabelFromDate(basis);

          const row = {
            id: t.id || t.gid || `${(t.permalink_url||'')}-${(t.title||'')}`,
            title: t.title,
            output_count: t.output_count ?? t.count ?? 0,
            brand: t.brand || t.project || t.workspace || t.project_abbr,
            task_type: t.task_type || t.category || t.section,
            performed_by: t.performed_by || t.poc,
            status: t.status,
            due_on: t.due_on,
            completed_at: t.completed_at,
            date_submitted: t.date_submitted,
            time_minutes: t.time_minutes ?? t.time_spent_minutes,
            permalink_url: t.permalink_url
          };

          (bucket[key] = bucket[key] || []).push(row);
        });

        Object.keys(bucket).forEach(k=>{
          bucket[k].sort((a,b)=>{
            const ad = new Date(a.completed_at || a.date_submitted || a.due_on || 0).getTime();
            const bd = new Date(b.completed_at || b.date_submitted || b.due_on || 0).getTime();
            return bd - ad;
          });
        });

        const ordered = {};
        Object.keys(bucket)
          .sort((A,B)=>{
            const endDateOf = (lab)=>{
              const m = lab.match(/\(([A-Za-z]{3}) (\d{1,2}) - (\d{1,2})\)/);
              if(!m) return 0;
              const mon = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'].indexOf(m[1]);
              const yr = new Date().getFullYear();
              return new Date(yr, mon, parseInt(m[3],10)).getTime();
            };
            return endDateOf(B) - endDateOf(A);
          })
          .forEach(k=> ordered[k]=bucket[k]);
        return ordered;
      }
    },
    watch:{ filteredTasks(){ this.renderAll(); } },
    methods:{
      fmt(d){ const y=d.getFullYear(), m=('0'+(d.getMonth()+1)).slice(-2), day=('0'+d.getDate()).slice(-2); return `${y}-${m}-${day}`; },
      setMTD(){ const t=new Date(), first=new Date(t.getFullYear(), t.getMonth(), 1); this.filters.startDate=this.fmt(first); this.filters.endDate=this.fmt(t); },
      clearDateRange(){ this.filters.startDate=''; this.filters.endDate=''; },
      sortBy(key){ if(this.sortKey===key) this.sortAsc=!this.sortAsc; else { this.sortKey=key; this.sortAsc=true; } },
      toggleDetail(p){ this.$set(this.expanded,p,!this.expanded[p]); },

      fmtDate(v){
        if(!v) return '—';
        const d = (''+v).length <= 10 ? new Date(v+'T00:00:00') : new Date(v);
        if(isNaN(d.getTime())) return '—';
        const mm=('0'+(d.getMonth()+1)).slice(-2), dd=('0'+d.getDate()).slice(-2);
        return `${mm}/${dd}/${d.getFullYear()}`;
      },
      statusClass(status, completed_at, due_on){
        const s = (status||'').toLowerCase();
        if(completed_at) return 'status--completed';
        if(s.includes('complete')) return 'status--completed';
        const today = new Date(); const due = due_on ? new Date(due_on) : null;
        if(due && due < new Date(today.getFullYear(), today.getMonth(), today.getDate())) return 'status--incomplete';
        return 'status--progress';
      },
      prettyStatus(status, completed_at, due_on){
        if(completed_at || (status||'').toLowerCase().includes('complete')) return 'Completed';
        const today = new Date(); const due = due_on ? new Date(due_on) : null;
        if(due && due < new Date(today.getFullYear(), today.getMonth(), today.getDate())) return 'Incomplete';
        return 'Progress';
      },
      weekLabelFromDate(d){
        // Monday-based week, shows "WEEK X (Mon DD - Fri DD)"
        const dt = new Date(d.getFullYear(), d.getMonth(), d.getDate());
        const day = (dt.getDay()+6)%7; // 0=Mon..6=Sun
        const monday = new Date(dt); monday.setDate(dt.getDate()-day);
        const friday = new Date(monday); friday.setDate(monday.getDate()+4);

        const monthShort = (x)=>['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][x];
        const label = `${monthShort(monday.getMonth())} ${monday.getDate()} - ${friday.getDate()}`;

        const first = new Date(dt.getFullYear(), dt.getMonth(), 1);
        const firstDay = (first.getDay()+6)%7;
        const offset = first.getDate() - firstDay;
        const weekNumber = Math.floor((dt.getDate()-offset-1)/7)+1;

        return `WEEK ${weekNumber} (${label})`;
      },

      async fetchData(){
        const res = await fetch("http://31.97.43.196/kpidashboardapi/kpi/getGraphicsTeam", CONFIG.HEADER);
        const json = await res.json();
        if(json.status){
          this.tasks = json.response;

          const performers = Array.from(new Set(this.tasks.map(t=>t.performed_by||'Unassigned'))).sort();
          this.filterOptions.performedBy = performers;
          this.donutLabels = performers;

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
        const centerTextPlugin = {
          id: 'centerTextPlugin',
          afterDraw: (chart) => {
            const { ctx, chartArea } = chart;
            if (!chartArea) return;
            const cx = (chartArea.left + chartArea.right) / 2;
            const cy = (chartArea.top + chartArea.bottom) / 2;

            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            ctx.fillStyle = '#2d3b4a';
            ctx.font = 'bold 16px sans-serif';
            ctx.fillText('Tasks', cx, cy - 10);

            ctx.font = '12px sans-serif';
            ctx.fillStyle = '#607489';
            ctx.fillText('Completed', cx, cy + 8);
            ctx.fillText('By Performer', cx, cy + 22);
            ctx.restore();
          }
        };

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
          options:{ cutout:'62%', plugins:{ legend:{ display:false } }, maintainAspectRatio:true, responsive:true },
          plugins:[centerTextPlugin]
        });
      },

      buildBar(){
        const buckets = {};
        const today = new Date();

        this.filteredTasks.forEach(t=>{
          const basis = t.completed_at || t.due_on;
          if(!basis) return;
          const key = basis.slice(0,7); // YYYY-MM

          if(!buckets[key]) buckets[key] = { complete:0, progress:0, incomplete:0 };

          if(t.completed_at && String(t.completed_at).trim()!==''){
            buckets[key].complete++;
          } else {
            const due = t.due_on ? new Date(t.due_on) : null;
            if(due && due.getTime() >= today.getTime()) buckets[key].progress++;
            else buckets[key].incomplete++;
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

      renderAll(){ this.buildDonut(); this.buildBar(); }
    },
    mounted(){ this.fetchData(); }
  });
});
</script>
