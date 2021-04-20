<template>
	
	<div class="row">

		<div id="content" class="site-content col-sm-12">

			<div v-if="loading">

        <client-panel-loader :size="60"></client-panel-loader>
      </div>

      <div v-if="!hasDataPopulated">

        <loader :size="60"></loader>
      </div>

      <alert componentName="License"/>

			<article class="hentry" v-if="verify">

				<header class="entry-header text-center">

					<h3 class="entry-title">{{lang('licenseCode')}}</h3>
				</header>

				<div class="entry-content clearfix">

					<p class="text-center">You can find your license code in our billing portal 
						<a href="https://billing.faveohelpdesk.com" target="_blank">billing.faveohelpdesk.com</a>
					</p>

					<p class="text-center"><b>Please enter your License Code for Faveo Helpdesk Enterprise</b></p>

					 <ValidationObserver ref="licenseErrorPage">
						<template slot-scope="{ invalid, classes }">

								<p class="container text-center">

									<span v-for="input in inputArr" :key="input.current">
								    	
							    		<ValidationProvider :name="input.current" rules="required">
											
											<template slot-scope="{ classes }">
									      	<input
									      		class="form-control inline_block text-uppercase"
									      		:class="classes"
									        		type="text"
									        		:name="input.current"
									        		:id="input.current"
										        	:ref="input.current"
										        	v-model="input.value"
										        	@input="onInput(input)"
										        	maxlength="4"
										        	size="4"
										        	@paste="onPaste($event)"
									      	/>
									      <span v-if="input.next" id="dash">-</span>&nbsp;
									   </template>
									</ValidationProvider>
							    </span>
								</p>

								<p class="text-center"> 

									<button class="btn btn-primary" :disabled="invalid" @click="onSubmit()">
										<i class="fa fa-save"> </i> {{lang('submit')}}
									</button>
								</p>
						</template>
					</ValidationObserver>
				</div>
			</article>
		</div>
	</div>
</template>

<script>
	
	import { errorHandler, successHandler } from 'helpers/responseHandler'
	import { validateFormData } from 'helpers/formUtils'

	import axios from  'axios'

	export default {
		
		data(){

			return {

				inputArr: [
		        {
		          current: "first",
		          next: "second",
		          value: "",
		        },
		        {
		          current: "second",
		          next: "third",
		          value: "",
		        },
		        {
		          current: "third",
		          next: "forth",
		          value: "",
		        },
		        {
		          current: "forth",
		          next: null,
		          value: "",
		        },
		      ],

				loading : false,

				hasDataPopulated : false,

				verify : false
			}
		},

		beforeMount(){

			this.getValues();
		},

		methods : {

			getValues(){

				this.$Progress.start();

				axios.get('/api/licenseError').then(res=>{

					this.hasDataPopulated = true;

					this.verify = true;

					this.$Progress.finish();

				}).catch(error=>{

					this.$Progress.fail();

					this.hasDataPopulated = true;

					errorHandler(error,'License');

					var msg = error.response.data.message;

					if(msg.includes('not') || msg.includes('expired')){

						this.verify = true	
					} else {

						this.redirect('/')
					}
				})
			},

			onInput(input) {
      		
      		if (input.value.length === 4) {
        			
        			input.next && this.$refs[input.next][0].focus();
      		}
    		},
    		
    		onPaste(event) {
      		
      		event.preventDefault();
      		
      		const text = event.clipboardData.getData("text/plain");

      		if (!text) return;

      		const sanitizedText = this.sanitizeText(text);

      		let startAt = 0;
      		
      		let endAt = 4;

      		this.inputArr.forEach((input) => {
        			
        			input.value = sanitizedText.substring(startAt, endAt);
        			
        			startAt = endAt;
        			
        			endAt = startAt + 4;
      		});
    		},

    		sanitizeText(string) {
      		
      		return string.trim().replace(/[^A-Za-z0-9]/g, "");
    		},

			isValid(){
				
				const {errors, isValid} = validateLicenseSettings(this.$data);
				
				if(!isValid){
				
					return false
				}
				return true
			},

			onSubmit() {

				if (validateFormData(this.$refs.licenseErrorPage)) {
											
					this.$Progress.start();

					this.loading = true;

					const data = {};

					for(let i in this.inputArr) {

						data[this.inputArr[i].current] = this.inputArr[i].value
					}

					axios.post('/licenseVerification',data).then(res=>{

						this.loading = false;

						successHandler(res,'License');

						this.redirect('/');
						
						this.$Progress.finish();
					
					}).catch(error=>{

						this.loading = false;

						errorHandler(error,'License');

						this.$Progress.fail();

					})
				}
			}
		},

		components : {

			'client-panel-loader' : require('components/Client/ClientPanelLayoutComponents/ReusableComponents/Loader.vue'),

			'alert' : require('components/MiniComponent/Alert'),
		}
	};
</script>

<style scoped>

	.inline_block{
		width: auto !important;
		display: inline-block !important;
	}

	#dash{
		margin-top: 5px;
    	color: #838586;
	}
	.field-danger{
		border : 1px solid red;
	}
</style>