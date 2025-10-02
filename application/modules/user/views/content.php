<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<script>
    
$(document).ready(function () {
    new Vue({
        el: '#app',
        data: {
            profileData: [],  // ✅ Add this line
            contentData: [],
            filters: {
              report: '',
              month: '',
              language: '',
              brand: '',
              search: ''  // ✅ Add this line
            },
            currentPage: 1,
            itemsPerPage: 20,

            gbpData: [],
            gbpFilters: {
                month: '',
                brand: ''
            },
            currentGBPPage: 1,
            itemsGBPPerPage: 20,

            gbpReviewFilters: {
                month: '',
                brand: ''
            },
            currentGBPReviewPage: 1,
            itemsGBPReviewPerPage: 20,

            clientData: [],
            clientFilters: {
                month: '',
                brand: ''
            },
            currentClientPage: 1,
            itemsClientPerPage: 20,
            
            toggledPerformers: {},
            selectedRow: {
              person: '',
              report: ''
            },
            detailPage: 1,

        },
        computed: {
            // Blog/News Reports
            groupedData() {
              const excluded = ['Ray', 'Jacob', 'Nina'];
              const grouped = {};
            
              this.filteredData.forEach(item => {
                const person = item.performed_by;
                const report = item.report;
            
                if (!excluded.includes(person)) {
                  if (!grouped[person]) {
                    grouped[person] = {};
                  }
            
                  if (!grouped[person][report]) {
                    grouped[person][report] = 0;
                  }
            
                  grouped[person][report]++;
                }
              });
            
              return grouped;
            },


            
            filteredData() {
                const search = (this.filters.search || '').toLowerCase();  // 🛠️ FIXED LINE
            
                return this.contentData
                    .filter(item => {
                        const monthName = new Date(item.date).toLocaleString('default', { month: 'long' });
            
                        const matchesSearch = !search || (
                            (item.performed_by && item.performed_by.toLowerCase().includes(search)) ||
                            (item.report && item.report.toLowerCase().includes(search)) ||
                            (item.brand && item.brand.toLowerCase().includes(search)) ||
                            (item.platform && item.platform.toLowerCase().includes(search)) ||
                            (item.notes && item.notes.toLowerCase().includes(search))
                        );
            
                        return (
                            item.report !== 'SocMed' &&
                            ['Faye', 'Myla'].includes(item.performed_by) &&
                            matchesSearch &&
                            (!this.filters.report || item.report === this.filters.report) &&
                            (!this.filters.month || monthName === this.filters.month) &&
                            (!this.filters.brand || item.brand === this.filters.brand) &&
                            (!this.filters.date || item.date === this.filters.date) &&
                            (!this.filters.platform || item.platform === this.filters.platform)
                        );
                    })
                    .sort((a, b) => new Date(b.date) - new Date(a.date));
            },


            
            paginatedReports() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredData.slice(start, start + this.itemsPerPage);
            },
            totalPages() {
                return Math.ceil(this.filteredData.length / this.itemsPerPage);
            },

            // GBP with Location
            filteredGBPWithLocation() {
                return this.gbpData.filter(item => {
                    const itemMonth = new Date(item.date).toLocaleString('default', { month: 'long' });
                    return (
                        item.location !== null &&
                        item.clientName == null &&
                        (!this.gbpFilters.month || itemMonth === this.gbpFilters.month) &&
                        (!this.gbpFilters.brand || item.brand === this.gbpFilters.brand)
                    );
                });
            },
            paginatedGBPWithLocation() {
                const start = (this.currentGBPPage - 1) * this.itemsGBPPerPage;
                return this.filteredGBPWithLocation.slice(start, start + this.itemsGBPPerPage);
            },
            totalGBPWithLocationPages() {
                return Math.ceil(this.filteredGBPWithLocation.length / this.itemsGBPPerPage);
            },

            // GBP with Client Review
            filteredGBPWithClientReview() {
                return this.gbpData.filter(item => {
                    const itemMonth = new Date(item.date).toLocaleString('default', { month: 'long' });
                    return (
                        item.clientName !== null &&
                        (!this.gbpReviewFilters.month || itemMonth === this.gbpReviewFilters.month) &&
                        (!this.gbpReviewFilters.brand || item.brand === this.gbpReviewFilters.brand)
                    );
                });
            },
            paginatedGBPWithClientReview() {
                const start = (this.currentGBPReviewPage - 1) * this.itemsGBPReviewPerPage;
                return this.filteredGBPWithClientReview.slice(start, start + this.itemsGBPReviewPerPage);
            },
            totalGBPWithClientReviewPages() {
                return Math.ceil(this.filteredGBPWithClientReview.length / this.itemsGBPReviewPerPage);
            },

            // Client Data (if needed separately)
            filteredClientData() {
                return this.clientData.filter(item => {
                    const itemMonth = new Date(item.date).toLocaleString('default', { month: 'long' });
                    return (
                        item.location !== null &&
                        item.clientName !== null &&
                        (!this.clientFilters.month || itemMonth === this.clientFilters.month) &&
                        (!this.clientFilters.brand || item.brand === this.clientFilters.brand)
                    );
                });
            },
            paginatedClientData() {
                const start = (this.currentClientPage - 1) * this.itemsClientPerPage;
                return this.filteredClientData.slice(start, start + this.itemsClientPerPage);
            },
            totalClientPages() {
                return Math.ceil(this.filteredClientData.length / this.itemsClientPerPage);
            },

            // Shared dropdown options
            uniqueMonths() {
                const set = new Set();
                this.contentData.forEach(item => {
                    const month = new Date(item.date).toLocaleString('default', { month: 'long' });
                    set.add(month);
                });
                return Array.from(set);
            },
            uniqueBrands() {
                return Array.from(new Set(this.contentData.map(item => item.brand).filter(Boolean)));
            },
            uniqueLanguages() {
                return ['EN', 'ES'];
            },
            uniqueGBPMonths() {
                const set = new Set();
                this.gbpData.forEach(item => {
                    const month = new Date(item.date).toLocaleString('default', { month: 'long' });
                    set.add(month);
                });
                return Array.from(set);
            },
            uniqueGBPBrands() {
                return Array.from(new Set(this.gbpData.map(item => item.brand).filter(Boolean)));
            }
        },
        watch: {
            filters: {
                handler() { this.currentPage = 1; },
                deep: true
            },
            gbpFilters: {
                handler() { this.currentGBPPage = 1; },
                deep: true
            },
            gbpReviewFilters: {
                handler() { this.currentGBPReviewPage = 1; },
                deep: true
            },
            clientFilters: {
                handler() { this.currentClientPage = 1; },
                deep: true
            },
        },
        methods: {

          isDetailsShown(person, report) {
                const key = `${person}-${report}`;
                return this.toggledPerformers[key];
            },
            getDetailedItems(person, report) {
                return this.filteredData.filter(item => item.performed_by === person && item.report === report);
            },
            async fetchKPIReports() {
                try {
                    const res = await axios.get('http://31.97.43.196/kpidashboardapi/kpi/show', CONFIG.HEADER);
                    this.contentData = res.data.response || [];
                } catch (error) {
                    console.error('Failed to fetch KPI report data:', error);
                }
            },
            async fetchGBPReports() {
                try {
                    const res = await axios.get('http://31.97.43.196/kpidashboardapi/kpi/fetchGBPTask', CONFIG.HEADER);
                    this.gbpData = res.data.response || [];
                    this.clientData = res.data.response || []; // Separate if needed
                } catch (error) {
                    console.error('Failed to fetch GBP task data:', error);
                }
            },
            
            getTarget(person, report) {
                const key = `${person}-${report}`;
                return this.targetData[key] || 20; // default target if not defined
            },
            getBalance(person, report) {
              if (
                this.groupedData &&
                this.groupedData[person] &&
                typeof this.groupedData[person][report] !== 'undefined'
              ) {
                // Example logic
                return this.groupedData[person][report] - 1; // Replace with real logic
              }
              return 0;
            },
            getPercentage(person, report) {
              if (
                this.groupedData &&
                this.groupedData[person] &&
                typeof this.groupedData[person][report] !== 'undefined'
              ) {
                const total = Object.values(this.groupedData[person]).reduce((sum, val) => sum + val, 0);
                const count = this.groupedData[person][report];
                return ((count / total) * 100).toFixed(1) + '%';
              }
              return '0%';
            },
            
            toggleDetails(person, report) {
                const key = `${person}-${report}`;
                this.$set(this.toggledPerformers, key, !this.toggledPerformers[key]);

              // Reset detail pagination if used
              this.detailPage = 1;
            },
            paginatedDetailList() {
              const items = this.filteredData.filter(item =>
                item.performed_by === this.selectedRow.person &&
                item.report === this.selectedRow.report
              );
              const start = (this.detailPage - 1) * 10;
              return items.slice(start, start + 10);
            },
            detailTotalPages() {
              const items = this.filteredData.filter(item =>
                item.performed_by === this.selectedRow.person &&
                item.report === this.selectedRow.report
              );
              return Math.ceil(items.length / 10) || 1;
            }




        },
        mounted() {
            this.fetchKPIReports();
          this.fetchGBPReports();
          setTimeout(() => {
            console.log('Fetched contentData:', this.contentData);
            console.log('Filtered Data:', this.filteredData);
            console.log('Grouped Data:', this.groupedData);
          }, 1000);
        }
    });
});

