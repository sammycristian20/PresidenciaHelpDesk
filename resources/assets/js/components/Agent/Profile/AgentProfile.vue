<template>
	
	<div class="box box-primary">
		
		<div class="box-header with-border" v-if="!loading">
			
			<div class="row">
				
				<div class="col-md-4">
					
					<h2 class="box-title">{{lang('profile')}}</h2>
				</div>
				
				<div class="col-md-8">
					
					<a class="btn btn-primary pull-right" :href="basePath()+'/profile-edit'">
						
						<span class="fa fa-edit">  </span> {{lang('edit')}}
					</a>
				</div>
			</div>
		</div>

		<div class="row" v-if="loading" id="loader">

			<loader :animation-duration="4000" :size="60"/>
		</div>

		<div class="row" v-if="!loading">
			
			<div class="col-md-6">
				
				<div class="box-header with-border">
					
					<h2 class="box-title">{{lang('user_information')}}</h2>

				</div>
				
				<div class="box-body">
					
					<div class="form-group  row">
						
						<div class="col-xs-4"><label>{{lang('department')}} :</label></div> 

						<div class="col-xs-7"> {{profileData.departments ? getNames(profileData.departments).toString() : '---'}}</div>
					</div>
					
					<div class="form-group  row">
						
						<div class="col-xs-4"><label>{{lang('permissions')}} :</label></div> 

						<div class="col-xs-7"> {{profileData.permision ? getKeys(profileData.permision.permision) : '---'}}</div>
					</div>

					<div class="form-group  row">
						
						<div class="col-xs-4"><label>{{lang('role')}} :</label></div> 

						<div class="col-xs-7 text-capitalize"> {{profileData.role}}</div>
					</div>

					<div class="form-group  row">
						
						<div class="col-xs-4"><label>{{lang('location')}} :</label></div> 

						<div class="col-xs-7"> {{profileData.location ? profileData.location.name : '---'}}</div>
					</div>
				</div>
			</div>
			
			<div class="col-md-6">
				
				<div class="box-header with-border">
					
					<h2 class="box-title">{{lang('contact_information')}}</h2>
				</div>
				
				<div class="box-body">
					
					<div class="form-group row">
						
						<div class="col-xs-4"><label>{{lang('email')}} :</label></div>
						<div class="col-xs-7"> {{profileData.email ? profileData.email : '---'}}</div>
					</div>
					
					<div class="form-group row">
						
						<div class="col-xs-4"><label>{{lang('phone_number')}} :</label></div> 
						<div class="col-xs-7"> {{profileData.phone_number ? profileData.phone_number : '---'}}</div>
					</div>

					<div class="form-group row">
						
						<div class="col-xs-4"><label>{{lang('mobile')}} :</label></div> 
						<div class="col-xs-7"> {{profileData.mobile ? profileData.mobile : '---'}}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	
	import axios from 'axios';

	import { lang } from 'helpers/extraLogics';

	export default {

		name : 'agent-profie',

		description  : 'Agent Profile Page',

		data(){

			return {

				loading : false,

				profileData : ''
			}
		},

		beforeMount(){

			this.getValues();
		},


		methods : {

			getValues(){

				this.loading = true;

				axios.get('/api/profile/info').then(res=>{

					this.profileData = res.data.data;

					this.loading = false;

				}).catch(error=>{

					this.profileData = '';
					
					this.loading = false;

				});
			},

			getNames(arr){
				return arr.map(function (el) { return el.name; })
			},

			getKeys(obj){

				let langArray = [];

				let keys = Object.keys(obj);

				for(var i in keys){

					langArray.push(lang(keys[i]));
				}

				return langArray.toString();
			}
		},

		components : {

			'loader': require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>

<style scoped>
	
	#loader{
		margin-top: 30px;
		margin-bottom: 30px;
	}
</style>