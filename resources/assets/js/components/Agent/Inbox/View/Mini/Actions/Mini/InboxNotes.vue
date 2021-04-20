<template>
	
	<div class="position_rel">
		
		<div  class="form-group">
						
			<div class="row" :key="counter">
			
				<template>
					
					<time-field v-if="actions.show_thread_worktime && !fieldLoader" :label="lang('worktime')" :formStyle="formStyle"
						:value="worktime" type="number" name="worktime" :onChange="onChange" classname="col-sm-12" 
						:required="false">
											
					</time-field>

					<div v-if="actions.show_thread_worktime && fieldLoader" class="col-sm-6 load_margin">

						<loader :size="30" :duration="4000"></loader>
					</div>

					<add-media :attachments="attachments" :inlineFile="inline" :description="internal_note" classname="col-sm-12" name="internal_note" :onAttach="setAttachments" :onInline="setInlineImages" :onInput="onChange" page_name="internal_note">
					</add-media>

					<div class="col-sm-12" style="padding-top:1rem">

						<button type="button" class="btn btn-primary mt_10" @click="onSubmit" :disabled="!internal_note || loading">

							<i class="fas fa-sync-alt"> </i> {{lang('update')}}
						</button>
					</div>
				</template>

				<div v-if="loading">

           		<custom-loader :size="60" :duration="4000"></custom-loader>
        		</div>
			</div>
		</div>
	</div>
</template>

<script>
	
	import axios from 'axios'

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	export default {

		name : 'inbox-notes',

		description : 'Inbox Internal Notes Component',

		props : {

			actions : { type : Object, default : ()=>{} },

			id : { type : String | Number, default : '' },

			updateThreads : { type : Function }
		},

		data() {

			return {

				worktime:0,

				internal_note : '',

				loading : false,

				formStyle : { width:'24%' },

				fieldLoader : false,

				attachments : [],

				inline : [],

				counter : 0
			}
		},

		methods : {

			setAttachments(files){
				this.attachments = files;
			},

			setInlineImages(files){
				this.inline = files;
			},

			onChange(value, name){
				this[name]= value;
			},

			onSubmit() {

				/** Perform sanity check for required fields */
				if(!this.internal_note){
					return;
				}

				this.loading = true;

				if(this.worktime != 0) {
					this.saveTimeTrack();
				}

				const params = {
					content: this.internal_note,
					ticket_id: this.id,
					is_internal_note: true,
					attachment: this.attachments
				}

				axios.post('api/thread/reply/'+this.id, params).then(res => {
					this.counter++;
					this.loading = false;
					this.internal_note = '';
					this.attachments = []
					this.worktime = 0;
					this.fieldLoader = true;
					setTimeout(()=>{
						this.fieldLoader = false;
					}, 1)
					successHandler(res,'inboxActions');

					this.updateThreads();

				}).catch(err=>{
					this.loading = false;
					errorHandler(err,'inboxActions')
				})
			},

			saveTimeTrack() {
				
				const data = {};

				data['work_time'] = this.worktime;

				data['entrypoint'] = 'note';
				
				axios.post('ticket/'+this.id+'/time-track',data).then(res=>{
					 
					 this.worktime = 0;

					 window.eventHub.$emit('refreshTimeTrack')
					 
				}).catch(err=>{

					this.worktime = 0;
				})
			}
		},

		components : {
			
			'time-field': require('components/MiniComponent/FormField/TimeField'),
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
			
			"add-media": require("components/MiniComponent/FormField/AddMedia"),

			'custom-loader': require('components/MiniComponent/Loader'),
		}
	};
</script>

<style scoped>
	
	.load_margin { margin-top: 50px; margin-bottom: 50px; }

	.position_rel { position: relative; }
</style>