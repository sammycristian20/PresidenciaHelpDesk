<template>

	<div>

		<meta-component :dynamic_title="lang('check-ticket-title')"
      :dynamic_description="lang('check-ticket-description')" :layout="layout" >

    </meta-component>

		<div v-if="pageChange" class="row">

			<client-panel-loader :size="60" :color="layout.portal.client_header_color"></client-panel-loader>
		</div>

		<div v-if="loading" class="row" style="margin-top:30px;margin-bottom:30px">

			<loader :color="layout.portal.client_header_color" :animation-duration="4000" :size="60"/>
		</div>

		<alert componentName="statusBox"/>

		<div class="row" v-if="hasDataPopulated">

			<div id="content" class="site-content col-sm-12">
						
				<article class="hentry">
					
					<header class="row">
						
						<div class="col-sm-8">
							
							<h3 class="entry-title" :title="TicketDetail.title" :class="{align1: lang_locale == 'ar'}">
								<i class="fas fa-ticket-alt"> </i> 
								{{subString(comments_list[0].title === '' ? TicketDetail.title : comments_list[0].title) }}

								<small>( {{TicketDetail.ticket_number }} )</small>

							</h3>
						</div>

						<div class="col-sm-4">
							
							<div :class="{left: lang_locale == 'ar'}" class="float-right status">

								<ticket-status v-if="tic_status == 1" :status_type="status_type"
									:layout="layout" :updateStatus="updateStatus" :ticketIds="selected">

								</ticket-status>
							</div>
						</div>
					</header>

					<div class="clearfix">

						<div class="entry-attribute clearfix" id="rate_entry">
				
							<div class="row">
								
								<div class="share-links col-sm-6"></div>

								<div class="rate-post col-sm-6" :class="[(lang_locale === 'ar') ? 'text-left' : 'text-right']">
									
									<rating-component v-if="AreaRatings" url="rating/" :AreaRatings="AreaRatings" :layout="layout"
										:auth="auth" :department="TicketDetail.department" :ticketId="ticket_Id" area="Helpdesk Area">

									</rating-component>
										
								</div>
							</div>
						</div>

						<ticket-details v-if="ticketData" :ticket="ticketData" :layout="layout" :auth="auth"></ticket-details>

						<div id="comments" class="comments-area" :class="{align1: lang_locale == 'ar'}">

							<ol class="comment-list">

								<ticket-thread :userId="user_id" v-for="comments in comments_list1" :key="comments.id" :comments="comments"
									:layout="layout" :auth="auth" :dept="TicketDetail.department">

								</ticket-thread>
							</ol>
								
							<div :class="{left: lang_locale == 'ar'}" class="float-right" v-if="records > 10">

								<uib-pagination :total-items="records" v-model="pagination" :max-size="3" class="pagination"  @change="changePage" 
									:boundary-links="true" :items-per-page="perpage" :force-ellipses="true">
										
								</uib-pagination><br><br><br><br>
							</div>

							<div v-if="records > 10"><br><br><br><br></div>

							<alert componentName="replyBox"></alert>

							<div v-if="showEditor==1" id="reply_threads" class="comment-respond form-border">

								<h3 class="comment-reply-title section-title reply_title" id="H3" >

									<i class="line" :class="{left0 : lang_locale == 'ar'}" :style="lineStyle"></i>{{ lang('leave_a_reply') }}

									<span style="color:red"> *</span>
								</h3>

								<form method="POST" action="" accept-charset="UTF-8" id="client-reply">

									<dynamic-select :label="lang('cc')" :multiple="true" name="cc" :elements="ccArr" :value="cc" 
										:onChange="onChange" :clearable="cc ? true : false" classname="col-sm-6 pad_0" :taggable="true"
										:placeholder="lang('type_and_select')">

									</dynamic-select>

									<tiny-editor :value="reply" type="text" :onChange="onChange" name="reply" :label="lang('reply')"
										classname="row col-sm-12" :required="true" :lang="lang_locale">
									
									</tiny-editor>

									<br>

									<span id="fileselector">

										<input ref="fileInput" multiple type="file" @change="onFileSelected" style="display:none">

										<div v-if="files.length>0">

											<div v-for="(file,index) in files">

												<li id="list">{{file.name}}

													<i id="close_attach" class="fa fa-times" @click="removeFile(index)"></i>

												</li>
											</div>
										</div>

										<button id="btun" type="button" class="btn btn-primary" @click="$refs.fileInput.click()"  :style="buttonStyle">
											{{ lang('Attach files') }}

										</button>

										<i> {{ lang(str) }}</i>
									</span>

									<div :class="{rem: lang_locale == 'ar'}" class="text-right">

										<button type="button" @click="verify" class="btn btn-custom btn-lg" :style="buttonStyle"
											:disabled="disableButton">

											<i class="fa fa-reply-all"></i>&nbsp;{{ lang(btnName) | to-up}}
										</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</article>
			</div>
		</div>
		<div v-if="loading+hasDataPopulated==0">
			<h3 style="text-align:center">{{lang('no-data-to-show')}}</h3>
		</div>
	</div>
