<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">
		
		<div slot="title">

			<h4>{{lang('turn_off_authenticator')}}</h4>
		</div>
			
		<div slot="fields">
				
			<span v-if=!showPasswordRequiredFrom> {{lang('turn_off_authenticator_setup')}}</span>	

			<div v-else>
				<text-field :label="lang('to_continue_first_verify')" :value="password" type="password" name="password" 
									placehold="Enter Password..." :onChange="onChange" classname="col-sm-12">
											
							</text-field>
			</div>

		</div>

		<div class="row" v-if="loading"  slot="fields">

			<custom-loader :duration="4000" :color="color"></custom-loader>		
		</div>

		<button slot="controls" class="btn pull-right float-right" :class="showPasswordRequiredFrom ? 'btn-primary' : 'btn-danger'" @click="submit()"> 

			<i class="fas" :class="showPasswordRequiredFrom ? 'fa-check' : 'fa-power-off'"></i> {{ showPasswordRequiredFrom ? lang('validate') : lang('turn_off')}}
		</button>
	</modal>
</template>

<script type="text/javascript">
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {
		
		name : 'remove-authenticator-modal',

		description : 'Remove Authenticator Modal component',

		props:{
	
			showModal:{type:Boolean,default:false},

			onClose:{type: Function},

			alertName : { type : String, default : 'edit_agent_profile'},

			id : { type : String | Number, default : ''},

			color : { type : String, default : '#1d78ff'}
		},

		data(){

			return {

				isDisabled:true,

				containerStyle:{ width:'600px' },

				loading:false,

				showPasswordRequiredFrom: false,

				password: ''
			}
		},

		methods:{

			validatePass(){

				this.verifyLoader=true;

				const data = {}

				data['password'] = this.password;

				axios.post('/verify/password', data).then(res=>{

					

					this.loading = false;

					this.onRemove();
				
				}).catch(err=>{


					this.loading = false;

					this.$store.dispatch('setValidationError', {'password' : 'Incorrect password.'})

				})
			},

			onRemove(){

				this.loading = true;

				this.isDisabled = true;

				axios.post('/2fa/disable/'+this.id).then(res=>{

					window.eventHub.$emit('updateEditData',false);

					this.onClose();

					successHandler(res, this.alertName)

					this.loading = false;

					this.isDisabled = false;

				}).catch(err=>{

					if (err.response.data.message === 'password_confirmation_required') {
						this.showPasswordRequiredFrom = true;
					} else {
						this.onClose();

						errorHandler(err, this.alertName);
					}
					

					this.isDisabled = false;

					this.loading = false;
				})
			},
			onChange(value, name) {

				this[name] = value;
			},

			submit() {

				if(this.showPasswordRequiredFrom) {
					this.validatePass()
				} else {
					this.onRemove()
				}

			}
		},

		components:{

			'modal':require('components/Common/Modal.vue'),
			
			'alert' : require('components/MiniComponent/Alert'),

			'custom-loader' : require('components/MiniComponent/Loader'),

			'text-field': require('components/MiniComponent/FormField/TextField'),
		}
	};
</script>