<template>
	
	<div class="tab-pane active" id="General">
		
		<div class="row reply" v-if="reply_loader === true">
			
			<loader :size="60" :duration="4000" :color="'#1d78ff'"></loader>
		</div>
		
		<!-- Notification popup-->
		<alert-notification  :successShow="successShow" :errorShow="errorShow" :successMsg="success" :errorMsg="''" 
			:extraData="notification">
				
		</alert-notification>
		
		<!-- loader -->
		<div  v-if="loader1">
			
			<loader :size="60" :duration="4000" :color="'#1d78ff'"></loader>
		</div>

		<div v-else>

			<form id="reply-form" v-if="ticket.title!=undefined">
				
				<div id="t1">

					<div id="reply-response"></div>
					
					<div v-if="showtimeTrack" class="form-group">
						
						<div class="row">

							<div class="col-md-2">

								<label for="To">{{ lang('worktime') }}:</label>
							</div>

							<div class="col-md-10" style="margin-left:-14px">
								
								<time-field :label="lang('worktime')" :labelStyle="labelStyle" :formStyle="formStyle" :value="worktime"  type="number" 	
									name="worktime" :onChange="onChange" classname="col-xs-12" :required="false">
										
								</time-field>
							</div>
						</div>
					</div>

					<div class="form-group">

						<div class="row">

							<input type="hidden" name="ticket_id" id="ticketId" value="3">
							
							<div class="form-group">

								<div class="col-md-2">

									<label for="To">{{ lang('to') }}:</label>
								</div>

								<div class="col-md-10">

									<div class="form-group" id="reply-to-box-container">

										<!--
											Currently in vue-select when prop update happens when vue-select muliple is false, it doesn't update the value rendered on the UI. For workaround, we are rerendering the dynamic select component so that value update gets visible on the screen
											NOTE: it might get fixed in upcoming releases of vue-select, so should be removed once that happens
											Issue link : https://github.com/sagalbot/vue-select/issues/802
										-->
										<dynamic-select v-if="showTo" id="reply-to-box" :label="lang('users')" name="to" :value="to" :onChange="onChange" 
											:disabled="true" :multiple="false" classname="col-md-6" :labelStyle="{display: 'none'}">
										</dynamic-select>
									</div>
								</div>
								
								<div class="col-md-2">

									<label for="To">{{ lang('cc') }}:</label>
								</div>

								<div class="col-md-10">

									<div class="form-group" id="reply-to-box-container">

										<dynamic-select id="reply-to-box" :label="lang('users')" name="cc" :value="cc" :onChange="onChange" 
											:taggable="true" :close_on_select="true" apiEndpoint="/api/dependency/users?meta=true"  :multiple="true" classname="col-md-6" :labelStyle="{display: 'none'}">
										</dynamic-select>
									</div>
								</div>

								<div class="col-md-2">

									<label>{{ lang('response') }}</label>
								</div>

								<div class="col-md-10">

									<select class="form-control reply-width" id="canned-res" multiple="true">
									
									</select>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">

						<div class="row">
							<!-- reply content -->
							<div class="form-group " id="reply_content_class">
								
								<div class="col-md-2">
									
									<label for="Reply Content">{{ lang('reply_content') }}:</label><span class="text-red"> *</span>
								</div>

								<div class="col-md-10">

									<media-gallery v-on:getAttach="getAttachment" v-on:inlineAttach="getInline" :apiUrl="filesApi" 
										:target_url="chunkApi" :page="page_name" :onInlinePdf="getPDF" :inlineFiles="inline" :attachmentFiles="attchments">
									</media-gallery>
									
									<!-- ckeditor -->
									
									<tiny-editor :value="reply" type="text" :onChange="onChange" name="reply" :label="lang('reply')" classname="width55"
										:required="true" :labelStyle="labelStyle" :lang="'en'">
									
									</tiny-editor>

									<div id="file_details">
                    
                    <div  id='hidden-attach' contenteditable='false' v-for="(attachment,index) in attchments">

                    	{{attachment.filename}}({{attachment.size}} bytes)

                    	<i class='fa fa-times close-icon' aria-hidden='true' @click='removeAttachment(index)'></i>
                    </div>
                  </div>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">

						<div class="row">

							<div class="form-group ">

								<div class="col-md-2"></div>

								<div class="col-md-10">

									<div>

										<button id="replybtn" @click="checkTimeTrack()" type="button" class="btn btn-primary" :disabled="isDisabled">
											
											<i class="fa fa-refresh" style="color:white;"> </i> {{ lang('update') }}
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</template>

