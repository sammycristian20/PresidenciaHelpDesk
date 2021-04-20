<template>

  <modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title">

			<h4>{{lang('task_create')}}</h4>
		</div>

		<div  slot="fields" id="release_fields">

			<task :popup="true" ref="taskCreate" :ticketId="ticketId" :onComplete="onCompleted" 
				alertName="timeline">
					
			</task>
		</div>
		
		<div v-if="loading" class="row" slot="fields" >

		  	<loader :animation-duration="4000" :size="60"/>
		</div>

		<div slot="controls">

			<button type="button" @click="onSubmit" class="btn btn-primary" :disabled="isDisabled">

			 	<i class="fa fa-save"></i> {{lang('save')}}
			 </button>
		</div>
	</modal>
</template>

<script>

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {

		name : 'task-add-modal',

		description : 'Task add Modal component',

		props:{

			showModal : { type : Boolean, default : false },

			onClose : { type : Function },

			ticketId : { type : String | Number, default : '' },

			reloadDetails : { type : Function }
		},

		data(){

			return {

				isDisabled:false,

				containerStyle:{ width:'800px' },

				loading:false,
			}
		},

		methods:{

			onSubmit(){

		  		this.$refs.taskCreate.taskSubmit();
			},

			onCompleted(){

				this.onClose();
				
				this.reloadDetails();
			}
		},

		components:{

			'modal':require('components/Common/Modal.vue'),

		 	'loader':require('components/Client/Pages/ReusableComponents/Loader'),

		 	'task' : require('Calendar/components/TaskCreate')
		}
	};
</script>

<style type="text/css">
		
	#release_fields {
		margin-left: 15px;
	 margin-right: 15px;
	 max-height: 300px;
	 overflow-y: auto;
	 overflow-x: hidden;
	}
</style>