</template>

<script>

	import axios from 'axios'

	import { mapGetters } from 'vuex'

	import { findObjectByKey, getSubStringValue } from 'helpers/extraLogics'

	import {errorHandler, successHandler} from 'helpers/responseHandler';

	import { validateReplySettings } from "helpers/validator/replyRules";

	export default {

		name : 'client-ticket-timeline',

		description : 'Client panel ticket timeline page',

		props : {

			layout : { type : Object, default : ()=>{}},

			auth : { type : Object, default : ()=>{}}
		},

		components:{

			'ticket-status': require('./MiniComponents/ChangeStatus'),

			'rating-component':require('./MiniComponents/ClientPanelTimelineRatings'),

			'ticket-thread':require('./MiniComponents/ThreadsList'),

			'ticket-details': require('./MiniComponents/TicketDetails'),

			'client-panel-loader' : require('components/Client/ClientPanelLayoutComponents/ReusableComponents/Loader.vue'),

			'alert' : require('components/MiniComponent/Alert'),

			'dynamic-select' : require('components/MiniComponent/FormField/DynamicSelect'),
		},

		beforeMount(){

			this.$store.dispatch('setRatingTypes');
		},


		data() {

			return {

				reply : '',

				cc : [],

				ccArr : [],

				labelStyle:{ display:'none' },

				tic_status : this.layout.user_set_ticket_status.status,

				buttonStyle : {

					borderColor : this.layout.portal.client_button_border_color,

					backgroundColor : this.layout.portal.client_button_color
				},

				lineStyle : { borderColor : this.layout.portal.client_header_color},

				str:'',

				loading : true,

				perpage:10,

				pagination:{currentPage: 1},

				records:0,

				TicketDetail:'',

				ticketData :'',

				comments_list:[],

				comments_list1:[],

				ticket_Id:'',

				selectedFile:[],

				files:[],

				url:'',

				btnName:'reply',

				disableButton: false,

				status_type:'',

				AreaRatings : '',

				showEditor:'',

				max_size:0,

				file_size:0,

				max_file:0,

				user_id : this.auth.user_data.id,

				tic_user_id:null,

				check_id:null,

				paramsObj:{},

				base : this.auth.system_url,

				lang_locale : this.layout.language,

				pageChange : false,

				actionPerformed : 'initialLoad',

				selected : [],

				hasDataPopulated: false
			}
		},

		created() {

			this.path=location.pathname.split('/');

			this.check_id=this.path[this.path.length-1];

			this.paramsObj['page']=this.pagination.currentPage;

			this.checkTicketId(this.paramsObj);
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

			onChange(value,name){

				this[name]=value;

			},

			checkTicketId(x) {

				this.$Progress.start();

				var params = x;

				axios.get('api/check-ticket/'+this.check_id,{params}).then(response => {

					if(x.page === 1){

						this.ticketData = response.data.data.ticket;
					}

					this.loading = false;

					this.hasDataPopulated = true;

					this.pageChange = false;

					this.$Progress.finish();

					this.fileUploadRules(response.data.data);

					this.TicketDetail 		= response.data.data.ticket;

					this.tic_user_id = this.TicketDetail.user.id;

					this.ticket_Id 		= this.TicketDetail.id;

					this.selected.push(this.ticket_Id)

					this.status_type 					= this.TicketDetail.status;

					this.AreaRatings = this.TicketDetail.ratings;

					this.comments_list 	= this.TicketDetail.threads;

					this.comments_list1 	= (this.TicketDetail.threads).reverse();

					this.records = this.TicketDetail.thread_count;

					this.cc = [];
					
					for(var i in this.TicketDetail.collaborators){
						if(this.TicketDetail.collaborators.hasOwnProperty(i) && this.auth.user_data.id != this.TicketDetail.collaborators[i].id){

							this.cc.push({ id : this.TicketDetail.collaborators[i].id, name : this.TicketDetail.collaborators[i].email})
						}
					}

					this.url = 'rating/'

					if(this.tic_user_id == this.user_id || this.layout.organization != undefined){

						this.showEditor = response.data.data.replybutton === 1 ? 1 : 0;
					} else {

						if(response.data.data.replybutton === 1){

							this.showEditor = response.data.data.org_member_reply;
						} else{

							this.showEditor = 0;
						}
					}
				}).catch(error=>{

					this.loading = false;
					this.hasDataPopulated = false;
					this.$Progress.fail();
					errorHandler(error, 'statusBox');
				})
			},

			fileUploadRules(data){

				this.max_size = data.uploadfilesize;

				this.file_size = data.uploadSingleFileSize;

				this.max_file= data.uploadFilecount;

				this.str = 'Maximum File Upload size : ' + this.formatBytes(this.max_size) +',Single File size : ' + this.formatBytes(this.file_size) + ' and Max Number of Files '+ this.max_file;
			},

			onFileSelected(event) {

				var element=this.$refs.fileInput;

				this.selectedFile = event.target.files;

				for (var i=0; i < this.selectedFile.length; i++){

					if(this.selectedFile[0].size < this.file_size) {

						if(this.files.length < this.max_file){

							this.files.push(this.selectedFile[i]);
						} else {

							this.$store.dispatch('setAlert',{type:'danger',message:'Max Number of Files :'+this.max_file, component_name : 'replyBox'});
						}
					} else {

						this.$store.dispatch('setAlert',{type:'danger',message:'Maximum File upload size is ' + this.formatBytes(this.file_size), component_name : 'replyBox'});
					}
				}
				element.value="";
			},

			formatBytes(bytes,decimals) {

				if(bytes == 0) return '0 Bytes';

				var k = 1024,

				dm = decimals || 2,

				sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],

				i = Math.floor(Math.log(bytes) / Math.log(k));

				return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
			},

			removeFile(i) {

				this.files.splice(i, 1);
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

			updateStatus(status){

				this.pageChange = true;

				this.$Progress.start();

				axios.post('api/ticket/change-status/' +this.TicketDetail.id+'/'+status.id).then(res=> {

					this.actionPerformed = 'statusChange';

					this.pagination.currentPage = 1 ;

					this.paramsObj['page'] = this.pagination.currentPage;

					this.checkTicketId(this.paramsObj);

					successHandler(res, 'statusBox');

					this.$Progress.finish();

				}).catch(error=>{

					this.pageChange = false;

					this.$Progress.fail();

					errorHandler(error, 'statusBox')
				})
			},

			verify(){

				if(this.files.length > 0){

					var fileArr=this.files.map(a => a.size);

					const totalSize = fileArr.reduce( (accumulator, currentValue) => accumulator + currentValue);

					if(totalSize < this.max_size) {

						this.postTicketComment();
					} else {

						this.loading = false;
						this.$store.dispatch('setAlert', {
							type: 'danger',
							message: this.trans('max_upload_size') + this.formatBytes(this.max_size), 
							component_name : 'statusBox'
						});
					}

				} else {

					this.postTicketComment();
				}
			},

			isValid() {

				const { errors, isValid } = validateReplySettings(this.$data);
				
				if (!isValid) {
				
					return false;
				}
				
				return true;
			},

			postTicketComment() {
				if(this.isValid()){

					this.$Progress.start();

					this.pageChange = true;

					this.disableButton = true;

					var fd = new FormData();

					if(this.cc.length > 0){

						for(var i in this.cc){

							fd.append('cc['+i+']', this.cc[i].name);
						}
					}

					fd.append('content', this.reply);

					for(var i in this.files){

						fd.append('attachment['+i+']', this.files[i]);
					}

					const config = { headers: { 'Content-Type': 'multipart/form-data' } };

					axios.post('api/thread/reply/'+this.ticket_Id,fd,config).then(res=> {

						this.reply = '';
						
						this.disableButton = false;

						this.pagination.currentPage = 1;

						this.paramsObj['page']=this.pagination.currentPage;

						this.checkTicketId(this.paramsObj)

						var elmnt = document.getElementById('reply_threads');

			  		elmnt.scrollIntoView({ behavior : "smooth"});

			  		this.$store.dispatch('setAlert', {
		 						type: 'success',
		 						message: 'Successfully replied', component_name : 'statusBox'
		 					});

						this.files=[];

						this.$Progress.finish();

						window.eventHub.$emit('refreshThreads');

					}).catch(error=>{

					errorHandler(error,'statusBox');

						this.pageChange = false;

						this.disableButton = false;

						this.$Progress.fail();
					});
				}
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

	.pad_0{
		padding: 0px !important;
	}

	.reply_title{
		margin-bottom: 0.5em !important;
	}
</style>

<style>
	.pad_0 .dynamic-select{
		background: white !important;
	}
</style>
