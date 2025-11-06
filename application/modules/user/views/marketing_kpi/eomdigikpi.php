<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<style>
  .dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 24px;
    margin-top: 40px;
  }
  .dashboard-card {
    background: #b4d4f5;
    border-radius: 8px;
    padding: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    display: flex;
    flex-direction: column;
  }
  .dashboard-card h5 { font-size: 16px; font-weight: 600; margin-bottom: 16px; }
  .red { border-bottom: red 5px solid; }
  .orange { border-bottom: orange 5px solid; }
  .green { border-bottom: green 5px solid; }

  /* ====== New KPI tables (match screenshot) ====== */
  .kpi-table { width:100%; border-collapse: separate; border-spacing:0; }
  .kpi-table th, .kpi-table td { padding:10px 12px; border:1px solid #e8edf3; text-align:center; vertical-align:middle; }
  .kpi-table thead th { background:#6e561b; color:#fff; font-weight:700; }
  .kpi-table .subhead { background:#f0f1f3; color:#333; font-weight:700; text-align:left; }
  .kpi-table .total-row { background:#0c2a52; color:#fff; font-weight:800; }
  .kpi-table .target-col { background:#e7e8ea; font-weight:700; }
  .kpi-table .signed-inhouse { background:#fff679; font-weight:800; color:#0033cc; }
  .kpi-table .signup-unique { background:#7aff6a; font-weight:800; color:#0b2239; }
  .kpi-table .signed-referred { background:#7de8ff; font-weight:800; color:#0b2239; }
  .kpi-table .total-signed { background:#305e22; color:#fff; font-weight:800; }
  .kpi-table .conv-col { font-weight:800; }
  .kpi-caption { margin: 0 0 6px; font-weight:700; }
</style>

<section class="content" id="app">
  <div class="container-fluid pt-3 pl-md-2 pr-md-2 pl-md-5 pr-md-5">

    <!-- Date Range Filter -->
    <div class="row mb-3">
      <div class="col-12 col-md-4">
        <label for="startDate">Start Date</label>
        <input type="date" class="form-control" v-model="startDate" @change="filterByDateRange">
      </div>
      <div class="col-12 col-md-4">
        <label for="endDate">End Date</label>
        <input type="date" class="form-control" v-model="endDate" @change="filterByDateRange">
      </div>
    </div>

    <div class="container text-center">
      <h5 class="fw-300 mb-3 text-center">DIGITAL MARKETING DEPARTMENT</h5>
      <h2 class="mb-3 text-center">MTD PERFORMANCE SUMMARY</h2>
      <h4 class="text-center mb-4">{{ displayRange }}</h4>
    </div>

    <!-- ===================== NEW PRESENTATION (replacing old KPI cards) ===================== -->
    <div class="row">
      <div class="col-12">
        <div class="table-responsive">
          <p class="kpi-caption">DIGITAL MARKETING TEAM</p>
          <table class="kpi-table">
            <thead>
              <tr>
                <th class="subhead" style="text-align:left;">Name</th>
                <th>Total Leads</th>
                <th class="signed-referred">Signed Referred Out</th>
                <th class="signup-unique">Sign-up Unique Count</th>
                <th class="signed-inhouse">Signed In-House</th>
                <th class="total-signed">Total Signed</th>
                <th class="target-col">Target</th>
                <th class="conv-col">Lead vs Sign Up Conversion</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="row in firstTableRows" :key="row.name">
                <td class="subhead">{{ row.name }}</td>
                <td>{{ row.totalLeads }}</td>
                <td class="signed-referred">{{ row.signedReferredOut }}</td>
                <td class="signup-unique">{{ row.uniqueSignups }}</td>
                <td class="signed-inhouse"><em>{{ row.signedInHouse }}</em></td>
                <td class="total-signed">{{ row.totalSigned }}</td>
                <td class="target-col">{{ row.target }}</td>
                <td class="conv-col">{{ row.conversion }}</td>
              </tr>
              <tr class="total-row">
                <td style="text-align:left;">TOTAL</td>
                <td>{{ firstTableTotals.totalLeads }}</td>
                <td>{{ firstTableTotals.signedReferredOut }}</td>
                <td>{{ firstTableTotals.uniqueSignups }}</td>
                <td>{{ firstTableTotals.signedInHouse }}</td>
                <td>{{ firstTableTotals.totalSigned }}</td>
                <td>{{ firstTableTotals.target }}</td>
                <td>{{ firstTableTotals.conversion }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-12">
        <div class="table-responsive">
          <p class="kpi-caption">OPEN CASES &amp; PENDING REFERRAL</p>
          <table class="kpi-table">
            <thead>
              <tr>
                <th class="subhead" style="text-align:left;">Source</th>
                <th>Open Cases</th>
                <th>Pending Referral</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="r in openPendingRows" :key="r.source">
                <td class="subhead">{{ r.source }}</td>
                <td>{{ r.openCases }}</td>
                <td>{{ r.pendingReferral }}</td>
              </tr>
              <tr class="total-row">
                <td style="text-align:left;">Total</td>
                <td>{{ openPendingTotals.openCases }} Cases</td>
                <td>{{ openPendingTotals.pendingReferral }} Cases</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- ===================== END NEW PRESENTATION ===================== -->

    <div class="leadSummary">
        <hr>
        <!-- ===== your original sections continue as-is (no structural changes) ===== -->

        <div class="row">
            <div class="col-md">
                <h4 class="mb-3 text-center">In-House Sign Up Summary</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                              <th scope="col">Lead Source</th>
                              <th scope="col">Sign Ups</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in inhousesignupsummarycount">
                                <td>{{item.marketing_source}}</td>
                                <td>{{ item.total_signed }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md">
                <h4 class="mb-3 text-center">Category</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                              <th scope="col">Lead Source</th>
                              <th scope="col">Sign Ups</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in categorysignupsummarycount">
                                <td>{{item.value}}</td>
                                <td>{{ item.total_signed }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md">
                <h4 class="mb-3 text-center">Successful Referred Out</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                              <th scope="col">Referred Out</th>
                              <th scope="col">Count</th>
                              <th scope="col">Successful Referred Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in referredsummarycount">
                                <td>{{item.marketing_source}}</td>
                                <td>{{ item.total_referred }}</td>
                                <td>{{ item.successful_referred_count }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <hr>
        <div class="row">
            <div class="col-md">
                <h4 class="mb-3">In-House Signup Details</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                              <th scope="col">Lead Source</th>
                              <th scope="col">Case Value</th>
                              <th scope="col">Case Type</th>
                              <th scope="col">Client Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in inhousesignupsummarylist">
                                <td>{{item.marketing_source}}</td>
                                <td>{{ item.value }}</td>
                                <td>{{ item.case_type }}</td>
                                <td>{{ item.client_name }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md">
                <h4 class="mb-3">Successful Referred Out Details</h4>
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">Lead Source</th>
                        <th scope="col">Case Value</th>
                        <th scope="col">Case Type</th>
                        <th scope="col">Client Name</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="item in successfulReferredList" :key="item.marketing_source + '-' + item.client_name">
                        <td>{{ item.marketing_source }}</td>
                        <td>{{ item.value }}</td>
                        <td>{{ item.case_type }}</td>
                        <td>{{ item.client_name }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
            </div>
        </div>

        <!-- ===== the remainder of your original dashboard stays the same ===== -->
        <!-- (SEO PERFORMANCE MONITORING, GBP, RND, GA, Emails, etc.) -->
        <!-- … your existing long markup unchanged … -->

        <hr>
        <div class="row">
            <div class="col-md">
                <!-- Date Range Filter -->
                <div class="row mb-3">
                  <div class="col-12 col-md-4">
                    <label for="startDate">Start Date</label>
                    <input type="date" class="form-control" v-model="startDate" @change="filterByDateRange">
                  </div>
                  <div class="col-12 col-md-4">
                    <label for="endDate">End Date</label>
                    <input type="date" class="form-control" v-model="endDate" @change="filterByDateRange">
                  </div>
                </div>
                <h2 class="mb-3 text-center">SEO PERFORMANCE MONITORING</h2>
                <h4 class="text-center mb-5">{{ displayRange }}</h4>
                <!-- … keep ALL tables and sections you posted originally … -->
                <!-- (I’m not trimming anything—your original markup continues below) -->

                <!-- ================== BEGIN: your original (unchanged) tables ================== -->
                <!-- seoperformancelist table -->
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                              <th scope="col">MTD Leads</th>
                              <th scope="col">MTD Signed</th>
                              <th scope="col">Successful Referrals</th>
                              <th scope="col">MTD Acquisituin Rate</th>
                              <th scope="col">Unique Sign Ups</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in seoperformancelist">
                                <td>{{item.mtd_lead}}</td>
                                <td>{{ item.mtd_signed }}</td>
                                <td>{{ item.successful_referrals }}</td>
                                <td>{{ item.mtd_acquisition_rate }}</td>
                                <td>{{ item.unique_sign_ups }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- (… and the rest of your original markup exactly as before …) -->

                <!-- Your whole original block continues here unchanged -->
                <!-- BEGIN copy-paste of your remaining HTML from the question -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                      <th scope="col">Case Value</th>
                                      <th scope="col">Signed</th>
                                      <th scope="col">Successful Referred Out</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in seoperformancevaluelist">
                                        <td>{{item.case_value}}</td>
                                        <td>{{ item.signed }}</td>
                                        <td>{{ item.successful_referred_out }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md">
                        <h4 class="mb-3 text-center">Other Brands SEO PERFORMANCE MONITORING</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                      <th scope="col">Brands</th>
                                      <th scope="col">MTD Leads</th>
                                      <th scope="col">MTD Signed</th>
                                      <th scope="col">Successful Referrals</th>
                                      <th scope="col">MTD Acquisituin Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in otherbrandseoperformancelist">
                                        <td>{{item.brand}}</td>
                                        <td>{{ item.mtd_lead }}</td>
                                        <td>{{ item.mtd_signed }}</td>
                                        <td>{{ item.successful_referrals }}</td>
                                        <td>{{ item.mtd_acquisition_rate }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md">
                        <div class="table-responsive">
                          <table class="table">
                            <thead>
                              <tr>
                                <th scope="col">Case Value</th>
                                <th v-for="brand in otherbrandseoperformancebrands" :key="brand">{{ brand }}</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="item in otherbrandseoperformancecasevalue" :key="item.value">
                                <td>{{ item.value }}</td>
                                <td v-for="brand in otherbrandseoperformancebrands" :key="brand">{{ item[brand] }}</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Lead Summary Outcome</th>
                                        <th v-for="brand in seoperformanceleadsummarybrands" :key="brand">{{ brand }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in seoperformanceleadsummaryoutcome" :key="item.value">
                                        <td>{{ item.value }}</td>
                                        <td v-for="brand in seoperformanceleadsummarybrands" :key="brand">{{ item[brand] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md">
                        <h4 class="mb-3 text-center">Website Leads (From Intakes Sheet)</h4>
                        <div class="table-responsive">
                          <table class="table">
                            <thead>
                              <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Source</th>
                                <th scope="col">Name</th>
                                <th scope="col">Number</th>
                                <th scope="col">Case Type</th>
                                <th scope="col">Status</th>
                                <th scope="col">Quality/Qualified?</th>
                                <th scope="col">Details of Case</th>
                                <th scope="col">Call Summary from CallRail</th>
                                <th scope="col">Attribution</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="item in websiteLeads" :key="item.id">
                                <td>{{ item.created_date || 'N/A' }}</td>
                                <td>{{ item.marketing_source || 'N/A' }}</td>
                                <td>{{ item.full_name || 'N/A' }}</td>
                                <td>{{ item.mobile_phone || 'N/A' }}</td>
                                <td>{{ item.case_type || 'N/A' }}</td>
                                <td>{{ item.status || 'N/A' }}</td>
                                <td>Qualified</td>
                                <td>{{ item.last_note || 'N/A' }}</td>
                                <td>{{ item.call_outcome || 'N/A' }}</td>
                                <td>{{ item.attribution || 'N/A' }}</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                    </div>
                    <div class="col-md">
                        <h4 class="mb-3 text-center ">Top 10 Most Visited Pages (GA)</h4>
                        <div class="table-responsive">
                          <table class="table">
                            <thead>
                              <tr>
                                <th>Page Path</th>
                                <th>Title</th>
                                <th>Pageviews</th>
                                <th>Engaged Sessions</th>
                                <th>Engagement Rate</th>
                                <th>Avg. Session Duration</th>
                                <th>Bounce Rate</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="item in topVisitedPages" :key="item.id">
                                <td>{{ item.page_path }}</td>
                                <td>{{ item.page_title }}</td>
                                <td>{{ item.screen_pageviews }}</td>
                                <td>{{ item.engaged_sessions }}</td>
                                <td>{{ item.engagement_rate }}</td>
                                <td>{{ item.avg_session_duration }}</td>
                                <td>{{ item.bounce_rate }}</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                    </div>
                </div>

                <h4 class="mb-3">Articles Posted</h4>
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Type of Publication</th>
                        <th>Target</th>
                        <th>Published</th>
                        <th>% Completion</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="item in blogs" :key="item.id">
                        <td>{{ item.article_type }}</td>
                        <td>{{ item.target }}</td>
                        <td>{{ item.count }}</td>
                        <td>{{ item.targetcompliant }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>

            </div>
        </div>

        <hr>
        <div class="row">
            <div class="col-md">
                <!-- Date Range Filter -->
                <div class="row mb-3">
                  <div class="col-12 col-md-4">
                    <label for="startDate">Start Date</label>
                    <input type="date" class="form-control" v-model="startDate" @change="filterByDateRange">
                  </div>
                  <div class="col-12 col-md-4">
                    <label for="endDate">End Date</label>
                    <input type="date" class="form-control" v-model="endDate" @change="filterByDateRange">
                  </div>
                </div>
                <h2 class="mb-3 text-center">GBP PERFORMANCE MONITORING</h2>
                <h4 class="text-center mb-5">{{ displayRange }}</h4>

                <div class="row">
                    <div class="col-md">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                      <th scope="col">MTD Leads</th>
                                      <th scope="col">MTD Signed Cases</th>
                                      <th scope="col">MTD Acquisituin Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in gbpperformancemonitor">
                                        <td>{{item.mtd_lead}}</td>
                                        <td>{{ item.mtd_signed }}</td>
                                        <td>{{ item.mtd_acquisition_rate }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                      <th scope="col">Case Value</th>
                                      <th scope="col">Signed Cases</th>
                                      <th scope="col">Successful Referred Out</th>
                                      <th scope="col">Unique Signed Up</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in gbpcasevalue">
                                        <td>{{item.case_value}}</td>
                                        <td>{{ item.signed }}</td>
                                        <td>{{ item.successful_referred_out }}</td>
                                        <td>{{ item.unique_sign_ups }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md">
                        <h4 class="mb-3 text-center">GBP DAILY SUMMARY</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                      <th scope="col">Lead Summary Outcome</th>
                                      <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in gbpsummaryoutcome">
                                        <td>{{item.case_value}}</td>
                                        <td>{{ item.signed }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <h4 class="mb-3 text-center">GBP Posts</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                      <th scope="col">Brand</th>
                                      <th scope="col">Number of Posts MTD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in gbppostsummary">
                                        <td>{{item.brand}}</td>
                                        <td>{{ item.count }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md">
                        <h4 class="mb-3 text-center">GBP Performance per location</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                      <th scope="col">GMB Location</th>
                                      <th scope="col">Discovery <br> (Search/Maps)</th>
                                      <th scope="col">Interactions</th>
                                      <th scope="col">Calls</th>
                                      <th scope="col">Directions</th>
                                      <th scope="col">Website Clicks</th>
                                      <th scope="col">Leads</th>
                                      <th scope="col">Signups</th>
                                      <th scope="col">% Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in gmblocation">
                                        <td>{{ item.location }}</td>
                                        <td>{{ item.discovery }}</td>
                                        <td>{{ item.interactions }}</td>
                                        <td>{{ item.calls }}</td>
                                        <td>{{ item.directions }}</td>
                                        <td>{{ item.website_clicks }}</td>
                                        <td>{{ item.leads }}</td>
                                        <td>{{ item.signups }}</td>
                                        <td>{{ item.rate == null ? '-' : (Number(item.rate) * 100).toFixed(0) + '%' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md">
                <!-- Date Range Filter -->
                <div class="row mb-3">
                  <div class="col-12 col-md-4">
                    <label for="startDate">Start Date</label>
                    <input type="date" class="form-control" v-model="startDate" @change="filterByDateRange">
                  </div>
                  <div class="col-12 col-md-4">
                    <label for="endDate">End Date</label>
                    <input type="date" class="form-control" v-model="endDate" @change="filterByDateRange">
                  </div>
                </div>
                <h2 class="mb-3 text-center">RNDworx GOOGLE ADS PERFORMANCE SUMMARY</h2>
                <h4 class="text-center mb-5">{{ displayRange }}</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                              <th scope="col">MTD Cost</th>
                              <th scope="col">MTD Leads</th>
                              <th scope="col">MTD Signed</th>
                              <th scope="col">MTD CPL</th>
                              <th scope="col">MTD CPA per signup</th>
                              <th scope="col">MTD Acquisition Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in rndperformancesummary">
                                <td>$3,320.96</td>
                                <td>{{ item.mtd_lead }}</td>
                                <td>{{ item.mtd_signed }}</td>
                                <td>{{ item.mtd_cpl }}</td>
                                <td>{{ item.mtd_cpa }}</td>
                                <td>{{ item.mtd_acquisition_rate }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                      <th scope="col">Case Value</th>
                                      <th scope="col">Signed Cases</th>
                                      <th scope="col">Successful Referred Out</th>
                                      <th scope="col">Unique Signed Up</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in rndcasevalue">
                                        <td>{{ item.case_value }}</td>
                                        <td>{{ item.signed }}</td>
                                        <td>{{ item.successful_referred_out }}</td>
                                        <td>{{ item.unique_sign_ups }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="table-responsive">
                          <table class="table">
                            <thead>
                              <tr>
                                <th scope="col">Lead Summary Outcome</th>
                                <th scope="col">Count</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="item in rndleadsummaryoutcome" :key="item.status">
                                <td>{{ item.status }}</td>
                                <td>{{ item.count }}</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                      <th scope="col">Daily</th>
                                      <th scope="col">Cost</th>
                                      <th scope="col">Leads</th>
                                      <th scope="col">Signed</th>
                                      <th scope="col">CPL</th>
                                      <th scope="col">CPA</th>
                                      <th scope="col">Conversion Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in rndperformancesummary">
                                        <td>RNDWorx</td>
                                        <td>{{ item.mtd_cost }}</td>
                                        <td>{{ item.mtd_lead }}</td>
                                        <td>{{ item.mtd_signed }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <h4 class="mb-3">Ads Performance per campaign</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                              <th scope="col">RND Worx Campaign</th>
                              <th scope="col">Cost</th>
                              <th scope="col">Clicks</th>
                              <th scope="col">Impressions</th>
                              <th scope="col">Leads</th>
                              <th scope="col">Signed</th>
                              <th scope="col">CPC</th>
                              <th scope="col">CTR</th>
                              <th scope="col">CPL</th>
                              <th scope="col">CPA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Car/Truck Accident SKAG Campaign</td>
                                <td>$130.37</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>Performance Max Campaign (Car Accident)</td>
                                <td>$3.09</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>Performance Max Campaign (Motorcycle Accident)</td>
                                <td>$4.00</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>Performance Max Campaign (Truck Accident)</td>
                                <td>$3.74</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>Performance Max Campaign (Uber/Lyft)</td>
                                <td>$2.64</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>Personal Injury SKAG Campaign</td>
                                <td>$141.48</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr>
                <!-- Date Range Filter -->
                <div class="row mb-3">
                  <div class="col-12 col-md-4">
                    <label for="startDate">Start Date</label>
                    <input type="date" class="form-control" v-model="startDate" @change="filterByDateRange">
                  </div>
                  <div class="col-12 col-md-4">
                    <label for="endDate">End Date</label>
                    <input type="date" class="form-control" v-model="endDate" @change="filterByDateRange">
                  </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <h4 class="mb-3 text-center">JFJ Email Form</h4>
                        <div class="table-responsive">
                          <table class="table">
                            <thead>
                              <tr>
                                <th>Type</th>
                                <th>Count</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="item in emailTypeSummary" :key="item.type">
                                <td>{{ item.type }}</td>
                                <td>{{ item.count }}</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="table-responsive">
                          <table class="table">
                            <thead>
                              <tr>
                                <th scope="col">Email</th>
                                <th scope="col">Message</th>
                                <th scope="col">Type</th>
                                <th scope="col">Date</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="item in emailsummary" :key="item.type">
                                <td>{{ item.email }}</td>
                                <td>{{ item.message }}</td>
                                <td>{{ item.type }}</td>
                                <td>{{ item.date }}</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                    </div>
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
        startDate: '',
        endDate: '',
        entries: [],

        /* ===== NEW state for the first two tables ===== */
        firstTableRows: [],
        firstTableTotals: { totalLeads:0, signedReferredOut:0, uniqueSignups:0, signedInHouse:0, totalSigned:0, target:0, conversion:'0%' },
        openPendingRows: [],
        openPendingTotals: { openCases:0, pendingReferral:0 },

        // KPI Targets (kept)
        targetlead: '250',
        targetsignup: '45',

        // Existing state (unchanged)
        mtdleadcount: 0,
        mtdsigncount: 0,
        referredcount: 0,
        totalsignedcount: 0,

        inhousesignupsummarycount: [],
        categorysignupsummarycount: [],
        referredsummarycount: [],
        inhousesignupsummarylist: [],
        seoperformancelist: [],
        seoperformancevaluelist: [],
        otherbrandseoperformancelist: [],
        otherbrandseoperformancecasevalue: [],
        otherbrandseoperformancebrands: [],
        seoperformanceleadsummaryoutcome: [],
        seoperformanceleadsummarybrands: [],
        websiteLeads: [],
        gaEntries: [],
        topVisitedPages: [],
        postedblogs: [],
        blogs: [],
        onpagetarget: '173',
        offpagetarget: '55',
        gbpperformancemonitor: [],
        gbpcasevalue: [],
        gbpsummaryoutcome: [],
        gbppostsummary: [],
        rndperformancesummary: [],
        rndcasevalue: [],
        rndleadsummaryoutcome: [],
        rawEmailsummary: [],
        emailsummary: [],
        emailTypeSummary: [],
        rawGmblocsummary: [],
        gmblocation: [],
        gmbTypesummary: [],
        successfulReferredList: [],
    },
    computed: {
      displayRange() {
        return this.startDate && this.endDate ? `${this.startDate} to ${this.endDate}` : '—';
      }
    },
    mounted() {
      const now = new Date();
      const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
      this.startDate = firstDay.toISOString().slice(0, 10);
      this.endDate = now.toISOString().slice(0, 10);

      fetch('http://31.97.43.196/kpidashboardapi/kpi/fetchleaddocket', CONFIG.HEADER)
        .then(res => res.json())
        .then(data => { this.entries = data.response || []; this.filterByDateRange(); });

      fetch('http://31.97.43.196/kpidashboardapi/kpi/fetchga', CONFIG.HEADER)
        .then(res => res.json())
        .then(data => { this.gaEntries = data.response || []; this.filterByDateRange(); });

      fetch('http://31.97.43.196/kpidashboardapi/kpi/content', CONFIG.HEADER)
        .then(res => res.json())
        .then(data => { this.postedblogs = data.response || []; this.filterByDateRange(); });

      fetch('http://31.97.43.196/kpidashboardapi/kpi/fetchleadForm', CONFIG.HEADER)
        .then(res => res.json())
        .then(data => { this.rawEmailsummary = data.response || []; this.filterByDateRange(); });

      fetch('http://31.97.43.196/kpidashboardapi/GmbMetrics/gmbloclist', CONFIG.HEADER)
        .then(res => res.json())
        .then(data => { this.rawGmblocsummary = data.response || []; this.filterByDateRange(); });
    },
    methods: {
      buildSummaryRow(label, entriesAll, entriesSigned, target) {
        const totalLeads = entriesAll.length;
        const signedInHouse = entriesSigned.filter(e => (e.lead_outcome || '').trim() === 'Signed').length;
        const signedReferredOut = entriesSigned.filter(e => (e.lead_outcome || '').trim() === 'Referred out').length;
        const totalSigned = signedInHouse + signedReferredOut;
        const uniqueSet = new Set(
          entriesSigned
            .filter(e => ['Signed','Referred out'].includes((e.lead_outcome || '').trim()))
            .map(e => (e.full_name || '').trim())
        );
        const uniqueSignups = uniqueSet.size;
        const conversion = totalLeads ? ((totalSigned / totalLeads) * 100).toFixed(2) + '%' : '0%';
        return { name: label, totalLeads, signedReferredOut, uniqueSignups, signedInHouse, totalSigned, target, conversion };
      },

      filterByDateRange() {
        const start = new Date(this.startDate);
        const end = new Date(this.endDate); end.setHours(23, 59, 59, 999);
        const inRange = (dateStr) => { const d = new Date(dateStr); return d >= start && d <= end; };

        const keywords = ['gmb','intaker','avvo','rnd','yelp','ayuda','social','justin','kapwa','labor','brain','motorcyclist','jfj','website'];

        const filtered = this.entries.filter(entry => {
          const date = entry.created_date;
          const source = (entry.marketing_source || '').toLowerCase();
          return date && inRange(date) && keywords.some(k => source.includes(k));
        });

        const filteredWholeData = this.entries.filter(entry => {
          const date = entry.created_date || entry.sign_up_date;
          const source = (entry.marketing_source || '').toLowerCase();
          return date && inRange(date) && keywords.some(k => source.includes(k));
        });

        const filteredReferred = this.entries.filter(entry => {
          const date = entry.sign_up_date;
          const source = (entry.marketing_source || '').toLowerCase();
          return date && inRange(date) && keywords.some(k => source.includes(k));
        });

        /* ===== Build the two NEW tables ===== */
        const isGBP = (s) => (s || '').toLowerCase().includes('gmb');
        const gbpAll = filteredWholeData.filter(e => isGBP(e.marketing_source));
        const gbpSignedSet = filteredReferred.filter(e => isGBP(e.marketing_source));

        const isSEO = (s) => {
          const v = (s || '').toLowerCase();
          return v.includes('jfj') || v.includes('justin') || v.includes('website') || v.includes('web search') || v.includes('intaker');
        };
        const seoAll = filteredWholeData.filter(e => isSEO(e.marketing_source));
        const seoSignedSet = filteredReferred.filter(e => isSEO(e.marketing_source));

        const r1 = this.buildSummaryRow('GBP', gbpAll, gbpSignedSet, 30);
        const r2 = this.buildSummaryRow('SEO/ Website (JFJ)', seoAll, seoSignedSet, 15);
        this.firstTableRows = [r1, r2];

        const tLeads = r1.totalLeads + r2.totalLeads;
        const tRef = r1.signedReferredOut + r2.signedReferredOut;
        const tUniq = r1.uniqueSignups + r2.uniqueSignups;
        const tIn = r1.signedInHouse + r2.signedInHouse;
        const tSigned = r1.totalSigned + r2.totalSigned;
        const tTarget = r1.target + r2.target;
        const tConv = tLeads ? ((tSigned / tLeads) * 100).toFixed(2) + '%' : '0%';
        this.firstTableTotals = { totalLeads: tLeads, signedReferredOut: tRef, uniqueSignups: tUniq, signedInHouse: tIn, totalSigned: tSigned, target: tTarget, conversion: tConv };

        const openStatuses = ['Chase','Pending Agreement','Under Review','Pending Review'];
        const countOpen = (list) => list.filter(e => openStatuses.includes((e.status || '').trim())).length;
        const countPendingRef = (list) => list.filter(e => ((e.status || '').trim() === 'Pending Referral')).length;

        const openGBP = countOpen(gbpAll);
        const pendGBP = countPendingRef(gbpAll);
        const openSEO = countOpen(seoAll);
        const pendSEO = countPendingRef(seoAll);
        this.openPendingRows = [
          { source: 'GBP', openCases: openGBP, pendingReferral: pendGBP },
          { source: 'SEO (Website)', openCases: openSEO, pendingReferral: pendSEO }
        ];
        this.openPendingTotals = { openCases: openGBP + openSEO, pendingReferral: pendGBP + pendSEO };

        /* ====== Your original calculations continue below (unchanged) ====== */

        // MTD Lead Count
        this.mtdleadcount = filteredWholeData.filter(e =>
          ['Signed Up', 'Referred', 'Rejected', 'Chase', 'Pending Agreement', 'Pending Referral', 'Under Review', 'Lost', 'Signed'].includes(e.status) ||
          ['Signed', 'Referred out'].includes(e.lead_outcome)
        ).length;

        // Signed Count
        this.mtdsigncount = filteredReferred.filter(e => (e.lead_outcome) === 'Signed').length;

        // Referred Count
        this.referredcount = filteredReferred.filter(e =>
          (e.lead_outcome) === 'Referred out' || (e.status === 'Referred')
        ).length;

        // Generate in-house signup summary grouped by marketing_source
        const signedEntries = filteredWholeData.filter(e => (e.lead_outcome || e.status) === 'Signed');
        const AllEntries = filteredReferred.filter(e => (e.lead_outcome || e.status) === 'Signed' || (e.lead_outcome) === 'Referred out');

        const grouped = {};
        signedEntries.forEach(entry => {
          const source = entry.marketing_source || 'Unknown';
          if (!grouped[source]) grouped[source] = 0;
          if (entry.lead_outcome === 'Signed' || entry.lead_outcome === 'Referred out') grouped[source]++;
        });
        this.inhousesignupsummarycount = Object.entries(grouped).map(([source, count]) => ({
          marketing_source: source, total_signed: count
        }));

        const groupedCategories = {};
        AllEntries.forEach(entry => {
          const category = entry.value || 'Uncategorized';
          if (!groupedCategories[category]) groupedCategories[category] = 0;
          if (entry.value) groupedCategories[category]++;
        });
        this.categorysignupsummarycount = Object.entries(groupedCategories).map(([value, count]) => ({
          value, total_signed: count
        }));

        const referredEntries = filteredReferred.filter(e => e.lead_outcome === 'Referred out');
        this.successfulReferredList = referredEntries.map(entry => ({
          marketing_source: entry.marketing_source || 'Unknown',
          value: entry.value || 'N/A',
          case_type: entry.case_type || 'N/A',
          client_name: entry.full_name || 'N/A'
        }));

        const getGroupedLabel = (source) => {
          if (!source) return 'Unknown';
          const s = source.toLowerCase();
          if (s.includes('gmb')) return 'GBP';
          if (s.includes('seo')) return 'SEO';
          return source;
        };
        const groupedReferred = {};
        referredEntries.forEach(entry => {
          const groupLabel = getGroupedLabel(entry.marketing_source);
          if (!groupedReferred[groupLabel]) groupedReferred[groupLabel] = { total_referred: 0, successful_referred_count: 0 };
          groupedReferred[groupLabel].total_referred++;
          if (entry.lead_outcome === 'Referred out') groupedReferred[groupLabel].successful_referred_count++;
        });
        this.referredsummarycount = Object.entries(groupedReferred).map(([source, data]) => ({
          marketing_source: source, total_referred: data.total_referred, successful_referred_count: data.successful_referred_count
        }));

        this.inhousesignupsummarylist = signedEntries.map(entry => ({
          marketing_source: entry.marketing_source || 'Unknown',
          value: entry.value || 'N/A',
          case_type: entry.case_type || 'N/A',
          client_name: entry.full_name || 'N/A'
        }));

        /* ====== (SEO / GBP / RND / GA / Emails / GMB) — unchanged from your code ====== */
        // --- SEO section (unchanged logic) ---
        const seoKeywords = ['Website','Ayuda','Web Search','justin','Kapwa','Labor','Brain','Motorcyclist','intaker'];
        const seoEntries = filteredWholeData.filter(entry => {
          const source = entry.marketing_source || '';
          return seoKeywords.some(keyword => source.toLowerCase().includes(keyword.toLowerCase()));
        });
        const seoEntriesdata = filteredReferred.filter(entry => {
          const source = entry.marketing_source || '';
          return seoKeywords.some(keyword => source.toLowerCase().includes(keyword.toLowerCase()));
        });
        const seoMTDLeads = seoEntries.length;
        const seoSignedEntries = seoEntriesdata.filter(e => e.lead_outcome === 'Signed');
        const seoSigned = seoSignedEntries.length;
        const seoReferred = seoEntriesdata.filter(e => e.lead_outcome === 'Referred out').length;
        const seoAR = ((seoSigned + seoReferred) / seoMTDLeads) * 100 || 0;
        const seoUniqueNames = new Set(seoSignedEntries.map(e => e.full_name)).size;
        this.seoperformancelist = [{ mtd_lead: seoMTDLeads, mtd_signed: seoSigned, successful_referrals: seoReferred, mtd_acquisition_rate: seoAR.toFixed(2) + '%', unique_sign_ups: seoUniqueNames }];

        const seoEntriesData = filteredWholeData.filter(entry => {
          const source = entry.marketing_source || '';
          return seoKeywords.some(keyword => source.toLowerCase().includes(keyword.toLowerCase()));
        });
        const groupedSEOByValue = {};
        seoEntriesData.forEach(entry => {
          const val = entry.value?.trim() || 'Uncategorized';
          if (!groupedSEOByValue[val]) groupedSEOByValue[val] = { case_value: val, signed: 0, successful_referred_out: 0 };
          if (entry.status === 'Signed Up') groupedSEOByValue[val].signed++;
          if (entry.status === 'Pending Referral') groupedSEOByValue[val].signed++;
          if (entry.status === 'Referred') groupedSEOByValue[val].successful_referred_out++;
        });
        this.seoperformancevaluelist = Object.values(groupedSEOByValue);

        const brandKeywords = {
          'Ayuda California': ['ayuda'],
          'Justin For Justice': ['jfj','justin','intaker','Justin For Justice'],
          'Kapwa Justice Community ': ['kapwa','kj'],
          'Labor Law Advocates': ['lla','labor law'],
          'Brain Injury Help Center ': ['brain'],
          'Motorcyclist': ['motorcyclist'],
          'Other Marketing Channels (Avvo)': ['avvo'],
        };
        const brandStats = {};
        Object.entries(brandKeywords).forEach(([brand, keywords]) => {
          const matchingEntries = seoEntriesData.filter(entry => {
            const src = (entry.marketing_source || '').toLowerCase();
            return keywords.some(kw => src.includes(kw));
          });
          const mtd_lead = matchingEntries.length;
          const signed = matchingEntries.filter(e => e.status === 'Signed Up').length;
          const referred = matchingEntries.filter(e => e.status === 'Referred').length;
          const ar = ((signed + referred) / mtd_lead) * 100 || 0;
          if (mtd_lead > 0) brandStats[brand] = { brand, mtd_lead, mtd_signed: signed, successful_referrals: referred, mtd_acquisition_rate: ar.toFixed(2) + '%' };
        });
        this.otherbrandseoperformancelist = Object.values(brandStats);

        const caseValueMatrix = {};
        const allBrands = Object.keys(brandKeywords);
        const caseValueSet = new Set();
        seoEntries.forEach(entry => {
          const val = entry.status?.trim() || 'Uncategorized';
          const src = (entry.marketing_source || '').toLowerCase();
          let matchedBrand = null;
          for (const [brand, keywords] of Object.entries(brandKeywords)) {
            if (keywords.some(kw => src.includes(kw))) { matchedBrand = brand; break; }
          }
          if (!matchedBrand) return;
          caseValueSet.add(val);
          if (!caseValueMatrix[val]) caseValueMatrix[val] = {};
          if (!caseValueMatrix[val][matchedBrand]) caseValueMatrix[val][matchedBrand] = 0;
          if (entry.status === 'Signed Up' || entry.status === 'Referred') caseValueMatrix[val][matchedBrand]++;
        });
        const caseValueList = Array.from(caseValueSet).sort();
        this.otherbrandseoperformancecasevalue = caseValueList.map(value => {
          const row = { value };
          allBrands.forEach(brand => { row[brand] = caseValueMatrix[value]?.[brand] || 0; });
          return row;
        });
        this.otherbrandseoperformancebrands = allBrands;

        const outcomeMatrix = {};
        const outcomeSet = new Set();
        const brandSet = new Set();
        seoEntries.forEach(entry => {
          const outcome = entry.status?.trim();
          const source = entry.marketing_source?.toLowerCase();
          let matchedBrand = null;
          for (const [brand, keywords] of Object.entries(brandKeywords)) {
            if (keywords.some(kw => source.includes(kw))) { matchedBrand = brand; break; }
          }
          if (!matchedBrand) return;
          outcomeSet.add(outcome);
          brandSet.add(matchedBrand);
          if (!outcomeMatrix[outcome]) outcomeMatrix[outcome] = {};
          if (!outcomeMatrix[outcome][matchedBrand]) outcomeMatrix[outcome][matchedBrand] = 0;
          outcomeMatrix[outcome][matchedBrand]++;
        });
        const sortedOutcomes = Array.from(outcomeSet).sort();
        const sortedBrands = Array.from(brandSet).sort();
        this.seoperformanceleadsummarybrands = sortedBrands;
        this.seoperformanceleadsummaryoutcome = sortedOutcomes.map(outcome => {
          const row = { value: outcome };
          sortedBrands.forEach(brand => { row[brand] = outcomeMatrix[outcome]?.[brand] || 0; });
          return row;
        });

        this.websiteLeads = filtered.filter(entry => {
          const source = entry.marketing_source?.toLowerCase() || '';
          const status = entry.status;
          const validSource = source.includes('justin') || source.includes('labor') || source.includes('brain') || source.includes('motorcyclist');
          const validStatus = ['Signed Up','Pending Agreement','Chase','Pending Referral'].includes(status);
          return validSource && validStatus;
        });

        const filteredGA = this.gaEntries.filter(entry => entry.report_date && inRange(entry.report_date));
        const uniquePagesMap = {};
        filteredGA.forEach(entry => {
          const path = entry.page_path;
          const views = parseInt(entry.screen_pageviews || 0);
          if (!uniquePagesMap[path] || views > parseInt(uniquePagesMap[path].screen_pageviews || 0)) uniquePagesMap[path] = entry;
        });
        this.topVisitedPages = Object.values(uniquePagesMap)
          .sort((a, b) => parseInt(b.screen_pageviews || 0) - parseInt(a.screen_pageviews || 0))
          .slice(0, 10);

        const filteredArticles = this.postedblogs.filter(entry => entry.task_date && inRange(entry.task_date) && (entry.type === 'On Page Blog' || entry.type === 'Off Page Blog'));
        const onpageCount = filteredArticles.filter(e => e.article_type === 'On Page Blog').length;
        const offpageCount = filteredArticles.filter(e => e.article_type === 'Off Page Blog').length;
        this.blogs = [
          { article_type: 'On Page Blog', count: onpageCount, target: this.onpagetarget, targetcompliant: ((onpageCount / this.onpagetarget) * 100).toFixed(2) },
          { article_type: 'Off Page Blog', count: offpageCount, target: this.offpagetarget, targetcompliant: ((offpageCount / this.offpagetarget) * 100).toFixed(2) }
        ];

        // GBP performance (unchanged)
        const gbpKeywords = ['GMB'];
        const gbpEntries = filteredWholeData.filter(entry => {
          const source = entry.marketing_source?.toLowerCase() || '';
          return gbpKeywords.some(keyword => source.includes(keyword.toLowerCase()));
        });
        const gbpEntrySigned = filteredReferred.filter(entry => {
          const source = entry.marketing_source?.toLowerCase() || '';
          return gbpKeywords.some(keyword => source.includes(keyword.toLowerCase()));
        });
        const gbpMTDLeads = gbpEntries.length;
        const gbpSignedEntries = gbpEntrySigned.filter(e => e.lead_outcome === 'Signed');
        const gbpSigned = gbpSignedEntries.length;
        const gbpReferred = gbpEntrySigned.filter(e => e.status === 'Referred out').length;
        const gbpAR = ((gbpSigned + gbpReferred) / gbpMTDLeads) * 100 || 0;
        this.gbpperformancemonitor = [{ mtd_lead: gbpMTDLeads, mtd_signed: gbpSigned, mtd_acquisition_rate: gbpAR.toFixed(2) + '%' }];

        const gbpEntriesData = gbpEntries;
        const groupedGBPByValue = {};
        gbpEntriesData.forEach(entry => {
          const val = entry.value?.trim() || 'Uncategorized';
          if (!groupedGBPByValue[val]) groupedGBPByValue[val] = { case_value: val, signed: 0, successful_referred_out: 0, unique_sign_ups: 0 };
          if (entry.lead_outcome === 'Signed') { groupedGBPByValue[val].signed++; groupedGBPByValue[val].unique_sign_ups++; }
          if (entry.lead_outcome === 'Referred out') { groupedGBPByValue[val].successful_referred_out++; groupedGBPByValue[val].unique_sign_ups++; }
        });
        this.gbpcasevalue = Object.values(groupedGBPByValue);

        const gbpleadkeyword = { 'GMB': ['gmb'] };
        const gbpoutcomeMatrix = {};
        const gbpoutcomeSet = new Set();
        gbpEntriesData.forEach(entry => {
          const outcome = entry.status?.trim() || 'No Answer';
          const source = entry.marketing_source?.toLowerCase() || '';
          let matchedBrand = null;
          for (const [brand, keywords] of Object.entries(gbpleadkeyword)) {
            if (keywords.some(kw => source.includes(kw))) { matchedBrand = brand; break; }
          }
          if (!matchedBrand) return;
          gbpoutcomeSet.add(outcome);
          if (!gbpoutcomeMatrix[outcome]) gbpoutcomeMatrix[outcome] = {};
          if (!gbpoutcomeMatrix[outcome][matchedBrand]) gbpoutcomeMatrix[outcome][matchedBrand] = 0;
          gbpoutcomeMatrix[outcome][matchedBrand]++;
        });
        const gbpsortedOutcomes = Array.from(gbpoutcomeSet).sort();
        this.gbpsummaryoutcome = gbpsortedOutcomes.map(outcome => ({
          case_value: outcome,
          signed: gbpoutcomeMatrix[outcome]?.['GMB'] || 0,
          referred: gbpoutcomeMatrix[outcome]?.['GMB'] || 0,
          lost: gbpoutcomeMatrix[outcome]?.['GMB'] || 0,
          rejected: gbpoutcomeMatrix[outcome]?.['GMB'] || 0,
          open: gbpoutcomeMatrix[outcome]?.['GMB'] || 0
        }));

        const filtereOnPageBlog = this.postedblogs.filter(entry => entry.task_date && inRange(entry.task_date) && entry.type === 'On Page Blog');
        const countsByBrand = filtereOnPageBlog.reduce((acc, entry) => { acc[entry.brand] = (acc[entry.brand] || 0) + 1; return acc; }, {});
        this.gbppostsummary = Object.entries(countsByBrand).map(([brand, count]) => ({ brand, count }));

        // RND (unchanged)
        const rndKeywords = ['RND'];
        const rndEntries = filtered.filter(entry => {
          const source = entry.marketing_source || '';
          return rndKeywords.some(keyword => source.toLowerCase().includes(keyword.toLowerCase()));
        });
        const rndMTDLeads = rndEntries.length;
        const rndSignedEntries = rndEntries.filter(e => e.status === 'Signed Up');
        const rndSigned = rndSignedEntries.length || 0;
        const rndReferred = rndEntries.filter(e => e.status === 'Referred').length;
        const rndAR = ((rndSigned + rndReferred) / rndMTDLeads) * 100 || 0;
        const rndCPL = (3,320.96 / rndMTDLeads) || 0;
        const rndcost = (9.48 * rndEntries.length) || 0;
        const rndCPA = (3,320.96 / rndSigned) || 0;
        const rndUniqueNames = new Set(rndSignedEntries.map(e => e.full_name)).size;
        this.rndperformancesummary = [{ mtd_cost: rndcost, mtd_lead: rndMTDLeads, mtd_cpl: rndCPL.toFixed(2), mtd_cpa: rndCPA, mtd_signed: rndSigned, mtd_acquisition_rate: rndAR.toFixed(2) + '%' }];

        const groupedRNDByValue = {};
        rndEntries.forEach(entry => {
          const val = entry.value?.trim() || 'Uncategorized';
          if (!groupedRNDByValue[val]) groupedRNDByValue[val] = { case_value: val, signed: 0, successful_referred_out: 0, uniqueClients: new Set() };
          const status = entry.status;
          if (['Signed','Chase','Pending Referral','Pending Review'].includes(status)) {
            groupedRNDByValue[val].signed++;
            if (entry.full_name) groupedRNDByValue[val].uniqueClients.add(entry.full_name);
          }
          if (status === 'Referred out' || entry.status === 'Referred') groupedRNDByValue[val].successful_referred_out++;
        });
        this.rndcasevalue = Object.values(groupedRNDByValue).map(item => ({
          case_value: item.case_value, signed: item.signed, successful_referred_out: item.successful_referred_out, unique_sign_ups: item.uniqueClients.size
        }));

        const statusCounts = {};
        rndEntries.forEach(entry => {
          const status = entry.status?.trim() || 'Unknown';
          statusCounts[status] = (statusCounts[status] || 0) + 1;
        });
        this.rndleadsummaryoutcome = Object.keys(statusCounts).map(status => ({ status, count: statusCounts[status] }));

        // Emails
        this.emailsummary = this.rawEmailsummary.filter(item => item.date && inRange(item.date));
        const typeCountMap = {};
        this.emailsummary.forEach(item => { const t = item?.type || 'Unknown'; typeCountMap[t] = (typeCountMap[t] || 0) + 1; });
        this.emailTypeSummary = Object.entries(typeCountMap).map(([type, count]) => ({ type, count }));

        // GMB latest per location
        const getDateField = (it) => it.date;
        const gmbFilteredByRange = (this.rawGmblocsummary || []).filter(it => { const ds = getDateField(it); return ds && inRange(ds); });
        const sourceRows = gmbFilteredByRange.length ? gmbFilteredByRange : (this.rawGmblocsummary || []);
        const newestByLocation = {};
        sourceRows.forEach(it => {
          const loc = it.location || 'Unknown';
          const ds = getDateField(it);
          const d  = ds ? new Date(ds) : new Date(0);
          if (!newestByLocation[loc] || d > newestByLocation[loc]._d) newestByLocation[loc] = { ...it, _d: d };
        });
        this.gmblocation = Object.values(newestByLocation).sort((a, b) => b._d - a._d).map(({ _d, ...rest }) => rest);

        // (keep single-per-location fallback)
        const seen = new Set();
        this.gmblocation = (this.rawGmblocsummary || [])
          .filter(item => { const key = item.location; if (seen.has(key)) return false; seen.add(key); return true; });
      }
    }
  });
});
</script>
