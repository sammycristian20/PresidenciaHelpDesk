<template>
	<div id="approval-progress" class="appproval">
			
			<div  v-if="!loading" id="approval-progress">

				<div class="card" v-for="(workflow, workflowIndex) in workflowData" :key="'workflow'+workflowIndex" :id="'approval-progress-element-'+workflowIndex" :style="{'border-color': getIconStyle(workflow.status,'color')}">
					<div class="card-header" :style="{'background-color': getIconStyle(workflow.status,'color')}">
						<h3 class="card-title">{{workflow.name}}</h3>
						<div class="card-tools">
							
							<span v-tooltip="workflow.status" class="btn btn-tool" :class="getIconStyle(workflow.status)"></span>
						</div>
					</div>
						<!--  levels -->
						<div class="card-body">
						<div class="levels">
							<div class="approval-level" v-for="(level,levelIndex) in workflow.approval_levels" :id="'approval-level-'+levelIndex" :style="{opacity : getOpacity(level.is_active)}">
								<div class="card" :style="{'border-color': getIconStyle(level.status,'color')}">
									<div class="card-header" :style="{'background-color': getIconStyle(level.status,'color')}">
										<h3 class="card-title">
											{{level.name}}
										</h3>

										<div class="card-tools">
											
											<span :title="level.status" class="btn btn-tool flow-icons" :class="[getIconStyle(level.status)]"></span>
										</div>
									</div>

									<div class="card-body">
										<!-- user-approvers -->
										<div class="no-wrap flex" v-for="(user,userIndex) in level.approve_users" :id="'level-user-'+userIndex">
												<span class="approver-name">{{user.name}}&nbsp;&nbsp;&nbsp;&nbsp;</span>
												<span :title="user.status" :class="['approver-status', getIconStyle(user.status)]"></span>
										</div>

										<!-- user-type-approvers -->
										<div class="no-wrap flex" v-for="(userType,userTypeIndex) in level.approve_user_types" :id="'level-user-type-'+userTypeIndex">
											<span class="approver-name">{{lang(userType.name)}}&nbsp;&nbsp;&nbsp;&nbsp;</span>
											<span :title="userType.status" :class="['approver-status', getIconStyle(userType.status)]"></span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div v-if="!loading" id="approval-workflow-actions">
				<button v-if="shallApprovalActionsBeVisible" class="btn btn-success" id="approve-button"
					@click="onWorkflowAction('approve')" name="button">
					<span class="glyphicon glyphicon-ok"></span>&nbsp;{{lang('approve')}}
				</button>
				<button v-if="shallApprovalActionsBeVisible" class="btn btn-danger" id="deny-button"
					@click="onWorkflowAction('deny')" name="button">
					<span class="glyphicon glyphicon-remove"></span>&nbsp;{{lang('deny')}}
				</button>
				<button v-if="actions.remove_approval_workflow" class="btn btn-danger" id="remove-button"
					@click="onWorkflowAction('remove')" name="button">
					<span class="fas fa-trash"></span>&nbsp;{{lang('remove')}}
				</button>
			</div>


			<!-- confirmation pop-up -->
			<modal v-if="showModal" :showModal="true" :onClose="() => showModal = false">
				<div slot="title">

					<h4 class="modal-title">{{lang(modalTitle)}}</h4>
				</div>

				<div slot="alert"><alert/></div>

				<div slot="fields">
					
					<loader v-if="loading"></loader>

					<span v-if="!loading">{{lang(modalQuestion)}}</span>

					<div class="row">

						<text-field v-if="!loading && action !== 'remove'" id="textarea" :label="lang('comment')" :value="comment" type="textarea" name="comment"
						:onChange="onChange" classname="col-sm-12" :required="true"></text-field>
					</div>
				</div>

				<div slot="controls">
					<button id="approval-action-confirm" type="button" @click = "onWorkflowActionConfirm()" class="btn btn-primary">
						<i class="glyphicon glyphicon-ok" aria-hidden="true"></i>&nbsp;{{lang('confirm')}}
					</button>
				</div>
			</modal>

	</div>
</template>

<script>

import axios from 'axios';
import Modal from 'components/Common/Modal';
import {errorHandler, successHandler} from 'helpers/responseHandler';
import {store} from 'store';
import { mapGetters } from 'vuex';
import {validateApprovalTicketSettings} from "helpers/validator/approvalTicketRules";
import { lang } from 'helpers/extraLogics';

