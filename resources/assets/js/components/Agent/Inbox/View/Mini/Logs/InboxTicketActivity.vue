<template>

	<div>

		<div class="scrollable-ul">

			<div class="timeline timeline-inverse" v-if="activityLogs.length > 0">
			
				<template v-for="(log,index) in activityLogs">
						
					<div class="time-label" v-if="checkDate(index)">

						<span class="bg-success">{{formattedDate(log.created_at)}}</span>
					</div>

					<div>

						<i class="far fa-dot-circle"></i>

						<div class="timeline-item">

							<span class="time">

								<i class="far fa-clock"></i> {{formattedTime(log.created_at)}}
							</span>

							<div class="timeline-body">
									
								<span v-html="log.text" id="log_desc"></span>
							</div>
						</div>
					</div>

					<div v-if="showThreadEnd(index)">

	              <i class="fas fa-history bg-gray"></i>
	            </div>
				</template>

				<infinite-loading @infinite="getLogs" ref="infiniteLoading">		
					<div slot="spinner"></div>								
					<div slot="no-results"></div>
					<div slot="no-more"></div>
				</infinite-loading>
			</div>
			<div v-if="!showLoader && !activityLogs.length">
				<h6 class="text-center">{{trans('no-records')}}</h6>
			</div>
		</div>
		
		<custom-loader :duration="4000" v-if="showLoader" />
	</div>
</template>

<script>

	import { mapGetters } from 'vuex';
	
	import { successHandler, errorHandler } from 'helpers/responseHandler';

	import axios from 'axios';

	export default {

		name : 'inbox-ticket-activity-log',

		description : 'Inbox Ticket Activity Log Component',

		props : {

			ticketId : { type : String | Number, default : '' }
		},

		data() {

			return {

				activityLogs : [],
				
				page : 1,

				showLoader: false
			}
		},

		computed : {

			...mapGetters(['formattedTime','formattedDate'])
		},

		beforeMount() {

			this.getLogs()
		},

		methods : {

			updateLogs() {

				this.activityLogs = [];

				this.page = 1;

				this.getLogs(undefined, true);
			},

			getLogs($state, isRefresh = false) {

				this.showLoader = true;

		  		axios.get('/api/agent/ticket-activity-log/'+this.ticketId, { params: { page: this.page } }).then(res => {

					if(res.data.data.data.length) {

						if(isRefresh) {
							
							this.activityLogs = res.data.data.data;
						
						} else {
							
							this.activityLogs.push(...res.data.data.data);
						}
											  			
			  			this.page += 1;
					
					} else {
			  			
			  			$state && $state.complete();
					}			
		  		}).catch(error => {

			 		errorHandler(error, 'inbox-threads');

			 		this.activityLogs = [];
		  		
		  		}).finally(() => {
					
					$state && $state.loaded();

					this.showLoader = false;
		  		})
			},

			checkDate(x){

				if(x==0){

					return true;

				}else{

					var date1=this.formattedDate(this.activityLogs[x-1].created_at);

					var date2=this.formattedDate(this.activityLogs[x].created_at);

					if(date1!=date2){

						return true;
					}
				}
			},

			showThreadEnd(x){

				return x === this.activityLogs.length-1 
			}
		},

		components : {

			'thread-body' : require('./Mini/ThreadBody'),

			'faveo-box': require('components/MiniComponent/FaveoBox'),

			'custom-loader': require('components/MiniComponent/Loader')
		}
	};
</script>