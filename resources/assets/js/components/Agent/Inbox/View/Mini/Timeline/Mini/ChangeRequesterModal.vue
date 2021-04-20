<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="divStyle">

		<div slot="title">
	
			<h4 class="modal-title">{{lang('change_requester')}}</h4>
		</div>

		<div v-if="!loading" slot="fields" class="row">
			
			<dynamic-select :label="lang('users')" :multiple="false" name="user" :required="true"
				 classname="col-sm-12" apiEndpoint="/api/dependency/users?meta=true" 
				:value="user" :onChange="onChange" :clearable="user ? true : false" strlength="60">

			</dynamic-select>
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

		name:'change-requester-modal',

		description: 'Change requester modal component',

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

				user : '',

				divStyle : { width : '500px' }
			} 
		},

		beforeMount() {

			this.isDisabled = this.user ? false : true;
		},

		methods:{

			onChange(value,name) {

				this[name] = value;

				this.isDisabled = value ? false : true;
			},

			onSubmit(){
				
				this.loading = true;

				this.isDisabled = true;

				const data = {};

				data['owner_id']=this.user.id;
				
				axios.post('/api/change-owner/'+this.ticketId,data).then(res=>{

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

			'dynamic-select': require('components/MiniComponent/FormField/DynamicSelect'),
		},
	};
</script>