<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">
		
		<div slot="title">
	
			<h4 class="modal-title">{{'Are you sure you want to set status to ' +status.name+ '?'}}</h4>
		</div>
			
		<div v-if="!loading" slot="fields">
	
			<div class="row">

				<text-field :label="lang(status.comment ? 'leave_comment_status_change_required' : 'leave_comment_status_change_optional')" :value="commnetValue" type="textarea" name="commnetValue" 
					 	:onChange="onChange" :required="status.comment ? true : false" classname="col-sm-12" rows="4">
						
				</text-field>
			</div>

				<div class="col-sm-12">

					<label class="label_align1">

						<input class="checkbox_align" type="checkbox" v-model="checked">&nbsp;{{lang('post_comment_as_reply')}}
					</label>
				</div>
		</div> 
			
		<div v-if="loading" class="row" slot="fields" >

			<loader :animation-duration="4000" color="#1d78ff" :size="60"/>
		</div>
						
		<button slot="controls" id="submit_btn" type="button" @click="validate()" class="btn btn-primary" :disabled="isDisabled">

			<i class="fa fa-check" aria-hidden="true"></i> {{lang('proceed')}}
		</button>
	</modal>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import {validateChangeStatusSettings} from "helpers/validator/changeStatusSettingsRules.js"

	import axios from 'axios'

	export default {
		
		name : 'change-status-modal',

		description : 'Change status Modal component',

		props:{
			
			showModal:{type:Boolean,default:false},

			ticketIds : { type : Array, default : ()=>[]},

			status : { type : Object, default : ()=>{}},
	
			onClose:{type: Function, default : ()=>{}},

			componentTitle : { type : String, default :'UserTickets'},

			reloadTickets : { type : Function, default: () => {} }
		},

		data:()=>({
			
			isDisabled:false,
			
			containerStyle:{ width:'650px' },
	
			loading:false,

			commnetValue : '',

			checked : false
		}),

		methods:{
			
			isValid(){
        
        const {errors, isValid} = validateChangeStatusSettings(this.$data);
        
        if(!isValid){
        
          return false
        }
        
        return true
      },

      validate() {

      	if(this.status.comment){

	          if(this.isValid()){

	            this.onSubmit()
	          }
	        } else {
	          this.onSubmit()
        	}
      },

			onSubmit(){
			
				this.loading = true
			
				this.isDisabled = true

				axios.post('/ticket/change-status/'+this.ticketIds+'/'+this.status.id,
					{ 
						'comment': this.commnetValue, 
						'as-reply': this.checked
					}).then(res=>{

					this.loading = false;
					
					this.isDisabled = true;

					this.reloadTickets();

					successHandler(res,this.componentTitle);
					
					window.eventHub.$emit(this.componentTitle+'refreshData');
					
					window.eventHub.$emit(this.componentTitle+'uncheckCheckbox');

					window.eventHub.$emit('refreshUserReport')
					
					window.eventHub.$emit('refreshOrgReport')
					
					this.onClose();
				
				}).catch(err => {

					this.loading = false;

					this.isDisabled = false

					errorHandler(err,this.componentTitle);

					this.onClose();
				})
			},

			onChange(value, name){
				
				this[name]= value;	
			},
		},

		components:{
			
			'modal':require('components/Common/Modal.vue'),
			
			'alert' : require('components/MiniComponent/Alert'),
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
			
			'text-field': require('components/MiniComponent/FormField/TextField'),
		}
	};
</script>

<style scoped>
	.label_align1 {
		
		display: block; padding-left: 10px; text-indent: -15px; font-weight: 500 !important; padding-top: 0px;
	}
	.checkbox_align {
		display:inline !important;width: 13px; height: 13px; padding: 0; margin:0; vertical-align: bottom; position: relative; 
		top: -3px; overflow: hidden;
	}
</style>