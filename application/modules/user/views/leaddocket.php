<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<section class="content" id="app">
    <div class="container-fluid pt-3 pl-md-2 pr-md-2 pl-md-5 pr-md-5">
        <h4 class="fw-300 mb-3">LEAD DOCKET TRACKER</h4>
        
        <!-- Filter Inputs -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label>Created Date From</label>
                <input type="date" class="form-control" v-model="filters.created_date.start">
            </div>
            <div class="col-md-3">
                <label>Created Date To</label>
                <input type="date" class="form-control" v-model="filters.created_date.end">
            </div>
            <div class="col-md-3">
                <label>Incident Date From</label>
                <input type="date" class="form-control" v-model="filters.incident_date.start">
            </div>
            <div class="col-md-3">
                <label>Incident Date To</label>
                <input type="date" class="form-control" v-model="filters.incident_date.end">
            </div>

            <div class="col-md-3">
                <label>Last Update Date From</label>
                <input type="date" class="form-control" v-model="filters.last_update_date.start">
            </div>
            <div class="col-md-3">
                <label>Last Update Date To</label>
                <input type="date" class="form-control" v-model="filters.last_update_date.end">
            </div>

            <div class="col-md-3 mt-2">
                <label>Status</label>
                <select class="form-control" v-model="filters.status" multiple>
                  <option value="Signed Up">Signed Up</option>
                  <option value="Referred">Referred</option>
                  <option value="Rejected">Rejected</option>
                  <option value="Chase">Chase</option>
                  <option value="Lost">Lost</option>
                  <option value="Pending Referral">Pending Referral</option>
                  <option value="Rejected - PD Leads Only">Rejected - PD Leads Only</option>
                  <option value="Under Review">Under Review</option>
                </select>
            </div>
            <div class="col-md-3 mt-2">
                <label>Marketing Source</label>
                <select class="form-control" v-model="filters.marketing_source" multiple>
                  <option value="GMB">GMB</option>
                  <option value="Website">Website</option>
                  <option value="Intaker">Intaker</option>
                  <option value="AVVO">AVVO</option>
                  <option value="RND">RND</option>
                  <option value="Yelp">Yelp</option>
                  <option value="social">Social</option>
                  <option value="Kapwa">Kapwa</option>
                  <option value="LLA">LLA</option>
                  <option value="Labor Law">Labor Law</option>
                  <option value="Brain">Brain</option>
                  <option value="motorcyclist">Motorcyclist</option>
                </select>
            </div>
            <div class="col-md-3 mt-2">
                <label>Full Name</label>
                <input type="text" class="form-control" v-model="filters.full_name">
            </div>
            <div class="col-md-3 mt-2">
                <label>Case Type</label>
                <select class="form-control" v-model="filters.case_type" multiple>
                  <option value="Auto">Auto</option>
                  <option value="Bicycle">Bicycle</option>
                  <option value="Dog Bite">Dog Bite</option>
                  <option value="Employment">Employment</option>
                  <option value="Motorcycle">Motorcycle</option>
                  <option value="Other">Other</option>
                  <option value="Pedestrian">Pedestrian</option>
                  <option value="Slip and Fall">Slip and Fall</option>
                  <option value="Unrelated Practice Area">Unrelated Practice Area</option>
                  <option value="Worker Comp">Worker Comp</option>
                  <option value="Personal Injury">Personal Injury</option>
                  <option value="Medical Malpractice">Medical Malpractice</option>
                </select>
            </div>
            <div class="col-md-3 mt-3">
                <button class="btn btn-primary mt-4" @click="applyFilters">Apply Filters</button>
            </div>
        </div>
        <hr>
        <div class="row mt-4">
          <div class="col-md-6 offset-md-3 mb-4">
            <canvas id="goalPieChart" height="300"></canvas>
          </div>
        </div>
        
        
        <!-- Summary Counts -->
        <div class="row mb-3">
            <div class="col-md-12">
                <h5 class="fw-bold">Marketing Source Summary</h5>
                <div class="row">
                    <div class="col-md-3 mb-2" v-for="(count, source) in countsByMarketingSource" :key="source">
                        <div class="border p-3 rounded bg-light">
                            <strong>{{ source }}</strong><br>
                            <span>{{ count }} record{{ count > 1 ? 's' : '' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <!-- Grouped Table -->
        <div v-for="(names, source) in groupedByMarketingSource" :key="source" class="mb-5">
            <h5 class="mt-4">
                {{ source }} 
                ({{ countsByMarketingSource[source] }} total records, 
                {{ Object.keys(names).length }} unique names)
            </h5>

            <div v-for="(entries, fullName) in names" :key="fullName" class="mb-4">
                <h6 class="mb-2">
                    {{ fullName }}
                    <span v-if="entries.length > 1" class="text-danger">(Duplicate - {{ entries.length }} records)</span>
                </h6>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>Full Name</th>
                                <th>Mobile Phone</th>
                                <th>Status</th>
                                <th>Substatus</th>
                                <th>Case Type</th>
                                <th>Marketing Source</th>
                                <th>Created Date</th>
                                <th>Incident Date</th>
                                <th>Last Status Change Date</th>
                                <th>Last Update Date</th>
                                <th>Last Note Date</th>
                                <th>Last Note</th>
                                <th>Call Outcome</th>
                                <th>Not Interested Disposition</th>
                                <th>Not Responsive Disposition</th>
                                <th>Intake Completed By</th>
                                <th>Initial Call Taken By</th>
                                <th>Open Disposition</th>
                                <th>Referred Out Disposition</th>
                                <th>Rejected Disposition</th>
                                <th>Signed Disposition</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="entry in entries" :key="entry.created_date + entry.status">
                                <td>{{ entry.full_name }}</td>
                                <td>{{ entry.mobile_phone }}</td>
                                <td>{{ entry.status }}</td>
                                <td>{{ entry.substatus }}</td>
                                <td>{{ entry.case_type }}</td>
                                <td>{{ entry.marketing_source }}</td>
                                <td>{{ entry.created_date }}</td>
                                <td>{{ entry.incident_date }}</td>
                                <td>{{ entry.last_status_change_date }}</td>
                                <td>{{ entry.last_update_date }}</td>
                                <td>{{ entry.last_note_date }}</td>
                                <td>{{ entry.last_note }}</td>
                                <td>{{ entry.call_outcome }}</td>
                                <td>{{ entry.not_interested_disposition }}</td>
                                <td>{{ entry.not_responsive_disposition }}</td>
                                <td>{{ entry.intake_completed_by }}</td>
                                <td>{{ entry.initial_call_taken_by }}</td>
                                <td>{{ entry.open_disposition }}</td>
                                <td>{{ entry.referred_out_disposition }}</td>
                                <td>{{ entry.rejected_disposition }}</td>
                                <td>{{ entry.signed_disposition }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function () {
    new Vue({
        el: '#app',
        data: {
            rawData: [],
            filteredData: [],
            filters: {
                created_date: { start: '', end: '' },
                incident_date: { start: '', end: '' },
                last_update_date: { start: '', end: '' },
                marketing_source: [],
                full_name: '',
                case_type: [],
                status: []
            },
            targetlead: '250',
            targetsignup: '55',
            mtdleadcount: '0',
            mtdsigncount: '0',
            referredcount: '0',
            _pieChartInstance: null
        },
        computed: {
            groupedByMarketingSource() {
                const result = {};
                this.filteredData.forEach(entry => {
                    const source = entry.marketing_source || 'Unknown';
                    const name = entry.full_name || 'Unnamed';
                    if (!result[source]) result[source] = {};
                    if (!result[source][name]) result[source][name] = [];
                    result[source][name].push(entry);
                });
                return result;
            },
            countsByMarketingSource() {
                const counts = {};
                Object.entries(this.groupedByMarketingSource).forEach(([source, names]) => {
                    let total = 0;
                    Object.values(names).forEach(entries => {
                        total += entries.length;
                    });
                    counts[source] = total;
                });
                return counts;
            }
        },
        methods: {
            applyFilters() {
                const contains = (value, keyword) => {
                    if (!keyword) return true;
                    if (!value) return false;
                    return String(value).toLowerCase().includes(keyword.toLowerCase());
                };

                const containsMulti = (value, selected) => {
                    if (!selected || selected.length === 0) return true;
                    if (!value) return false;
                    return selected.includes(value);
                };

                const matchDate = (field, range, item) => {
                    const dateStr = item[field];
                    if (!dateStr) return false;
                    const itemDate = new Date(dateStr);
                    const start = range.start ? new Date(range.start + 'T00:00:00') : null;
                    const end = range.end ? new Date(range.end + 'T23:59:59') : null;
                    return (!start || itemDate >= start) && (!end || itemDate <= end);
                };

                this.filteredData = this.rawData.filter(item => {
                    return matchDate('created_date', this.filters.created_date, item)
                        && matchDate('incident_date', this.filters.incident_date, item)
                        && matchDate('last_update_date', this.filters.last_update_date, item)
                        && contains(item.full_name, this.filters.full_name)
                        && containsMulti(item.case_type, this.filters.case_type)
                        && containsMulti(item.status, this.filters.status)
                        && containsMulti(item.marketing_source, this.filters.marketing_source);
                });

                this.renderPieChart();
            },
            renderPieChart() {
                // Clean up previous chart
                if (this._pieChartInstance) {
                    this._pieChartInstance.destroy();
                }

                const filtered = this.filteredData;

                const mtdLead = filtered.length;
                const mtdSigned = filtered.filter(e => e.status === 'Signed Up').length;
                const referred = filtered.filter(e => e.status === 'Referred').length;
                const actualSignup = mtdSigned + referred;

                const targetLead = parseInt(this.targetlead);
                const targetSignup = parseInt(this.targetsignup);

                const data = {
                    labels: [
                        `Target Leads (${targetLead})`,
                        `Filtered Leads (${mtdLead})`,
                        `Target Sign-ups (${targetSignup})`,
                        `Signed + Referred (${actualSignup})`
                    ],
                    datasets: [{
                        data: [targetLead, mtdLead, targetSignup, actualSignup],
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
                    }]
                };

                const ctx = document.getElementById('goalPieChart').getContext('2d');
                this._pieChartInstance = new Chart(ctx, {
                    type: 'pie',
                    data: data,
                    options: {
                        responsive: true,
                        title: {
                            display: true,
                            text: 'Filtered Lead & Signup Progress'
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                });
            }
        },
        mounted() {
            // Initial data load
            fetch("https://lmthrp.com/api/kpi/fetchleaddocket", CONFIG.HEADER)
                .then(res => res.json())
                .then(data => {
                    this.rawData = data.response;
                    this.filteredData = data.response;
                    this.renderPieChart();
                })
                .catch(err => console.error("API fetch error:", err));

            Promise.all([
                fetch('https://lmthrp.com/api/kpi/countsign', CONFIG.HEADER).then(res => res.json()),
                fetch('https://lmthrp.com/api/kpi/countmtd', CONFIG.HEADER).then(res => res.json()),
                fetch('https://lmthrp.com/api/kpi/referredcount', CONFIG.HEADER).then(res => res.json())
            ]).then(([signData, mtdData, referredData]) => {
                this.mtdsigncount = signData.response;
                this.mtdleadcount = mtdData.response;
                this.referredcount = referredData.response;
            });
        }
    });
});
</script>

