<template>

	<div>
		
		<meta-component :dynamic_title="lang('show-ticket-title')" 
      :dynamic_description="lang('show-ticket-description')" :layout="layout" >
        
    </meta-component>

		<div v-if="pageChange" class="row">

			<client-panel-loader :color="layout.portal.client_header_color" :size="60"></client-panel-loader>
		</div>

		<div v-if="loading" class="row" style="margin-top:30px;margin-bottom:30px">
		
			<loader :color="layout.portal.client_header_color" :animation-duration="4000" :size="60"/>
		</div>

		<alert/>

		<div class="row" v-if="!loading">

			<div id="content" class="site-content col-sm-12">
						
				<article class="hentry">
								
					<header class="entry-header">
									
						<h3 class="entry-title" :title="TicketDetail.title" :class="{align1: lang_locale == 'ar'}">
							<i class="fas fa-ticket-alt"> </i> 
							{{subString(comments_list[0].title === '' ? TicketDetail.title : comments_list[0].title) }} 

							<small>( {{TicketDetail.ticket_number }} )</small>
						</h3>
					</header>

					<div class="entry-content clearfix">

						<div class="entry-attribute clearfix" id="rate_entry">
							
							<div class="row">
											
								<div class="share-links col-sm-6"></div>

								<div class="rate-post col-sm-6" :class="[(lang_locale === 'ar') ? 'text-left' : 'text-right']">
									
									<rating-component v-if="AreaRatings" url="rating/" :AreaRatings="AreaRatings" :layout="layout" 
										from="show-ticket" :auth="auth" :department="TicketDetail.department" :ticketId="ticket_Id" 
										area="Helpdesk Area"> 

									</rating-component>				
								</div>
							</div>
						</div>

						<ticket-details v-if="ticketData" :ticket="ticketData" :layout="layout" :auth="auth"></ticket-details>

						<div id="comments" class="comments-area" :class="{align1: lang_locale == 'ar'}">

							<ol class="comment-list">

								<ticket-thread :userId="user_id" v-for="comments in comments_list1" :key="comments.id" :comments="comments" 
									:layout="layout" :auth="auth" :dept="TicketDetail.department" from="show-ticket">
									
								</ticket-thread> 
								</ol>
											
								<div :class="{left: lang_locale == 'ar'}" class="float-right" v-if="records > 10">

									<uib-pagination :total-items="records" v-model="pagination" :max-size="3" class="pagination"  @change="changePage" 
										:boundary-links="true" :items-per-page="perpage" :force-ellipses="true">
													
									</uib-pagination><br><br><br><br>
								</div>

								<div v-if="records > 10"><br><br><br><br></div>
							</div>
						</div>
					</article>
				</div>
			</div>
		</div>
</template>

<script>
	
	import axios from 'axios'
	
	import { mapGetters } from 'vuex'
	
	import { findObjectByKey } from 'helpers/extraLogics'

	import { getSubStringValue } from 'helpers/extraLogics';
	
	export default {
		
		name : 'show-ticket',

		description : 'Show ticket page',

		props : {

			layout : { type : Object, default : ()=>{}},

			auth : { type : Object, default : ()=>{}}
		},

		components:{
			
			'rating-component':require('./MiniComponents/ClientPanelTimelineRatings'),
			
			'ticket-thread':require('./MiniComponents/ThreadsList'),
			
			'ticket-details': require('./MiniComponents/TicketDetails'),

			'client-panel-loader' : require('components/Client/ClientPanelLayoutComponents/ReusableComponents/Loader.vue'),

			'alert' : require('components/MiniComponent/Alert'),
		},

		data() {
			
			return {

				loading : true,

				perpage:10,
				
				pagination:{currentPage: 1},
				
				records:0,

				TicketDetail:'',

				ticketData :'',

				comments_list:[],

				comments_list1:[], 

				ticket_Id:'',
	
				url:'',

				AreaRatings : '',

				user_id : this.auth.user_data.id,

				tic_user_id:null,

				check_id:null,
				
				paramsObj:{},

				lang_locale : this.layout.language,

				pageChange : false,

				actionPerformed : 'initialLoad',

				lineStyle : { borderColor : this.layout.portal.client_header_color},
			}
		},

		created() {
		 	
		 	this.$Progress.start();

			this.path=location.pathname.split('/');
			
			this.check_id=this.path[this.path.length-1];

			this.paramsObj['page']=this.pagination.currentPage;

			this.checkTicketId(this.paramsObj);
		},

		beforeMount(){
  		
  		this.$store.dispatch('setRatingTypes');
  	},

		filters : {
		
			toUp(value){
			
				return value.toUpperCase();
			},
		},

		computed: {

			...mapGetters(['formattedTime','formattedDate'])
		},
		
		methods : {

			checkTicketId(x) {
				
				this.$Progress.start();
				
				var params = x;
				
				axios.get('/api/have-a-ticket/'+this.check_id,{params}).then(response => {

					if(x.page === 1){
					
						this.ticketData = response.data.data.ticket;
					}

					this.loading = false;

					this.pageChange = false;

					this.$Progress.finish(); 

					this.TicketDetail 		= response.data.data.ticket;
						
					this.tic_user_id = this.TicketDetail.user.id;
						
					this.ticket_Id 		= this.TicketDetail.id;
						
					this.AreaRatings = this.TicketDetail.ratings;  
						
					this.comments_list 	= this.TicketDetail.threads;
						
					this.comments_list1 	= (this.TicketDetail.threads).reverse();
						
					this.records = this.TicketDetail.thread_count;
						
					this.url = 'rating/'

				}).catch(error=>{
					
					this.loading = false;

					this.$Progress.fail();
				})
			},
			
			changePage(){
				
				this.actionPerformed = 'pageChange';

				var elmnt = document.getElementById('comments');
		  		
		  	elmnt.scrollIntoView({ behavior : "smooth"});

				this.$Progress.start();
				
				this.pageChange = true;

				this.paramsObj['page']=this.pagination.currentPage;
				
				this.checkTicketId(this.paramsObj);
			},

			subString(value){
			
				return getSubStringValue(value,15)
			}
		},
	};
</script>
<style scoped>
		.status{
		margin-top: -5px;
		margin-bottom: 5px;
	}

	.rate-post{
		margin-top: -0.5rem !important;
	}

	#rate_entry{
    margin-bottom: 1.5em !important;
    border-bottom: 0px solid #eee !important; 
    line-height: 2.35em;
	}

	#time_reply{
		margin-top: 0.5rem !important;
	}

	#close_attach {
    cursor: pointer;
    float: right;
    margin-top: 3px;
	}

	#list {
    list-style-type: none;
    background-color: #f5f5f5;
    border: 1px solid #dcdcdc;
    font-weight: 700;
    margin: 8px 8px 8px 0;
    overflow-y: hidden;
    padding: 4px 4px 4px 8px;
    max-width: 448px;
	}
	.text-right{
		text-align: -webkit-right !important;
		text-align: -moz-right !important;
	}
	.text-left{
		text-align: -webkit-left !important;
		text-align: -moz-left !important;
	}
</style>