export default {
	name:'approval-progress',

	description: 'Handles Approval Progress of a ticket at any given time',

	props:{

		/**
		 * If of the ticket for which approval progress is required
		 */
		ticketId: {type: Number|String, required:true},

		actions : { type : Object, default : ()=>{}},

		alertName : { type : String, default : ''},
	},
	data(){
		return {

			/**
			 * If the API response is  still pending
			 * @type {Boolean}
			 */
			loading: false,

			/**
			 * array of approval workflow that were/are enforced on the ticket
			 * @type {Array}
			 */
			workflowData:[],

			/**
			 * Comment on the approval
			 * @type {String}
			 */
			comment:'',

			/**
			 * Whether to show confirmation pop-up or not
			 * @type {Boolean}
			 */
			showModal:false,

			/**
			 * It can be
			 * @type {String}
			 */
			action: '',
		}
	},

	beforeMount(){
		this.getDataFromServer();
	},
	created(){
		window.eventHub.$on('approvalWorkflowApplied', this.getDataFromServer);
	},

	methods:{

		/**
		 * gets approval workflow data from server
		 * @return {undefined}
		 */
		getDataFromServer(){

			this.workflowData = [];
			
			this.loading = true;
			
			axios.get('/api/ticket-approval-status/'+this.ticketId).then(res => {
			
				this.workflowData = res.data.data
			}).catch(err => {
			
				errorHandler(err);
			}).finally(res => {
			
				this.loading = false;
			})
		},

	 /**
		* checks if the given form is valid
		* @return {Boolean} true if form is valid, else false
		* */
		isValid(){
			const {errors, isValid} = validateApprovalTicketSettings(this.$data)
			if(!isValid){
				return false
			}
			return true
		},

		/**
		* populates the states corresponding to 'name' with 'value'
		* @param  {string} value
		* @param  {[type]} name
		* @return {void}
		*/
		onChange(value, name){
				this[name]= value;
		},

		/**
		 * Shows the pop-up for approval/denial
		 * @param  {string} type
		 * @return {undefined}
		 */
		onWorkflowAction(type){
			this.showModal = true
			this.action = type;
		},

		/**
		 * Gets icons class
		 * @param {String} type   `icon` or `color`
		 * @param {String} status   `PENDING`,`APPROVED` or `DENIED`
		 * @return {String}
		 */
		getIconStyle(status, type="icon"){
			switch (status) {
				case 'PENDING':
					return type == "icon" ? 'glyphicon glyphicon-time text-warning' : '#eee';

				case 'APPROVED':
					return type == "icon" ? 'glyphicon glyphicon-check text-success' : '#cbe0d3';

				case 'DENIED':
					return type == "icon" ? 'glyphicon glyphicon-exclamation-sign text-danger' : '#f6ddd8';

				default:
					return null;
			}
		},

		/**
		 * Gets the opacity based on if the level is active
		 * @param  {Boolean} isActive
		 * @return {Number}
		 */
		getOpacity(isActive){
			if(isActive == 1){
				return 1;
			}
			return 0.5;
		},

		/**
		 * Makes the API call to the server trigger approval/denial of the ticket
		 * @return {undefined}
		 */
		onWorkflowActionConfirm(){
		
			if(this.action !== 'remove'){

				if(this.isValid()){
					
					this.loading = true;
					
					axios.post('/api/approval-action-without-hash/'+this.ticketId, { action_type: this.action, comment: this.comment })
					.then(res => {
					
						successHandler(res,this.alertName);
					}).catch(err => {
					
						errorHandler(err);
					}).finally(res => {
						
						window.eventHub.$emit('refreshTableAndData',true);
						
						this.showModal = false;
						
						this.action = '';
						
						this.comment = '';

						this.workflowData = [];

						//refreshes the data again
						this.getDataFromServer();
					 
					})
				}
			} else {

				this.loading = true;

				axios.delete('/api/remove-approval-workflow/'+this.ticketId).then(res=>{
				 
					successHandler(res,this.alertName);
					
					window.eventHub.$emit('refreshTableAndData',false);

					window.eventHub.$emit('updateActionsTab','reply');

					this.showModal = false;
					
					this.action = '';
					
					this.comment = '';

					this.workflowData = [];
					
					this.getDataFromServer();
			 
				}).catch(err=>{
					
					errorHandler(err);
				})
			}
		}
	},

	computed:{

		modalTitle(){

			return this.action == 'approve' ? 'approve_ticket' : this.action == 'remove' ? 'remove_workflow' : 'deny_ticket';
		},

		modalQuestion(){

			let message = `${lang('are_you_sure_you_want_to')} ${this.action}?`;

			return this.action == 'remove' ? message : `${message} ${lang('mention_a_reason')}`;
		},

		...mapGetters({shallApprovalActionsBeVisible:'getApprovalActionVisibility'})
	},

	components : {
		'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		'modal': Modal,
		'alert': require("components/MiniComponent/Alert"),
		'text-field': require('components/MiniComponent/FormField/TextField'),
	}
};
</script>

<style scoped>

	.appproval { 

		min-height: 100px !important;
		position: relative;
	}
	
	.approval-level{
		margin: 5px;
		width: fit-content;
	}

	.levels {
		display: flex;
		overflow: auto;
	}

	.approver-status {
		margin-left: auto;
		order: 2;
		margin-top: 4px;
	}

	.approver-name{
		width: max-content;
	}

	.workflow-status{
		right: 12px;
		position: absolute;
		margin-top: 8px;
	}

	.box-header>.fa, .box-header>.glyphicon, .box-header>.ion, .box-header .box-title{
			margin-right:0;
	}

	#approval-workflow-actions {
		margin-top: 10px;
	}
	.padding-left-15{
		padding-left: 15px !important;
	}

	.margin_box { margin: 0 !important;margin-bottom: 10px !important; }

	.font-size-14 { font-size: 14px !important; }

	.load_margin { margin-top: 70px; margin-bottom: 70px; }

	.mb_5 { margin-bottom: 5px; }

	.approval_title{ margin-bottom: 5px; margin-top: -2px; }

	.flow-icons{ margin-right: -17px !important; }
</style>
