<template>

	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title">

				<h4 class="modal-title">{{lang('change_status')}}</h4>
		</div>

		<div slot="fields" v-if="!loading">

			<span v-if="ticketIds.length > 0">{{'Are you sure you want to set status to ' +status.name+ '?'}}</span>

			<span v-else>{{lang('select-ticket')}}</span>

		</div>

		<div v-if="loading" class="row" slot="fields" >

			<loader :color="layout.portal.client_header_color" :animation-duration="4000" :size="60"/>
		</div>

		<template slot="controls" v-if="ticketIds.length > 0">

			<button id="submit_btn" type="button" @click="onSubmit()" class="btn btn-primary float-right" :disabled="isDisabled"
				:style="buttonStyle">

				<i class="fas fa-check" aria-hidden="true"></i> {{lang('proceed')}}
			</button>
		</template>
	</modal>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {

		name : 'change-status-modal',

		description : 'Change status Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			ticketIds : { type : Array, default : ()=>[]},

			status : { type : Object, default : ()=>{}},

			onClose:{type: Function},

			layout : { type : Object, default : ()=>{}}
		},

		data(){

			return {

				isDisabled:false,

				containerStyle:{ width:'500px'},

				loading:false,

				lang_locale : '',

				buttonStyle : {

					borderColor : this.layout.portal.client_button_border_color,

					backgroundColor : this.layout.portal.client_button_color
				},
			}
		},

		beforeMount(){

			this.lang_locale = this.layout.language;
		},

		methods:{

		onSubmit(){

			this.loading = true

			this.isDisabled = true

			this.$Progress.start();

			axios.post('api/ticket/change-status/' +this.ticketIds+'/'+this.status.id).then(res=> {

				this.$Progress.finish();

				this.loading = false;

				this.isDisabled = true

				window.eventHub.$emit('refreshData');

				window.eventHub.$emit('uncheckCheckbox')

				this.onClose();

				successHandler(res,'dataTableModal');
				
			}).catch(error=>{

				this.loading = false;

				this.isDisabled = false

				errorHandler(err,'dataTableModal')

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