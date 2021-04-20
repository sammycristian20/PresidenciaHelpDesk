<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title">
			<h4 class="modal-title">{{lang('delete')}}</h4>
		</div>

		<div v-if="!loading" slot="fields">
			<span>{{lang('are_you_sure_you_want_to_delete')}}</span>
		</div>

		<div v-if="loading" slot="fields" >
			<loader :animation-duration="4000" color="#1d78ff" :size="60"/>
		</div>

		<div slot="controls">
			<button type="button" @click = "onSubmit" class="btn btn-danger" :disabled="loading"><i class="fas fa-trash" aria-hidden="true"></i> {{lang('delete')}}</button>
		</div>
	</modal>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	export default {

		name : 'delete-modal',

		description : 'Delete Modal component',

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

			alertComponentName : { type : String, default : 'dataTableModal'},

			redirectUrl : { type : String, default : ''},

			componentTitle : { type : String, default : ''}

		},

		data:()=>({


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
			axios.delete(this.deleteUrl).then(res=>{

				successHandler(res,this.alertComponentName);

				this.afterRespond();
			}).catch(err => {

				errorHandler(err,this.alertComponentName);

				this.afterRespond();
			})
		},

		afterRespond(){

			window.eventHub.$emit(this.componentTitle+'refreshData');

			window.eventHub.$emit(this.componentTitle+'uncheckCheckbox');

			if(this.redirectUrl){
			
				this.redirect(this.redirectUrl);
			}

			if(this.alertComponentName == 'timeline') {

				window.eventHub.$emit('actionDone');
			}

			this.onClose();

			this.loading = false;

		}
	},

	components:{
		'modal':require('components/Common/Modal.vue'),
		'alert' : require('components/MiniComponent/Alert'),
		'loader': require('components/Extra/Loader'),
	}

};
</script>
