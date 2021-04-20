<template>
	
	<div>
		
		<alert componentName="recaptcha-settings"></alert>

		<faveo-box :title="trans('recaptcha-settings')">
		
			<div>

				<div class="row" v-if="loading" id="loader_margin">

					<loader :animation-duration="4000" :size="60"/>
				</div>

				<template v-if="!loading">
					
					<div class="row">
					
						<radio-button :options="typeOptions" :label="trans('recaptcha_type')" name="type" :value="type"
							:onChange="onChange" classname="form-group col-sm-6" hint="recaptcha_type_tooltip">
								
						</radio-button>

						<radio-button :options="statusOptions" :label="trans('status')" name="status" :value="status"
							:onChange="onChange" classname="form-group col-sm-6" >
								
						</radio-button>
					</div>

					<div class="row">
						
						<text-field :label="trans('googleServiceKey')" :value="googleServiceKey" type="text" name="googleServiceKey"
							:onChange="onChange" classname="col-sm-6" :required="true">
										
						</text-field>

						<text-field :label="trans('googleSecretKey')" :value="googleSecretKey" type="text" name="googleSecretKey"
							:onChange="onChange" classname="col-sm-6" :required="true">
										
						</text-field>
					</div>

					<div class="row">
						
						<check-box :options="checkboxOptions" name="applyfor" :value="applyfor" :label="trans('apply_for')"
							:onChange="onChange" classname="col-sm-12">
							
						</check-box>
					</div>

					<div>
						<br>
						<div v-if="!verified" class="row">
							
							<div class="col-sm-6">
								
								<loader :animation-duration="4000" :size="30"/>
							</div>
						</div>

						<recaptcha-field v-if="verified && googleServiceKey":node="{}"
							name="recaptcha"
							:siteKeyValue="googleServiceKey"
							:captchaVersion="type"
							:verifyCaptcha="verifyCaptcha">

						</recaptcha-field>
					</div>
				</template>
			</div>

			<div slot="actions" class="card-footer">

				<button class="btn btn-primary" @click="onSubmit()" :disabled="!recaptchaVerified">
						
					<i class="fas fa-save"></i> {{trans('save')}}
				</button>	
			</div>
		</faveo-box>
	</div>
</template>

<script>
	
	import axios from "axios";
	
	import { errorHandler, successHandler } from "helpers/responseHandler";

	import { validateReCaptchaSettings } from "helpers/validator/recaptchaRules.js"

	import { lang } from 'helpers/extraLogics'

	export default {

		name : 'recaptcha-settings',

		description : 'ReCaptcha Settings Page',

		data () {

			return {

				status : false,

				type : 'v2',

				typeOptions :'',

				statusOptions : [{ name : 'active', value : true }, { name : 'inactive', value : false}],

				googleServiceKey : '',

				googleSecretKey : '',

				/**
				 * Options for showing recaptcha in pages
				 * @type {Array}
				 */
				applyfor : [],

				loading : true,

				isDisabled : false,

				checkboxOptions : [],

				verified : true,

				recaptchaVerified : ''
			}
		},

		beforeMount () {

			this.typeOptions =  [
				{ name : lang('recaptcha') + ' v2', value : 'v2',hint:'v2_tooltip' },
				{ name : lang('recaptcha') + ' v3', value : 'v3' , hint:'v3_tooltip'}
			]
			this.getValues();
		},

		methods : {

			verifyCaptcha(value) {

				this.recaptchaVerified = value;
			},

			getValues () {

				this.isDisabled = true;

				axios.get('/api/admin/recaptcha').then(res=>{

					let details = res.data.data;

					this.status = Boolean(details.status);

					this.type = details.type ? details.type : 'v2';

					this.googleServiceKey = details.googleServiceKey;

					this.googleSecretKey = details.googleSecretKey;

					this.applyfor = details.applyfor;

          this.checkboxOptions = details.recaptchaOptions;

          this.loading = false;

					this.isDisabled = false;

				}).catch(err=>{

					this.loading = false;

					this.isDisabled = false;

				})
			},

			onChange (value,name) {

				this[name] = value;

				if(name === 'googleServiceKey' || name === 'type'){

					this.recaptchaVerified = '';

					this.verified = false;

					setTimeout(()=>{

						this.verified = true
					},2000)
				}
			},

			isValid () {

				const { errors, isValid } = validateReCaptchaSettings(this.$data);

				return isValid;
			},

			verify() {

				this.showCaptcha = true;
			},

			onSubmit () {

				if(this.isValid()){

					this.loading = true;

					this.isDisabled = true;

					let data = {};

					data['status'] = this.status;

					data['type'] = this.type;
					
					data['google_service_key'] = this.googleServiceKey;
					
					data['google_secret_key'] = this.googleSecretKey;

					data['validate_secret_key'] = this.recaptchaVerified;

					data['apply_for'] = this.applyfor;

					axios.post('/store/recaptcha',data).then(res=>{

						this.getValues();

						successHandler(res,'recaptcha-settings');

					}).catch(err=>{

						errorHandler(err,'recaptcha-settings');

						if(err.response.data.message && err.response.data.message['google_secret_key']) {

								this.$store.dispatch('setValidationError',{ googleSecretKey: "Secret Key is not valid" })
							}

					}).finally(res => {
						
						this.recaptchaVerified = ''; 

						this.loading = false;

						this.isDisabled = false;
					})
				}
			}
		},

		components : {

			'radio-button':require('components/MiniComponent/FormField/RadioButton'),

			'text-field':require('components/MiniComponent/FormField/TextField'),

			'check-box': require('components/MiniComponent/FormField/CheckBoxComponent'),
	 
			"alert": require("components/MiniComponent/Alert"),

			'custom-loader' : require('components/MiniComponent/Loader'),

			'loader' : require('components/Client/Pages/ReusableComponents/Loader'),

			"recaptcha-field": require("components/FormFields/RecaptchaField.vue"),

			"faveo-box" : require('components/MiniComponent/FaveoBox')
		}
	};
</script>

<style scoped>
	.pad_0 {
		padding: 0px !important;
	}
	.ml_10{
		margin-left: 10px;
	}
	#loader_margin {
		margin-top: 30px;
		margin-bottom: 30px;
	}
	.mar_20 {
		margin: 0px 10px 0px 10px;
	}
	.margin-left-0 { margin-left: -10px !important; }
</style>