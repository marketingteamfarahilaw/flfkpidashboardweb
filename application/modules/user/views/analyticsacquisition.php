<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dashboard/portfolio.css') ?>">

<!-- Vue, Chart.js, Flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
.card {
  border-radius: 0.5rem;
  border-left: 0.25rem solid #4e73df;
  background-color: #fff;
}
.font-weight-bold { font-weight: 600; }
.text-uppercase { text-transform: uppercase; font-size: 0.75rem; }
.text-gray-800 { color: #5a5c69; }
</style>

<section class="content" id="app">
  <div class="container-fluid pt-3 pl-md-2 pr-md-2 pl-md-5 pr-md-5">
    <div class="mb-4">
      <h2>Google Analytics Acquisition (This Week or Date Filtered)</h2>
      <div v-if="loading">Loading data...</div>
      <div v-if="error" class="alert alert-danger">{{ error }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-md-4">
        <label for="dateRangePicker">Select Date Range:</label>
        <input type="text" id="dateRangePicker" class="form-control" placeholder="Pick a date range">
      </div>
    </div>

    <div v-if="filteredData.length">
      <div class="row mt-4">
        <div class="col-md-4 mb-4">
          <canvas id="channelGroupPie"></canvas>
        </div>
        <div class="col-md-4 mb-4">
          <canvas id="sourceBar"></canvas>
        </div>
        <div class="col-md-4 mb-4">
          <canvas id="sessionsLine"></canvas>
        </div>
        <div class="col-md-6 mb-4">
          <canvas id="stackedSessions"></canvas>
        </div>
        <div class="col-md-6 mb-4">
          <canvas id="topEventsBar"></canvas>
        </div>
        <div class="col-md-12 mb-5">
          <canvas id="eventDateLine"></canvas>
        </div>
      </div>
    </div>

    <div v-if="!filteredData.length && !loading">
      <p>No events found for the selected date range.</p>
    </div>
  </div>
</section>

<script>
let pieChart, barChart, lineChart, stackedChart, eventChart, eventDateChart;

function destroyIfExists(chart) {
  if (chart) chart.destroy();
}

function renderCharts(data) {
  const byChannel = {};
  const bySource = {};
  const byDate = {};
  const byMedium = {};
  const byEvent = {};
  const eventTrend = {}; // New: Event name over date trend

  data.forEach(item => {
    const { default_channel_group, session_source, session_medium, sessions, new_users, event_count, event_name, event_date } = item;

    byChannel[default_channel_group] = (byChannel[default_channel_group] || 0) + (parseInt(sessions) || 0);
    bySource[session_source] = (bySource[session_source] || 0) + (parseInt(new_users) || 0);
    byDate[event_date] = (byDate[event_date] || 0) + (parseInt(sessions) || 0);

    if (!byMedium[session_medium]) byMedium[session_medium] = { sessions: 0, new_users: 0 };
    byMedium[session_medium].sessions += (parseInt(sessions) || 0);
    byMedium[session_medium].new_users += (parseInt(new_users) || 0);

    if (event_name) {
      byEvent[event_name] = (byEvent[event_name] || 0) + (parseInt(event_count) || 0);
      if (!eventTrend[event_name]) eventTrend[event_name] = {};
      eventTrend[event_name][event_date] = (eventTrend[event_name][event_date] || 0) + (parseInt(event_count) || 0);
    }
  });

  destroyIfExists(pieChart);
  destroyIfExists(barChart);
  destroyIfExists(lineChart);
  destroyIfExists(stackedChart);
  destroyIfExists(eventChart);
  destroyIfExists(eventDateChart);

  pieChart = new Chart(document.getElementById('channelGroupPie'), {
    type: 'pie',
    data: {
      labels: Object.keys(byChannel),
      datasets: [{
        data: Object.values(byChannel),
        backgroundColor: Object.keys(byChannel).map((_, i) => `hsl(${i * 40}, 70%, 60%)`)
      }]
    }
  });

  barChart = new Chart(document.getElementById('sourceBar'), {
    type: 'bar',
    data: {
      labels: Object.keys(bySource),
      datasets: [{
        label: 'New Users',
        data: Object.values(bySource),
        backgroundColor: '#36a2eb'
      }]
    }
  });

  lineChart = new Chart(document.getElementById('sessionsLine'), {
    type: 'line',
    data: {
      labels: Object.keys(byDate),
      datasets: [{
        label: 'Sessions',
        data: Object.values(byDate),
        borderColor: '#4e73df',
        fill: false
      }]
    }
  });

  stackedChart = new Chart(document.getElementById('stackedSessions'), {
    type: 'bar',
    data: {
      labels: Object.keys(byMedium),
      datasets: [
        {
          label: 'Sessions',
          data: Object.values(byMedium).map(d => d.sessions),
          backgroundColor: '#4caf50'
        },
        {
          label: 'New Users',
          data: Object.values(byMedium).map(d => d.new_users),
          backgroundColor: '#ff9800'
        }
      ]
    },
    options: {
      scales: {
        x: { stacked: true },
        y: { stacked: true }
      }
    }
  });

  eventChart = new Chart(document.getElementById('topEventsBar'), {
    type: 'bar',
    data: {
      labels: Object.keys(byEvent),
      datasets: [{
        label: 'Event Count',
        data: Object.values(byEvent),
        backgroundColor: '#9c27b0'
      }]
    },
    options: {
      indexAxis: 'y'
    }
  });

  const eventDateLabels = Array.from(new Set(data.map(d => d.event_date))).sort();
  const eventDateDatasets = Object.keys(eventTrend).map((eventName, idx) => {
    return {
      label: eventName,
      data: eventDateLabels.map(date => eventTrend[eventName][date] || 0),
      borderColor: `hsl(${idx * 60}, 70%, 50%)`,
      fill: false
    };
  });

  eventDateChart = new Chart(document.getElementById('eventDateLine'), {
    type: 'line',
    data: {
      labels: eventDateLabels,
      datasets: eventDateDatasets
    }
  });
}

$(document).ready(function () {
  new Vue({
    el: '#app',
    data: {
      rawData: [],
      filteredData: [],
      loading: false,
      error: '',
      dateRange: {
        start: null,
        end: null
      }
    },
    methods: {
      async fetchData() {
        try {
          this.loading = true;
          const res = await fetch("https://lmthrp.com/api/kpi/fetchgaacqiosition", CONFIG.HEADER);
          const json = await res.json();

          if (json.status && Array.isArray(json.response)) {
            const cleanedData = json.response.map(item => ({
              ...item,
              event_count: parseInt(item.event_count || 0),
              sessions: parseInt(item.sessions || 0),
              new_users: parseInt(item.new_users || 0),
              event_date: item.date
            }));
            this.rawData = cleanedData;
            this.filterThisWeek();
          } else {
            this.error = 'Invalid response format';
          }
        } catch (err) {
          console.error(err);
          this.error = 'An error occurred while fetching data';
        } finally {
          this.loading = false;
        }
      },

      filterThisWeek() {
        const today = new Date();
        const day = today.getDay();
        const start = new Date(today);
        const end = new Date(today);
        start.setDate(today.getDate() - day);
        end.setDate(today.getDate() + (6 - day));

        this.dateRange.start = start;
        this.dateRange.end = end;
        this.applyFilter();
      },

      applyFilter() {
        const start = new Date(this.dateRange.start);
        const end = new Date(this.dateRange.end);
        end.setHours(23, 59, 59, 999);

        this.filteredData = this.rawData.filter(item => {
          const eventDate = new Date(item.event_date);
          return eventDate >= start && eventDate <= end;
        });

        this.$nextTick(() => {
          renderCharts(this.filteredData);
        });
      }
    },
    mounted() {
      this.fetchData();

      flatpickr("#dateRangePicker", {
        mode: "range",
        dateFormat: "Y-m-d",
        onChange: (selectedDates) => {
          if (selectedDates.length === 2) {
            this.dateRange.start = selectedDates[0];
            this.dateRange.end = selectedDates[1];
            this.applyFilter();
          } else {
            this.filteredData = this.rawData;
            this.$nextTick(() => {
              renderCharts(this.filteredData);
            });
          }
        }
      });
    }
  });
});
</script>
