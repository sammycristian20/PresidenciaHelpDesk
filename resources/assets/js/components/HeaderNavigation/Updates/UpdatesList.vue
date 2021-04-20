<template>
	
	<div>
		
		<alert componentName="SystemUpdates"></alert> 

		<faveo-box :title="trans('application_updates')">
			
			<div class="row" v-if="!hasDataPopulated || loading">

				<loader :animation-duration="4000" :size="60"/>
			</div>

			<div v-if="hasDataPopulated">

				<template v-if="latest != current">
					
					<template v-if="updatesData.length">

						<div v-if="updateStatus == 'fails'" class="alert alert-warning">
	                  	
			            <i class="icon fas fa-exclamation-triangle"></i> {{trans(updateMsg)}}
			         </div>

			   		<div v-else class="error-page">
			     
			     			<div class="error-content">
			       		
			       			<h3 class="text-green fw_500">

			       				<i class="fas fa-info-circle"> </i>  {{trans(updateMsg)}}
			       			</h3>
							</div>
			   		</div><br>

			   		<template v-if="updateStatus != 'fails'">
				
							<div class="row center_align">
				
								<div class="card bg-secondary col-sm-3 prl-0 current_card">
							  		
							  		<div class="card-header border_bottom_w">
								 		
								 		<h4 class="text-center">{{trans('current_version')}}</h4>

							 		 </div>
							  			
							  		<div class="card-body text-center">
								 			
								 		<b class="fs-30">{{current}}</b>
							  		</div>
							  	</div>
						 		
						 		<div class="card bg-info col-sm-3 prl-0 latest_card">
							  	
							  		<div class="card-header border_bottom_w">
								 		
								 		<h4 class="text-center">{{trans('latest_version')}}</h4>

								 	</div>
							  		
							  		<div class="card-body text-center">
								 		
								 		<b class="fs-30">{{latest}}</b>
							  		</div>
							  	</div>
						 	</div>
						 	
						 	<div class="row center_align">
								
								<button type="button" class="btn btn-light upd_btn" @click="showModal = true">

									<i class="fas fa-sync"></i> {{trans('update')}}
								</button>
							</div><br>

					 	</template>

						<div class="row">
			            
			            <div class="col-12 col-md-12 col-lg-12">

			              <div class="row">
			                	
			                	<div class="col-12">
			                  	
			                  	<h5 class="mb-3">{{trans('recent_versions')}}</h5>
				       				
				       				<template v-for="update in updatesData">
									
											<template  v-if="!update.is_private">
			                    		
					                    	<div class="post clearfix">
					                      	
					                      	<div class="user-block">
					                        	
					                        	<i class="img-circle img-bordered fas fa-tag tag_align"></i>

					                        	<span class="username">
					                          	
					                          		<a href="javascript:;">{{trans('version')}} {{update.version}}</a>

					                          		<span v-if="update.is_restricted" class="badge badge-warning restricted" v-tooltip="trans('update_restricted')">

																{{trans('restricted')}}
															</span>
					                        	</span>
					                        	
					                        	<span class="description">{{trans('released_on')}} - {{formattedTime(update.created_at)}}</span>
					                      	</div>
					                      	<p v-if="!whiteLabel" v-html="update.description" class="ml-3-1"></p>
					                    	</div>
					                  </template>
					               </template>
			                	</div>
			              	</div>
			            </div>
			         </div>
			      </template>
				</template>

				<template v-else>
					
					<span>{{trans('no_update_available')}}</span>
				</template>
	      </div>
		</faveo-box>
		
		<transition name="modal">

			<system-update-modal  v-if="showModal" :onClose="onClose" :showModal="showModal" 
				:space="space" :path="backup_path">

			</system-update-modal>
		</transition>
	</div>
</template>

<script>
	
	import { errorHandler, successHandler } from "helpers/responseHandler";

	import axios from 'axios'

	import { mapGetters } from 'vuex'

	export default {

		name : 'updates-list',

		data () {

			return {

				loading : true,

				hasDataPopulated : false,

				updatesData : [],

				pageLoad : false,

				current : '',

				latest : '' ,

				space : '',

				backup_path : '',

				showModal : false,

				updateStatus : 'success',

				updateMsg : 'an_update_is_available',

				whiteLabel: false
			}
		},

		beforeMount() {

			this.getUpdates()
		},

		computed : {

			...mapGetters(['formattedTime'])
		},

		methods : {

			getUpdates() {

				axios.get('/api/check-updates').then(res=>{

					this.latest = res.data.message.latest;

					this.current = res.data.message.current;

					this.space = res.data.message.filesystem_space;

					this.backup_path = res.data.message.backup_path;

					this.updatesData = res.data.message.version.reverse();

					this.loading = false;

					this.hasDataPopulated = true;

					this.updateStatus = res.data.data.status;

					this.updateMsg = res.data.data.message;

					this.whiteLabel = res.data.message.white_label;

				}).catch(err=>{

					this.loading = false;

					this.hasDataPopulated = true;

					let message = err.response.data.message;

					if (message == this.lang('already_on_latest_version')) {
						this.$store.dispatch('setAlert', {
							type: 'success',
							message: message,
							component_name: 'SystemUpdates'
						});
					} else {
						errorHandler(err,'SystemUpdates');
					}

					setTimeout(()=>{
					this.redirect('/dashboard')
					},2000)
				})
			},

			onClose () {

				this.showModal = false;
			}
		},

		components : {

			'alert' : require('components/MiniComponent/Alert'),

			'custom-loader' : require('components/MiniComponent/Loader'),

			'loader' : require('components/Client/Pages/ReusableComponents/Loader'),

			'faveo-box' : require('components/MiniComponent/FaveoBox'),

			'system-update-modal' : require('./UpdateModal'),
		}
	};
</script>

<style scoped>
	
	.center_align {
		justify-content: center;
	}

	.fs-30{ 
		font-size: 30px;
	}

	.prl-0 { padding-left: 0; padding-right: 0; }

	.error-content { margin-left: 175px !important; }

	.restricted { font-size : 10px !important; position: relative; bottom: 2px; }

	.tag_align {
		 position: absolute;padding: 10px;
	}

	.ml-3-1 { margin-left: 3.1rem!important; }

	.latest_card{border-bottom-left-radius: 0px;border-top-left-radius: 0px;}

	.current_card{border-bottom-right-radius: 0px;border-top-right-radius: 0px;}

	.border_bottom_w{ border-bottom-color: white !important; padding: .35rem !important; }

	.upd_btn { border: 1px solid grey; }

	.fw_500 { font-weight: 500 !important; }
</style>