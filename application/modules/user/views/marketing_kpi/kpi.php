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
        
            selectedRow: {
                person: '',
                report: '',
            },
            detailSearch: '',
            detailPage: 1,
            detailPerPage: 10,
            profileData: [],
            groupedData: {},
            filters: {
                search: '',
                report: '',
                month: '',
                brand: '',
                date: '',
                platform: '',
            },

            uniqueOptions: {
                reports: [],
                months: [],
                brands: [],
                dates: [],
                platforms: [],
            },
            barChartInstance: null,
            targetData: {
                'Blog Optimized': 25,
                'Blog Published': 50,
                'News Published': 20,
                'Web App Developed' : 20,
                'Web App Optimized': 20,
                'Landing Page Developed': 10,
                'Landing Page Optimized': 25
            },
            
            webdevEOW: '',
        },
        computed: {
            
            uniqueCompletedMonths() {
                const months = new Set();
                this.allAsanaTasks.forEach(task => {
                if (task.completed_at) {
                    const month = new Date(task.completed_at).toLocaleString('default', { month: 'long' });
                    months.add(month);
                }
                });
                return Array.from(months);
            },
            
            uniqueAssignees() {
                const names = new Set(this.allAsanaTasks.map(task => task.project || 'Unassigned'));
                return Array.from(names);
            },
            
            
            uniqueMonths() {
                const months = new Set();
                this.profileData.forEach(item => {
                if (item.report === 'TLC') {
                    const month = new Date(item.date).toLocaleString('default', { month: 'long' });
                    months.add(month);
                }
                });
                return Array.from(months);
            },
            uniqueBrands() {
                const brands = new Set();
                this.profileData.forEach(item => {
                if (item.report === 'TLC') {
                    brands.add(item.brand);
                }
                });
                return Array.from(brands);
            },
            uniquePlatforms() {
                const platforms = new Set();
                this.profileData.forEach(item => {
                if (item.report === 'TLC') {
                    platforms.add(item.platform);
                }
                });
                return Array.from(platforms);
            },
            
            uniquePerformedBy() {
                const names = new Set();
                this.profileData.forEach(item => {
                if (item.report === 'TLC') {
                    names.add(item.performed_by);
                }
                });
                return Array.from(names);
            },

            weeklyDataByPerson() {
                const today = new Date();
                const startOfWeek = new Date(today);
                const dayOfWeek = today.getDay(); // Sunday = 0
                const diffToMonday = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
                startOfWeek.setDate(today.getDate() - diffToMonday);
                startOfWeek.setHours(0, 0, 0, 0);
            
                const endOfWeek = new Date(startOfWeek);
                endOfWeek.setDate(startOfWeek.getDate() + 6);
                endOfWeek.setHours(23, 59, 59, 999);
            
                const result = {};
            
                this.filteredData.forEach(item => {
                const itemDate = new Date(item.date);
                if (itemDate >= startOfWeek && itemDate <= endOfWeek) {
                    const person = item.performed_by || 'Unknown';
                    const report = item.report;
            
                    if (!result[person]) {
                    result[person] = { total: 0, reports: {} };
                    }
            
                    result[person].total++;
            
                    if (!result[person].reports[report]) {
                    result[person].reports[report] = 0;
                    }
                    result[person].reports[report]++;
                }
                });
            
                return result;
            },

    
            filteredData() {
            const search = this.filters.search.toLowerCase();
            
            return this.profileData
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
                    item.report !== 'TLC' && // 👈 Filter out SocMed here
                    matchesSearch &&
                    (!this.filters.report || item.report === this.filters.report) &&
                    (!this.filters.month || monthName === this.filters.month) &&
                    (!this.filters.brand || item.brand === this.filters.brand) &&
                    (!this.filters.date || item.date === this.filters.date) &&
                    (!this.filters.platform || item.platform === this.filters.platform)
                );
                })
                .sort((a, b) => new Date(b.date) - new Date(a.date)); // latest to oldest
            },

            mtdData() {
                const currentMonth = new Date().getMonth();
                const currentYear = new Date().getFullYear();
            
                const filteredMTD = this.filteredData.filter(item => {
                    const date = new Date(item.date);
                    return date.getMonth() === currentMonth && date.getFullYear() === currentYear;
                });
            
                const totalReports = filteredMTD.length;
                const uniquePerformers = new Set(filteredMTD.map(item => item.performed_by)).size;
            
                const reportCounts = {};
                filteredMTD.forEach(item => {
                    if (!reportCounts[item.report]) {
                        reportCounts[item.report] = 0;
                    }
                    reportCounts[item.report]++;
                });
            
                return {
                    totalReports,
                    uniquePerformers,
                    reportCounts,
                };
            }

        },
        watch: {
            filteredData: {
                handler() {
                this.groupRecords();
                this.updateBarChart();
                },
                immediate: true
            },
        },
        methods: {
            toggleAssignee(assignee) {
                if (this.expandedAssignee === assignee) {
                this.expandedAssignee = null;
                } else {
                this.expandedAssignee = assignee;
                this.$set(this.assigneeDetailPage, assignee, 1); // reset to page 1
                }
            },
            tasksByAssignee(assignee) {
                return this.allAsanaTasks.filter(task => task.assignee === assignee);
            },
            
            paginatedTasksByAssignee(assignee) {
                const all = this.tasksByAssignee(assignee);
                const page = this.assigneeDetailPage[assignee] || 1;
                const start = (page - 1) * this.assigneeDetailPerPage;
                return all.slice(start, start + this.assigneeDetailPerPage);
            },
            
            totalPagesByAssignee(assignee) {
                const total = this.tasksByAssignee(assignee).length;
                return Math.ceil(total / this.assigneeDetailPerPage);
            },
            
            
            tasksByAssignee(project) {
                return this.allAsanaTasks.filter(t => t.project === project);
            },
            
            toggleDetails(person, report) {
                if (this.selectedRow.person === person && this.selectedRow.report === report) {
                    this.selectedRow = { person: '', report: '' };
                    this.detailSearch = '';
                } else {
                    this.selectedRow = { person, report };
                    this.detailPage = 1;
                    this.detailSearch = ''; // 🔄 Reset search when changing row
                }
            },
            getDetailList(person, report) {
                return this.filteredData.filter(
                    item => item.performed_by === person && item.report === report
                );
            },
            paginatedDetailList() {
                const all = this.getDetailList(this.selectedRow.person, this.selectedRow.report);
            
                const filtered = all.filter(item => {
                    const search = this.detailSearch.toLowerCase();
                    return (
                        !search ||
                        (item.date && item.date.toLowerCase().includes(search)) ||
                        (item.brand && item.brand.toLowerCase().includes(search)) ||
                        (item.title && item.title.toLowerCase().includes(search)) ||
                        (item.platform && item.platform.toLowerCase().includes(search)) ||
                        (item.notes && item.notes.toLowerCase().includes(search))
                    );
                });
            
                const start = (this.detailPage - 1) * this.detailPerPage;
                return filtered.slice(start, start + this.detailPerPage);
            },
            detailTotalPages() {
                const all = this.getDetailList(this.selectedRow.person, this.selectedRow.report);
            
                const filtered = all.filter(item => {
                    const search = this.detailSearch.toLowerCase();
                    return (
                        !search ||
                        (item.date && item.date.toLowerCase().includes(search)) ||
                        (item.brand && item.brand.toLowerCase().includes(search)) ||
                        (item.platform && item.platform.toLowerCase().includes(search)) ||
                        (item.notes && item.notes.toLowerCase().includes(search))
                    );
                });
            
                return Math.ceil(filtered.length / this.detailPerPage);
            },
            
            updateBarChart() {
                if (this.barChartInstance) {
                    const updatedData = this.prepareBarChartData();
                    this.barChartInstance.data = updatedData;
                    this.barChartInstance.update();
                }
            },
            prepareBarChartData() {
                // Count how many reports per person from filteredData
                const performerCounts = {};
            
                this.filteredData.forEach(item => {
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

            getTarget(person, report) {
                const key = `${person}-${report}`;
                return this.targetData[key] || 20; // default target if not defined
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
            exportGroupedToExcel() {
                // ========== 1. Grouped Summary Sheet ==========
                const summarySheet = [['Performed By', 'Report', 'Count', 'Target', 'Balance', '% Achieved']];
            
                for (const person in this.groupedData) {
                    const reports = this.groupedData[person];
                    for (const report in reports) {
                        const count = reports[report];
                        const target = this.getTarget(person, report);
                        const balance = target - count;
                        const percent = target ? ((count / target) * 100).toFixed(1) + '%' : '0%';
                        summarySheet.push([person, report, count, target, balance, percent]);
                    }
                }
            
                const summaryWS = XLSX.utils.aoa_to_sheet(summarySheet);
            
                // ========== 2. Selected Row Detail Sheet ==========
                const detailSheet = [['Performed By', 'Report', 'Date', 'Brand', 'Platform', 'Notes']];
                if (this.selectedRow.person && this.selectedRow.report) {
                    const details = this.getDetailList(this.selectedRow.person, this.selectedRow.report);
                    details.forEach(item => {
                            detailSheet.push([
                            item.performed_by,
                            item.report,
                            item.date,
                            item.brand,
                            item.platform,
                            item.notes || '',
                        ]);
                    });
                }
            
                const detailWS = XLSX.utils.aoa_to_sheet(detailSheet);
            
                // ========== 3. MTD Summary Sheet ==========
                const mtd = this.mtdData;
                const mtdSheet = [
                    ['📊 MTD Summary'],
                    ['Date Exported', new Date().toLocaleString()],
                    [],
                    ['Total Reports', mtd.totalReports],
                    ['Unique Team Members', mtd.uniquePerformers],
                    [],
                    ['Report Type', 'Count'],
                ];
            
                for (const [report, count] of Object.entries(mtd.reportCounts)) {
                    mtdSheet.push([report, count]);
                }
            
                const mtdWS = XLSX.utils.aoa_to_sheet(mtdSheet);
            
                // ========== 4. Create Workbook and Export ==========
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, summaryWS, 'Grouped Summary');
                if (detailSheet.length > 1) {
                    XLSX.utils.book_append_sheet(wb, detailWS, 'Selected Details');
                }
                XLSX.utils.book_append_sheet(wb, mtdWS, 'MTD Summary');
            
                const filename = `Export_${new Date().toISOString().slice(0, 10)}.xlsx`;
                XLSX.writeFile(wb, filename);
            },


            async getProfileDetail() {
                try {
                    let response = await axios.get('http://31.97.43.196/kpidashboardapi/kpi/fetchGBPTask', CONFIG.HEADER);
                    return response;
                } catch (error) {
                    console.log(error);
                }
            },

            async setKPI() {
                try {
                    let result = await this.getProfileDetail();
                    this.profileData = result.data.response;
                    this.webdevEOW = result.data.conclusionEOW;
                    this.setFilterOptions();
                    this.groupRecords();
                } catch (error) {
                    console.log(error);
                }
            },
            setFilterOptions() {
                const reports = new Set();
                const months = new Set();
                const brands = new Set();
                const dates = new Set();
                const platforms = new Set();

                this.profileData.forEach(item => {
                    reports.add(item.report);
                    months.add(new Date(item.date).toLocaleString('default', { month: 'long' }));
                    brands.add(item.brand);
                    dates.add(item.date);
                    platforms.add(item.platform);
                });

                this.uniqueOptions = {
                    reports: Array.from(reports),
                    months: Array.from(months),
                    brands: Array.from(brands),
                    dates: Array.from(dates),
                    platforms: Array.from(platforms),
                };
            },
            groupRecords() {
                const grouped = {};
            
                this.filteredData.forEach(item => {
                    const person = item.performed_by;
                    const report = item.report;
            
            
                    if (!grouped[person]) {
                        grouped[person] = {};
                    }
            
                    if (!grouped[person][report]) {
                        grouped[person][report] = 0;
                    }
            
                    grouped[person][report]++;
                });
            
                this.groupedData = grouped;
            }

        },
        mounted() {
            this.setKPI().then(() => {
                this.renderBarChart(); // Create chart after data loads
            });
        }
    })
});
</script>

