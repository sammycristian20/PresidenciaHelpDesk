
<template>
  <div>
	 <!-- Loader -->
	 <loader v-if="isLoading" :duration="4000"></loader>

	 <div class="row mb-3">
		
		<div class="col-sm-12">

			<!-- category dropdown -->
			<div class="btn-group float-right" v-if="categories && categories.length">
			
				<button type="button" class="btn btn-sm btn-default dropdown-toggle text-capitalize" data-toggle="dropdown"
					id="category-dropdown">
					{{lang(categoryPrefix) }} {{selectedCategory}}
				</button>

				<div class="dropdown-menu dropdown-menu-right" style="" aria-labelledby="category-dropdown">
				
					<template v-for="(item, index) in categories">
						
						<a class="dropdown-item text-capitalize" @click="onCategoryChange(item)" href="javascript:;" 
							:class="selectedCategory === item ? 'active' : ''" :key="index" >
							{{lang(categoryPrefix)}} {{item}}
						</a>
					</template>
				</div>
			</div>

			<!-- Chart types -->
			<div class="float-right btn-group">
				
				<button class="btn btn-sm btn-default dropdown-toggle" id="chart-type-dropdown" data-toggle="dropdown">
					{{ lang('chart_type') }}
				</button>

			  <div class="dropdown-menu dropdown-menu-right" style="" aria-labelledby="chart-type-dropdown">

					<template v-for="item in chartTypes">
						
						<a class="dropdown-item" href="javascript:;" @click="onChangeChartType(item)" 
							:class="selectedChartType === item ? 'active' : ''" v-bind:key="item">
							{{lang(categoryPrefix)}} {{lang(item)}}
						</a>
					</template>
				</div>
			</div>
		</div>
	 </div>

		<div>
	 	
	 	 	<data-widget v-if="widgetDataApi" :data-widget-data="dataWidgetData" />
	 	</div>

	 <div class="row">
		  <!-- Chart panels -->
		  <div :class="layoutClass" v-for="panel in chartApiData" v-bind:key="panel.id">

			<div class="card card-light ">

				<div class="card-header">
					
					<h3 class="card-title">{{panel.panelTitle}}</h3>
				</div>
				
				<div class="card-body">
					
					<faveo-chart :chart-data="panel" :chart-type="selectedChartType" />
				</div>
			</div>
		  </div>
	 </div>

  </div>

</template>

<script>
import axios from 'axios';
import { lang, boolean } from 'helpers/extraLogics';
import { parseCategoryBasedChartApiData } from 'ChartFactory/utils';
import { errorHandler, successHandler } from 'helpers/responseHandler';
import { getValidFilterObject } from '../../helpers/utils';

export default {

  name: 'category-based-report',

  components: {
	 'ticket-filter': require('components/Agent/tickets/filters/TicketFilter.vue'),
	 'time-series-chart': require('../Common/TimeSeriesChart'),
	 'faveo-chart': require('ChartFactory/FaveoChart'),
	 'loader': require("components/MiniComponent/Loader"),
	 'data-widget': require('components/Common/DataVisualization/DataWidget')
  },

  props: {

	 // Api endpoint to fetch category based charts
	 categoryChartDataApi: {
		type: String,
		default: () => ''
	 },

	 // Api endpoint to fetch time series charts
	 timeSeriesChartDataApi: {
		type: String,
		default: () => ''
	 },

	 widgetDataApi: {
		type: String,
		default: () => undefined
	 },

	 // Array of categories; decides the current view of chart
	 categories: {
		type: Array,
		default: () => []
	 },

	 // Default category
	 defaultCategory: {
		type: Number | String,
		default: () => undefined
	 },

	 /**
	  * string to show in the category dropdown
	  * used as key for fetching data from server basis of category
	  */
	 categoryPrefix: {
		type: String,
		default: ''
	 },

	 defaultChartType: {
		type: String,
		default: 'bar'
	 },

	 layoutClass: {
		type: String,
		required: true
	 },

	 // Defualt filter field value objec
	 filterParams: {
		type: Object,
		default: () => {}
	 },

	 reportIndex: {
		type: Number,
		required: true
	 }

  },

  data: () => {
	 return {

		// selected chart type, default is bar chart
		selectedChartType: '',

		// selected caytegory option
		selectedCategory: '',

		// some of the chart types supported by chart js
		chartTypes: ['bar', 'horizontal_bar', 'pie', 'doughnut'],

		isLoading: true,
		chartApiData: null, // Api response chart data
		dataWidgetData: null
	 }
  },

  beforeMount() {
	 // Assign defualt category to selected category
	 this.selectedCategory = this.defaultCategory;

	 this.selectedChartType = this.defaultChartType;

	 // Make api call to fetch chart data
	 this.getDataFromServer();
  },

  methods: {

	 getColumnClass() {
		return getColumnClass()
	 },

	 // Fetch data from server
	 getDataFromServer() {
		this.isLoading = true;

		// Fetch data widget data if datawidget api endpoint provided 
		if(boolean(this.widgetDataApi)) {
		  axios.get(this.widgetDataApi, { params: this.getUrlParams() })
		  .then(res => {
			 this.dataWidgetData = res.data.data;
		  })
		  .catch(err => {
			 errorHandler(err, 'category-based-report');
		  })
		}

		axios.get(this.categoryChartDataApi , { params: this.getUrlParams() })
		.then(res => {
		  this.chartApiData = parseCategoryBasedChartApiData(res.data.data);
		  this.isLoading = false;
		})
		.catch(err => {
		  errorHandler(err, 'category-based-report');
		})
	 },

	 // Fetch chart data basis of category
	 onCategoryChange(value) {
		this.selectedCategory = value;
		this.$emit('updateChangedValue', this.selectedCategory, this.reportIndex, 'selected_view_by');
		this.getDataFromServer();
	 },

	 onChangeChartType(value) {
		this.selectedChartType = value;
		this.$emit('updateChangedValue', this.selectedChartType, this.reportIndex, 'selected_chart_type');
	 },


	 /**
	  * get Url parameters
	  * may contain slected filter values, selected category limit
	  */
	 getUrlParams() {
		let params = getValidFilterObject(this.filterParams);

		// set category type for fetching chart data, if categoryPrefix is valid
		if(typeof this.categoryPrefix !== 'undefined') {
		  params[this.categoryPrefix] = this.selectedCategory;
		}
		return params;
	 }
  },

  watch: {
	 filterParams(){
		this.getDataFromServer();
	 }
  }

}
</script>

<style lang="css" scoped>
.btn-group {
  padding-left: 3px;
  padding-right: 3px;
}
.report-box-primary {
  padding: 0px !important;
}
.ml-10 { margin-left : 11px;}
</style>