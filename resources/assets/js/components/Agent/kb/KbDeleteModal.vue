<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title">
	
			<h4 class="modal-title">{{lang('delete')}}</h4>
		</div>

		<div slot="fields" v-if="!loading">
			
			<span>{{lang('are_you_sure_you_want_to_delete')}}</span>
		</div>
			
		<div slot="fields" class="row" v-if="loading">
	        
	    	<loader :duration="4000"></loader>
	  	</div>
						
		<div slot="controls">
			
			<button type="button" id="submit_btn"  @click = "onSubmit" class="btn btn-danger" :disabled="isDisabled">
			
				<i class="fas fa-trash"></i> {{lang('delete')}}
			</button>
		</div>
	</modal>
</template>

<script type="text/javascript">

import {errorHandler, successHandler} from 'helpers/responseHandler'

import axios from 'axios'

	export default {

		name:'kb-delete-modal',

		description:'Kb delete modal Component',

		props:{

			showModal : { type : Boolean, default : false },

			onClose : { type : Function },

			apiUrl : { type : String , default : ''},

			alert : { type : String , default : ''},
			
			redirectUrl : { type : String , default : ''},
		},

		components:{
			
			'modal':require('components/Common/Modal.vue'),
			
			'alert' : require('components/MiniComponent/Alert'),
			
			"loader": require("components/Client/Pages/ReusableComponents/Loader")
		},

		data(){
			
			return {

				isDisabled:false,

				loading:false,

				containerStyle:{ width:'500px' },
			} 
		},

		methods:{

			onSubmit(){
			
				this.loading = true
			
				this.isDisabled = true
			
				axios.delete(this.apiUrl).then(res=>{
						
					this.loading = false;
				
					this.isDisabled = true

					successHandler(res,this.alert);

					window.eventHub.$emit('updateKbList');

					window.eventHub.$emit('updateCommentsList');
			
					this.onClose()
					
					if(this.redirectUrl){
						
						this.redirect(this.redirectUrl)			
					}
		
				}).catch(err => {
					
					this.loading = false

					this.onClose()

					errorHandler(err,this.alert)
				})
			},
		}
	};
</script>