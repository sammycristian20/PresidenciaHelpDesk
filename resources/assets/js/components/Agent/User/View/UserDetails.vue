<template>
	
		<div class="card card-light card-outline">
			
			<template v-if="loading || !data">
				
				<div id="load" class="row">

					<loader :animation-duration="4000" :size="40"/>
				</div>
			</template>
			
			<template v-if="data && !loading">

				<div class="card-body box-profile">
					
					<div class="text-center">
						
						<faveo-image-element id="userimg" :source-url="data.profile_pic" :classes="['profile-user-img img-fluid img-circle']" 
							alternative-text="User Image"/>

					</div>

					<h3 class="profile-username text-center" v-tooltip="data.full_name">{{subString(data.full_name)}}</h3>

					<p class="text-muted text-center">
						
						<span :class="{'success':data.email_verified == 1,'danger':data.email_verified != 1}" 
							class="fas fa-envelope" v-tooltip="data.email_verified == 1 ? lang('user_email_is_verified') : lang('user_has_not_verified_email')" 
							@click="userModal('email_verified')">
							
						</span>
							
						<span :class="{'success':data.mobile_verified == 1,'danger':data.mobile_verified != 1}" 
							class="fas fa-mobile-alt" v-tooltip="data.mobile_verified == 1 ? lang('user_mobile_is_verified') : lang('user_has_not_verified_mobile')"  @click="userModal('mobile_verified')">
								
						</span>

						<span :class="{'success':data.is_2fa_enabled == 1,'danger':data.is_2fa_enabled != 1}"
								:id="'2fa_status_user__' + data.id" class="fas fa-shield-alt" v-tooltip="data.is_2fa_enabled == 1 ? lang('user_enabled_2fa') : lang('user_not_enabled_2fa')">
						</span>
					</p>

					<ul class="list-group list-group-unbordered mb-3">
						
						<li class="list-group-item">
							 
							<b>{{trans('user_name')}}</b> 

							<a class="float-right" v-tooltip="data.user_name">{{subString(data.user_name)}}</a>
						</li>	

						<li class="list-group-item">
						
							<b>{{trans('email')}}</b>
								
							<a class="float-right" v-tooltip="data.email"> {{data.email}}</a>
						</li>

						<li class="list-group-item">
						
							<b>{{lang('role')}}</b>
								
							<a class="float-right text-capitalize text-green" v-tooltip="data.role"> {{subString(data.role)}}</a>
						</li>

						<li class="list-group-item">
						
							<b>{{lang('work_phone')}}</b>
								
							<a class="float-right" v-tooltip="data.phone_number"> 
								{{!data.phone_number || data.phone_number === 'Not available' ? '---' : subString(data.phone_number)}}
							</a>
						</li>

						<li class="list-group-item">
						
							<b>{{lang('mobile')}}</b>
								
							<a class="float-right" v-tooltip="data.mobile"> 
								{{!data.mobile || data.mobile === 'Not available' ? '---' : subString(data.mobile)}}
							</a>
						</li>

						<li v-if="data.role != 'user' && data.country_code" class="list-group-item">
						
							<b>{{lang('country_code')}}</b>
								
							<a class="float-right" v-tooltip="data.country_code"> {{data.country_code ? subString(data.country_code) : '---'}}</a>
						</li>

						<li class="list-group-item" v-if="data.role !== 'user'">
						
							<b>{{lang('agent_time_zone')}}</b>
								
							<a class="float-right" v-tooltip="data.timezone ? data.timezone.name :''"> 

								{{data.timezone ? subString(data.timezone.name) : '---'}}
							</a>
						</li>

						<li class="list-group-item" v-if="showLocation">
								
							<b>{{lang('location')}}</b>
								
							<a class="float-right" v-tooltip="data.location ? data.location.title : ''"> 
								
								{{data.location ? subString(data.location.title) : '---'}}
							</a>
						</li>

						<li v-if="data.role === 'user'" class="list-group-item">

							<div id="refresh-org">

								<b>{{lang('organization')}}</b>
								
								<template v-if="data.organizations.length === 0 && !data.processing_account_disabling">
									
									<a class="float-right" @click="orgClick('assign')" href="javascript:;">

										<i class="fas fa-hand-point-right"> </i> {{lang('assign')}} 
									</a>
									
									<org-create-modal :showModal="showOrgModal" :userId="data.id"></org-create-modal>

								</template>

								<template v-else>
									
									<ol v-for="org in data.organizations">

										<a :href="basePath()+'/organizations/'+org.id" v-tooltip="org.name">
											
											<span style="color:green;">{{subString(org.name)}}</span>
										</a>

										<a class="float-right" v-tooltip="lang('remove')" id="cursor" @click="orgClick('remove',org.id)">
											
											<i class="fas fa-times" style="color:red;"> </i>
										</a>
									</ol>
								</template>
							</div>
						</li>

						<li v-if="data.OrganizationDepartmentStatus && data.role == 'user'" class="list-group-item"
							v-tooltip="lang('organization_department')">

							<b>{{subString(lang('organization_department'))}}</b>
								
							<template v-for="dept in data.organization_dept">
									
								<a class="float-right" v-tooltip="dept.name"> {{subString(dept.name,8)}}</a>
							</template>
						</li>

						<li v-if="data.role !== 'user'" class="list-group-item">

							<div id="refresh-org">

								<b>{{lang('departments')}}</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								
								<a v-if="data.departments.length === 0" class="float-right">---</a>

								<template v-else>
									
									<a v-for="(dept,index) in data.departments" :href="basePath()+'/department/'+dept.id" v-tooltip="dept.name">
										
										{{subString(dept.name)}}<span v-if="index != Object.keys(data.departments).length - 1">, </span>
									</a>
								</template>
							</div>
						</li>

						<li v-if="data.role !== 'user'" class="list-group-item">
					
							<div id="refresh-team">

								<b>{{lang('teams')}}</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								
								<a v-if="data.teams.length === 0" class="float-right">---</a>

								<template v-else>
									
									<a v-for="(team,index) in data.teams" :href="basePath()+'/assign-teams/'+team.id" v-tooltip="team.name">
										
										{{subString(team.name)}}<span v-if="index != Object.keys(data.teams).length - 1">, </span>
									</a>
								</template>
							</div>
						</li>

						<li v-if="data.role !== 'user'" class="list-group-item">
					
							<div id="refresh-team">

								<b>{{lang('type')}}</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								
								<a v-if="data.types.length === 0" class="float-right">---</a>

								<template v-else>
									
									<a v-for="(type,index) in data.types" v-tooltip="type.name">
										
										{{subString(type.name)}}<span v-if="index != Object.keys(data.types).length - 1">, </span>
									</a>
								</template>
							</div>
						</li>

						<li v-if="data.role === 'user'" class="list-group-item">
						
							<b>{{lang('address')}}</b>
								
							<p v-tooltip="data.internal_note"> {{data.internal_note}}</p>
						</li>

						<li v-for="value in data.custom_field_values" class="list-group-item">
								
							<b>{{value.label}}</b>
								
							<a v-if="Array.isArray(value.value)" v-tooltip="value.value.toString()">
								&nbsp;{{value.value.toString()}}
							</a>

							<a v-else class="float-right" v-tooltip="value.value"> {{subString(value.value,15)}}</a>

						</li>
					</ul>
				</div>
			</template>

			<transition  name="modal">
			
				<org-modal v-if="showOrgModal" :onClose="onClose" :showModal="showOrgModal" :title="orgModalTitle" 
					:orgId="orgId" :userId="data.id" :deptCondition="data.OrganizationDepartmentStatus">
					
				</org-modal>
			</transition>

			<transition  name="modal">
				
				<user-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :title="modalTitle" 
					:userData="data">
					
				</user-modal>
			</transition>
		</div>
