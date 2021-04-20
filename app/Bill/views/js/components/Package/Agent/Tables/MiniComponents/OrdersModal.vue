<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" 
		@close="showModal = false" :containerStyle="containerStyle">
		
		<div slot="title">
		
			<h4 class="modal-title">{{lang(title)}}{{id}}</h4>
		</div>

		<div v-if="!loading" slot="fields">
			
			<div slot="alert">
        
            <alert componentName="add-invoice"/>
        </div>

			<div class="row">

				<static-select :label="lang('select_package')"  
					:elements="packages" name="package" :value="id" 
					classname="col-sm-12" :onChange="usePackage" :required="true">
				</static-select>
			</div>
			
			<div class="row">
			
				<div class="form-group col-md-12" id="align">
					
					<label class="label_align">
					
						<input class="checkbox_align" type="checkbox" 
							 name="meta" v-model="checked">
							 {{lang('send_invoice_to_client')}}
					</label>
				</div>
			</div> 
		</div>
		
		<div slot="controls">
         
         <button type="button" class="btn btn-primary" @click="createInvoice()" :disabled="submitDisabled">

         	<i class="fas fa-save"> </i> {{lang('create')}}
         </button>
      </div>
        
		<div v-if="loading" class="row" slot="fields">
		
			<loader :animation-duration="4000" color="#1d78ff" :size="size"/>
		</div>
	</modal>
</template>

<script type="text/javascript">
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'
	
	import axios from 'axios' 

	export default {
		
		name : 'settings-modal',

		description : 'Settings Modal component',

		props:{
	
			showModal:{type:Boolean,default:false},

			onClose:{type: Function},

			title : { type : String , default :''},

			id : { type : String | Number, default : '' },

			userId: {type: String, default:0}
		},

		data:()=>({

			containerStyle:{ width:'800px' },

			loading:false,

			size: 60,

			status : 0,

			packages : [],

			packageId: 0,

			submitDisabled: true,

			checked: false
		}),

		beforeMount(){

			this.getData()
		},

		methods:{

			getData(){
				this.loading = true;

				axios.get('bill/package/get-active-packages').then(res=>{
					this.loading = false;
					this.packages = res.data.data.data;
				}).catch(error=>{
					this.submitDisabled=true;
					this.loading = false;
					errorHandler(error, 'add-invoice');
				})
			},

			createInvoice()
			{
				let meta = this.checked ? 1 : 0;
				this.loading = true;
				axios.get('bill/package/user-checkout?package_id='+this.packageId+"&user_id="+this.userId+"&meta="+meta).then(res=>{
					this.loading = false;

					successHandler(res,'add-invoice')

					window.eventHub.$emit('refreshData'); 
					
					setTimeout(()=>{

						this.onClose();
					},1500)
				}).catch(error=>{
					console.log(error);
					this.submitDisabled=true;
					errorHandler(error, 'add-invoice');
					this.loading = false;
				})
			},

			usePackage(value, name)
			{
				this.submitDisabled = value != '' ? false : true;
				this.packageId = value;
			}
		},

		components:{
			
			'modal':require('components/Common/Modal.vue'),
			"static-select": require("components/MiniComponent/FormField/StaticSelect"),
			'alert' : require('components/MiniComponent/Alert'),
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>

<style type="text/css" scoped>

	.label_align {
		display: block; padding-left: 15px; text-indent: -15px; font-weight: normal !important; padding-top: 6px;
	}
	.checkbox_align {
		width: 13px; height: 13px; padding: 0; margin:0; vertical-align: bottom; position: relative; top: -3px; overflow: hidden;
	}
</style>