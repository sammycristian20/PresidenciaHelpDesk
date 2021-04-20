<template>
	
	<div>
	
		<div class="row" v-if="!hasDataPopulated || loading">
	
			<custom-loader :duration="loadingSpeed"></custom-loader>
		</div>
	
		<alert componentName="page-create"/>

		<div class="card card-light " v-if="hasDataPopulated === true">
	
			<div class="card-header">
				
				<h3 class="card-title">{{lang(title)}}</h3>

				<div class="card-tools">
		
					<a v-if="buttonVisible" id="view_page" class="btn btn-tool" type="button"
						:href="status ? basePath()+'/pages/'+link :'javascript:;'"  :target="status ? '_blank' : ''" :disabled="!status" 
						v-tooltip="lang('view')">
								
						<i class="fas fa-eye"> </i>
					</a>

					<button v-if="buttonVisible" id="delete_page" class="btn btn-tool" type="button" 
						@click="modalMethod('delete_page')" v-tooltip="lang('delete')">
								
						<i class="fas fa-trash"> </i>
					</button>
				</div>
			</div>
			
			<div class="card-body">

				<div class="row">
					
					<text-field label="Name" :value="name" type="text" name="name" :onChange="onChange" classname="col-sm-6" 
						:required="true"></text-field>
					
					<perma-link :label="lang('perma_link')" :value="slug" name="slug" :onChange="onChange" classname="col-sm-6" 
						:required="true" :link="link"></perma-link>
				</div>

				<div class="row">
					
					<add-media :description="description" :classname="classname" :show_error="show_err"  page_name="page"
						name="description" :onInput="onChange" chunkApi="/chunk/upload/public" filesApi="/media/files/public">
						
					</add-media>
				</div>

				<div class="row">
				
					<radio-button :options="visibility_options" :label="lang('visibility')" name="visibility" :value="visibility"
						:onChange="onChange" classname="form-group col-sm-6" ></radio-button>
			
					<radio-button :options="radioOptions" :label="lang('status')" name="status" :value="status"
						:onChange="onChange" classname="form-group col-sm-6" ></radio-button>
				</div>

				<div class="card card-light ">
					
					<div class="card-header">
					
						<h3 class="card-title">{{lang('seo')}}</h3>
					</div>
					
					<div class="card-body">
					
						<div class="row">
					
							<text-field :label="lang('seo_title')" :value="seo_title" type="text" name="seo_title" :onChange="onChange" classname="col-sm-12" :required="false"></text-field>

							<div class="progress sm col-md-12 p-0" id="prog"><div :class="seoClass" :style="seoStyle"></div></div>
						</div>
						
						<div class="row">
					
							<text-field :label="lang('description')" :value="meta_description" type="textarea" name="meta_description" :onChange="onChange" classname="col-sm-12" :required="false"></text-field>

							<div class="progress sm col-md-12 p-0" id="prog"><div :class="seoDesClass" :style="seoDesStyle"></div></div>
						</div>
					</div>
				</div>
			</div>

			<div class="card-footer">
				
				<button type="button" v-on:click="onSubmit" :disabled="loading" class="btn btn-primary">
					
					<span :class="iconClass"></span>&nbsp;{{lang(btnName)}}
				</button>
			</div>
		
			<transition  name="modal">
				<page-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :apiUrl="'/page/delete/'+page_id"
				alert="page-create" redirectUrl="/page">

				</page-modal>
			</transition>
		</div>
	</div>
</template>

