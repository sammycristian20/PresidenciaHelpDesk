<template>
	
	<div>
		
		<alert componentName="email-diagno"></alert> 

		<div class="card card-light">
			
			<div class="card-header">
				
				<h3 class="card-title">{{trans('send-mail-to-diagnos')}}</h3>
			</div>

			<div class="card-body">
				
				<div class="row">
					
					<dynamic-select :label="trans('from')" :multiple="false" 
						name='from' classname="col-sm-6"
						apiEndpoint="api/emails-list?outgoing_emails=true" 
						:value="from" :onChange="onChange" :strlength="60"
						:required="true" :clearable="from ? true : false">
					</dynamic-select> 

					<text-field :label="trans('to')" :value="to" type="text" name="to" :onChange="onChange" 
       				classname="col-sm-6" :required="true">
					</text-field> 
				</div>

				<div class="row">
					
					<text-field :label="trans('subject')" :value="subject" type="text" name="subject" :onChange="onChange" 
       				classname="col-sm-12" :required="true">
					</text-field> 	

					<text-field :label="trans('message')" :value="message" type="textarea" name="message" :onChange="onChange" 
       				classname="col-sm-12" :required="true">
					</text-field>
				</div>
			</div>

			<div class="card-footer">
				
				<button type="button" class="btn btn-primary" @click="onSubmit">
				
					<i class="fas fa-paper-plane"> </i> {{trans('send')}}
				</button>
			</div>
		</div>

		<div class="row" v-if="loading">

			<custom-loader :duration="4000"></custom-loader>
		</div>
	</div>
</template>

<script>
	
	import { errorHandler, successHandler } from "helpers/responseHandler";
	
	import { validateDiagnoSettings } from "helpers/validator/diagnoRules.js"

	import axios from 'axios'

	export default {

		name : 'email-diagno',

		data() {

			return {

				from : '',

				to : '',

				subject : '',

				message : '',

				loading : false
			}
		},

		methods : {

			onChange (value, name) {

				this[name] = value;
			},

			isValid() {

				const { errors, isValid } = validateDiagnoSettings(this.$data);
				
				return isValid
			},

			onSubmit () {

				if(this.isValid()){
			
					this.loading = true 
		
					let data = {};
					
					data['from'] = this.from.id;
					
					data['to'] = this.to;

					data['subject'] = this.subject;
					
					data['message'] = this.message.replace(/\r?\n/g, '<br />');
					
					axios.post('/api/postdiagno', data).then(res => {
					
						this.loading = false
						
						successHandler(res,'email-diagno');

						this.resetFields()
						
					}).catch(err => {
					
						this.loading = false
						
						errorHandler(err,'email-diagno')					
					});
				}
			},

			resetFields() {

				this.from = '';

				this.to = '';

				this.subject = '';

				this.message = '';
			}
		},

		components : {

			'alert' : require('components/MiniComponent/Alert'),

			'custom-loader' : require('components/MiniComponent/Loader'),

			"text-field": require("components/MiniComponent/FormField/TextField"),

			'dynamic-select':require('components/MiniComponent/FormField/DynamicSelect'),
		}
	};
</script>