</script>

<section class="content" id="app">
    <div class="container-fluid pt-3 pl-md-2 pr-md-2 pl-md-5 pr-md-5">
        
        <!-- User Data -->
       <div class="card mb-4">
           <h4 class="fw-300 mb-3">CONTENT OUTPUT TRACKER</h4>
        	<div class="card-body">
        		<!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-3">
                    	<label>Report Type</label>
                    	<select class="form-control" v-model="filters.report">
                    	<option value="">All</option>
                    	<option value="Blog Published">Blog</option>
                    	<option value="News Published">News</option>
                    	</select>
                    </div>
                    <div class="col-md-3">
                    	<label>Month</label>
                    	<select class="form-control" v-model="filters.month">
                    	<option value="">All</option>
                    	<option v-for="month in uniqueMonths" :key="month" :value="month">{{ month }}</option>
                    	</select>
                    </div>
                    <div class="col-md-3">
                    	<label>Language</label>
                    	<select class="form-control" v-model="filters.language">
                    	<option value="">All</option>
                    	<option v-for="lang in uniqueLanguages" :key="lang" :value="lang">{{ lang }}</option>
                    	</select>
                    </div>
                    <div class="col-md-3">
                    	<label>Brand</label>
                    	<select class="form-control" v-model="filters.brand">
                    	<option value="">All</option>
                    	<option v-for="brand in uniqueBrands" :key="brand" :value="brand">{{ brand }}</option>
                    	</select>
                    </div>
                    </div>
                    
                    <!-- Table -->
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Performer</th>
                          <th>Report</th>
                          <th>Count</th>
                          <th>Balance</th>
                          <th>%</th>
                        </tr>
                      </thead>
                      <tbody>
                        <template v-for="(reports, person) in groupedData">
                          <template v-for="(count, report, index) in reports">
                            <tr @click="toggleDetails(person, report)" style="cursor: pointer;">
                              <td v-if="index === 0" :rowspan="Object.keys(reports).length"><strong>{{ person }}</strong></td>
                              <td>{{ report }}</td>
                              <td>{{ count }}</td>
                              <td>{{ getBalance(person, report) }}</td>
                              <td>{{ getPercentage(person, report) }}</td>
                            </tr>
                            <!-- Nested rows or details (if needed) -->
                            <tr v-if="isDetailsShown(person, report)">
                                <td colspan="5">
                                  <ul class="mb-0">
                                    <li v-for="item in getDetailedItems(person, report)" :key="item.id">
                                      <strong>Date:</strong> {{ item.date }} —
                                      <strong>Link:</strong> {{ item.link }}
                                    </li>
                                  </ul>
                                </td>
                              </tr>
                          </template>
                        </template>
                      </tbody>
                    </table>




                    
                    <!-- Pagination Controls -->
                    <!-- <div class="d-flex justify-content-between align-items-center mt-2">
                    <button class="btn btn-sm btn-outline-secondary"
                    		@click="currentPage--"
                    		:disabled="currentPage === 1">
                    	Previous
                    </button>
                    <span>Page {{ currentPage }} of {{ totalPages }}</span>
                    <button class="btn btn-sm btn-outline-secondary"
                    		@click="currentPage++"
                    		:disabled="currentPage >= totalPages">
                    	Next
                    </button> -->
                </div>
        	</div>
        	
        	
           <h4 class="fw-300 mb-3">GBP POSTING</h4>
        	<div class="card-body">
        		<!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-3">
                    	<label>Month</label>
                    	<select class="form-control" v-model="gbpFilters.month">
                    	<option value="">All</option>
                    	<option v-for="month in uniqueMonths" :key="month" :value="month">{{ month }}</option>
                    	</select>
                    </div>
                    <div class="col-md-3">
                    	<label>Brand</label>
                    	<select class="form-control" v-model="gbpFilters.brand">
                    	<option value="">All</option>
                    	<option v-for="brand in uniqueBrands" :key="brand" :value="brand">{{ brand }}</option>
                    	</select>
                    </div>
                </div>
                    
                    <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                    	<thead class="table-light">
                    	<tr>
                    		<th>Date</th>
                    		<th>Brand</th>
                    		<th>Link (Proof of Work)</th>
                    		<th>Remarks</th>
                    	</tr>
                    	</thead>
                    	<tbody>
                    	<tr v-for="item in filteredGBPWithLocation">
                    		<td>{{ item.date }}</td>
                    		<td>{{ item.brand }}</td>
                    		<td>{{ item.link }}</td>
                    		<td>{{ item.remarks }}</td>
                    	</tr>
                    	</tbody>
                    </table>
                    </div>
                    
                    <div class="mt-4 flex gap-2">
                        <button @click="currentPage--" :disabled="currentPage <= 1">Prev</button>
                        <span>Page {{ currentPage }} of {{ totalGBPWithLocationPages }}</span>
                        <button @click="currentPage++" :disabled="currentPage >= totalGBPWithLocationPages">Next</button>
                    </div>
                </div>
        	</div>
        	
        	
        	
           <h4 class="fw-300 mb-3">GBP REVIEWS</h4>
        	<div class="card-body">
        		<!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-3">
                    	<label>Month</label>
                    	<select class="form-control" v-model="gbpFilters.month">
                    	<option value="">All</option>
                    	<option v-for="month in uniqueMonths" :key="month" :value="month">{{ month }}</option>
                    	</select>
                    </div>
                    <div class="col-md-3">
                    	<label>Brand</label>
                    	<select class="form-control" v-model="gbpFilters.brand">
                    	<option value="">All</option>
                    	<option v-for="brand in uniqueBrands" :key="brand" :value="brand">{{ brand }}</option>
                    	</select>
                    </div>
                </div>
                    
                    <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                    	<thead class="table-light">
                    	<tr>
                    		<th>Date</th>
                    		<th>Brand</th>
                    		<th>Link (Proof of Work)</th>
                    		<th>Remarks</th>
                    	</tr>
                    	</thead>
                    	<tbody>
                    	<tr v-for="item in filteredClientData">
                    		<td>{{ item.date }}</td>
                    		<td>{{ item.brand }}</td>
                    		<td>{{ item.link }}</td>
                    		<td>{{ item.remarks }}</td>
                    	</tr>
                    	</tbody>
                    </table>
                    </div>
                    
                    <div class="mt-4 flex gap-2">
                        <button @click="currentPage--" :disabled="currentPage <= 1">Prev</button>
                        <span>Page {{ currentPage }} of {{ totalGBPWithLocationPages }}</span>
                        <button @click="currentPage++" :disabled="currentPage >= totalGBPWithLocationPages">Next</button>
                    </div>
                </div>
        	</div>
        </div>

    </div>
</section>
