<template>
	<!-- holiday table -->

	<div class="card card-light">

		<div class="card-header">

			<h3 class="card-title">Yearly Holiday List
			
				<tool-tip :message="lang('holidays_will_be_ignored_when_calculating_SLA_for_a_ticket')" size="medium">
					
				</tool-tip>
			</h3>

			<div class="card-tools">

			<button class="btn btn-tool" @click="showModal = true" v-tooltip="lang('add_new')">

				<span class="glyphicon glyphicon-plus"> </span>
			</button>
			</div>
		</div>

		<div class="card-body">
								
			<!-- alert component -->
			<alert-notification :successShow="SuccessAlert" :errorShow="ErrorAlert" :successMsg="lang(success)" :errorMsg="lang(error)" />
			<!-- table -->
			<table v-if="data.length != 0" class="table">
				<thead>
					<th class="head">Date</th>
					<th class="head">Description</th> 
					<th class="head">Action</th> 
				</thead>
				<tbody>
					<tr v-for="(day,index) in data" class="head">
						<td>{{formattedDate(moment(day.date, "MM-DD-YYYY"))}}</td>
						<td>{{day.description}}</td>
						<td>
							<a id="edit" @click="editHoliday(day,index)" href="javascript:void(0)">
								<span title="Edit Holiday" class="btn btn-primary"><i class="fa fa-edit"></i></span>
							</a>
							<a id="delete" @click="deleteHoliday(index)" href="javascript:void(0)">
								<span title="Delete Holiday" class="btn btn-danger"><i class="fas fa-trash"></i></span>
							</a>
						</td>
						
					</tr>
				</tbody>
			</table>

			<table v-else class="table">
				<p style="text-align:center">{{lang('no-data-to-show')}}</p>
			</table>
			<!-- popup -->
			<holiday-event v-if="showModal" :onClose="onClose" :data="holidayData" :index="index" :editData="editData" :deleteData="deleteData" :showModal="showModal" :addRow="addRoww" :deleteRow="deleteRoww"></holiday-event>
							
		</div>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';

	import moment from 'moment';

	import { mapGetters } from 'vuex'

	export default {
		
		name:'holiday-list',

		description:'displays list of agents in tabular form with required actions',

		props:{

			/**
			* The function which will be called as soon as user click on the save button        
			* @type {Function}
			*/
			addRow : {type:Function},

			/**
			* The function which will be called as soon as user click on the delete button        
			* @type {Function}
			*/
			deleteRow : {type:Function},

			/**
			 * contains holiday data
			 * @type {Array} 
			 */
			holidayData: {type: Array, default:()=>[]}
		},

		data(){
			return {

				/**
				 * modasl popup status
				 * @type {Boolean}
				 */
				showModal:false,

				/**
				 * holiday list data
				 * @type {Array}
				 */
				data:this.holidayData,

				/**
				 * edit holiday data
				 * @type {Object}
				 */
				editData : {},

				/**
				 * delete holiday status
				 * @type {String}
				 */
				deleteData: 'no',

				/**
				 * index of the holiday
				 * @type {Number}
				 */
				index:0,

				/**
				 * alert fields
				 * @type {String}
				 */
				SuccessAlert:'none',success:'',error:'',ErrorAlert:'none',

				moment:moment
			}
		},

		watch:{
			/**
			 * returns new holiday data for validation
			 * @param  {Array} newValue 
			 * @param  {Array} oldValue 
			 * @return {Array} newvalue
			 */
			holidayData(newValue,oldValue){
				this.data = newValue
			}
		},

		computed:{
			...mapGetters(['formattedTime','formattedDate'])
		},

		methods:{

			/**
			 * this method will be called when we click on close button on the modal popup 
			 * after closing modal this method will reset the default values
			 * @return {Void}
			 */
			onClose(){
				this.showModal = !this.showModal;
				this.editData = {}
				this.deleteData = 'no'
			},

			/**
			 * adding new value in holiday list
			 * @param {Object}
			 */
			addRoww(row){
				this.data.push(row);
				this.onClose();
			},

			/**
			 * deletes a value in holiday list
			 * @param {Number}
			 */
			deleteRoww(index){
				this.data.splice(index,1);
				this.onClose();
			},

			/**
			 * this method will be called when we click on delete button on the table
			 * it will make showModal value as true for showing delete modal popup
			 * @return {Void}
			 */
			deleteHoliday(index) {
				this.showModal = true;
				this.deleteData = 'yes';
				this.index = index;
			},

			/**
			 * this method will be called when we click on edit button on the table
			 * it will make showModal value as true for showing edit modal popup
			 * @return {Void}
			 */
			editHoliday(day,index){
				this.showModal = true;
				this.editData = day;
				this.index = index;
			}
		},
		
		components:{
		
			'holiday-event' : require('./Holiday.vue'),

			'alert-notification' : require('components/Common/pageNotification.vue'),

			"tool-tip": require("components/MiniComponent/ToolTip"),
		
		} 
	};

</script>

<style type="text/css" scoped>
	.flex {
		display: flex; float: right; margin-left:5px; margin-top: -5px;
	}
	.flex .btn{
		margin:5px;
	}
	.head{
		text-align: center !important;
	}
	#holidayTable{
		border:1px solid #6253530d !important;
	}
	.margin {
		margin-top: 10px !important;
	}
	.alert-success{
		margin-left: 10px; margin-right: 10px;
	}
</style>