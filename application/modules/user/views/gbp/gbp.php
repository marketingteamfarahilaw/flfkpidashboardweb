<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>


<script type="text/javascript">
    $(document).ready(function () {
        new Vue({
            el: '#app',
            data: {
                gbpData: [],
                activeTab: '',
                linkStatusMap: {},
                tabSearch: {},        // { [local]: 'search term' }
                tabPage: {},          // { [local]: 1 }
                itemsPerPage: 20,
                
                gbpTask: [],
                searchQuery: '',
                filterYear: '',
                filterMonth: '',
                filterTeam: '',
                filterMainFunction: '',
                selectedTeam: null,
                detailCurrentPage: 1,
                itemsPerPage: 10,
                
                contentData: [],
                filters: {
                    report: '',
                    month: '',
                    language: '',
                    brand: ''
                },
                currentPage: 1,
                itemsPerPage: 20,
                
                clientData: [],
                clientFilters: {
                    month: '',
                    brand: ''
                },
                currentClientPage: 1,
                itemsClientPerPage: 20,
                
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
                
                seoLinkBuilding: [],
            	searchQuery: '',
            	filterMonth: '',
            	filterYear: '',
            	filterBrand: '',
            	filterStatus: '',
            	expanded: {},
            	months: [
                	{ value: '01', text: 'January' },
                	{ value: '02', text: 'February' },
                	{ value: '03', text: 'March' },
                	{ value: '04', text: 'April' },
                	{ value: '05', text: 'May' },
                	{ value: '06', text: 'June' },
                	{ value: '07', text: 'July' },
                	{ value: '08', text: 'August' },
                	{ value: '09', text: 'September' },
                	{ value: '10', text: 'October' },
                	{ value: '11', text: 'November' },
                	{ value: '12', text: 'December' }
            	],
            	
            	
            // 	SEO Index Monitoring
            	filterMonitoringYear: '',
            	indexmonitoring: [],
                activeMonitoringTab: '',
                tabMonitoringSearch: {},        // { [local]: 'search term' }
                tabMonitoringPage: {},          // { [local]: 1 }
                itemsMonitoringPerPage: 20,
                
                
                gbpEOW: '',
            },
            computed: {
                groupedGBPData() {
                    const grouped = {};
                    this.gbpData.forEach(item => {
                        const local = item.location || 'Unknown';
                        if (!grouped[local]) grouped[local] = [];
                        grouped[local].push(item);
                    });
                    return grouped;
                },
            
                filteredTabData() {
                    const search = (this.tabSearch[this.activeTab] || '').toLowerCase();
                    const all = this.groupedGBPData[this.activeTab] || [];
                    return all.filter(item =>
                        (item.url || '').toLowerCase().includes(search)
                    );
                },
            
                paginatedTabData() {
                    const page = this.tabPage[this.activeTab] || 1;
                    const start = (page - 1) * this.itemsPerPage;
                    return this.filteredTabData.slice(start, start + this.itemsPerPage);
                },
            
                totalPages() {
                    return Math.ceil(this.filteredTabData.length / this.itemsPerPage);
                },
            
                // SEO TASK (Optimization)
                filteredTasks() {
                    return this.gbpTask.filter(item => {
                        const date = new Date(item.date);
                        return (!this.searchQuery || (item.gbpTask || '').toLowerCase().includes(this.searchQuery.toLowerCase())) &&
                            (!this.filterYear || date.getFullYear().toString() === this.filterYear) &&
                            (!this.filterMonth || (date.getMonth() + 1).toString() === this.filterMonth) &&
                            (!this.filterMainFunction || item.mainFunction === this.filterMainFunction) &&
                            (!this.filterTeam || item.seoTeam === this.filterTeam);
                    });
                },
            
                groupedData() {
                    return this.filteredTasks.reduce((acc, item) => {
                        const team = item.seoTeam || 'Unknown';
                        if (!acc[team]) acc[team] = [];
                        acc[team].push(item);
                        return acc;
                    }, {});
                },
            
                paginatedDetails() {
                    if (!this.selectedTeam) return [];
                    const items = this.groupedData[this.selectedTeam];
                    const start = (this.detailCurrentPage - 1) * this.itemsPerPage;
                    return items.slice(start, start + this.itemsPerPage);
                },
            
                detailTotalPages() {
                    return this.selectedTeam ? Math.ceil(this.groupedData[this.selectedTeam].length / this.itemsPerPage) : 1;
                },
            
                availableYears() {
                    return Array.from(new Set(this.gbpTask.map(task => new Date(task.date).getFullYear()))).sort();
                },
            
                availableMonths() {
                    return Array.from({ length: 12 }, (_, i) => (i + 1).toString());
                },
            
                availableTeams() {
                    return Array.from(new Set(this.gbpTask.map(task => task.seoTeam)));
                },
            
                availableMainFunction() {
                    return Array.from(new Set(this.gbpTask.map(task => task.mainFunction)));
                },
            
                // Link Building
                filteredData() {
                    return this.seoLinkBuilding.filter(item => {
                        const itemMonth = item.postingDate.slice(5, 7);
                        const itemYear = item.postingDate.slice(0, 4);
                        const matchesSearch = this.searchQuery ? (item.website || '').toLowerCase().includes(this.searchQuery.toLowerCase()) : true;
                        const matchesMonth = this.filterMonth ? itemMonth === this.filterMonth : true;
                        const matchesYear = this.filterYear ? itemYear === this.filterYear : true;
                        const matchesBrand = this.filterBrand ? item.brand === this.filterBrand : true;
                        const matchesStatus = this.filterStatus ? item.status === this.filterStatus : true;
                        return matchesSearch && matchesMonth && matchesYear && matchesBrand && matchesStatus;
                    });
                },
            
                uniqueBrands() {
                    return [...new Set(this.seoLinkBuilding.map(item => item.brand))];
                },
            
                uniqueStatuses() {
                    return [...new Set(this.seoLinkBuilding.map(item => item.status))];
                },
            
                uniqueYears() {
                    return [...new Set(this.seoLinkBuilding.map(item => item.postingDate.slice(0, 4)))];
                },
            
                groupedLBData() {
                    const groups = {};
                    this.filteredData.forEach(item => {
                        const key = `${item.brand}-${item.status}`;
                        if (!groups[key]) {
                            groups[key] = { brand: item.brand, status: item.status, items: [] };
                        }
                        groups[key].items.push(item);
                    });
                    return Object.values(groups);
                },
            
                groupedMonitoringData() {
                    const grouped = {};
                    const monthNames = [
                        "January", "February", "March", "April", "May", "June",
                        "July", "August", "September", "October", "November", "December"
                    ];
            
                    this.indexmonitoring.forEach(item => {
                        const monthIndex = parseInt(item.datePosted.slice(5, 7), 10) - 1;
                        const itemMonth = monthNames[monthIndex] || 'Unknown';
                        if (!grouped[itemMonth]) grouped[itemMonth] = [];
                        grouped[itemMonth].push(item);
                    });
            
                    const orderedGrouped = {};
                    monthNames.forEach(month => {
                        if (grouped[month]) {
                            orderedGrouped[month] = grouped[month];
                        }
                    });
            
                    return orderedGrouped;
                },
            
                filteredMonitoringTabData() {
                    const search = (this.tabMonitoringSearch[this.activeMonitoringTab] || '').toLowerCase();
                    const all = this.groupedMonitoringData[this.activeMonitoringTab] || [];
            
                    return all.filter(item =>
                        (item.link || '').toLowerCase().includes(search)
                    );
                },
            
                paginatedMonitoringTabData() {
                    const page = this.tabMonitoringPage[this.activeMonitoringTab] || 1;
                    const start = (page - 1) * this.itemsMonitoringPerPage;
                    return this.filteredMonitoringTabData.slice(start, start + this.itemsMonitoringPerPage);
                },
            
                totalMonitoringPages() {
                    return Math.ceil(this.filteredMonitoringTabData.length / this.itemsMonitoringPerPage);
                },
            
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
                filterYear() { this.selectedTeam = null; },
                filterMonth() { this.selectedTeam = null; },
                filterTeam() { this.selectedTeam = null; },
                filterMainFunction() { this.selectedTeam = null; },
                searchQuery() { this.selectedTeam = null; },
                
                gbpFilters: {
                    handler() { this.currentGBPPage = 1; },
                    deep: true
                },
            },
            methods: {
                fetchTasks: async function () {
                    let response = axios.get('https://lmthrp.com/api/kpi/fetchGBPTask', CONFIG.HEADER);
                    
                    return response;
                },
                setGBPTask: async function () {
                    try {
                        const result = await this.fetchTasks();
                        this.gbpTask = result.data.response;
                    } catch (error) {
                        console.log(error);
                    }
                },
                
                toggleDetails(team) {
                    this.selectedTeam = this.selectedTeam === team ? null : team;
                    this.detailCurrentPage = 1;
                },
                fetchGBPData: async function () {
                    let response = axios.get('https://lmthrp.com/api/kpi/showcitation', CONFIG.HEADER);

                    return response;
                },
                setGBP: async function () {
                    try {
                        const result = await this.fetchGBPData();
                        this.gbpData = result.data.response;
                
                        // Set first tab as active
                        const locations = Object.keys(this.groupedGBPData);
                        if (locations.length) {
                            this.activeTab = locations[0];
                        }
                        
                        // Initialize search & pagination state per tab
                        locations.forEach(loc => {
                            this.$set(this.tabSearch, loc, '');
                            this.$set(this.tabPage, loc, 1);
                        });
                        
                        await this.checkAllLinks();
                    } catch (error) {
                        console.log(error);
                    }
                },
                
                // async checkAllLinks() {
                //     for (const item of this.gbpData) {
                //         this.$set(this.linkStatusMap, item.url, null); // loading state
                //         await this.checkLinkViaBackend(item.url);
                //     }
                // },
                
                // async checkLinkViaBackend(url) {
                //     try {
                //         const encoded = encodeURIComponent(url);
                //         const res = await axios.get(`https://lmthrp.com/api/kpi/checkLinkStatus?url=${encoded}`, CONFIG.HEADER);
                //         if (res.data && typeof res.data.isLive !== 'undefined') {
                //             this.$set(this.linkStatusMap, url, res.data.isLive);
                //         }
                //     } catch (e) {
                //         this.$set(this.linkStatusMap, url, false);
                //     }
                // },
                
                fetchLinkBuilding: async function(){
                    let response = axios.get('https://lmthrp.com/api/kpi/fetchSEOLinkBuilding', CONFIG.HEADER);
                    
                    return response;  
                },
                setLinkBuilding: async function() {
                    try {
                        const result = await this.fetchLinkBuilding();
                        this.seoLinkBuilding = result.data.response;
                    } catch (error) {
                        console.log(error);
                    }
                },
                toggleExpand(index) {
                    this.$set(this.expanded, index, !this.expanded[index]);
                },
                
                fetchIndexMonitoring: async function(){
                    let response = axios.get('https://lmthrp.com/api/kpi/fetchIndexMonitoring', CONFIG.HEADER);
                    
                    return response;  
                },
                setIndexMonitoring: async function() {
                    try {
                        const result = await this.fetchIndexMonitoring();
                        this.indexmonitoring = result.data.response;
                        
                         // Set first tab as active
                        const date = Object.keys(this.indexmonitoring);
                        if (date.length) {
                            this.activeMonitoringTab = date[0];
                        }
                        
                        // Initialize search & pagination state per tab
                        date.forEach(dat => {
                            this.$set(this.tabMonitoringSearch, dat, '');
                            this.$set(this.tabMonitoringPage, dat, 1);
                        });
                    } catch (error) {
                        console.log(error);
                    }
                },
                
                
                async fetchKPIReports() {
                    try {
                        const res = await axios.get('https://lmthrp.com/api/kpi/show', CONFIG.HEADER);
                        this.contentData = res.data.response || [];
                    } catch (error) {
                        console.error('Failed to fetch KPI report data:', error);
                    }
                },
                
                async fetchGBPReports() {
                    try {
                        const res = await axios.get('https://lmthrp.com/api/kpi/fetchGBPTask', CONFIG.HEADER);
                        this.gbpData = res.data.response || [];
                        this.clientData = res.data.response || []; // Separate if needed
                        
                        this.gbpEOW = res.data.conclusionEOM;
                    } catch (error) {
                        console.error('Failed to fetch GBP task data:', error);
                    }
                }
            },
            mounted() {
                this.fetchKPIReports();
                this.fetchGBPReports();
                this.setGBPTask();
                this.setGBP();
                this.setLinkBuilding();
                this.setIndexMonitoring();
            }
        });
    });



