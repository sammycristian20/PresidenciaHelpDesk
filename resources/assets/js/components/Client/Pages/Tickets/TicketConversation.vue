<template>

		<div>

			<meta-component dynamic_title="Ticket Conversation" :dynamic_description="lang('approval_settings-page-description')" :layout="layout" >

			</meta-component>

			<div id="alert" class="row">

				<alert/>
			</div>

			<div v-if="pageChange" class="row">

				<client-panel-loader :size="60" :color="layout.portal.client_header_color"></client-panel-loader>
			</div>

			<div v-if="statusText === ''" class="row">

				<div id="content" class="site-content col-sm-12">
										
					<article class="hentry">
												
						<header v-if="hasDataPopulated" class="entry-header">
													
							<h3 class="entry-title" :title="title" :class="{align1: lang_locale == 'ar'}">
									
								<i class="fas fa-ticket-alt"> </i> 
									
								{{ title }} 

								<small> ( {{ ticket_number }} )</small>
							</h3>
						</header>

						<div v-if="hasDataPopulated && thread_count > 0" class="entry-content clearfix">

							<div id="comments" class="comments-area" :class="{align1: lang_locale == 'ar'}">

								<ol class="comment-list">
									
									<thread-list :userId="user.id" v-for="thread in threads" :key="thread.id" :comments="thread"
										:layout="layout" :auth="auth" :dept="department" :showRatings="false">

									</thread-list>
								</ol>

								<div :class="{left: lang_locale == 'ar'}" class="float-right" v-if="thread_count > 10  && loading === false">

									<uib-pagination :total-items="thread_count" v-model="pagination" :max-size="3" 
										:class="{left: lang_locale == 'ar'}" class="pull-right"  @change="changePage" :boundary-links="true" 
										:items-per-page="perpage" :force-ellipses="true">

									</uib-pagination><br><br><br><br>
								</div>

								<div v-if="thread_count > 10"><br><br><br><br></div>
							</div>
						</div>
					</article>
				</div>
			</div>

			<div v-if="loading" id="loader" class="row">

				<loader :animation-duration="4000" :size="60" :color="layout.portal.client_header_color"/>
			</div>
		</div>
	</template>

<script>

	import axios from 'axios'

	import {validateApprovalTicketSettings} from "helpers/validator/approvalTicketRules"

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	export default {

		name : 'ticket-conversation',

		description : 'Ticket Conversation Component',

		props : {

			layout : { type :  Object, default : ()=>{}},

			auth : { type : Object, default : ()=>{}}
		},

		data () {
			return {
				thread_count: 0,

				title: '',

				ticket_number: 0,

				threads: [],

				user: {},

				pagination: {currentPage: 1},

				perpage: 10,

				loading: true,

				hasDataPopulated : false,

				paramsObj: {},

				isDisabled: false,

				comment:'',

				/**
				 * locale of the user
				 * @type {String}
				 */
				lang_locale : this.layout.language,

				hash: '',

				statusText : '',

				shallShowApprovalAction: true,

				department : '',

				pageChange : false,

			}
		},

		beforeMount() {

			this.$Progress.start();

			this.paramsObj['page'] = this.pagination.currentPage

			this.getInitialValues(this.paramsObj);
		},


		methods:{

			getHashFromUrl(url){

		    let urlArray = url.split("/");

		    return urlArray[urlArray.length - 1];

			},

			getInitialValues(params){

				this.$Progress.start();

				const path = window.location.pathname;

				this.hash = this.getHashFromUrl(path);

				axios.get('/api/ticket-conversation-guest/'+this.hash,{params}).then(res => {

					this.updateStatesWithData(res.data.data.ticket);

					this.loading = false

					this.pageChange = false;

					this.hasDataPopulated = true;

					this.$Progress.finish();

				}).catch(err => {

					this.statusText = err.response.data.message

					this.hasDataPopulated = true

					this.loading = false

					this.pageChange = false;

					this.$Progress.fail();

					this.$router.push({ path:'/not-found',name: 'NotFound',params: { statusText: this.statusText }});
				})
			},

    	updateStatesWithData(data) {

      	const self = this;

      	const stateData = this.$data;

      	Object.keys(data).map(key => {

	        if (stateData.hasOwnProperty(key)) {

	          self[key] = data[key];
	        }
	      });
    	},

    	isValid(){

				const {errors, isValid} = validateApprovalTicketSettings(this.$data)

				if(!isValid){

					return false
				}
				return true
			},

			onChange(value, name){

				this[name]= value;
			},

			changePage(){

				this.pageChange = true;

				window.scrollTo({top: 100,behavior: 'smooth'})

				this.pagination.currentPage = this.pagination.currentPage;

				this.paramsObj['page'] = this.pagination.currentPage

				this.getInitialValues(this.paramsObj);
			}
		},
		components:{

			'alert' : require('components/MiniComponent/Alert'),

			'thread-list':require('components/Client/Pages/Tickets/MiniComponents/ThreadsList'),

			'text-field': require('components/MiniComponent/FormField/TextField'),

			'client-panel-loader' : require('components/Client/ClientPanelLayoutComponents/ReusableComponents/Loader.vue'),
		}
	};
</script>

<style scoped type="text/css">
	#comment{
		margin-left:0px !important;
		border: 1px solid #d2d6de45 !important;
	}
	#textarea{
		margin-top: 10px !important;
	}
</style>