</template>

<script>

	import { getSubStringValue } from 'helpers/extraLogics';

	import { mapGetters } from 'vuex'

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {

		name : 'user-details',

		description : 'User details  page',

		props : { 

			data : { type : Object | String , default : ''}
		},

		data() {

			return {
		
				loading : false,

				showOrgModal : false,

				orgModalTitle : '',

				orgId  : '',

				modalTitle : '',

				showModal : false,

				ban : this.data.ban,

				obj : {}
			}
		},

		computed: {
		  showLocation() {
				if (this.data.role !== 'user') {
					return true;
				} else {
					return !!(this.data.location)
				}
			}
    	},

		methods :{

			subString(name, length = 15){

				return getSubStringValue(name,length)
			},

			onClose(){
				
				this.showModal = false;

				this.showOrgModal = false;
				
				this.$store.dispatch('unsetValidationError');
			},

			userModal(title){

				this.showModal = !this.data[title];

				this.modalTitle = title; 
			},


			commonMethod(api,value){

				if(api === '/settings/user/status'){

					this.obj['settings_status'] = value;
				}

				this.obj['user_id'] = this.data.id;

				axios.post(api,this.obj).then(res=>{

					this.loading = false;

					window.eventHub.$emit('refreshUserData');

					successHandler(res,'user-view')

				}).catch(error=>{

					this.loading = false;

					errorHandler(error,'user-view');
				})
			},

			orgClick(name,id){

				this.orgId = id;

				this.showOrgModal = true;

				this.orgModalTitle = name;
			},
		},

		components : {

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'org-modal': require('./MiniComponents/OrganizationModal'),

			'user-modal': require('./MiniComponents/UserStatusModal'),

			'org-create-modal': require('./MiniComponents/OrganizationCreateModal'),

			'faveo-image-element': require('components/Common/FaveoImageElement')
		}
	};
</script>

<style scoped>
	
	#user_img{
		border:3px solid #CBCBDA;
		padding:3px;
	}
	#load{
		margin-top:30px;margin-bottom:30px;
	}

	.success{
		color : #017701 !important;
		cursor: pointer;
		padding: 3px;
	}
	.danger{
		color : red !important;
		padding: 3px;
		cursor: pointer;
	}
	#cursor{
		cursor: pointer;
	}
</style>