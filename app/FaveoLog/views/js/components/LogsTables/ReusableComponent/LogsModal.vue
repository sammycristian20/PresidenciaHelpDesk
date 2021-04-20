<template>

	<div>

		<modal v-if="showModal" :showModal="showModal" :onClose="onClose" @close="showModal = false" :containerStyle="containerStyle">

			<div slot="title">

				<h4 class="modal-title">{{lang(title)}}</h4>
			</div>

			<div v-if="!loading && title !== 'delete_logs'" slot="fields" class="mod_width">

				<p v-html="content" :class="[{'trace' : title === 'trace'}]"></p>
			</div>

			<div class="span" v-if="mail_message != ''" slot="fields">

				<br>

				<span v-html="mail_message"></span>
			</div>

			<div v-if="!loading && title === 'delete_logs'" slot="fields" class="row">


					<check-box id="log_delete_checkbox" :options="checkOptions" :label="lang('logs')" name="logs" :value="logs"
						:onChange="onChange" classname="col-sm-4" class_name="col-sm-4">
					</check-box>

					<date-time-field id="log_delete_date1" :label="lang('delete_after')"
						:value="delete_after_date"
						type="datetime"
						:time-picker-options="timeOptions"
						name="delete_after_date"
						:required="false"
						:onChange="onChange"
						:currentYearDate="false"
						format="YYYY-MM-DD HH:mm:ss" classname="col-sm-4"
						:clearable="true" :disabled="false">
					</date-time-field>

					<date-time-field id="log_delete_date2" :label="lang('delete_before')"
						:value="delete_before_date"
						type="datetime"
						:time-picker-options="timeOptions"
						name="delete_before_date"
						:required="false"
						:onChange="onChange"
						:currentYearDate="false"
						format="YYYY-MM-DD HH:mm:ss" classname="col-sm-4"
						:clearable="true" :disabled="false">
					</date-time-field>
			</div>

			<div v-if="title === 'delete_logs'" slot="controls">
				<button id="log_delete_btn" type="button" @click = "onSubmit()" class="btn btn-danger" :disabled="isDisabled"><i class="fa fa-trash" aria-hidden="true"></i> {{lang('delete')}}</button>
			</div>

			<div v-if="loading" class="row" slot="fields">

				<loader :animation-duration="4000" color="#1d78ff" :size="size" />
			</div>

		</modal>
	</div>
</template>

<script>

	import moment from 'moment';
	import axios from 'axios';
	import {errorHandler, successHandler} from 'helpers/responseHandler';
	import Modal from 'components/Common/Modal';

	export default {

		name : 'logs-modal',

		description : 'Logs Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			onClose:{type: Function},

			data : { type : Object , default:()=>{}},

			title : { type : String , default :''}

		},

		data:()=>({

			containerStyle:{
				width:'950px'
			},

			/**
			 * initial state of loader
			 * @type {Boolean}
			 */
			loading:true,

			/**
			 * size of the loader
			 * @type {Number}
			 */
			size: 60,

			/**
			 * for rtl support
			 * @type {String}
			*/
			lang_locale:'',

			content : '',

			mail_message : '' ,

			isDisabled : true,

			delete_after_date : '',

			delete_before_date : '',

			timeOptions:{
						start: '00:00',
						step: '00:30',
						end: '23:30'
			},

			checkOptions:[
				{name:'cron',value:'cron'},
				{name:'mail',value:'mail'},
				{name:'exception',value:'exception'}
			],

			logs : []

		}),

		beforeMount() {

			this.checkTitle();
		},

		created(){

			this.lang_locale = localStorage.getItem('LANGUAGE');
		},

		methods:{

			checkTitle(){
				if(this.title !== 'delete_logs'){

					if(this.title === 'logs_content'){

						this.getLogsContent(this.data.id)
					} else{

						this.content = this.data.trace

						this. containerStyle.width = "1000px"

						this.loading = false;
					}
				} else{
					this.loading = false
				}
			},

			getLogsContent(id){

				axios.get('/api/get-log-mail-body/'+id).then(res=>{

					this.loading = false

					this.content = res.data.data.mail_body;

					this.mail_message = res.data.message;
				}).catch(err => {

					this.loading = false
				})
			},

			onChange(value,name){

				this[name] = value;

				if(name === 'logs'){
					this.isDisabled = value.length === 0 ? true : false
				}
			},

			onSubmit(){
			//for delete
				this.loading = true
				this.isDisabled = true

				const data={};
				data['categories'] = this.logs;
				data['delete_before']=this.delete_before_date !== ''  && this.delete_before_date !== null ? moment(this.delete_before_date).format('YYYY-MM-DD HH:mm:ss') : '';
				data['delete_after']=this.delete_after_date !== '' && this.delete_after_date !== null ? moment(this.delete_after_date).format('YYYY-MM-DD HH:mm:ss') : '';
				axios.delete('/api/delete-logs',{data}).then(res=>{
					successHandler(res,'dataTableModal');
					window.eventHub.$emit('cronLogsrefreshData');
					window.eventHub.$emit('mailLogsrefreshData');
					window.eventHub.$emit('exceptionLogsrefreshData');
					this.onClose();
					this.loading = false;
					this.isDisabled = true
				}).catch(err => {
					errorHandler(err,'dataTableModal')
				})
			},

		},

	components:{

		'modal': Modal,

		'loader':require('components/Client/Pages/ReusableComponents/Loader'),

		'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),

		'check-box': require('components/MiniComponent/FormField/CheckBoxComponent'),
	}

};
</script>

<style type="text/css">

 .mod_width{
    max-height: 400px;
    overflow-x: hidden;
    overflow-y: auto;
 }
 .trace {
 	background: black !important;
    color: aliceblue;
    padding: 10px;
    font-family: monospace;
    font-size: 13px;
    line-height: 1.5 !important;
 }
</style>