</script>

    <section class="content" id="app">
        <div class="container-fluid pt-3 pl-md-2 pr-md-2 pl-md-5 pr-md-5">
          
            <div>
              
                <!-- 📍 GBP Listings in Tabs -->
                <div class="card mb-4">
                	<div class="card-body">
                	  	<h2 class="fw-300 mb-3">GBP - SEO Optimization</h2>
                	  	
                        <h5>{{gbpEOW}}</h5>
                        <!-- Filters -->
                        <div class="mb-3">
                            <input type="text" class="form-control mb-2" v-model="searchQuery" placeholder="Search SEO Task">
                    
                            <div class="d-flex gap-2 mb-2">
                                
                    
                                <!--<select class="form-select form-control" v-model="filterFunction">-->
                                <!--    <option value="">Function</option>-->
                                <!--    <option v-for="month in availableMonths" :key="month">{{ function }}</option>-->
                                <!--</select>-->
                                
                                <select class="form-select form-control" v-model="filterYear">
                                    <option value="">All Years</option>
                                    <option v-for="year in availableYears" :key="year">{{ year }}</option>
                                </select>
                    
                                <select class="form-select form-control" v-model="filterMonth">
                                    <option value="">All Months</option>
                                    <option v-for="month in availableMonths">{{ month }}</option>
                                </select>
                    
                                <select class="form-select form-control" v-model="filterMainFunction">
                                    <option value="">All Functions</option>
                                    <option v-for="mainFunction in availableMainFunction" :key="mainFunction">{{ mainFunction }}</option>
                                </select>
                            </div>
                        </div>
                    
                        <!-- Summary Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>SEO Team</th>
                                        <th>Task Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(tasks, team) in groupedData" :key="team" @click="toggleDetails(team)" style="cursor:pointer">
                                        <td>{{ team }}</td>
                                        <td>{{ tasks.length }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    
                        <!-- Detailed Table -->
                        <div class="table-responsive" v-if="selectedTeam">
                            <h5>{{ selectedTeam }} - Task Details</h5>
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Main Function</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                        <th>Link to Outputs</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in paginatedDetails">
                                        <td>{{ item.date }}</td>
                                        <td>{{ item.mainFunction }}</td>
                                        <td>{{ item.quantity }}</td>
                                        <td>{{ item.status }}</td>
                                        <td><a :href="item.link" target="_blank">View Output</a></td>
                                    </tr>
                                </tbody>
                            </table>
                    
                            <!-- Pagination for details -->
                            <nav>
                                <ul class="pagination">
                                    <li class="page-item" :class="{disabled: detailCurrentPage === 1}">
                                        <button class="page-link" @click="detailCurrentPage--">Previous</button>
                                    </li>
                                    <li class="page-item" v-for="page in detailTotalPages" :key="page" :class="{active: page === detailCurrentPage}">
                                        <button class="page-link" @click="detailCurrentPage = page">{{ page }}</button>
                                    </li>
                                    <li class="page-item" :class="{disabled: detailCurrentPage === detailTotalPages}">
                                        <button class="page-link" @click="detailCurrentPage++">Next</button>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                	</div>
                </div>
                <!-- 📍 GBP Listings in Tabs -->
                <!--<div class="card mb-4">-->
                <!--	<div class="card-body">-->
                <!--	  	<h4 class="fw-300 mb-3">Google Business Profile (GBP) Listings</h4>-->
                        <!-- Tabs -->
                <!--		<ul class="nav nav-tabs mb-3">-->
                <!--			<li class="nav-item" v-for="(records, location) in groupedGBPData" :key="location">-->
                <!--			<a href="#" class="nav-link"-->
                <!--				:class="{ active: activeTab === location }"-->
                <!--				@click.prevent="activeTab = location">-->
                <!--				{{ location }} ({{ records.length }})-->
                <!--			</a>-->
                <!--			</li>-->
                <!--		</ul>-->
                  
                		<!-- Search bar -->
                <!--		<div class="mb-3">-->
                <!--			<input type="text" class="form-control"-->
                <!--				v-model="tabSearch[activeTab]"-->
                <!--				placeholder="Search this location...">-->
                <!--		</div>-->
                  
                		<!-- Table -->
                <!--		<div class="table-responsive" v-if="paginatedTabData.length">-->
                <!--			<table class="table table-bordered table-sm">-->
                <!--			<thead class="table-light">-->
                <!--				<tr>-->
                <!--				<th>Domain</th>-->
                <!--				<th>Date</th>-->
                <!--				<th>Address</th>-->
                <!--				<th>Phone</th>-->
                <!--				<th>Site Type</th>-->
                <!--				<th>Link</th>-->
                <!--				<th>Status</th>-->
                <!--				</tr>-->
                <!--			</thead>-->
                <!--			<tbody>-->
                <!--				<tr v-for="item in paginatedTabData">-->
                <!--				<td>{{ item.url }}</td>-->
                <!--				<td>{{ item.created_at }}</td>-->
                <!--				<td>{{ item.address }}</td>-->
                <!--				<td>{{ item.phone }}</td>-->
                <!--				<td>{{ item.site_type }}</td>-->
                <!--				<td>-->
                <!--					<a :href="item.url" target="_blank" rel="noopener noreferrer"-->
                <!--					:class="{-->
                <!--						'text-danger fw-bold': linkStatusMap[item.url] === false,-->
                <!--						'text-muted': linkStatusMap[item.url] === null,-->
                <!--						'text-primary': linkStatusMap[item.url] !== false-->
                <!--					}">-->
                <!--					Visit-->
                <!--					</a>-->
                <!--				</td>-->
                <!--				<td>-->
                <!--					<span class="badge bg-success text-uppercase" v-if="item.status === 'active'">{{ item.status }}</span>-->
                <!--					<span class="badge bg-secondary" v-else>{{ item.status }}</span>-->
                <!--				</td>-->
                <!--				</tr>-->
                <!--			</tbody>-->
                <!--			</table>-->
                  
                			<!-- Pagination Controls -->
                <!--			<div class="d-flex justify-content-between align-items-center mt-2">-->
                <!--				<button class="btn btn-sm btn-outline-secondary"-->
                <!--						@click="tabPage[activeTab]--"-->
                <!--						:disabled="tabPage[activeTab] <= 1">-->
                <!--					Previous-->
                <!--				</button>-->
                <!--				<span>Page {{ tabPage[activeTab] }} of {{ totalPages }}</span>-->
                <!--				<button class="btn btn-sm btn-outline-secondary"-->
                <!--						@click="tabPage[activeTab]++"-->
                <!--						:disabled="tabPage[activeTab] >= totalPages">-->
                <!--					Next-->
                <!--				</button>-->
                <!--			</div>-->
                <!--	  </div>-->
                  
            	   <!--     <div v-else class="text-muted">No results found for this location.</div>-->
                <!--	</div>-->
                <!--</div>-->
                

                <!--<div class="card mb-4">-->
                <!--	<div class="card-body">    -->
                <!--        <h4 class="fw-300 mb-3">GBP Performance Monitoring</h4>-->
                        
                <!--    </div>-->
                <!--</div>-->
                <!--<div class="card mb-4">-->
                <!--	<div class="card-body">-->
                <!--        <h4 class="fw-300 mb-3">GBP Reviews Monitoring</h4>-->
                <!--        <div class="card-body">-->
                    		<!-- Filters -->
                <!--            <div class="row mb-3">-->
                <!--                <div class="col-md-3">-->
                <!--                	<label>Month</label>-->
                <!--                	<select class="form-control" v-model="gbpFilters.month">-->
                <!--                	<option value="">All</option>-->
                <!--                	<option v-for="month in uniqueMonths" :key="month" :value="month">{{ month }}</option>-->
                <!--                	</select>-->
                <!--                </div>-->
                <!--                <div class="col-md-3">-->
                <!--                	<label>Brand</label>-->
                <!--                	<select class="form-control" v-model="gbpFilters.brand">-->
                <!--                	<option value="">All</option>-->
                <!--                	<option v-for="brand in uniqueBrands" :key="brand" :value="brand">{{ brand }}</option>-->
                <!--                	</select>-->
                <!--                </div>-->
                <!--            </div>-->
                                
                                <!-- Table -->
                <!--            <div class="table-responsive">-->
                <!--                <table class="table table-bordered table-sm">-->
                <!--                	<thead class="table-light">-->
                <!--                	<tr>-->
                <!--                		<th>Date</th>-->
                <!--                		<th>Brand</th>-->
                <!--                		<th>Link (Proof of Work)</th>-->
                <!--                		<th>Remarks</th>-->
                <!--                	</tr>-->
                <!--                	</thead>-->
                <!--                	<tbody>-->
                <!--                	<tr v-for="item in filteredClientData">-->
                <!--                		<td>{{ item.date }}</td>-->
                <!--                		<td>{{ item.brand }}</td>-->
                <!--                		<td>{{ item.link }}</td>-->
                <!--                		<td>{{ item.remarks }}</td>-->
                <!--                	</tr>-->
                <!--                	</tbody>-->
                <!--                </table>-->
                <!--                </div>-->
                                
                <!--                <div class="mt-4 flex gap-2">-->
                <!--                    <button @click="currentPage--" :disabled="currentPage <= 1">Prev</button>-->
                <!--                    <span>Page {{ currentPage }} of {{ totalGBPWithLocationPages }}</span>-->
                <!--                    <button @click="currentPage++" :disabled="currentPage >= totalGBPWithLocationPages">Next</button>-->
                <!--                </div>-->
                <!--            </div>-->
                <!--    	</div>-->
                <!--    </div>-->
                <!--</div>-->
                    
                <div class="card mb-4">
                	<div class="card-body">
                	  	<h4 class="fw-300 mb-3">SEO Link Building</h4>
                	  	
            	  		<!-- Filters -->
                    	<div class="filters mb-3">
                    		<input type="text" class="form-control mb-2" v-model="searchQuery" placeholder="Search by website">
                            <div class="d-flex gap-2 mb-2">
                        		<select class="form-control mb-2" v-model="filterMonth">
                            		<option value="">All Months</option>
                            		<option v-for="month in months" :key="month.value">{{ month.text }}</option>
                        		</select>
                        
                        		<select class="form-control mb-2" v-model="filterYear">
                            		<option value="">All Years</option>
                            		<option v-for="year in uniqueYears" :key="year">{{ year }}</option>
                        		</select>
                        
                        		<select class="form-control mb-2" v-model="filterBrand">
                            		<option value="">All Brands</option>
                            		<option v-for="brand in uniqueBrands" :key="brand">{{ brand }}</option>
                        		</select>
                        
                        		<select class="form-control" v-model="filterStatus">
                            		<option value="">All Statuses</option>
                            		<option v-for="status in uniqueStatuses" :key="status">{{ status }}</option>
                        		</select>
                        	</div>
                    	</div>
                    
                    	<!-- Grouped Table -->
                    	<div class="table-responsive" v-if="groupedLBData.length">
                    		<table class="table table-bordered table-sm">
                    		<thead class="table-light">
                    			<tr>
                    			<th></th>
                    			<th>Brand</th>
                    			<th>Status</th>
                    			<th>Total</th>
                    			</tr>
                    		</thead>
                    		<tbody>
                    			<template v-for="(group, index) in groupedLBData">
                    			<tr @click="toggleExpand(index)" style="cursor: pointer;">
                    				<td>{{ expanded[index] ? '-' : '+' }}</td>
                    				<td>{{ group.brand }}</td>
                    				<td>{{ group.status }}</td>
                    				<td>{{ group.items.length }}</td>
                    			</tr>
                    			<tr v-if="expanded[index]">
                    				<td colspan="4">
                    				<table class="table table-sm table-bordered">
                    					<thead class="table-secondary">
                    					<tr>
                    						<th>Posting Date</th>
                    						<th>Website</th>
                    						<th>Content Source</th>
                    					</tr>
                    					</thead>
                    					<tbody>
                    					<tr v-for="item in group.items" :key="item.id">
                    						<td>{{ item.postingDate }}</td>
                    						<td>{{ item.website }}</td>
                    						<td>{{ item.contentSource }}</td>
                    					</tr>
                    					</tbody>
                    				</table>
                    				</td>
                    			</tr>
                    			</template>
                    		</tbody>
                    		</table>
                    	</div>
                    
                    	<p v-else>No records found.</p>
                	</div>
                </div>
                
                <!--<div class="card mb-4">-->
                <!--	<div class="card-body">-->
                <!--	    <h4 class="fw-300 mb-3">SEO Reporting</h4>-->
                <!--	</div>-->
                <!--</div>-->
                <div class="card mb-4">
                	<div class="card-body">
                	    <h4 class="fw-300 mb-3">SEO Blog Indexation Monitoring</h4>
                	    <!-- Tabs -->
                		<ul class="nav nav-tabs mb-3">
                            <li class="nav-item" v-for="(records, datePosted) in groupedMonitoringData" :key="datePosted">
                                <a href="#" class="nav-link"
                                    :class="{ active: activeMonitoringTab === datePosted }"
                                    @click.prevent="activeMonitoringTab = datePosted">
                                    {{ datePosted }}
                                </a>
                            </li>
                        </ul>
                		
                  
                		<!-- Search bar -->
                		<div class="mb-3">
                			<input type="text" class="form-control"
                				v-model="tabMonitoringSearch[activeMonitoringTab]"
                				placeholder="Search this location...">
                				
                				
                        		<select class="form-control mb-2 col-md-3" v-model="filterMonitoringYear">
                            		<option value="">All Years</option>
                            		<option v-for="year in uniqueYears" :key="year">{{ year }}</option>
                        		</select>
                		</div>
                		
                  
                		<!-- Table -->
                		<div class="table-responsive">
                			<table class="table table-bordered table-sm">
                    			<thead class="table-light">
                    				<tr>
                    				<th>Date Posted</th>
                    				<th>Link</th>
                    				<th>Type</th>
                    				<th>Status</th>
                    				</tr>
                    			</thead>
                    			<tbody>
                    				<tr v-for="item in paginatedMonitoringTabData">
                    				<td>{{ item.datePosted }}</td>
                    				<td>{{ item.link }}</td>
                    				<td>{{ item.type }}</td>
                    				<td>{{ item.indexed }}</td>
                    				</tr>
                    			</tbody>
                			</table>
                  
                			<!-- Pagination Controls -->
                			<div class="d-flex justify-content-between align-items-center mt-2">
                				<button class="btn btn-sm btn-outline-secondary"
                						@click="tabMonitoringPage[activeMonitoringTab]--"
                						:disabled="tabMonitoringPage[activeMonitoringTab] <= 1">
                					Previous
                				</button>
                				<span>Page {{ tabMonitoringPage[activeMonitoringTab] }} of {{ totalMonitoringPages }}</span>
                				<button class="btn btn-sm btn-outline-secondary"
                						@click="tabMonitoringPage[activeMonitoringTab]++"
                						:disabled="tabMonitoringPage[activeMonitoringTab] >= totalMonitoringPages">
                					Next
                				</button>
                			</div>
                			
                	  </div>
                  
                	</div>
                </div>
            
            </div>
        </div>
    </section>