<script>

	import axios from "axios";
	
	import { errorHandler, successHandler } from "helpers/responseHandler";
	
	import { validatePageSettings } from "helpers/validator/pageCreateRules";
	
	import {getIdFromUrl} from 'helpers/extraLogics';
	
	import { mapGetters } from 'vuex'
	
	export default {
	
		name: "kb-page-component",

		description: "page create and edit page",
		
		data: () => ({
			
			showModal: false,//modal status
			
			name: "",//typed page name
			
			link: "", //page link
			
			loading: false,//loader status
			
			loadingSpeed: 4000,
			
			hasDataPopulated: true,
			
			slug:'',//page slug
			
			radioOptions:[{name:'published',value:1},{name:'draft',value:0}],//page status
			
			status : 1,//page status
			
			visibility_options:[{name:'public',value:1},{name:'private',value:0}],

			visibility: 1,
			
			buttonVisible : false,//buttons config for create and edit page

			seo_title :'',
			
			meta_description :'',
			
			description : '',
			
			seoDesStyle : { width : "0%"},//progress bar width
			
			seoDesClass : '',//progress bar class
			
			seoStyle : { width : "0%"},//progress bar width
			
			seoClass : '',//progress bar class
			
			classname : 'form-group',//ckeditor class
			
			progStart : 'progress-bar progress-bar-primary progress-bar-striped',//progress bar class
			
			progWarn : 'progress-bar progress-bar-warning progress-bar-striped',//progress bar class
			
			progSuc : 'progress-bar progress-bar-success progress-bar-striped',//progress bar class
			
			progDan : 'progress-bar progress-bar-danger progress-bar-striped',//progress bar class
			
			show_err : false,//ckeditor error class

			title :'create_new_page', 

			iconClass:'fas fa-save',

			btnName:'save',//page title and btn classes for create and edit page
			
			page_id :'',
			
			modal_title :'',
		}),

		
		beforeMount() {
			
			const path = window.location.pathname;
			
			this.getValues(path);
		},
		
		methods: {

			getValues(path){
			
				const pageId = getIdFromUrl(path);

			  	if (path.indexOf("edit") >= 0) {
				
					this.title = 'edit_page';
					
					this.iconClass = 'fas fa-sync'
					
					this.btnName = 'update'
					
					this.buttonVisible = true
				
					this.hasDataPopulated = false;
					
					this.getInitialValues(pageId);
			  	} else {

					this.loading = false;

					this.hasDataPopulated = true
				}
			},
			
			getInitialValues(pageId) {
				
				this.loading = true;			
			
				axios.get('/api/edit/page/'+pageId).then(res => {

					this.page_id  = pageId
					
					this.hasDataPopulated = true;
					
					this.loading = false;

					this.updateStatesWithData(res.data.message.page);
					
				}).catch(err => {
					
					errorHandler(err)
					
					this.hasDataPopulated = true;
					
					this.loading = false;
				});
			},
			
			updateStatesWithData(page) {

				this.link = page.slug
				
				this.seoTitleProgress(page.seo_title)
				
				this.metaProgress(page.meta_description);
				
				const self = this;
				
				const stateData = this.$data;
				
				Object.keys(page).map(key => {
				
					if (stateData.hasOwnProperty(key)) {
				
						self[key] = page[key];
					}
				});

				this.seo_title = page.seo_title === '' ? page.name : page.seo_title
				
				this.meta_description = page.meta_description === '' ? page.description : page.meta_description
			},

			isValid() {
				
				const { errors, isValid } = validatePageSettings(this.$data);
				
				if (!isValid) {
				
					return false;
				}
				
				return true;
			},
			
			modalMethod(name){
			
				this.showModal = true
			
				this.$store.dispatch('unsetValidationError');
			},	


			onSubmit() {
			
				this.classname = this.description === '' ? 'form-group has-error'  : 'form-group'
			
				this.show_err = this.description === '' ? true  : false
			
				if (this.isValid() && this.description != '') {
			
					this.loadingSpeed = 8000;
			
					this.loading = true;
			
					var fd = new FormData();
			
					if(this.page_id != ''){
			
						fd.append('pageid', this.page_id);
					}
			
					fd.append('name', this.name);
			
					fd.append('slug', this.link);
			
					fd.append('description', this.description);
			
					fd.append('visibility', this.visibility);
			
					fd.append('status', this.status);
			
					fd.append('seo_title', this.seo_title);
			
					fd.append('meta_description', this.meta_description);
			
					const config = { headers: { 'Content-Type': 'multipart/form-data' } };
			
					axios.post('/api/page/create', fd,config).then(res => {
			
						this.loading = false;
			
						successHandler(res,'page-create');
			
						if(this.page_id === ''){
			
							this.redirect('/page')
						}else {

							this.getInitialValues(this.page_id);
						}
					}).catch(err => {
			
						this.loading = false;
			
						errorHandler(err,'page-create');
					});
				}
			},
			
			onChange(value, name) {
			
				this[name] = value;
			
				if(name === 'name' || name==="slug"){
			
					var regex = value.replace(/[^\w\s]/gi, '').toLowerCase();
			
					var regex1 = regex.replace(/_+/g,'')
			
					this.link= regex1.replace(/\s+/g,"-");
			
					this.slug = value
				}
			
				if(name === 'seo_title'){ this.seoTitleProgress(value) }
			
				if(name === 'meta_description'){ this.metaProgress(value) }
			},
			
			seoTitleProgress(value){
			
				this.seoStyle.width = value.length/0.6+'%'
			
				var len = value.length;
			
				this.seoClass = len > 60 ? this.progDan : len > 25 && len <= 50 ? this.progSuc : len > 50 && len <= 60 ? this.progWarn :  this.progStart;
			},
			
			metaProgress(value){
			
				this.seoDesStyle.width = value.length/1.6+'%'
			
				var len = value.length;
			
				this.seoDesClass = len > 160 ? this.progDan : len > 80 && len <= 120 ? this.progSuc : len > 120 && len <= 160 ? this.progWarn :  this.progStart;
			},
			
			onClose(){
			
				this.$store.dispatch('unsetValidationError');
			
				this.showModal = false
			},
		},
		
		components: {
		
			"text-field": require("components/MiniComponent/FormField/TextField"),
		
			"perma-link": require("components/MiniComponent/FormField/PermaLink"),
		
			"add-media": require("components/MiniComponent/FormField/AddMedia"),
		
			'alert': require("components/MiniComponent/Alert"),
		
			"custom-loader": require("components/MiniComponent/Loader"),
		
			'radio-button':require('components/MiniComponent/FormField/RadioButton'),
		
			'static-select':require('components/MiniComponent/FormField/StaticSelect'),
		
			'page-modal': require('components/Agent/kb/KbDeleteModal'),
		}
	};	
</script>