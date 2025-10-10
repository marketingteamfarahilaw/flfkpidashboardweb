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
  .dashboard-card h5 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 16px;
  }
  .red {
    border-bottom: red 5px solid;
    }
  .orange {
        border-bottom: orange 5px solid;
    }
  .green {
        border-bottom: green 5px solid;
    }
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
      <h2 class="mb-3 text-center">MTD MARKETING PERFORMANCE SUMMARY</h2>
      <h4 class="text-center mb-5">{{ displayRange }}</h4>
    </div>
    <div class="leadSummary">
        <div class="row mt-3">
            <div class="col-md">
                <div class="dashboard-card">
                  <h5>Target Leads</h5>
                  <h4>{{targetlead}}</h4>
                </div>
            </div>
            <div class="col-md">
                <div class="dashboard-card">
                  <h5>Target Sign-ups</h5>
                  <h4>{{targetsignup}}</h4>
                </div>
            </div>
            <div class="col-md">
                <div class="dashboard-card">
                  <h5>Target Acquisition Rate</h5>
                  <h4>{{ ((parseInt(targetsignup) / parseInt(targetlead)) * 100) + '%' }}</h4>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md">
                <div class="dashboard-card" :class="colorClass">
                  <h5>MTD Leads</h5>
                  <h4>{{mtdleadcount}}</h4>
                </div>
            </div>
            <div class="col-md"  :class="signedColorClass" style="background: #b4d4f5;border-radius: 8px;padding: 16px;box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);">
                <div class="row" style="border-bottom: 1px solid #000; margin-bottom: 2px; padding-top: 2px;">
                    <div class="col-md text-center">
                        <h6>MTD Client Sign-ups</h6>
                        <h5>{{mtdsigncount}}</h5>
                    </div>
                    <div class="col-md text-center">
                        <h6>Successful Referrals</h6>
                        <h5>{{referredcount}}</h5>
                    </div>
                </div>  
                </hr>
                <h4 class="text-center">{{totalsignedcount}}</h4>
            </div>
            <!--totalsignedcount-->
            <div class="col-md">
                <div class="dashboard-card" :class="mtdAcquisitionRateColorClass">
                  <h5>MTD Acquisition Rate</h5>
                  <h4>{{ (((parseInt(mtdsigncount) + parseInt(referredcount)) / parseInt(mtdleadcount)) * 100).toFixed(2) + '%' }}</h4>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md">
                <div class="dashboard-card">
                  <h5>% to Goal (Leads)</h5>
                  <h4>{{ (( parseInt(mtdleadcount)/parseInt(targetlead) ) * 100).toFixed(2) }}%</h4>
                </div>
            </div>
            <div class="col-md">
                <div class="dashboard-card">
                  <h5>% to Goal (Sign-ups)</h5>
                  <h4>{{ (((parseInt(mtdsigncount) + parseInt(referredcount)) / parseInt(targetsignup)) * 100).toFixed(2) + '%' }}</h4>
                </div>
            </div>
            <div class="col-md">
                <div class="dashboard-card">
                  <h5>% to Goal (AR)</h5>
                  <h4>{{ ((((parseInt(mtdsigncount) + parseInt(referredcount)) / parseInt(mtdleadcount)) / (parseInt(targetsignup) / parseInt(targetlead))) * 100).toFixed(2) + '%' }}</h4>
                </div>
            </div>
        </div>
    
        
        <hr>
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
                <h2 class="mb-3 text-center">SEO PERFORMANCE MONITORING</h2>
                <h4 class="text-center mb-5">{{ displayRange }}</h4>
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
                        <td>{{ item. target }}</td>
                        <td>{{ item.count }}</td>
                        <td>{{ item.targetcompliant }}</td>
                        <!--<td>{{ item.target }}</td>-->
                        <!--<td>{{ item.onpage || 'N/A' }}</td>-->
                        <!--<td>{{ item.offpage || 'N/A' }}</td>-->
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
        
        // KPI Targets
        targetlead: '250',
        targetsignup: '55',
        
        // KPI Output
        mtdleadcount: 0,
        mtdsigncount: 0,
        referredcount: 0,
        totalsignedcount: 0,
        
        // Summary Tables (optional)
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
    },
    computed: {
      displayRange() {
        return this.startDate && this.endDate ? `${this.startDate} to ${this.endDate}` : '—';
      },
      colorClass() {
        const lead = parseInt(this.mtdleadcount);
        const target = parseInt(this.targetlead);

        if (isNaN(lead) || isNaN(target) || target === 0) {
          return ''; // No class
        }

        const percentage = (lead / target) * 100;

        if (percentage < 50) {
          return 'red';
        } else if (percentage >= 50 && percentage < 100) {
          return 'orange';
        } else {
          return 'green';
        }
      },
      signedColorClass() {
        // const signed = parseInt(this.mtdsigncoun+this.referredcount);
        const signed = parseInt(this.mtdsigncount) + parseInt(this.referredcount);
        const target = parseInt(this.targetsignup);
        
        this.totalsignedcount = signed;

        if (isNaN(signed) || isNaN(target) || target === 0) {
          return '';
        }

        const percentage = (signed / target) * 100;

        if (percentage < 50) {
          return 'red';
        } else if (percentage >= 50 && percentage < 100) {
          return 'orange';
        } else {
          return 'green';
        }
      },
    //   referredColorClass() {
    //     const referred = parseInt(this.referredcount);
    //     const target = parseInt(this.targetsignup);

    //     if (isNaN(referred) || isNaN(target) || target === 0) {
    //       return '';
    //     }

    //     const percentage = (referred / target) * 100;

    //     if (percentage < 50) {
    //       return 'red';
    //     } else if (percentage >= 50 && percentage < 100) {
    //       return 'orange';
    //     } else {
    //       return 'green';
    //     }
    //   },
      mtdAcquisitionRateColorClass() {
        const mtdLead = parseInt(this.mtdleadcount);
        const mtdSigned = parseInt(this.mtdsigncount);
        const referred = parseInt(this.referredcount);
        const total = mtdSigned + referred;

        if (isNaN(mtdLead) || mtdLead === 0) {
          return '';
        }

        const rate = (total / mtdLead) * 100;

        if (rate < 50) {
          return 'red';
        } else if (rate >= 50 && rate < 100) {
          return 'orange';
        } else {
          return 'green';
        }
      }
    },
    mounted() {
      const now = new Date();
      const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
      this.startDate = firstDay.toISOString().slice(0, 10);
      this.endDate = now.toISOString().slice(0, 10);

      fetch('http://31.97.43.196/kpidashboardapi/kpi/fetchleaddocket', CONFIG.HEADER)
        .then(res => res.json())
        .then(data => {
          this.entries = data.response || [];
          this.filterByDateRange();
        });
        
      fetch('http://31.97.43.196/kpidashboardapi/kpi/fetchga', CONFIG.HEADER)
        .then(res => res.json())
        .then(data => {
          this.gaEntries = data.response || [];
          this.filterByDateRange();
        });
        
      fetch('http://31.97.43.196/kpidashboardapi/kpi/content', CONFIG.HEADER)
        .then(res => res.json())
        .then(data => {
          this.postedblogs = data.response || [];
          this.filterByDateRange();
        });
        
      fetch('http://31.97.43.196/kpidashboardapi/kpi/fetchleadForm', CONFIG.HEADER)
          .then(res => res.json())
          .then(data => {
            this.rawEmailsummary = data.response || [];
            this.filterByDateRange();
          });

      fetch('http://31.97.43.196/kpidashboardapi/GmbMetrics/gmbloclist', CONFIG.HEADER)
        .then(res => res.json())
        .then(data => {
          this.rawGmblocsummary = data.response || [];
          this.filterByDateRange();
        });
    },
    methods: {
      filterByDateRange() {
        
          const start = new Date(this.startDate);
          const end = new Date(this.endDate);
          end.setHours(23, 59, 59, 999);
        
          const inRange = (dateStr) => {
            const d = new Date(dateStr);
            return d >= start && d <= end;
          };
          
          const s = new Date(this.start), e = new Date(this.end);
        const startOfDay = (d) => new Date(d.getFullYear(), d.getMonth(), d.getDate(), 0,0,0,0);
        const endOfDay   = (d) => new Date(d.getFullYear(), d.getMonth(), d.getDate(), 23,59,59,999);
        const inRangeday = (dateStr) => {
          if (!dateStr) return false;
          const d = new Date(dateStr);
          return d >= startOfDay(s) && d <= endOfDay(e);
        };
         
          // Debug: Log a sample entry
        //   if (this.entries.length > 0) {
        //     console.log('Sample Entry Keys:', Object.keys(this.entries[0]));
        //     console.log('Sample Entry:', this.entries[0]);
        //   }
        
          const keywords = [
            'gmb', 'intaker', 'avvo', 'rnd', 'yelp', 'ayuda',
            'social', 'justin', 'kapwa', 'labor', 'brain', 'motorcyclist', 'jfj', 'website'
          ];
        
          const filtered = this.entries.filter(entry => {
            const date = entry.created_date;
            const source = (entry.marketing_source).toLowerCase();
            const matchesKeyword = keywords.some(k => source.includes(k));
            return date && inRange(date) && matchesKeyword;
          });

          const filteredWholeData = this.entries.filter(entry => {
            const date = entry.created_date || entry.sign_up_date;
            const source = (entry.marketing_source || '').toLowerCase();
            const matchesKeyword = keywords.some(k => source.includes(k));
            return date && inRange(date) && matchesKeyword;
          });
        
          const filteredReferred = this.entries.filter(entry => {
            const date = entry.sign_up_date;
            const source = (entry.marketing_source).toLowerCase();
            const matchesKeyword = keywords.some(k => source.includes(k));
            return date && inRange(date) && matchesKeyword;
          });
          
          
          const filteredData = this.entries.filter(entry => {
            const date = entry.timestamp;
            const source = (entry.marketing_source).toLowerCase();
            const matchesKeyword = keywords.some(k => source.includes(k));
            return date && inRange(date) && matchesKeyword;
          });
        
          // MTD Lead Count
          this.mtdleadcount = filteredWholeData.filter(e =>
            ['Signed Up', 'Referred', 'Rejected', 'Chase', 'Pending Agreement', 'Pending Referral', 'Under Review', 'Lost', 'Signed'].includes(e.status) || ['Signed', 'Referred out'].includes(e.lead_outcome)
            
            // (e.lead_outcome || e.status) === 'Signed' || (e.lead_outcome || e.status) === 'Signed Up' || (e.status) === 'Rejected' || (e.status) === 'Lost' || (e.status) === 'Chase' || (e.status) === 'Pending Agreement' || (e.status) === 'Pending Referral'
          ).length;
        
          // Signed Count (use lead_outcome or fallback to status)
          this.mtdsigncount = filteredReferred.filter(e =>
            (e.lead_outcome) === 'Signed'
            // ['Signed'].includes(e.lead_outcome)
          ).length;
        
        
          // Referred Count
          this.referredcount = filteredReferred.filter(e =>
            (e.lead_outcome) === 'Referred out' 
            || (e.status === 'Referred')
            // ['Referred'].includes(e.status)
          ).length;
        
        
        // Generate in-house signup summary grouped by marketing_source
        
        const signedEntries = filteredWholeData.filter(e =>
          (e.lead_outcome || e.status) === 'Signed'
        );
        const AllEntries = filteredReferred.filter(e =>
          (e.lead_outcome || e.status) === 'Signed' || (e.lead_outcome) === 'Referred out'
        );
        
        const grouped = {};
        signedEntries.forEach(entry => {
          const source = entry.marketing_source || 'Unknown';
          if (!grouped[source]) {
            grouped[source] = 0;
          }
          
          if (entry.lead_outcome === 'Signed' || entry.lead_outcome === 'Referred out') {
            grouped[source]++;
          }
        });
        
        // Convert grouped result to array for table
        this.inhousesignupsummarycount = Object.entries(grouped).map(([source, count]) => ({
          marketing_source: source,
          total_signed: count
        }));
        
        
        // Group by value field (category)
        const groupedCategories = {};
        AllEntries.forEach(entry => {
          const category = entry.value || 'Uncategorized';
          if (!groupedCategories[category]) {
            groupedCategories[category] = 0;
          }
          
          if (entry.value) {
            groupedCategories[category]++;
          }
        });
        
        this.categorysignupsummarycount = Object.entries(groupedCategories).map(([value, count]) => ({
          value: value,
          total_signed: count
        }));
        
        
        
        // Filter Referred entries
        // Filter entries where status is "Referred"
        const referredEntries = filteredReferred.filter(e =>
          e.lead_outcome === 'Referred out'
        );
        
        // Group label helper
        const getGroupedLabel = (source) => {
          if (!source) return 'Unknown';
        
          const s = source.toLowerCase();
          if (s.includes('gmb')) return 'GBP';
          if (s.includes('seo')) return 'SEO';
          // Add more keyword groupings as needed...
        
          return source; // fallback
        };
        
        const groupedReferred = {};
        
        referredEntries.forEach(entry => {
          const groupLabel = getGroupedLabel(entry.marketing_source);
        
          if (!groupedReferred[groupLabel]) {
            groupedReferred[groupLabel] = {
              total_referred: 0,
              successful_referred_count: 0
            };
          }
        
          // Count all with status "Referred"
          groupedReferred[groupLabel].total_referred++;
        
          // Count successful referred out from lead_outcome column
          if (entry.lead_outcome === 'Referred out') {
            groupedReferred[groupLabel].successful_referred_count++;
          }
        });
        
        this.referredsummarycount = Object.entries(groupedReferred).map(([source, data]) => ({
          marketing_source: source,
          total_referred: data.total_referred,
          successful_referred_count: data.successful_referred_count
        }));

        
        
        this.inhousesignupsummarylist = signedEntries.map(entry => ({
          marketing_source: entry.marketing_source || 'Unknown',
          value: entry.value || 'N/A',
          case_type: entry.case_type || 'N/A',
          client_name: entry.full_name || 'N/A'
        }));
        
        
        
        // SEO PERFORMANCE MONITORING: Filter leads with signed or referred status & keyword in marketing_source
        const seoKeywords = [
          'Website', 'Ayuda', 'Web Search', 'justin',
          'Kapwa', 'Labor', 'Brain', 'Motorcyclist', 'intaker'
        ];
        
        const seoEntries = filteredWholeData.filter(entry => {
          const source = entry.marketing_source || '';
          const hasKeyword = seoKeywords.some(keyword =>
            source.toLowerCase().includes(keyword.toLowerCase())
          );
          return hasKeyword;
        });
        const seoEntriesdata = filteredReferred.filter(entry => {
          const source = entry.marketing_source || '';
          const hasKeyword = seoKeywords.some(keyword =>
            source.toLowerCase().includes(keyword.toLowerCase())
          );
          return hasKeyword;
        });
        
        const seoMTDLeads = seoEntries.length;
        const seoSignedEntries = seoEntriesdata.filter(e => e.lead_outcome === 'Signed');
        const seoSigned = seoSignedEntries.length;
        const seoReferred = seoEntriesdata.filter(e => e.lead_outcome === 'Referred out').length;
        const seoAR = ((seoSigned + seoReferred) / seoMTDLeads) * 100 || 0;
        const seoUniqueNames = new Set(seoSignedEntries.map(e => e.full_name)).size;
        
        this.seoperformancelist = [{
          mtd_lead: seoMTDLeads,
          mtd_signed: seoSigned,
          successful_referrals: seoReferred,
          mtd_acquisition_rate: seoAR.toFixed(2) + '%',
          unique_sign_ups: seoUniqueNames
        }];

        const seoEntriesData = filteredWholeData.filter(entry => {
          const source = entry.marketing_source || '';
          const hasKeyword = seoKeywords.some(keyword =>
            source.toLowerCase().includes(keyword.toLowerCase())
          );
          return hasKeyword;
        });
        // Group SEO leads by case value (signed and referred counts, excluding Uncategorized)
        const groupedSEOByValue = {};
        
        seoEntriesData.forEach(entry => {
        //   const rawValue = entry.value;
        //   if (!rawValue || rawValue.trim() === '') return; // skip Uncategorized
        
          const val = entry.value?.trim() || 'Uncategorized';
        
        //   const val = rawValue.trim();
        
          if (!groupedSEOByValue[val]) {
            groupedSEOByValue[val] = {
              case_value: val,
              signed: 0,
              successful_referred_out: 0
            };
          }
        
          if (entry.status === 'Signed Up') {
            groupedSEOByValue[val].signed++;
          }
          if (entry.status === 'Pending Referral') {
            groupedSEOByValue[val].signed++;
          }
        
          if (entry.status === 'Referred') {
            groupedSEOByValue[val].successful_referred_out++;
          }
        });
        
        this.seoperformancevaluelist = Object.values(groupedSEOByValue);

        
        
        // Group SEO Entries by Brand Keywords
        const brandKeywords = {
          'Ayuda California': ['ayuda'],
          'Justin For Justice': ['jfj', 'justin', 'intaker', 'Justin For Justice'],
          'Kapwa Justice Community ': ['kapwa', 'kj'],
          'Labor Law Advocates': ['lla', 'labor law'],
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
          const referred = matchingEntries.filter(e =>  e.status === 'Referred').length;
          const ar = ((signed + referred) / mtd_lead) * 100 || 0;
        
          if (mtd_lead > 0) {
            brandStats[brand] = {
              brand: brand,
              mtd_lead,
              mtd_signed: signed,
              successful_referrals: referred,
              mtd_acquisition_rate: ar.toFixed(2) + '%'
            };
          }
        });
        
        this.otherbrandseoperformancelist = Object.values(brandStats);
        
        
        
        
        // Initialize brand-value matrix
        const caseValueMatrix = {};
        const allBrands = Object.keys(brandKeywords);
        const caseValueSet = new Set();
        
        // Build brand-case_value stats
        seoEntries.forEach(entry => {
        //   if (!entry.value || entry.value.trim() === '') return;
        //   const val = entry.value.trim();
          
          
          const val = entry.status?.trim() || 'Uncategorized';
          const src = (entry.marketing_source || '').toLowerCase();
        
          let matchedBrand = null;
          for (const [brand, keywords] of Object.entries(brandKeywords)) {
            if (keywords.some(kw => src.includes(kw))) {
              matchedBrand = brand;
              break;
            }
          }
        
          if (!matchedBrand) return; // Skip unmatched brands
        
          caseValueSet.add(val);
          const key = val;
        
          if (!caseValueMatrix[key]) caseValueMatrix[key] = {};
        
          if (!caseValueMatrix[key][matchedBrand]) {
            caseValueMatrix[key][matchedBrand] = 0;
          }
        
          if (entry.status === 'Signed Up' || entry.status === 'Referred') {
            caseValueMatrix[key][matchedBrand]++;
          }
        });
        
        // Convert to array for rendering
        const caseValueList = Array.from(caseValueSet).sort();
        this.otherbrandseoperformancecasevalue = caseValueList.map(value => {
          const row = { value };
          allBrands.forEach(brand => {
            row[brand] = caseValueMatrix[value]?.[brand] || 0;
          });
          return row;
        });
        
        this.otherbrandseoperformancebrands = allBrands;
        
        
        // Step: Group SEO leads by call_outcome and brand
        const outcomeMatrix = {};
        const outcomeSet = new Set();
        const brandSet = new Set();
        
        seoEntries.forEach(entry => {
          const outcome = entry.status?.trim();
          const source = entry.marketing_source?.toLowerCase();
          let matchedBrand = null;
        
          for (const [brand, keywords] of Object.entries(brandKeywords)) {
            if (keywords.some(kw => source.includes(kw))) {
              matchedBrand = brand;
              break;
            }
          }
        
          if (!matchedBrand) return; // skip if brand not matched
        
          outcomeSet.add(outcome);
          brandSet.add(matchedBrand);
        
          if (!outcomeMatrix[outcome]) outcomeMatrix[outcome] = {};
          if (!outcomeMatrix[outcome][matchedBrand]) outcomeMatrix[outcome][matchedBrand] = 0;
        
          outcomeMatrix[outcome][matchedBrand]++;
        });
        
        // Create final array
        const sortedOutcomes = Array.from(outcomeSet).sort();
        const sortedBrands = Array.from(brandSet).sort();
        
        this.seoperformanceleadsummarybrands = sortedBrands;
        
        this.seoperformanceleadsummaryoutcome = sortedOutcomes.map(outcome => {
          const row = { value: outcome };
          sortedBrands.forEach(brand => {
            row[brand] = outcomeMatrix[outcome]?.[brand] || 0;
          });
          return row;
        });


        // Website Leads - filter entries with 'website' or 'web search' in marketing_source
        this.websiteLeads = filtered.filter(entry => {
          const source = entry.marketing_source?.toLowerCase() || '';
          const status = entry.status;
          const validSource = source.includes('justin') || source.includes('labor') || source.includes('brain') || source.includes('motorcyclist');
          const validStatus = status === 'Signed Up' || status === 'Pending Agreement' || status === 'Chase' || status === 'Pending Referral';
          return validSource && validStatus;
        });
        
        
        
          // Existing filtering logic for this.entries (Lead Docket)
          // Update topVisitedPages from GA filtered entries
          const filteredGA = this.gaEntries.filter(entry => 
              entry.report_date && inRange(entry.report_date)
            );
          const uniquePagesMap = {};
            filteredGA.forEach(entry => {
              const path = entry.page_path;
              const views = parseInt(entry.screen_pageviews || 0);
            
              // Only keep the highest-view version of each unique page_path
              if (!uniquePagesMap[path] || views > parseInt(uniquePagesMap[path].screen_pageviews || 0)) {
                uniquePagesMap[path] = entry;
              }
            });
            
            // Convert to array, sort, slice top 10
            this.topVisitedPages = Object.values(uniquePagesMap)
              .sort((a, b) => parseInt(b.screen_pageviews || 0) - parseInt(a.screen_pageviews || 0))
              .slice(0, 10);
              
              
        
        
          // Filter postedblogs for "On Page Blog" and "Off Page Blog" in range
            const filteredArticles = this.postedblogs.filter(entry => {
              const date = entry.task_date;
              return date && inRange(date) && 
                (entry.type === 'On Page Blog' || entry.type === 'Off Page Blog');
            });
            
            // Count by type
            const onpageCount = filteredArticles.filter(e => e.article_type === 'On Page Blog').length;
            const offpageCount = filteredArticles.filter(e => e.article_type === 'Off Page Blog').length;
            
            // Store result
            this.blogs = [
              { article_type: 'On Page Blog', count: onpageCount, target: this.onpagetarget, targetcompliant: ((onpageCount / this.onpagetarget) * 100).toFixed(2) },
              { article_type: 'Off Page Blog', count: offpageCount, target: this.offpagetarget, targetcompliant: ((offpageCount / this.offpagetarget) * 100).toFixed(2) }
            ];
            
            
            
            
            
        
        // GBP PERFORMANCE MONITORING: Filter leads with signed or referred status & keyword in marketing_source
        const gbpKeywords = ['GMB'];
        
        const gbpEntries = filteredWholeData.filter(entry => {
          const source = entry.marketing_source?.toLowerCase() || '';
          const hasKeyword = gbpKeywords.some(keyword =>
            source.toLowerCase().includes(keyword.toLowerCase())
          );
          return hasKeyword;
        });
        
        const gbpEntrySigned = filteredReferred.filter(entry => {
          const source = entry.marketing_source?.toLowerCase() || '';
          const hasKeyword = gbpKeywords.some(keyword =>
            source.toLowerCase().includes(keyword.toLowerCase())
          );
          return hasKeyword;
        });
        
        const gbpMTDLeads = gbpEntries.length;
        const gbpSignedEntries = gbpEntrySigned.filter(e => e.lead_outcome === 'Signed');
        const gbpSigned = gbpSignedEntries.length;
        const gbpReferred = gbpEntrySigned.filter(e => e.status === 'Referred out').length; 
        const gbpAR = ((gbpSigned + gbpReferred) / gbpMTDLeads) * 100 || 0;
        const gbpUniqueNames = new Set(gbpSignedEntries.map(e => e.full_name)).size;
        
        this.gbpperformancemonitor = [{
          mtd_lead: gbpMTDLeads,
          mtd_signed: gbpSigned,
          mtd_acquisition_rate: gbpAR.toFixed(2) + '%',
        }];
        
        
        
        const gbpEntriesData = filteredWholeData.filter(entry => {
          const source = entry.marketing_source?.toLowerCase() || '';
          const hasKeyword = gbpKeywords.some(keyword =>
            source.toLowerCase().includes(keyword.toLowerCase())
          );
          return hasKeyword;
        });
        // Group GBP leads by case value (signed and referred counts, excluding Uncategorized)
        const groupedGBPByValue = {};
        
        gbpEntriesData.forEach(entry => {
        //   const rawValue = entry.value;
          
          const val = entry.value?.trim() || 'Uncategorized';
        //   if (!rawValue || rawValue.trim() === '') return; // skip Uncategorized
        
        //   const val = rawValue.trim();
        
          if (!groupedGBPByValue[val]) {
            groupedGBPByValue[val] = {
              case_value: val,
              signed: 0,
              successful_referred_out: 0,
              unique_sign_ups: 0
            };
          }
        
          if (entry.lead_outcome === 'Signed') {
            groupedGBPByValue[val].signed++;
            groupedGBPByValue[val].unique_sign_ups++;
            
          }
        
          if (entry.lead_outcome === 'Referred out') {
            groupedGBPByValue[val].successful_referred_out++;
            groupedGBPByValue[val].unique_sign_ups++;
          }
        });
        
        this.gbpcasevalue = Object.values(groupedGBPByValue);
        
        
        
        // Step: Group GBP leads by call_outcome and brand
        const gbpleadkeyword = {
          'GMB': ['gmb'],
        };
        
        const gbpoutcomeMatrix = {};
        const gbpoutcomeSet = new Set();
        const gbpbrandSet = new Set();
        
        gbpEntriesData.forEach(entry => {
          const outcome = entry.status?.trim() || 'No Answer';
          const source = entry.marketing_source?.toLowerCase() || '';
          let matchedBrand = null;
        
          for (const [brand, keywords] of Object.entries(gbpleadkeyword)) {
            if (keywords.some(kw => source.includes(kw))) {
              matchedBrand = brand;
              break;
            }
          }
        
          if (!matchedBrand) return; // skip if brand not matched
        
          gbpoutcomeSet.add(outcome);
          gbpbrandSet.add(matchedBrand);
        
          if (!gbpoutcomeMatrix[outcome]) gbpoutcomeMatrix[outcome] = {};
          if (!gbpoutcomeMatrix[outcome][matchedBrand]) gbpoutcomeMatrix[outcome][matchedBrand] = 0;
        
          gbpoutcomeMatrix[outcome][matchedBrand]++;
        });
        
        // Create final array
        const gbpsortedOutcomes = Array.from(gbpoutcomeSet).sort();

        this.gbpsummaryoutcome = gbpsortedOutcomes.map(outcome => {
          return {
            case_value: outcome,
            signed: gbpoutcomeMatrix[outcome]?.['GMB'] || 0,
            referred: gbpoutcomeMatrix[outcome]?.['GMB'] || 0,
            lost: gbpoutcomeMatrix[outcome]?.['GMB'] || 0,
            rejected: gbpoutcomeMatrix[outcome]?.['GMB'] || 0,
            open: gbpoutcomeMatrix[outcome]?.['GMB'] || 0
          };
        });
        
        // Filter On Page Blog posts within date range
        const filtereOnPageBlog = this.postedblogs.filter(entry => {
          const date = entry.task_date;
          return date && inRange(date) && entry.type === 'On Page Blog';
        });
        
        // Group by brand and count
        const countsByBrand = filtereOnPageBlog.reduce((acc, entry) => {
          if (!acc[entry.brand]) {
            acc[entry.brand] = 0;
          }
          acc[entry.brand]++;
          return acc;
        }, {});
        
        // Convert to array for gbppostsummary
        this.gbppostsummary = Object.entries(countsByBrand).map(([brand, count]) => ({
          brand,
          count,
        }));
        
        
        
        
        
        // RND worx PERFORMANCE MONITORING: Filter leads with signed or referred status & keyword in marketing_source
        const rndKeywords = ['RND'];
        
        const rndEntries = filtered.filter(entry => {
          const source = entry.marketing_source || '';
          const hasKeyword = rndKeywords.some(keyword =>
            source.toLowerCase().includes(keyword.toLowerCase())
          );
          return hasKeyword;
        });
        
        const rndMTDLeads = rndEntries.length;
        const rndSignedEntries = rndEntries.filter(e => e.status === 'Signed Up');
        const rndSigned = rndSignedEntries.length || 0;
        const rndReferred = rndEntries.filter(e => e.status === 'Referred').length;
        const rndAR = ((rndSigned + rndReferred) / rndMTDLeads) * 100 || 0;
        const rndCPL = (3,320.96 / rndMTDLeads) || 0;
        const rndcost = (9.48 * rndEntries.length) || 0;
        const rndCPA = (3,320.96 / rndSigned) || 0;
        // const rndCPA = rndSigned ? 3320.96 / rndSigned : 0;
        const rndUniqueNames = new Set(rndSignedEntries.map(e => e.full_name)).size;
        
        this.rndperformancesummary = [{
          mtd_cost: rndcost,
          mtd_lead: rndMTDLeads,
          mtd_cpl: rndCPL.toFixed(2),
          mtd_cpa: rndCPA,
          mtd_signed: rndSigned,
          mtd_acquisition_rate: rndAR.toFixed(2) + '%',
        }];
    
      
         // Group RND leads by case value (excluding Uncategorized/blank)
        const groupedRNDByValue = {};
        
        rndEntries.forEach(entry => {
        //   const val = entry.value ? entry.value.trim() : '';
        
          // Skip Uncategorized or empty values
        //   if (!val || val.toLowerCase() === 'uncategorized') return;
          
          
          const val = entry.value?.trim() || 'Uncategorized';
        
          if (!groupedRNDByValue[val]) {
            groupedRNDByValue[val] = {
              case_value: val,
              signed: 0,
              successful_referred_out: 0,
              uniqueClients: new Set() // store unique full_names or IDs
            };
          }
        
          const status = entry.status;
        
          if (status === 'Signed' || status === 'Chase' || status === 'Pending Referral' || status === 'Pending Review') {
            groupedRNDByValue[val].signed++;
        
            // Use full_name or another unique field like lead_id
            if (entry.full_name) {
              groupedRNDByValue[val].uniqueClients.add(entry.full_name);
            }
          }
        
          if (status === 'Referred out' || entry.status === 'Referred') {
            groupedRNDByValue[val].successful_referred_out++;
          }
        });
        
        // Convert to array and calculate unique_sign_ups from the Set
        this.rndcasevalue = Object.values(groupedRNDByValue).map(item => ({
          case_value: item.value,
          signed: item.signed,
          successful_referred_out: item.successful_referred_out,
          unique_sign_ups: item.uniqueClients.size
        }));
        
        
        // RND summary outcome
        const statusCounts = {};

        rndEntries.forEach(entry => {
        //   const status = entry.status || 'Unknown';
          
          const status = entry.status?.trim() || 'Unknown';
        
          if (!statusCounts[status]) {
            statusCounts[status] = 1;
          } else {
            statusCounts[status]++;
          }
        });
        
        // Step 3: Convert to array for rendering in Vue
        this.rndleadsummaryoutcome = Object.keys(statusCounts).map(status => ({
          status,
          count: statusCounts[status]
        }));



        // Filter emails table by date range
        this.emailsummary = this.rawEmailsummary.filter(item => {
          const date = item.date;
          return date && inRange(date);
        });
        // Group email summaries by type
        const typeCountMap = {};
        
        this.emailsummary.forEach(item => {
        //   const type = item.type || 'Unknown';
          const type = item?.type || 'Unknown';
          if (!typeCountMap[type]) {
            typeCountMap[type] = 1;
          } else {
            typeCountMap[type]++;
          }
        });
        
        this.emailTypeSummary = Object.entries(typeCountMap).map(([type, count]) => ({
          type,
          count
        }));
        
        
        
        // Filter GMB location table by date range
        
        // GMB location — show the latest record per location (respects the date range)
        const getDateField = (it) =>it.date;
        // First, try to keep only rows inside the selected range (if any match)
        const gmbFilteredByRange = (this.rawGmblocsummary || []).filter(it => {
          const ds = getDateField(it);
            console.log(ds);
          return ds && inRange(ds);
        });
        
        // If no rows match the range, fallback to using ALL rows (still picking the latest per location)
        const sourceRows = gmbFilteredByRange.length ? gmbFilteredByRange : (this.rawGmblocsummary || []);
        
        // Group by location and keep the newest row
        const newestByLocation = {};
        sourceRows.forEach(it => {
          const loc = it.location || 'Unknown';
          const ds = getDateField(it);
          const d  = ds ? new Date(ds) : new Date(0);
          if (!newestByLocation[loc] || d > newestByLocation[loc]._d) {
            newestByLocation[loc] = { ...it, _d: d }; // stash parsed date for sorting
          }
        });
        
        // Convert to array, sort by newest first, and strip helper field
        this.gmblocation = Object.values(newestByLocation)
          .sort((a, b) => b._d - a._d)
          .map(({ _d, ...rest }) => rest);
        
        
        // Filter GMB location table by date range
        const seen = new Set();
        this.gmblocation = (this.rawGmblocsummary || [])
          .filter(item => {
            const key = item.location;
            if (seen.has(key)) return false;
            seen.add(key);
            return true;
          });
        
      }

    }
  });
});
</script>


