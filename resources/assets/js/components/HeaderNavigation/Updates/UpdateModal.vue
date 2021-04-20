<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :showCloseBtn="closeBtn">

		<div slot="title">

			<h4 class="modal-title">{{trans('update_application')}}</h4>
		</div>

		<div slot="alert">
			
			<alert componentName="UpdatesModal"></alert>
		</div>

		<div v-if="!loading && showContent" slot="fields">

			<b>{{trans('update_perform_msg')}}</b>
			
			<span>
				
				<b>Note - </b> Once started, the update cannot be stopped. The backup and update may take several minutes to complete. Make sure you have atleast {{space}} of disk space available or it may lead to Backup/Updation failure.
			</span><br><br> 

			<div>

				<label class="label_align" name="share" >

					<input class="checkbox_align" type="checkbox" name="backup" id="backup" v-model="backup">
						&nbsp;<span v-tooltip="path">{{trans('system_backup_recommend_msg')}}</span>
				</label>
			</div>
		</div>

		<div v-if="loading" slot="fields" >
			
			<loader :animation-duration="4000" color="#1d78ff" :size="60"/>

			<div v-if="!closeBtn" class="mt-3 mb-2 text-center close_text">{{modalMessage}}</div>
		</div>

		<div slot="controls">
			
			<button type="button" @click = "onSubmit" class="btn btn-primary" :disabled="loading">
			
				<i class="fas fa-arrow-right" aria-hidden="true"></i> {{trans('continue')}}
			</button>
		</div>
	</modal>
</template>

<script>

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	export default {

		name : 'system-update-modal',

		description : 'System Update Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			onClose:{type: Function},

			space : { type : String, default : ''},

			path : { type : String, default : ''}
		},

		data () {

			return {

				loading : false,

				backup : true,

				closeBtn : true,

				showContent : true,
			}
		},

		methods:{

		onSubmit(){

			this.loading = true;

			let apiUrl = this.backup ? '/api/backup/take-system-backup' : '/auto-update-application';

			let data = this.backup ? {} : '';

			if(this.backup) {

				data['path'] = this.path;

				data['autoUpdate'] = true;
			
			} else {

				this.closeBtn = false;

				this.modalMessage = 'Maintenance Mode Enabled... File system Updating.. Do not close the window.'
			}

			axios.post(apiUrl,data).then(res=>{

				if(this.backup) {

					this.showContent = false;

					successHandler(res,'UpdatesModal');

					setTimeout(()=>{

						this.redirect('/check-updates');
					},3000)

				} else {

					this.updateDb()
				}
			}).catch(err => {

				this.loading = false;

				this.modalMessage = '';

				this.closeBtn = true;

				errorHandler(err,'UpdatesModal');
			})
		},

		updateDb() {

			this.modalMessage = 'Maintenance Mode Enabled... Database Updating.. Do not close the window.'

			axios.post('/auto-update-database').then(res=>{

				this.closeBtn = true;

				this.loading = false;

				this.modalMessage = '';

				this.showContent = false;

				successHandler(res,'UpdatesModal');

				setTimeout(()=>{

					this.redirect('/check-updates');
				},3000)

			}).catch(err=>{

				this.closeBtn = true;

				this.loading = false;

				this.modalMessage = '';

				errorHandler(err,'UpdatesModal');
			})
		}
	},

	components:{
	
		'modal':require('components/Common/Modal.vue'),
	
		'loader': require('components/Extra/Loader'),

		'alert' : require('components/MiniComponent/Alert'),
	}
};
</script>

<style scoped>
	.label_align {
		display: block; padding-left: 15px; text-indent: -15px; font-weight: 400 !important; padding-top: 6px;
	}
	.checkbox_align {
		width: 13px; height: 13px; padding: 0; margin:0; vertical-align: bottom; position: relative; top: -3px; overflow: hidden;
	}

	.close_text { font-weight: 600 !important; }
</style>