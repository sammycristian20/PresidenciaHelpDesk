<template>
	
	<div class="position_rel">
		
		<template>
			
			<div class="row">

				<dynamic-select :label="lang('to')" name="to" :value="to" :onChange="onChange" :disabled="true" 
					:multiple="false" classname="col-sm-6" :clearable="false">
					
				</dynamic-select>

				<dynamic-select :label="lang('cc')" name="cc" :value="cc" :onChange="onChange" :taggable="true" 
					:close_on_select="true" apiEndpoint="/api/dependency/users?meta=true"  :multiple="true" 
					classname="col-sm-6" >
					
				</dynamic-select>
			</div>

			<div class="row">

				<dynamic-select :label="lang('response')" name="response" :value="response" :onChange="onChange"
					:multiple="false" classname="col-sm-6" apiEndpoint="/api/dependency/canned-responses"
					>
					
				</dynamic-select>

				<time-field v-if="actions.show_thread_worktime && !fieldLoader" :label="lang('worktime')" :formStyle="formStyle"
					:value="worktime" type="number" name="worktime" :onChange="onChange" classname="col-sm-6" 
					:required="false">
											
				</time-field>

				<div v-if="actions.show_thread_worktime && fieldLoader" class="col-sm-6 load_margin">

					<field-loader :size="30" :duration="4000"></field-loader>
				</div>
			</div>

			<div class="row">

				<add-media :attachments="attachments" :inlineFile="inline" :description="reply" 
					classname="col-sm-12" name="reply" :onAttach="getAttachments" :onInline="getInlineImages" :onInput="onChange"
					page_name="reply">
							
				</add-media>
			</div>

			<div class="row">

				<div class="col-sm-12">

					<button type="button" class="btn btn-primary mt_10" @click="onSubmit" :disabled="isDisabled">

						<i class="fas fa-sync-alt"> </i> {{lang('update')}}
					</button>
				</div>
			</div>
		</template>

		<div v-if="loading">

			<loader :size="60" :duration="4000"></loader>
		</div>
	</div>
</template>

<script>
	
	import axios from 'axios'

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import { validateReplySettings } from "helpers/validator/replyRules";

	export default {

		name : 'inbox-reply',

		description : 'Inbox Reply Component',

		props : {

			actions : { type : Object, default : ()=>{} },

			id : { type : String | Number, default : '' },

			user : { type : Object, default : () => {} },

			ticket : { type : Object, default : () => {} },

			ccArray : { type : Array | String, default : () => [] },

			updateThreads : { type : Function }
		},

		data() {

			return {

				worktime:0,

				reply : '',

				to : this.user,

				cc : this.ccArray,

				response : '',

				attachments : [],

				inline : [],

				isDisabled : false,

				loading : false,

				formStyle : { width:'40%' },

				fieldLoader : false
			}
		},

		watch : {

			user(newValue,oldValue){

				this.to = newValue;
			}
		},

		created() {

			window.eventHub.$on('threadReply', this.updateReply)
		},

		methods : {

			updateReply(thread) {

				this.reply = thread.content;
			},

			onChange(value, name){
				
				this[name]= value;

				if(name == 'response') {

					if(value){

						axios.get('/get-canned-response-message/'+value.id).then(res=>{

							this.reply = res.data.data.message;

							this.attachments = res.data.data.attachments;

						}).catch(err=>{

							this.reply = '';

							this.attachments = [];
						})
					} else {

						this.reply = '';

						this.attachments = [];
					}
				}
			},

			isValid() {
		
				const { errors, isValid } = validateReplySettings(this.$data);
		
				if (!isValid) {
		
					return false;
				}
					return true;
			},

			getAttachments(files){

				this.attachments = files;
			},

			getInlineImages(files){

				this.inline = files;
			},

			onSubmit() {

				if(this.isValid())  {

					this.loading = true;

					this.isDisabled= true;

					if(this.worktime != 0) {

						this.saveTimeTrack();
					}

					const obj = {};

					obj['content'] = this.reply;
								
					obj['attachment']=this.attachments;
								
					obj['ticket_id']=this.id;

					obj['to'] = [this.to.id];

					if(this.cc.length > 0){

						obj['cc'] = this.cc.map(user => {

						if(user.hasOwnProperty('email') && user.email){
										
							return user.email;
						}	
							return user.name;
						});
					} else {
						obj['cc'] = []
					}

					axios.post("api/thread/reply/"+this.id,obj).then(res=>{

						this.initialState();

						successHandler(res,'inboxActions');

						window.eventHub.$emit('refreshTableAndData',true);

						this.updateThreads();

					}).catch(err=>{

						this.loading = false;

						this.isDisabled= false;

						errorHandler(err,'inboxActions')
					})
				}
			},

			initialState(){

				this.loading = false;

				this.isDisabled= false;

				this.reply = '';

				this.to = this.ticket.user;

				this.attachments = []

				this.response = ''

				 this.worktime = 0;

				 this.fieldLoader = true;
				
				 setTimeout(()=>{

				 	this.fieldLoader = false;
				 },1)
			},

			saveTimeTrack() {
				
				const data = {};

				data['work_time'] = this.worktime;

				data['entrypoint'] = 'reply';
				
				axios.post('ticket/'+this.id+'/time-track',data).then(res=>{

					this.worktime = 0;

					window.eventHub.$emit('refreshTimeTrack')
					 
				}).catch(err=>{

					this.worktime = 0;
				})
			}
		},

		components : {

			"add-media": require("components/MiniComponent/FormField/AddMedia"),
			
			'time-field': require('components/MiniComponent/FormField/TimeField'),
			
			"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),

			'loader':require('components/MiniComponent/Loader'),

			'field-loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>

<style scoped>
	
	.mt_10 { margin-top: 10px; }

	.load_margin { margin-top: 30px;margin-bottom: 30px; }

	.position_rel { position: relative; }
</style>