<section class="content" id="app">
    <div class="container-fluid pt-3 pl-md-2 pr-md-2 pl-md-5 pr-md-5">
        
        <div>
        <!-- User Data -->
        <div class="table-responsive">
            
            <h4 class="fw-300 mb-3"> Department Marketing KPI</h4>

            <!-- Filters -->
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
            </div>

            <!-- Grouped Table -->
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Performed By</th>
                            <th>Report</th>
                            <th>Count</th>
                            <th>Target</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <button class="btn btn-success mb-3" @click="exportGroupedToExcel">
                            Export Summary Table (Excel)
                        </button>
                    
                        <!-- Detail Search Bar -->
                        <div class="mb-2">
                            <input type="text" class="form-control form-control-sm" v-model="detailSearch" placeholder="Search in details...">
                        </div>
                        <template v-for="(reports, person) in groupedData">
                            <template v-for="(count, report, index) in reports">
                            <!-- Summary Row -->
                            <tr @click="toggleDetails(person, report)" style="cursor: pointer;">
                                <td v-if="index === 0" :rowspan="Object.keys(reports).length"><strong>{{ person }}</strong></td>
                                <td>{{ report }}</td>
                                <td>{{ count }}</td>
                                <td>{{ getBalance(person, report) }}</td>
                                <td>{{ getPercentage(person, report) }}</td>
                            </tr>
                            <!-- Nested Table Row -->
                            <!-- Inner Detail Table -->
                            <tr v-if="selectedRow.person === person && selectedRow.report === report">
                              <td colspan="6"> <!-- Update colspan if you added new columns -->
                            
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
                                        <td><a :href="item.link" target="_blank"> {{ item.link }}  </a></td>
                                        <td>{{ item.language }}</td>
                                        <td>{{ item.note }}</td>
                                      </tr>
                                    </tbody>
                                  </table>
                            
                                  <!-- Pagination Controls -->
                                  <div class="d-flex justify-content-between align-items-center mt-2">
                                    <button class="btn btn-sm btn-primary" @click="detailPage--" :disabled="detailPage === 1">
                                      Previous
                                    </button>
                                    <span>Page {{ detailPage }} of {{ detailTotalPages() }}</span>
                                    <button class="btn btn-sm btn-primary" @click="detailPage++" :disabled="detailPage === detailTotalPages()">
                                      Next
                                    </button>
                                  </div>
                                </div>
                              </td>
                            </tr>
        
        
        
                            </template>
                        </template>
                    </tbody>
    
                </table>
            </div>
        </div>
    </div>
</section>