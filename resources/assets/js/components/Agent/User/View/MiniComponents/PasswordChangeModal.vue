<template>

	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title">
		
			<h4 class="modal-title">{{lang('change_password')}}</h4>
		
		</div>

		<div slot="fields" class="row" v-if="!loading">

			<div class="col-sm-12 form-group" id="register">
					
				<label>{{lang('new_password')}}</label><span class="text-red"> *</span>

				<button class="btn btn-sm btn-default float-right" id="random" @click="getRandomPass()">

					<i class="fas fa-key">&nbsp;&nbsp;</i>{{lang('password_generator')}}
				</button>
				
				<div>	

					<input type="password" :class="class_name" name="change_password" id="changepassword1" v-model="password"
					@keyup="keyUp(password)">
							
					<a id="basic-addon" @click="mouseoverPass()">
									
						<span id="eye" class="fas fa-eye"></span>
					</a>
				</div>

				<div>
					
					<span :class="text_class" id="demo">{{lang(statusText)}}</span>

					<div v-if="bar_class" class="progress progress-sm">

                  <div class="progress-bar progress-bar-striped" :class="bar_class" role="progressbar" :style="styleObj">
                  
                  </div>
               </div>
				</div>
			</div>
		</div>
		<div v-if="loading" class="row" slot="fields" >
			
			<loader :animation-duration="4000" :size="60"/>
		</div>
					
		<div slot="controls">

			<button type="button" id="submit_btn" @click = "onSubmit" class="btn btn-primary" :disabled="isDisabled">
				
				<i class="fas fa-check"></i> {{lang('proceed')}}
			</button>
		</div>
	</modal>
</template>

<script type="text/javascript">
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import { passwordLengthValidation } from 'helpers/extraLogics'

	import { mapGetters } from 'vuex'

	import axios from 'axios'

	export default {
		
		name : 'password-modal',

		description : 'Password Modal component',

		props:{

			showModal : { type : Boolean, default : false},

			userId : { type : String | Number, default : '' },

			onClose : { type : Function},
		},

		data(){
			
			return {

				isDisabled:false,

				containerStyle:{ width:'800px' },

				loading:false,
				
				password  : '',

				class_name : 'form-control',

				text_class : '',

				statusText : '',

				styleObj : {},

				bar_class : '',
			}
		},

		methods:{
			
			mouseoverPass(){

				var obj = document.getElementById('changepassword1');

				var obj1 = document.getElementById('eye');
				
				obj.type =  obj.type === "text" ? "password" : "text";
				
				obj1.className = obj1.className === "fas fa-eye" ? "fas fa-eye-slash" : "fas fa-eye" ;
			},

			keyUp(password){

				this.statusText = this.checkStrLength(password);
			},

			checkStrLength(password) {

				let message = passwordLengthValidation(password);

				if(message === 'too_short' || message === 'weak'){
						
					this.class_name = 'form-control is-invalid'; 	

					this.text_class = 'text-red';		

					this.bar_class	= 'bg-danger';	

					this.styleObj = { 'width': password.length < 7 ? password.length/0.6+'%' : '20%' };
				
				} else if(message === 'good'){

					this.class_name = 'form-control is-warning'; 

					this.text_class = 'text-warning';

					this.bar_class	= 'bg-warning';	

					this.styleObj = { 'width': '70%' };

				} else {

					this.class_name = 'form-control is-valid';

					this.text_class = 'text-success';

					this.bar_class	= 'bg-success';	

					this.styleObj = { 'width': '100%' };

				}

				return message;
			},

			getRandomPass(){

				this.class_name = 'form-control';

				this.text_class = '';

				this.bar_class	= '';	

				this.statusText = '';

				this.styleObj = {};

				axios.get('/get/random/password').then(res=>{

					this.password = res.data.data;

				}).catch(error=>{ 

					this.password = ''
				})
			},

			onSubmit(){
				
				if (this.password.length < 6) {
				
					this.class_name = 'form-control is-invalid'

					this.text_class = 'text-red';

					this.bar_class	= 'bg-danger';	

					this.styleObj = { 'width': this.password.length/0.6+'%' };

					return this.statusText = 'your_password_must_be_6_character';

				} else {

					this.loading = true;

					this.isDisabled = true;

					const data = {};

					data['change_password'] = this.password;
					
					axios.post('/changepassword/'+this.userId,data).then(res=>{

						successHandler(res,'user-view')
							
						this.afterSuccess();

					}).catch(error=>{

						errorHandler(error,'user-view')
									
						this.afterResponse();
					})
				}
			},

			afterSuccess(){

				this.afterResponse();

				window.eventHub.$emit('refreshUserData');
			},

			afterResponse(){

				this.loading = false;

				this.isDisabled = false;
				
				this.onClose();
			},
		},

		components:{

			'modal':require('components/Common/Modal.vue'),
		
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>

<style type="text/css">
	
	#eye{
		float: right;margin-left: -25px;margin-top: -35px;
		margin-right: 6px;
		position: relative;
		z-index: 2;
		color: black;
	}
	#random{
		margin-bottom: 8px;
	}

	.form-control.is-valid, .form-control.is-warning, .form-control.is-invalid {
		background-image: none !important;
	}
</style>