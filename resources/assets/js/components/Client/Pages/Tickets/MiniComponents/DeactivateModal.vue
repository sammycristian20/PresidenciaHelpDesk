<template>
	
	<div> 
	
		<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">
		
			<div slot="title">
				
				<h4>{{lang('deactivate')}}</h4>
			</div>
			
			<div v-if="!loading" slot="fields">
				
				<p id="H5" :class="{margin: lang_locale == 'ar'}">{{lang('Are you sure you want to deactive this user?')}}
				</p>
			</div> 
			
			<div v-if="loading" class="row" slot="fields" >
				
				<loader :animation-duration="4000" :size="60" :color="layout.portal.client_header_color"/>
			</div>
						
				
			<button slot="controls" type="button" @click="onSubmit" class="btn btn-danger float-right" :disabled="isDisabled" :class="{left: lang_locale == 'ar'}" :style="buttonStyle">

				<i class="fas fa-trash"></i> {{lang('deactivate')}}
			</button>
		</modal>
	</div>
</template>

<script type="text/javascript">
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	export default {
		
		name : 'deactivate-modal',

		description : 'Deactivate Modal component',

		props:{
	
			showModal:{type:Boolean,default:false},

			deleteUrl:{type:String},

			onClose:{type: Function},

			layout : { type : Object, default : ()=>{}},
		},

		data(){

			return {

				isDisabled:false,

				containerStyle:{
					width:'600px'
				},

				buttonStyle: {
				
					borderColor : this.layout.portal.client_button_border_color,
					
					backgroundColor : this.layout.portal.client_button_color
				},

				loading:false,

				lang_locale : this.layout.language,	
			}
		},

		methods:{

		onSubmit(){
			
			this.$Progress.start();

			this.loading = true
			
			this.isDisabled = true
			
			axios.get(this.deleteUrl).then(res=>{
				
				window.eventHub.$emit('refreshData')

				successHandler(res,'organization');
			
				this.onClose();
			
				this.loading = false;
			
				this.isDisabled = false;

				this.$Progress.finish();

			}).catch(err => {
				
				this.loading = false;
			
				this.isDisabled = false

				errorHandler(err,'organization')

				this.$Progress.fail();
			})
		},
	},

	components:{

		'modal':require('components/Common/Modal.vue'),
		
		'alert' : require('components/MiniComponent/Alert'),
		
		'loader':require('components/Client/Pages/ReusableComponents/Loader'),
	}
};
</script>

<style type="text/css">

#H5{
	margin-left:16px; 
}
.fulfilling-bouncing-circle-spinner{
	margin: auto !important;
}
.margin {
	margin-right: 16px !important;margin-left: 0px !important;
}
.spin{
	left:0% !important;right: 43% !important;
 }
</style>