<script>

	import { mapGetters } from 'vuex';

	import { errorHandler } from 'helpers/responseHandler';

	import { validateReplySettings } from "helpers/validator/replyRules";

	export default{

		props:['treadId','showtimeTrack'],
			
		data(){
				
			return {

				reply : '',

				attchments:[],
					
				cannedAttachmentIndexes:[],
					
				inline:[],
					
				cannedInline:[],
					
				ticket:{},
					
				loader1:true,
					
				isDisabled : false,
					
				notification:"",
					
				content:true,
					
				cannedArray:[],
					
				errorShow:'none',
					
				successShow:'none',
					
				success:'',
					
				worktime:0,
					
				labelStyle : { display:'none' },

				formStyle : { width:'24%' },

				to : [],
					
				cc : [],
					
				reply_loader : false,

				showTo: true,

				selectedFile:[],

				files:[],

				chunkApi : '/chunk/upload',

				filesApi : '/media/files',

				page_name : 'reply'
			}
		},
		
		computed:{
			
			...mapGetters(['getUserData'])
		},

		created(){
			
			window.eventHub.$on('onReply',this.onReplyEvent);

			window.eventHub.$on('sendtoRplyEvent', this.getTicketInfo);

			window.eventHub.$on('disableReplyBtn', this.updateReplyBtn);
			
			window.eventHub.$on('thread-reply', (user)=> {
			
				this.to = [];
				
				this.to[0] = user;
			});
		},
		
		components: {

			'media-gallery':require('components/Common/ChunkUpload/ChunkUpload.vue'),
			
			'add-cc':require('../../../Extra/AddCC.vue'),
			
			'collabrator-list':require('../../../Extra/CollabratorList.vue'),
			
			'loader':require('../../../Extra/Loader.vue'),
			
			'time-field': require('components/MiniComponent/FormField/TimeField'),
			
			"custom-loader": require("components/MiniComponent/Loader"),
			
			"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),
		},

		watch : {
			
			"ticket.title"(){
				
				// rerending To box, so that value gets update
				this.showTo = false;
				
				setTimeout(()=>{
					
					this.showTo = true;
				}, 100)
			}
		},

		methods:{

			updateReplyBtn(value){

				this.isDisabled = value;
			},

			onReplyEvent(value){

				this.reply = value;
			},

			getPDF(value){

				this.reply = this.reply + '<br>' + value;
			},

			isValid() {

				const { errors, isValid } = validateReplySettings(this.$data);
						
				if (!isValid) {
						
					return false;
				}
						
				return true;
			},
			
			onChange(value, name){
				
				// if name is cc an
				this[name]= value;
			},
			
			//get Attchment
			getAttachment(x, cannedAttachment = false){
				
				var index = this.isAlreadyAttached(x);
				
				if(index < 0) {
					
					this.attchments.push(x);
					
					if(cannedAttachment){
						
						this.cannedAttachmentIndexes.push(this.attchments.length-1);
					}
				} else if(index >= 0 && !cannedAttachment) {
						
					this.cannedAttachmentIndexes.splice(index, 1);
				}
			},

			//remove attachment
			removeAttachment(x, attachment=true){
				
				this.attchments.splice(x,1);
				
				if (attachment) {
					
					let index = this.cannedAttachmentIndexes.indexOf(x);
					
					if(index>=0) {
					
						this.cannedAttachmentIndexes.splice(index, 1);
							
							for(let i = index; i < this.cannedAttachmentIndexes.length; i++) {
								
								this.cannedAttachmentIndexes[i] -= 1;
							}
					} else if(x < this.cannedAttachmentIndexes[0]) {
						
						for(let i = 0; i < this.cannedAttachmentIndexes.length; i++) {
							
							this.cannedAttachmentIndexes[i] -= 1;
						}
					}
				}
			},

			//get inline image
			getInline(x){
				
				this.inline.push(x);
			},

			isAlreadyAttached(x) {
				
				var result = -1;
				
				$.each(this.attchments, function(key, item){
					
					if(item.pathname == x.pathname && item.size == x.size) {
						
						result = key;
						
						return false;
					}
				});
				
				return result;
			},
					
			checkTimeTrack() {
						
				if(this.isValid()){

					if(this.showtimeTrack && this.worktime !== 0){
						
						this.reply_loader = true
						
						axios.post('ticket/'+this.treadId+'/time-track',{
						
							work_time:this.worktime,
						
							entrypoint:'reply'
						
						}).then(res=>{
							
							window.eventHub.$emit('refresh');
									
							window.eventHub.$emit('removeVal');
									
							this.worktime= 0;
									
							this.updateReply();
						})
					} else {
								
						this.updateReply();
					}
				}
			},
			
			//reply update
			updateReply(){
							
				var obj={};
							
				this.reply_loader = true;
							
				this.isDisabled = true;
								 
				$("html, body").animate({ scrollTop: $('#General').offset().top-70 }, 500);
								 
					obj['content'] = this.reply;
							
					obj['attachment']=this.attchments;
							
					obj['ticket_id']=this.treadId;

					obj['to'] = this.to.map(user => user.id);

					obj['cc'] = this.cc.map(user => {
							
					// if user has a property called email, it will be passed, else id
								
					if(user.hasOwnProperty('email')){
								
						return user.email;
					}
					
					return user.name;
				});

				axios.post('api/thread/reply'+this.treadId,obj).then((resp)=>{
								
					window.eventHub.$emit('refreshTicket',{});
								
					window.eventHub.$emit('updateThread',this.treadId);
								
					this.successShow='block';
								
					this.success="reply_added_successfully"
								
					this.reply = '';
								
					this.getTicketInfo(this.ticket);
								
					this.reply_loader=false;
								
					this.isDisabled = false;
								
					this.content=true;
								
					this.attchments=[];
								
					this.inline=[];
								
					setTimeout(()=>{
									
						window.eventHub.$emit('setAssignedUserId',this.treadId)
									
						window.eventHub.$emit('refreshTicket',{});
									
						window.eventHub.$emit('getTicketInfo',this.treadId);
									
						this.successShow='none';
					},2000);

				}).catch(err=>{
								
					errorHandler(err);
								
					this.errorShow='block';
								
					this.reply_loader=false;
								
					this.isDisabled = false;
								
					this.getTicketInfo(this.ticket);
								
					this.content=true;
								
					var res = "";
								
					$.each(err.response.data.content, function (idx, topic) {
						
						res += "<li>" + topic + "</li>";
					});
								
					this.notification="<strong>Whoops!</strong> There were some problems with your input.<br><br><ul>" +res+ "</ul>";
								
					setTimeout(()=>{
							
						this.errorShow='none';
					},2000)
				})
			},
			
			//get Ticket Info
			getTicketInfo(payload){
				
				this.clearEditor();
				
				this.loader1=false;
				
				this.ticket=payload;

				// adding the user who created ticket as to in the ticket
				this.to[0] = payload.user;
				
				this.cc = payload.collaborators;
				
				var globalScopedThis = this;
				
				axios.get('api/agent/canned').then((resp)=>{
					
					this.cannedArray=resp.data.data;
					
					var options="";

					for(var i in resp.data.data){

						options+="<option value='"+resp.data.data[i].id+"'>"+resp.data.data[i].title+"</option>";
					}

					$('#canned-res').html(options);
				}).catch((resp)=>{
					
					console.log(resp);
				})

				setTimeout(function(){

					$('#canned-res').select2({

						placeholder: 'Select a canned response', 

						maximumSelectionLength: 1,

						language: {

							noResults: function(){

								return "No canned response";
							},

							searching: function() {

								return "Searching";
							}
						}
					}).on("change", function(event) {
						
						var values = [];     // copy all option values from selected
						
						$(event.currentTarget).find("option:selected").each(function(i, selected){
							
							values[i] = $(selected).val();
						});
						
						if(values.length!=0){
						
							axios.get('get-canned-response-message/'+values[0]).then((resp)=>{
								
								globalScopedThis.reply = resp.data.data.message;
								
								$.each(resp.data.data.attachments, function($key, $item){
									
									globalScopedThis.getAttachment($item, true);
								});
								
								globalScopedThis.cannedInline = resp.data.data.inline;
							
							}).catch((resp)=>{
								
								console.log(resp);
							})
						} else{
							
							var i = 0;
							
							$.each(globalScopedThis.cannedAttachmentIndexes, function($key, $item){
								
								globalScopedThis.removeAttachment($item-i, false);
							
								i++;
							});

							globalScopedThis.cannedAttachmentIndexes = [];
							
							globalScopedThis.cannedInline = [];
							
							this.reply = '';
							
						}
					});
				},100);
			},

			clearEditor(){

				try {

					this.inline =[];
					
					this.attchments=[];
					
					this.cannedAttachmentIndexes=[];
					
					let editor = this.reply;
					
					if(typeof(editor) != 'undefined'){
						
						this.reply = '';
					}
				} catch(error) {
					
					console.log(error);
				}
			}
		}
	};
</script>

<style scoped>
	
	.reply-width{
		width: 62%;
	}
	
	#hidden-attach{
		background-color: #f5f5f5;border: 1px solid #dcdcdc;font-weight: bold;margin-top:9px;overflow-y: hidden;padding: 4px 4px 4px 8px;
		max-width: 62%;
	}
	
	.close-icon{
		float:right;cursor: pointer;
	}
	
	.col-md-offset-5{
		margin-top: 160px;
	}
	
	.reply {
		width: 100px; height: 100px; text-align: center; position: fixed;
		top: 25%; left: 25%; bottom: 0; right: 0; z-index:100000;  margin: auto;
	}

	#reply-to-box-container {
		display: grid;
	}

	#reply-to-box-container .form-group{
		padding-left: 0px!important;
		padding-right: 0px!important;
		margin-bottom: auto;
		width: 62%;
	}

	.width55{
		width: 62%;
	}

	#btun{
		margin-bottom: 5px;
	}
</style>
