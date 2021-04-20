<template>
	<div> 
		<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">
			<div slot="alert">
				<alert/>
			</div>

			<div slot="title">
				<h4>{{lang('approval_workflow')}}</h4>
			</div>
			
			<div v-if="loading === false" slot="fields">
				<h5 id="H5" :class="{margin: lang_locale == 'ar'}">{{lang('are_you_sure_you_want_to_delete')}}</h5>
			</div> 
			
			<div v-if="loading === true" class="row" slot="fields" style="margin-top:30px;margin-bottom:30px">
				<loader :animation-duration="4000" :color="color" :size="size" :class="{spin: lang_locale == 'ar'}" />
			</div>
						
			<div slot="controls">
				<button type="button" @click = "onSubmit" class="btn btn-danger" :disabled="isDisabled"><i class="fas fa-trash" aria-hidden="true"></i> {{lang('delete')}}</button>
			</div>

		</modal>
	</div>
</template>

<script type="text/javascript">
	
	// import {validateTimeTrackSettings} from "helpers/validator/timeTrackSettingsRules"
	import {errorHandler, successHandler} from 'helpers/responseHandler'
	export default {
		
		name : 'approval-workflow-delete',
		description : 'Approval workflow delete component',
		props:{
			
			/**
			 * status of the modal popup
			 * @type {Object}
			 */
			showModal:{type:Boolean,default:false},
			/**
			 * status of the delete popup modal
			 * @type {Object}
			 */
			deleteUrl:{type:String},
			/**
			 * The function which will be called as soon as user click on the close button        
			 * @type {Function}
			*/
			onClose:{type: Function},
		},
		components:{
			'modal':require('components/Common/Modal.vue'),
			'alert' : require('components/MiniComponent/Alert'),
			'loader':require('components/Client/ReusableComponents/loader'),
		},
		data:()=>({
			
			
			/**
			 * buttons disabled state
			 * @type {Boolean}
			 */
			isDisabled:false,
			/**
			 * width of the modal container
			 * @type {Object}
			 */
			containerStyle:{
				width:'500px'
			},
			/**
			 * initial state of loader
			 * @type {Boolean}
			 */
			loading:false,
			/**
			 * size of the loader
			 * @type {Number}
			 */
			size: 60,
			/**
			 * color of the loader
			 * @type {String}
			 */
			color: '#1d78ff',
			
			/**
			 * for rtl support
			 * @type {String}
			*/
			lang_locale:'',
		}),
		created(){
		// getting locale from localStorage
			this.lang_locale = localStorage.getItem('LANGUAGE');
		},
		methods:{
		/**
		 * api calls happens here
		 * @return {Void} 
		 */
		onSubmit(){
		//for delete
			this.loading = true
			this.isDisabled = true
			axios.get(this.deleteUrl).then(res=>{
				successHandler(res);
				setTimeout(()=>{
					this.onClose();
				},5000)
				this.loading = false;
				this.isDisabled = true
			}).catch(err => {
				errorHandler(err)
			})
		},
	}
};
</script>

<style type="text/css">
.has-feedback .form-control {
	padding-right: 0px !important;
}
#H5{
	margin-left:16px; margin-bottom:18px !important;
}
.fulfilling-bouncing-circle-spinner{
	left: 44% !important;
}
.margin {
	margin-right: 16px !important;margin-left: 0px !important;
}
.spin{
	left:0% !important;right: 43% !important;
 }
</style>