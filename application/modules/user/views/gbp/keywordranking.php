<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<style>
    td {
        font-size: 20px;
    }
</style>
<section class="content" id="keywordRankingsApp">
    <div class="container-fluid pt-3 pl-md-2 pr-md-2 pl-md-5 pr-md-5">
      
        <div>

        	<h4 class="fw-300 mb-3">Keyword Rankings Chart</h4>
            <div v-for="(records, engine) in groupedRankings" :key="engine" class="mb-5">
                <h4 class="bg-light p-2">{{ engine }}</h4>
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                      <tr>
                        <th rowspan="2">Keyword</th>
                        <th rowspan="2">Volume</th>
                        <th colspan="2" class="text-center"><img src="https://www.gstatic.com/images/icons/material/system/1x/place_gm_blue_24dp.png"> Local Pack</th>
                        <th colspan="2" class="text-center"><img src="https://www.gstatic.com/images/icons/material/system/1x/place_gm_blue_24dp.png"> Local Finder</th>
                      </tr>
                      <tr>
                        <th>Rank</th><th>Change</th>
                        <th>Rank</th><th>Change</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="item in combinedKeywords" :key="item.keyword">
                        <td>{{ item.keyword }}</td>
                        <td>{{ item.volume }}</td>
                
                        <td>{{ item.localpack?.rank ?? '-' }}</td>
                        <td :class="getChangeClass(item.localpack?.change)">{{ formatChange(item.localpack?.change) }}</td>
                
                        <td>{{ item.localfinder?.rank ?? '-' }}</td>
                        <td :class="getChangeClass(item.localfinder?.change)" >{{ formatChange(item.localfinder?.change) }}</td>
                      </tr>
                    </tbody>
                  </table>
              </div>
        	<div class="form-group mb-4">
            	<label for="engineSelect">Filter by Search Engine:</label>
            	<select class="form-select" v-model="selectedEngine" id="engineSelect">
            		<option value="">All Engines</option>
            		<option v-for="engine in uniqueEngines" :key="engine">{{ engine }}</option>
            	</select>
        	</div>
        	
	        <canvas id="keywordChart" height="100"></canvas>
        </div>
    </div>
</section>


<script>
	new Vue({
	el: '#keywordRankingsApp',
    	data: {
        	rankings: [],
        	selectedEngine: '',
        	chartInstance: null,
        	
            rawData: [],
            combinedKeywords: []
    	},
	computed: {
    	filteredRankings() {
    		return this.rankings.filter(r =>
    		!this.selectedEngine || r.search_engine === this.selectedEngine
    		);
    	},
    	uniqueEngines() {
    		return [...new Set(this.rankings.map(r => r.search_engine))];
    	},
    	
        groupedRankings() {
          const grouped = {};
          this.rankings.forEach(r => {
            if (this.selectedEngine && r.search_engine !== this.selectedEngine) return;
            if (!grouped[r.search_engine]) grouped[r.search_engine] = [];
            grouped[r.search_engine].push(r);
          });
          return grouped;
        }
	},
	methods: {
	    processData() {
          const grouped = {};
    
          this.rawData.forEach(item => {
            if (!grouped[item.keyword]) {
              grouped[item.keyword] = {
                keyword: item.keyword,
                volume: item.volume,
                desktop: null,
                mobile: null,
                localpack: null,
                localfinder: null
              };
            }
    
            const target = grouped[item.keyword];
            const engine = item.search_engine.toLowerCase();
    
            if (engine.includes('desktop')) target.desktop = item;
            else if (engine.includes('mobile')) target.mobile = item;
            else if (engine.includes('local-pack')) target.localpack = item;
            else if (engine.includes('local-finder') || engine.includes('places')) target.localfinder = item;
          });
    
          this.combinedKeywords = Object.values(grouped);
        },
        formatChange(change) {
          if (change === null || change === undefined || change === 'not_ranked') return '-';
          return change >= 0 ? `+${change}` : change;
        },
        getChangeClass(change) {
          if (typeof change !== 'number') return 'text-muted';
          if (change < 0) return 'text-success';
          if (change > 0) return 'text-danger';
          return 'text-muted';
        },
        
        fetchData: async function () {
    		
    		let response = axios.get('https://lmthrp.com/api/kpi/keywordrank', CONFIG.HEADER);
                    
            return response;
        },
        
        setKeywordRank: async function () {
            try {
                const result = await this.fetchData();
                this.rankings = result.data.response;
                this.rawData = this.rankings;
                this.processData();
    		    this.renderChart();
            } catch (error) {
                console.log(error);
            }
        },
    	renderChart() {
    		if (this.chartInstance) {
    		    this.chartInstance.destroy();
    		}
    
    		const labels = this.filteredRankings.map(r => r.keyword);
    		const ranks = this.filteredRankings.map(r => {
        		const val = parseInt(r.rank);
        		return isNaN(val) ? null : val;
    		});
    
    		const ctx = document.getElementById('keywordChart').getContext('2d');
    		this.chartInstance = new Chart(ctx, {
    		type: 'bar',
    		data: {
    			labels: labels,
    			datasets: [{
        			label: 'Current Rank (Lower is Better)',
        			data: ranks,
        			backgroundColor: 'rgba(54, 162, 235, 0.6)',
        			borderColor: 'rgba(54, 162, 235, 1)',
        			borderWidth: 1
    			}]
    		},
    		options: {
    			indexAxis: 'y',
    			scales: {
    			x: {
    				beginAtZero: true,
    				reverse: true,
    				title: { display: true, text: 'Ranking Position' }
    			},
    			y: {
    				title: { display: true, text: 'Keyword' }
    			}
    			},
    			responsive: true,
    			plugins: {
        			legend: { display: false },
        			tooltip: { mode: 'index', intersect: false }
    			}
    		}
    		});
    	}
	},
	watch: {
    	selectedEngine() {
    		this.renderChart();
    	}
	},
	mounted() {
	    this.setKeywordRank();
	}
	});
</script>
