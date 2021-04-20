<template>
  <div class="faveo-chart">
    <!-- Bar chart  -->
    <bar-chart
      v-if="chartType === 'bar'"
      :chart-id="chartData.chartId"
      :chart-data="getCategoryChartData()"
      :redirect-urls="chartData.redirectURLs"
      :label-string="chartData.axisLabel"
      :chart-title="getChartTitleObj"
      >
    </bar-chart>

    <!-- Horizontal bar chart-->
    <horizontal-bar-chart
      v-if="chartType === 'horizontal_bar'"
      :chart-id="chartData.chartId"
      :chart-data="getCategoryChartData()"
      :redirect-urls="chartData.redirectURLs"
      :label-string="chartData.axisLabel"
      :chart-title="getChartTitleObj"
      >
    </horizontal-bar-chart>

    <!-- Pie chart -->
    <pie-chart
      v-if="chartType === 'pie'"
      :chart-id="chartData.chartId"
      :chart-data="getCategoryChartData()"
      :redirect-urls="chartData.redirectURLs"
      :chart-title="getChartTitleObj"
      >
    </pie-chart>

    <!-- Doughnut chart -->
    <doughnut-chart
      v-if="chartType === 'doughnut'"
      :chart-id="chartData.chartId"
      :chart-data="getCategoryChartData()"
      :redirect-urls="chartData.redirectURLs"
      :chart-title="getChartTitleObj"
      >
    </doughnut-chart>

    <!-- Line chart -->
    <line-chart
      v-if="chartType === 'line'"
      :chart-id="chartData.data[0].chartId"
      :chart-labels="chartData.labels"
      :chart-data="getTimeChartData()"
      :redirect-urls="chartData.redirectURLs"
      :chart-title="getChartTitleObj"
      >
    </line-chart>

  </div>
</template>

<script>
import { getRandomColor } from 'helpers/extraLogics';
import BarChart from 'ChartFactory/BarChart';
import HorizontalBarChart from 'ChartFactory/HorizontalBarChart';
import DoughnutChart from 'ChartFactory/DoughnutChart';
import PieChart from 'ChartFactory/PieChart';
import LineChart from 'ChartFactory/LineChart';

export default {
  name: 'faveo-chart',
  components: {
    BarChart,
    HorizontalBarChart,
    DoughnutChart,
    PieChart,
    LineChart
  },

  props: {

    /**
     * Chart data object
     * must includes `chartDataKeys` elements as keys
     */
    chartData: {
      type: Object | Array,
      required: true,
      // validator: chartData => chartDataKeys.every(key => key in chartData)
    },

    chartType: {
      type: String,
      required: true,
      validator: function (value) {
        // The value must match one of these chart types
        return ['bar', 'horizontal_bar', 'pie', 'doughnut', 'line', 'area'].indexOf(value) !== -1
      }
    },

  },

  data: function () {
    return {
    }
  },

  methods: {
    /**
     * Getiing chart datasets
     */
    getCategoryChartData() {
      return {
        labels: this.chartData.labels,
        datasets: [
          {
            backgroundColor: getRandomColor(this.chartData.labels.length),
            label: this.chartData.axisLabel,
            data: this.chartData.data
          }
        ]
      };
    },

    getTimeChartData() {
      let _data = [];
      let _redirectURLs = [];
      for (let i = 0; i < this.chartData.data.length; i++) {
        const chartColor = getRandomColor();
        let timeSereis = {
          fill: false,
          label: this.chartData.data[i].label,
          borderColor: chartColor,
          backgroundColor: chartColor,
          pointBorderColor: chartColor,
          pointBackgroundColor: chartColor,
          pointBorderWidth: 1,
          pointHoverRadius: 5,
          pointHoverBackgroundColor: '#fff',
          pointHoverBorderColor: chartColor,
          pointHoverBorderWidth: 2,
          pointRadius: 3,
          pointHitRadius: 5,
          //Data to be represented on y-axis
          data: this.chartData.data[i].values
        }
        _data.push(timeSereis);
        let redirectURLs = this.chartData.data[i].redirectTo;
        _redirectURLs.push(redirectURLs);
      }
      return {
        data: _data,
        labels: this.chartData.labels,
        categoryLabel: this.chartData.categoryLabel,
        dataLabel: this.chartData.dataLabel,
        redirectURLs: _redirectURLs
      };
    }
  },

  computed: {

    getChartTitleObj: function () {
      let titleObj = { display: false };
      if(this.chartData.labels.length === 0) {
        titleObj = {
          display: true,
          text: 'No Data Available',
          fontSize: 14,
          padding: 15
        }
      }
      return titleObj;
    }
  }

}
</script>

<style lang="css" scoped>
/* .faveo-chart {
  margin-bottom: 1rem;
  transition: box-shadow 280ms cubic-bezier(.4,0,.2,1);
  display: block;
  border-radius: 4px;
  background: #fff;
  color: rgba(0,0,0,.87);
  box-shadow: 0 2px 1px -1px rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 1px 3px 0 rgba(0,0,0,.12);
} */
.no-data-div {
  padding-top: 10px;
  text-align: center;
}
.no-data-title {
  color: #666;
  font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
  font-weight: 600;
  line-height: 1.2;
  font-size: 14px;
}
</style>