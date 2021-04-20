<template>
	
	<div>
	
		<div class="row">
			
			<div class="col-md-12">
				
				<alert componentName="user-view"/>
			</div>
		</div>

		<div v-if="!loading && userData" class="row">

			<template v-if="userData.active">
				
				<div class="col-md-3">
		
					<user-details :data="userData"></user-details>
				</div>

				<div class="col-md-9">

					<user-tickets v-if="userId" :id="userId" :role="userData.role" :dept="userData.departments" :user="userData" :two_factor="userData.is_2fa_enabled" 
					:disabled_process="userData.processing_account_disabling">
						
					</user-tickets>

					<div v-if="location === 'user'" id="user-page-table">{{userPageVisible()}}</div>

					<div id="user-view-table">{{userBoxVisible()}}</div>

					<user-report v-if="userId" :id="userId" :name="userData.full_name"></user-report>
				</div>
			</template>

			<template v-else>
				
				<div class="col-md-12">
						
					<div class="row">

						<div class="col-md-12" style="margin-top:-15px;">
						
							<div id="page" class="hfeed site">
						
								<article class="hentry error404 text-center">
						
									<h1 class="error-title">
						
										<i class="fa fa-user-times text-info" style="color: grey"></i>
									</h1>
									
									<h2 class="entry-title text-muted">
										{{ userData.is_delete ? lang('user-account-deleted') : lang('user-account-deactivated')}}
									</h2>
									
									<div class="entry-content clearfix" v-if="!userData.is_delete">
											
										<p>
											<button type="button" class="btn btn-primary btn-xs" @click="showModal = true">{{lang('restore-account')}}</button>

											<button type="button" class="btn btn-primary btn-xs" @click="showDeleteModal = true">{{lang('delete_account')}}</button>
										</p>
									</div>
								</article>
							</div>
						</div>
					</div>
				</div>
			</template>
		</div>

		<div v-if="loading" class="row">
				
			<loader :animation-duration="4000" :size="60"/>
		</div>

		<transition  name="modal">
				
			<user-restore-modal v-if="showModal" :onClose="onClose" :showModal="showModal" 
				:userId="userId">
						
			</user-restore-modal>
		</transition>

		<transition  name="modal">
				
			<user-delete-modal v-if="showDeleteModal" :onClose="onClose" :showModal="showDeleteModal" 
				:userData="userData">
						
			</user-delete-modal>
		</transition>
	</div>
</template>

<script>
	
	import {errorHandler} from 'helpers/responseHandler'

	import {getIdFromUrl, lang} from 'helpers/extraLogics';

	import axios from 'axios'

	export default {
		
		name : 'user-view',

		description : 'User view page',

		props : {

			authInfo : { type : Object, default :  ()=>{}}
		},

		data() {
			return {

				userId : '',
				
				loading : true,

				userData : '',

				location : '',

				showModal : false,

				showDeleteModal : false
			}
		},

		created(){

			window.eventHub.$on('refreshUserData',this.refreshUserDetails);

			window.eventHub.$on('updateEditData',this.refreshUserDetails);
		},

		beforeMount(){

			const path = window.location.pathname;
			
			let splitPath = path.split('/');

			this.location = splitPath[splitPath.length-2];

			this.userId = getIdFromUrl(path);

			this.getInitialValues(this.userId);
		},

		methods :{

			refreshUserDetails(from = ''){

				if(from){
					this.loading = true;
				}

				this.getInitialValues(this.userId);
			},

			getInitialValues(id){

				axios.get('/api/get-user/view/'+id).then(res=>{
					
					this.userData = res.data.data;

					if(this.userData.processing_account_disabling) {

						this.$store.dispatch('setAlert',{type:'warning', duration : 500000,
							message:lang('processing_account_disabling'), component_name : 'user-view'})
					}

					this.loading = false
				
				}).catch(err=>{

					this.loading = false

					errorHandler(err,'user-view');

					this.redirect('/user');
				})
			},
			userPageVisible(){
				
				window.eventHub.$emit('user-page-mounted');
			},

			userBoxVisible(){
				
				window.eventHub.$emit('user-box-mounted',{user_id : this.userId,'from' : 'user', user:this.userData});
			},

			onClose(){
				
				this.showModal = false;

				this.showDeleteModal = false;
				
				this.$store.dispatch('unsetValidationError');
			},
		},

		components : {

			'user-details' : require('./View/UserDetails.vue'),
			
			'user-tickets' : require('./View/UserTickets.vue'),

			'user-report' : require('./View/UserReport.vue'),
			
			'user-restore-modal': require('./View/MiniComponents/UserRestoreModal'),

			'user-delete-modal': require('./View/MiniComponents/UserDeleteModal'),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'alert' : require('components/MiniComponent/Alert'),
		}
	};
</script>

<style scoped>
	
	#dept_mgr{
		min-height: 120px;
	}
</style>