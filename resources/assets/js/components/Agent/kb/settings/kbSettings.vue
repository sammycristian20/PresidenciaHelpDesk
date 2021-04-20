<template>

	<div>

		<alert componentName="KbSettings"/>
	
		<div class="card card-light ">

			<div class="card-header">
			  	
			  	<h3 class="card-title">{{lang('kb-settings')}}</h3>
		 	</div>

		 	<div class="card-body">

				<div class="row">
					
					<number-field :label="lang('number_of_elements_to_display')" :value="pagination" name="pagination" 
						classname="col-sm-6" :required="true" :onChange="onChange"type="number" 
						:hint="lang('number_of_elements_to_display_in_kb')">
													
					</number-field>

					<div class="form-group col-sm-2">
						
						<label for="status">{{lang('status')}}</label><span style="color:red"> *</span><br>
						
						<status-switch name="status" :value="status" :onChange="onChange" classname="float-left" :bold="true">
						
						</status-switch>   
					</div>

					<div class="form-group col-sm-3">
						
						<label for="status">{{lang('kb_comments')}}</label><br>
						
						<status-switch name="is_comment_enabled" :value="is_comment_enabled" :onChange="onChange" classname="float-left" :bold="true">
						
						</status-switch>   
					</div>
				</div>

				<div v-if="loading" class="row">

					<loader :animation-duration="4000"  :size="60"/>
				</div>
			</div>
			
			<div class="card-footer">
				
				<button id="post" @click="postData" class="btn btn-primary">

					<i class="fas fa-save"> </i> {{lang('save')}}
				</button>
			</div>
		</div>
	</div>
</template>

<script>

	import axios from 'axios'

	import {validateKbSettings} from "helpers/validator/kbSettingsRules.js"

	export default {
		
		data() {
			
			return {
				
				kbsettings:{},
				
				status:null,
				
				pagination:null,

				loading : false,

				is_comment_enabled : false,
		}
	},

	beforeMount() {
	
		this.commonApi();
	},

	methods: {

		onChange (value,name) {

			this[name] = value;
		},

		commonApi() {

			this.loading = true;

			axios.get('kb/settings/getvalue').then(res=>{
				
				this.loading = false;

				this.kbsettings = res.data.data.kbsettings;
			
				this.status = this.kbsettings.status;
			
				this.pagination = this.kbsettings.pagination;

				this.is_comment_enabled = this.kbsettings.is_comment_enabled;

			}).catch(res=>{
				
				this.loading = false;
			});
		},

		isValid() {

			const { errors, isValid } = validateKbSettings(this.$data);
		
			return isValid;
		},

		postData() {
			
			if(this.isValid()) {
				
				this.loading = true;

				const data={};

				data['pagination']=this.pagination;

				data['status']=this.status ? 1 : 0;

				data['is_comment_enabled'] = this.is_comment_enabled ? 1 : 0;

				axios.post('postsettings',data).then(res=>{

					this.$store.dispatch('setAlert',{type:'success',message:'Settings saved successfully',component_name:'KbSettings'})

					this.commonApi();

					this.loading = false;

				}).catch(res=>{

					this.loading = false;
				});
			}
		}
	},

	components : {

		'status-switch':require('components/MiniComponent/FormField/Switch'),

		'number-field':require('components/MiniComponent/FormField/NumberField'),

		"loader": require("components/MiniComponent/Loader"),

		'alert' : require('components/MiniComponent/Alert'),
	}
};
</script>
