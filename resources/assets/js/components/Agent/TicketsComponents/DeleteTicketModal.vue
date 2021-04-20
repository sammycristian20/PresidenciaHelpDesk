<template>
	
	<div> 
	
		<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">
		
			<div slot="title">
			
				<h4 class="modal-title">{{lang('delete_ticekt_forever')}}</h4>
			</div>
			
			<div v-if="!loading" slot="fields">
	
					<span>{{lang('are_you_sure_you_want_to_delete')}}</span>
   		</div> 
			
			<div v-if="loading" class="row" slot="fields" >
				
				<loader :animation-duration="4000" color="#1d78ff" :size="size" />
			</div>
						
			<div slot="controls">
				
				<button type="button" @click="onSubmit()" class="btn btn-danger" :disabled="isDisabled">

					<i class="fas fa-trash"></i> {{lang('delete')}}
				</button>
			</div>
		</modal>
	</div>
</template>

<script type="text/javascript">
	
	import axios from 'axios'

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	export default {
		
		name : 'delete-ticket-modal',

		description : 'Delete ticket Modal component',

		props:{
			
			showModal:{type:Boolean,default:false},

			ticketIds : { type : Array },

			onClose:{type: Function},

			componentTitle : { type : String, default :'UserTickets'},

			reloadTickets : { type : Function }
		},

		data:()=>({
			
			isDisabled:false,

			containerStyle:{ width:'600px' },

			loading:false,

			size: 60,
		}),

		methods:{

		onSubmit(){

			this.loading = true
			
			this.isDisabled = true

			const obj = { 

				'ticket-ids' : this.ticketIds
			}

			axios.post('api/ticket/delete-forever',obj).then(res=>{
				
				this.loading = false;
				
				this.isDisabled = true
				
				this.reloadTickets();
				
				successHandler(res,this.componentTitle);
				
				this.onClose();
				
			}).catch(err => {
				
				this.loading = false;
				
				this.isDisabled = false
				
				errorHandler(err,this.componentTitle)
			})
		},
	},

	components:{
		
		'modal':require('components/Common/Modal.vue'),
		
		'loader':require('components/Client/Pages/ReusableComponents/Loader'),
	}
};
</script>