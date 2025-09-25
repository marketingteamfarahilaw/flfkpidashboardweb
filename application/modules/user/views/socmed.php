<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>


<script src="<?= base_url('assets/js/actionlayer/kpi.js') ?>"></script>

<section class="content" id="app">
    <div class="container-fluid pt-3 pl-md-2 pr-md-2 pl-md-5 pr-md-5">
        
        <div>
        <!-- User Data -->
        <div class="table-responsive">
            
            <h3 class="fw-300 mb-3">Social Media Reports</h3>
            

            <div class="card mb-4">
                <div class="card-body">
                <!-- 🔍 SocMed Filters -->
                <div class="row mb-4">
                    <div class="col-12 mb-2">
                    <input type="text" class="form-control" v-model="socmedFilters.search" placeholder="Search SocMed records...">
                    </div>
                    <div class="col-md-4">
                    <label class="form-label">Month</label>
                    <select class="form-control" v-model="socmedFilters.month">
                        <option value="">All</option>
                        <option v-for="month in uniqueMonths" :key="month" :value="month">{{ month }}</option>
                    </select>
                    </div>
                    <div class="col-md-4">
                    <label class="form-label">Brand</label>
                    <select class="form-control" v-model="socmedFilters.brand">
                        <option value="">All</option>
                        <option v-for="brand in uniqueBrands" :key="brand" :value="brand">{{ brand }}</option>
                    </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Performed By</label>
                        <select class="form-control" v-model="socmedFilters.performed_by">
                        <option value="">All</option>
                        <option v-for="name in uniquePerformedBy" :key="name" :value="name">{{ name }}</option>
                        </select>
                    </div>
                </div>

                <button class="btn btn-success mb-3" @click="exportSocmedToExcel">
                    Export SocMed Reports to Excel
                </button>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                        <th>Performed By</th>
                        <th>Total Reports</th>
                        <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="(items, person) in groupedSocmedByPerson">
                        <!-- Summary Row -->
                        <tr>
                            <td>{{ person }}</td>
                            <td>{{ items.length }}</td>
                            <td>
                            <button class="btn btn-sm btn-outline-primary" @click="toggleSocmedDetails(person)">
                                {{ expandedSocmedPerson === person ? 'Hide' : 'View' }}
                            </button>
                            </td>
                        </tr>
                
                        <!-- Expanded Inner Row with Pagination -->
                        <tr v-if="expandedSocmedPerson === person">
                            <td colspan="3">
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm mb-0">
                                        <thead>
                                            <tr>
                                            <th>Date</th>
                                            <th>Brand</th>
                                            <th>Title</th>
                                            <th>Link</th>
                                            <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="item in paginatedSocmedDetails(person)" :key="item.id">
                                            <td>{{ item.date }}</td>
                                            <td>{{ item.brand }}</td>
                                            <td>{{ item.title }}</td>
                                            <td><a :href="item.link" target="_blank"> {{ item.link }}  </a></td>
                                            <td>{{ item.note }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                    
                                <!-- Pagination Controls -->
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <button class="btn btn-sm btn-outline-secondary"
                                            @click="socmedDetailPage[person]--"
                                            :disabled="socmedDetailPage[person] <= 1">
                                    Previous
                                    </button>
                                    <span>Page {{ socmedDetailPage[person] }} of {{ socmedTotalPages(person) }}</span>
                                    <button class="btn btn-sm btn-outline-secondary"
                                            @click="socmedDetailPage[person]++"
                                            :disabled="socmedDetailPage[person] >= socmedTotalPages(person)">
                                    Next
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </template>
                    </tbody>
                    </table>
                </div>

                </div>
            </div>
        </div>
    </div>
</section>

<script src="<?= base_url('assets/js/jCardInvoice/jCardInvoice.js'); ?>"></script>