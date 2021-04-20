<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="divStyle">

		<div slot="title">
	
			<h4 class="modal-title">{{lang('surrender')}}</h4>
		</div>

		<div slot="fields">
			
			<span>{{ lang('are_you_sure_you_want_to_surrender_this_ticket') }}?</span>
		</div>
		
		<div class="row" slot="fields" v-if="loading">

			<loader :duration="4000" :size="60"></loader>

		</div>

		<div slot="controls">
			
			<button type="button"  @click="onSubmit" class="btn btn-primary" :disabled="isDisabled">
			
				<i class="fas fa-sync-alt"></i> {{lang('update')}}
			</button>
		</div>
	</modal>
</template>

<script>

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {

		name:'surrender-modal',

		description:'Surrender modal Component',

		props:{

			showModal : { type : Boolean, default : false },

			onClose : { type : Function },

			ticketId : { type : String | Number, default : '' },

			reloadDetails : { type : Function }
		},

		data(){
			
			return {

				isDisabled:false,

				loading:false,

				divStyle : { width : '500px' }
			} 
		},

		methods:{

			onSubmit(){
				
				this.loading = true;

				this.isDisabled = true;

				axios.get('/ticket/surrender/'+this.ticketId).then(res=>{

					this.reloadDetails();

					successHandler(res,'timeline');

					this.loading = false;

					this.isDisabled = false;

					this.onClose();

				}).catch(err=>{

					errorHandler(err,'timeline');

					this.loading = false;

					this.isDisabled = false;

					this.onClose();
				})
			},
		},

		components:{
			
			'modal':require('components/Common/Modal.vue'),
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		},
	};
</script>

<style scoped>
	
</style>