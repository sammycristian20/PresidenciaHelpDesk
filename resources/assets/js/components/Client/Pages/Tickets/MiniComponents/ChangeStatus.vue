<template>
	
	<div class="btn-group">
	
		<button id="bttn" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" @click="statusList">
			
			<i class="fa fa-exchange-alt" style="color:teal;" > </i>
			
			<i class="fa fa-spinner fa-spin" style="color:teal; display:none;" id="spin"></i>
			
			{{ lang('change_status') }} <span class="caret"></span>
		</button>
		
		<ul class="dropdown-menu status-list" >
		
			<li v-if="loading">
		
				<div class="row">
		
					<loader :animation-duration="4000" :size="25" :color="layout.portal.client_header_color"/>
		
				</div>
			</li>

			<li v-if="!loading && TicketStatusList.length === 0">

            	<p class="status_rec">No records</p>

         	</li>

			<li v-if="!loading" v-for="status in TicketStatusList" :key="status.id">
					
				<a class="dropdown-item" :class="{align1: lang_locale == 'ar'}"  v-if="status.allow_client" 
					href="javascript:void(0)" @click="updateStatus(status)">

					<i :class="status.icon" :style="{color:status.icon_color}"></i> {{status.name}}
				</a>
			</li>
		</ul>
	</div>
</template>
<script>
	
import {arrayUnique} from 'helpers/extraLogics';

export default {

	props : { 

		layout : { type : Object, default : ()=>{}},

		updateStatus : { type : Function},

		ticketIds : { type : Array, default : ()=>[]}

	},

	data() {

		return {

			TicketStatusList : [],  

			statuslists : [],

			lang_locale : this.layout.language,

			loading : false,
		}
	},

	methods : {
	
		statusList() {

			this.loading = true;

			this.$Progress.start();

			let params = {meta: true, supplements:arrayUnique(this.ticketIds),limit : 'all'}

			axios.get('api/dependency/statuses',{params}).then(response=>{
				
				this.loading = false;
					
				this.statuslists=response.data.data.statuses;

				this.TicketStatusList = this.statuslists;

				this.$Progress.finish();

			}).catch(error=>{

				this.loading = false;

				this.$Progress.fail();
			})		
		}
	}
};
</script>
<style scoped>

.status-list { min-height : 45px; max-height: 250px; overflow-y: auto; overflow-x: hidden; }

#change{
	color:teal;
}
#bttn {
	background-color: #e7e7e7 !important;
		border-color: #adadad !important;
		font-size: 14px;
}
.btn .caret {
    margin-left: 0;
}

.caret {
    display: inline-block;
    width: 0;
    height: 0;
    margin-left: 2px;
    vertical-align: middle;
    border-top: 4px dashed;
    border-top: 4px solid\9;
    border-right: 4px solid transparent;
    border-left: 4px solid transparent;
}
.status_rec{ text-align: center;margin: auto;}

.fa-bell-o:before{content:"\f0a2"}

.fa-bookmark-o:before{content:"\f097"}

.fa-check-square-o:before{content:"\f046"}

.fa-circle-o-notch:before{content:"\f1ce"}

.fa-comment-o:before{content:"\f0e5"}

.fa-commenting:before{content:"\f27a"}

.fa-commenting-o:before{content:"\f27b"}

.fa-comments-o:before{content:"\f0e6"}

.fa-close:before{content:"\f00d"}

.fa-envelope-o:before{content:"\f003"}

.fa-exchange:before{content:"\f0ec"}

.fa-hand-paper-o:before{content:"\f256"}

.fa-external-link:before{content:"\f08e"}

.fa-feed:before,.fa-rss:before{content:"\f09e"}

.fa-flag-o:before{content:"\f11d"}

.fa-flash:before,.fa-bolt:before{content:"\f0e7"}

.fa-folder-o:before{content:"\f114"}.fa-folder-open-o:before{content:"\f115"}

.fa-group:before{content:"\f0c0"}

.fa-line-chart:before{content:"\f201"}

.fa-mail-forward:before{content:"\f064"}

.fa-mail-reply:before{content:"\f112"}

.fa-mail-reply-all:before{content:"\f122"}

.fa-trash-o:before{content:"\f014"}

.fa-refresh:before{content:"\f021"}
</style>