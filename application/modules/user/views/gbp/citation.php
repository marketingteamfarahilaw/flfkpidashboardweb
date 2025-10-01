<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<style>
    td, .nav-tabs .nav-link {
        font-size: 18px;
    }
</style>

<script type="text/javascript">
    $(document).ready(function () {
        new Vue({
            el: '#app',
            data: {
                citationsummary: [],
                pieChartInstance: null,
                
                
                gbpData: [],
                activeTab: '',
                linkStatusMap: {},
                tabSearch: {},        // { [local]: 'search term' }
                tabPage: {},          // { [local]: 1 }
                itemsPerPage: 20,
            },
            computed: {
                groupedGBPData() {
                    const grouped = {};
                    this.gbpData.forEach(item => {
                        console.log(item.location);
                        const local = item.location || 'Unknown';
                        if (!grouped[local]) grouped[local] = [];
                            grouped[local].push(item);
                    });
                    return grouped;
                },
                
                filteredTabData() {
                    const search = (this.tabSearch[this.activeTab] || '').toLowerCase();
                    const all = this.groupedGBPData[this.activeTab] || [];
                    console.log(all);
                    return all.filter(item =>
                        item.url.toLowerCase().includes(search)
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
            },
            watch: {},
            methods: {
                fetchGBPData: async function () {
                    let response = axios.get('http://31.97.43.196/kpidashboardapi/kpi/showcitation', CONFIG.HEADER);

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
                
                async checkAllLinks() {
                    for (const item of this.gbpData) {
                        this.$set(this.linkStatusMap, item.url, null); // loading state
                        await this.checkLinkViaBackend(item.url);
                    }
                },
                
                async checkLinkViaBackend(url) {
                    try {
                        const encoded = encodeURIComponent(url);
                        const res = await axios.get(`http://31.97.43.196/kpidashboardapi/kpi/checkLinkStatus?url=${encoded}`, CONFIG.HEADER);
                        if (res.data && typeof res.data.isLive !== 'undefined') {
                            this.$set(this.linkStatusMap, url, res.data.isLive);
                        }
                    } catch (e) {
                        this.$set(this.linkStatusMap, url, false);
                    }
                },
                
                fetchCitation: async function () {
                    let response = axios.get('http://31.97.43.196/kpidashboardapi/kpi/showcitationsummary', CONFIG.HEADER);

                    return response;
                },
                
                setCitationSummary: async function () {
                    try {
                        const result = await this.fetchCitation();
                        this.citationsummary = result.data.response;
                        
                        this.citationsummary = result.data.response.sort((a, b) => b.citation_score - a.citation_score);
                        console.log(this.citationsummary);
                
                    } catch (error) {
                        console.log(error);
                    }
                },
                renderSummaryCharts() {
                    // Destroy existing charts if any
                    if (this.pieChartInstance) {
                        this.pieChartInstance.forEach(chart => chart.destroy());
                    }
                    this.pieChartInstance = [];
                
                    this.$nextTick(() => {
                        this.citationsummary.forEach((item, index) => {
                            const canvasId = `summaryChart-${index}`;
                            const ctx = document.getElementById(canvasId).getContext('2d');
                
                            const listedPercentage = parseFloat(item.listed_percentage);
                            const remainingPercentage = 100 - listedPercentage;
                            
                            const chart = new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: [
                                        `Listed Percentage: ${listedPercentage}%`,
                                        `Remaining: ${remainingPercentage}%`
                                    ],
                                    datasets: [{
                                        data: [listedPercentage, remainingPercentage],
                                        backgroundColor: ['#2066a8', '#8cc5e3']
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        title: {
                                            display: true,
                                            font: {
                                                size: 18,
                                                weight: 'normal'
                                            },
                                            text: [
                                                `Listed Percentage: ${listedPercentage}%`,
                                                `Citation Score: ${item.citation_score}`,
                                                `Total Sites: ${item.total_sites}`,
                                                `Listed Sites: ${item.listed_sites}`
                                            ]
                                        }
                                    }
                                }
                            });
                
                            this.pieChartInstance.push(chart);
                        });
                    });
                },

                
                preparePieChartData() {
                    // Count how many reports per person from filteredData
                    const performerCounts = {};
                
                    this.citationsummary.forEach(item => {
                        const name = item.performed_by || 'Unknown';
                        if (!performerCounts[name]) performerCounts[name] = 0;
                        performerCounts[name]++;
                    });
                
                    const labels = Object.keys(performerCounts);
                    const counts = Object.values(performerCounts);
                
                    return {
                    labels: labels,
                        datasets: [{
                            label: 'Reports',
                            backgroundColor: '#42a5f5',
                            data: counts
                        }]
                    };
                },
                
                
            },
            mounted() {
                this.setCitationSummary().then(() => {
                    this.renderSummaryCharts(); // Render charts once summary loads
                });
                
                this.setGBP();
                
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
                	  	<h2 class="fw-300 mb-3">Citation Listings</h2>
                  
                		<!-- Tabs -->
                		<ul class="nav nav-tabs mb-3">
                			<li class="nav-item" v-for="(records, location) in groupedGBPData" :key="location">
                			<a href="#" class="nav-link"
                				:class="{ active: activeTab === location }"
                				@click.prevent="activeTab = location">
                				{{ location }} ({{ records.length }})
                			</a>
                			</li>
                		</ul>
                  
                		<!-- Search bar -->
                		<div class="mb-3">
                			<input type="text" class="form-control"
                				v-model="tabSearch[activeTab]"
                				placeholder="Search this location...">
                		</div>
                  
                		<!-- Table -->
                		<div class="table-responsive" v-if="paginatedTabData.length">
                			<table class="table table-bordered table-sm">
                			<thead class="table-light">
                				<tr>
                				<th>Domain</th>
                				<th>Date</th>
                				<th>Address</th>
                				<th>Phone</th>
                				<th>Site Type</th>
                				<th>Link</th>
                				<th>Status</th>
                				</tr>
                			</thead>
                			<tbody>
                				<tr v-for="item in paginatedTabData">
                				<td>{{ item.url }}</td>
                				<td>{{ item.created_at }}</td>
                				<td>{{ item.address }}</td>
                				<td>{{ item.phone }}</td>
                				<td>{{ item.site_type }}</td>
                				<td>
                					<a :href="item.url" target="_blank" rel="noopener noreferrer"
                					:class="{
                						'text-danger fw-bold': linkStatusMap[item.url] === false,
                						'text-muted': linkStatusMap[item.url] === null,
                						'text-primary': linkStatusMap[item.url] !== false
                					}">
                					Visit
                					</a>
                				</td>
                				<td>
                					<span class="badge bg-success text-uppercase" v-if="item.status === 'active'">{{ item.status }}</span>
                					<span class="badge bg-secondary" v-else>{{ item.status }}</span>
                				</td>
                				</tr>
                			</tbody>
                			</table>
                  
                			<!-- Pagination Controls -->
                			<div class="d-flex justify-content-between align-items-center mt-2">
                				<button class="btn btn-sm btn-outline-secondary"
                						@click="tabPage[activeTab]--"
                						:disabled="tabPage[activeTab] <= 1">
                					Previous
                				</button>
                				<span>Page {{ tabPage[activeTab] }} of {{ totalPages }}</span>
                				<button class="btn btn-sm btn-outline-secondary"
                						@click="tabPage[activeTab]++"
                						:disabled="tabPage[activeTab] >= totalPages">
                					Next
                				</button>
                			</div>
                	  </div>
                  
            	        <div v-else class="text-muted">No results found for this location.</div>
                	</div>
                </div>
                <div class="card mb-4">
                	<div class="card-body">    
                        <h2 class="fw-300 mb-3">Citation Tracker</h2>
                        <div class="row">
                            <div class="col-md-4 mb-4" v-for="(item, index) in citationsummary" :key="index">
                                <div class="card p-3 shadow-sm">
                                    <h5 class="text-center mb-2">{{ item.location }}</h5>
                                    <canvas :id="`summaryChart-${index}`" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <!--<div class="row">-->
                        <!--    <div class="col-md-4 mb-4" v-for="item in citationsummary">-->
                        <!--        <h5 class="text-center">Key Citation Score - {{ item.location }} </h5>-->
                        <!--        <h3 class="text-center">{{ item.citation_score }}/100 </h3>-->
                        <!--        <p class="text-center">Total Site: {{ item.total_sites }}/100 </p>-->
                        <!--        <p class="text-center">Listed Sites: {{ item.listed_sites }}/100 </p>-->
                        <!--        <p class="text-center">Listed Percentage: {{ item.listed_percentage }}% </p>-->
                        <!--    </div>-->
                        <!--</div>-->
            			<!--</table>-->
                    </div>
                </div>
            </div>
        </div>
    </section>