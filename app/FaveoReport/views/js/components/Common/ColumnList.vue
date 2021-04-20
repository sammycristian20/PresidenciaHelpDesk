<template>
  <span id="column-list">

	 <div class="btn-group float-right">
		
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="true"
			id="dropdown-menu-columns">
			
			<i class="fas fa-columns"> </i> {{lang('columns')}}
		</button>
		
		<div class="dropdown-menu dropdown-menu-right" role="menu" x-placement="bottom-end"
			aria-labelledby="dropdown-menu-columns" id="report-columns-dropdown">
			
			<draggable-element v-model="columns" handle=".drag-btn" class="report-column-element">
				
				<template v-for="column in columns">
					
					<a href="javascript:;" class="dropdown-item report-column-list" :key="column.id">
						
						<input type="checkbox" :value="column.key" v-model="column.is_visible">
						
						<span @click="(event) => onLabelClick(event, column)" class="column-label">{{column.label}}</span>

						<button v-if="column.is_custom" class="btn btn-danger btn-xs float-right" @click="() => onDelete(column.id)">
						  <i class="fas fa-trash"></i>
						</button>

						<button v-if="column.is_custom" class="btn btn-primary btn-xs float-right margin-horizontal-5" @click="()=> onEdit(column)">
						  <i class="fas fa-edit"></i>
						</button>

						<span
						  class="float-right margin-horizontal-5 drag-btn"
						  title="Move this column"
						  @click="preventToCloseBox">
						  <i class="fas fa-arrows-alt-v" aria-hidden="true"></i>
						</span>
					</a>
				</template>
				
				<div class="column-list-btn-element" slot="footer">
						
					<button type="button" id="save-columns" class="btn btn-sm btn-primary" @click="saveColumns">

						<i class="fas fa-sync"> </i> {{lang('apply')}}
					</button>

					<button class="btn btn-sm btn-primary" id="add-custom-column" @click="() => showAddCustomColumn = true">
					  
					  <i class="fas fa-plus"> </i> {{lang('add_custom_column')}}
					</button>
				 </div>
			</draggable-element>
		</div>
	</div>

	 <custom-column
		v-if="showAddCustomColumn"
		:add-custom-column-url="addCustomColumnUrl"
		:short-code-url="shortCodeUrl"
		:column="column"
		:closeView="hideCustomColumn"
		:isEditing="isEditingCustomColumn"
	 />

	 <!-- it will only come when data has been populated -->
	 <custom-loader v-if="loading && hasDataPopulated"></custom-loader>

  </span>
</template>

<script>

import axios from 'axios';
import {errorHandler, successHandler} from 'helpers/responseHandler';
import {lang, boolean} from 'helpers/extraLogics';
import draggable from 'vuedraggable'

export default {
  name : 'column-list',

  data(){
	 return {

		/**
		 * If waiting for server to respond
		 */
		loading: false,

		/**
		 * If first time api call has been made
		 */
		hasDataPopulated: false,

		/**
		 * List of columns
		 */
		columns: [],

		/**
		 * Currently Editing column
		 */
		column: {},

		/**
		 * If custom column pop is visible
		 */
		showAddCustomColumn: false,

		// If a modal is opened in edit mode
		isEditingCustomColumn: false
	 }
  },

  props : {

	 /**
	  * Url endpoint for getting table columns
	  */
	 tableColumns: {
		type: Array,
		required: true
	 },

	 /**
	  * Url endpoint for adding custom column
	  */
	 addCustomColumnUrl: {
		type: String,
		required: true
	 },

	 /**
	  * Url endpoint for deleting custom column
	  */
	 deleteCustomColumnUrl: {
		type: String,
		required: true
	 },

	 /**
	  * Url endpoint for getting short codes
	  */
	 shortCodeUrl: {
		type: String,
		required: true
	 },

	 /**
	  * Report id 
	  * used for column apply/update
	  */
	 subReportId: {
		type: Number,
		required: true
	 },

	 /**
	  * Index value of the report
	  * used for updating changed value
	  */
	 reportIndex: {
		type: Number,
		required: true
	 }
  },

  beforeMount(){

	 this.hasDataPopulated = false;

	 this.columns = this.tableColumns;

  },

  created(){
	 window.eventHub.$on('refresh-report', this.getDataFromServer);
  },

  methods: {

		/**
		 * Gets data from server
		 * @return {Void}
		 */
		getDataFromServer(){

		  this.loading = true;

		  axios.get('api/agent/report-columns/' + this.subReportId).then(res => {

			 this.columns = res.data.data;

			 window.eventHub.$emit('onColumnUpdate', this.columns, this.reportIndex);

		  }).catch(err => {

			 errorHandler(err, "tabular-report-layout");

		  }).finally(res => {

			 this.loading = false;
			 this.hasDataPopulated = true;
		  });
		},

	 /**
	  * Saves selected column on the server
	  * @return {Void}
	  */
	 saveColumns(){

		this.columns.forEach((element, index) => { element.order = index + 1 });

		window.eventHub.$emit('onColumnUpdate', this.columns, this.reportIndex)

		this.loading = true;

		axios.post('api/agent/report-columns/' + this.subReportId,  this.columns )
		  .then(res => {
			 successHandler(res, 'tabular-report-layout');
		  }).catch(err => {
			 errorHandler(err, 'tabular-report-layout');
		  }).finally(() => {
			 this.loading = false;
		  })
	 },

	 onDelete(id){
		this.loading = true;
		axios.delete(this.deleteCustomColumnUrl + '/' + id).then(res => {
		  successHandler(res, 'tabular-report-layout');
		  this.getDataFromServer();
		}).catch(err => {
		  errorHandler(err, 'tabular-report-layout');
		}).finally(() => {
		  this.loading = false;
		})
	 },

	 onLabelClick(event, clickedColumn){
		clickedColumn.is_visible = !clickedColumn.is_visible;
		this.preventToCloseBox(event);
	 },

	 preventToCloseBox(event) {
		event.stopPropagation();
	 },

	 onEdit(column){
		this.showAddCustomColumn = true;
		this.isEditingCustomColumn = true;
		this.column = column;
	 },

	 hideCustomColumn() {
		this.showAddCustomColumn = false;
		this.isEditingCustomColumn = false;
		this.column = {};
	 },
  },

  components : {
	 'custom-column': require('./CustomReportColumn'),
	 'custom-loader': require("components/MiniComponent/Loader"),
	 'draggable-element': draggable,
  }
}
</script>

<style scoped>

  .report-column-element{
	 min-width: max-content;
	 min-height: 30px;
  }

  .column-label {
	 cursor: pointer;
  }

  #report-columns-dropdown {
	 padding: 10px;
	 max-height: 55vh;
	 overflow: scroll;
	 border: 1px solid gainsboro;
  }

  .drag-btn {
	 cursor: move;
	 visibility: hidden;
  }

  .report-column-list:hover>.drag-btn{
	 visibility: visible; 
  }
  .column-list-btn-element {
	 margin: 1rem 0.8rem 0.8rem 0;
  }

</style>
