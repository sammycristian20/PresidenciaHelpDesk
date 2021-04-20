<template>

	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle"
		modalBodyClass="widget-card-div">

		<div slot="title">

			<h4 class="modal-title">{{lang(modalMode)}}</h4>
		</div>
			
		<div slot="fields" >
				
			<div slot="alert">

				<alert componentName="Widget-Modal"/>
			</div>

			<div v-if="modalMode === 'edit'">
				
				<text-field :label="lang('title')" :onChange="onChange" :value="title" type="text" name="title" 
					classname="col-sm-12" >
						
				</text-field>
		 

				<tiny-editor :value="value" type="text" :onChange="onChange" name="value"
					label="Value" classname="col-sm-12" width="100%" height="300">
						
				</tiny-editor>
			</div>
			
			<div v-if="modalMode === 'view'">
				
				<div v-if="value">
					
					<text-field :label="lang('title')" :onChange="onChange" :value="title" type="text" name="title" 
						classname="col-sm-12"  :disabled="true">
						
					</text-field>
		 

					<tiny-editor :value="value" type="text" :onChange="onChange" name="value"
						label="Value" classname="col-sm-12" v-html="value" width="100%" height="300">

					</tiny-editor>
				</div>
				
				<div v-if="!value" class="text-center">
					
					<span>{{trans('no_data_found')}}</span>
				</div>
			</div>	
		</div>	
						
		<div slot="controls">

			<button v-if="modalMode === 'edit'" type="button" id="submit_btn" @click="onSubmit()" class="btn btn-primary">

				<i class="fas fa-sync" aria-hidden="true"></i> {{lang('update')}}
			</button>				
		</div>
	</modal>
</template>

<script type="text/javascript">
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios';

	export default {
		
		name : 'Widget-Modal',

		description : 'Widget Modal component',

		props:{

			data : { type : Object, required : true },

			/**
			 * status of the modal popup
			 * @type {Object}
			 */
			showModal:{type:Boolean,default:false},

			/**
			 * The function which will be called as soon as user click on the close button        
			 * @type {Function}
			*/
			onClose:{type: Function},

			/**
		 	* Mode of the modal, edit/view
		 	*/
			modalMode : { type: String , required : true },
		},

		data:()=>{
			
			return {
				
				containerStyle:{ width:'700px' },

				title:"",

				value:"",

				loading:false,
			}
		},

		beforeMount(){
			
			this.title=this.data.title
			
			this.value=this.data.value
		},

		methods:{

			onChange(value, name){
				
				this[name] = value;
		 	},

			onSubmit() {
				
				this.loading = true;
				
				let params = this.getSubmitParams();
				
				axios.post('api/update-widget/' + this.data.id, params).then(res => {
					
					this.loading = false;
					
					this.onClose();
					
					successHandler(res, 'dataTableModal');
					
					window.eventHub.$emit('refreshData');

				}).catch(err => {
					
					this.loading = false;
					
					errorHandler(err, 'Widget-Modal');
				});
			},

			getSubmitParams(){
				 
				let params = { title: this.title, value: this.value }
				
				return params;
			},
		},
		
		components:{
			
			'text-field': require('components/MiniComponent/FormField/TextField'),
			
			'modal':require('components/Common/Modal.vue'),
			
			'alert' : require('components/MiniComponent/Alert'),
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>

<style>
	
	.widget-card-div { max-height: 470px; overflow-y: auto;overflow-x: hidden; }
</style>