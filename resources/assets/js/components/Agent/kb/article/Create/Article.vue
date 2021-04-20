<template>
	
	<div>
		
		<div class="row" v-if="hasDataPopulated === false || loading === true">
		
			<custom-loader :duration="loadingSpeed"></custom-loader>
		</div>
		
		<alert componentName="article-create"/>

		<div class="card card-light " v-if="hasDataPopulated === true">
			
			<div class="card-header">

				<h3 class="card-title">{{trans(title)}}</h3>

				<div class="card-tools">
			
					<a v-if="buttonVisible" id="view_article" class="btn btn-tool" type="button"
						:href="type ? basePath()+'/show/'+link :'javascript:;'" :target="!type ? '' : '_blank'" :disabled="!type" 
						v-tooltip="trans('view')">
								
						<i class="fas fa-eye"> </i>
					</a>

					<button v-if="buttonVisible" id="delete_article" class="btn btn-tool" type="button" 
						@click="modalMethod('delete')" v-tooltip="trans('delete')">
								
						<i class="fas fa-trash"> </i>
					</button>
							
					<template v-if="!buttonVisible">

						<a href="javascript:;" class="btn-tool dropdown-toggle" data-toggle="dropdown" v-tooltip="trans('add_fields')">
				
							<i class="fas fa-plus"> </i>
						</a>

						<div class="dropdown-menu dropdown-menu-right" role="menu">
							
							<a class="dropdown-item text-dark" @click="modalMethod('new_category')" href="javascript:;"
								id="add_category">
							
								{{trans('add_category')}}
							</a>

							<a class="dropdown-item text-dark" @click="modalMethod('new_tag')" href="javascript:;">

								{{trans('add_tag')}}
							</a>
						</div>
					</template>
				</div>
			</div>

			<div class="card-body">
				
				<div class="row">
			
					<text-field label="Name" :value="name" type="text" 
						name="name" :onChange="onChange" 
						classname="col-sm-6" :required="true">
					</text-field>
						
					<perma-link :label="trans('perma_link')" :value="slug" 
						name="slug" :onChange="onChange" 
						classname="col-sm-6" :required="true" :link="link" :disabled="buttonVisible">
					</perma-link>
				</div>
					
				<div class="row">
					
					<dynamic-select :label="trans('category')" 
						:multiple="true" name="category" 
						classname="col-sm-4" apiEndpoint="/api/dependency/categories" 
						:value="category" :onChange="onChange" :required="true" strlength="25">
					</dynamic-select>

					<dynamic-select :label="trans('author')" 
						:multiple="false" name="author" 
						classname="col-sm-4" apiEndpoint="/api/dependency/agents?meta=true" 
						:value="author" :onChange="onChange" :required="true" :clearable="author ? true : false" strlength="25">
					</dynamic-select>

					<dynamic-select :label="trans('tags')" 
						:multiple="true" name="tag_id" 
						classname="col-sm-4" apiEndpoint="/api/dependency/tags" 
						:value="tag_id" :onChange="onChange" :required="false" strlength="25" :hint="trans('relevant_tags_search')">
					</dynamic-select>
				</div>

				<div class="row" v-if="showComments">
					
					<div class="col-sm-3">
						
						<label for="status">{{trans('allow_comments')}}</label><br>
						
						<status-switch name="is_comment_enabled" :value="is_comment_enabled" :onChange="onChange" classname="float-left" :bold="true">
						
						</status-switch>   
					</div>
				</div>

				<div class="row">

					<add-media :description="description" :classname="classname" :show_error="show_err"  page_name="kb" 
						name="description" chunkApi="/chunk/upload/public" filesApi="/media/files/public" :onInput="onChange">
					
						<button id="article__template__button" slot="template" class="btn btn-default" @click="() => showTemplateModal = true">
							<i class="fas fa-align-left" aria-hidden="true"></i> {{trans('select_template')}}</button>
					</add-media>
				</div>

				<div class="row">
			
					<static-select :label="trans('visibility')"  
						:elements="visibility_options" name="visible_to" :value="visible_to" 
						classname="col-sm-4" :onChange="onChange" :required="true">
					</static-select>

					<date-time-field :label="trans('publish_immediately')" 
						:value="publish_time" type="datetime" name="publish_time" 
						:onChange="onChange" :time-picker-options="timeOptions" 
						:required="true" format="MMMM Do YYYY HH:mm" classname="col-sm-4" 
						:clearable="false" :disabled="false">
					</date-time-field>

					<radio-button :options="radioOptions" :label="trans('status')" 
						name="type" :value="type"
						:onChange="onChange" classname="form-group col-sm-4" >
					</radio-button>
				</div>

				<div class="card card-light ">
			
				<div class="card-header">
				
					<h3 class="card-title">{{trans('seo')}}</h3>
				</div>
				
				<div class="card-body">
				
					<div class="row">
				
						<text-field :label="trans('seo_title')" :value="seo_title" 
							type="text" name="seo_title" :onChange="onChange" 
							classname="col-sm-12" :required="false">
						</text-field>

						<div class="progress sm col-md-12 p-0" id="prog"><div :class="seoClass" :style="seoStyle"></div></div>
					</div>
					
					<div class="row">
						
						<text-field :label="trans('description')" :value="meta_description" 
							type="textarea" name="meta_description" :onChange="onChange" 
							classname="col-sm-12" :required="false">
						</text-field>

						<div class="progress sm col-md-12 p-0" id="prog"><div :class="seoDesClass" :style="seoDesStyle"></div></div>
					</div>
				</div>
			</div>
		</div>

		<div class="card-footer">
			
			<button type="button" v-on:click="onSubmit()" :disabled="loading" class="btn btn-primary">
				
				<span :class="iconClass"></span>&nbsp;{{trans(btnName)}}
			</button>
		</div>
		
		<transition  name="modal">
			
			<article-modal :category="category" v-if="showModal" 
				:newCategory="newCategory" :onClose="onClose" :newTag="newTag"
				:showModal="showModal" :title="modal_title" :id="link">
			</article-modal>
		</transition>

		<transition name="modal">
			<template-modal v-if="showTemplateModal" :onClose="onTemplateModalClose" :showModal="showTemplateModal" :modal-title="trans('select_template')"></template-modal>
		</transition>
	</div>
</div>
</template>

<script>

	import axios from "axios";
	
	import { errorHandler, successHandler } from "helpers/responseHandler";
	
	import { validateArticleSettings } from "helpers/validator/articleCreateRules";
	
	import {getIdFromUrl} from 'helpers/extraLogics';
	
	import { mapGetters } from 'vuex'

	import moment from 'moment'
	
	export default {
	
		name: "article-page",
	
		description: "article create and edit page",
	
		data: () => ({
	
			showModal: false,//modal status
	
			name: "",//typed article name
	
			link: "", //article link
	
			category: [], //selected category
	
			author: '', //selected author
	
			template: '', //selected author
	
			loading: false,//loader status
	
			loadingSpeed: 4000,
	
			hasDataPopulated: false,
	
			slug:'',//article slug
	
			radioOptions:[{name:'published',value:1},{name:'draft',value:0}],//article status
	
			type : 1,//article status
	
			visibility_options:[{ id:"all_users",name:"All Users" },{ id:"logged_in_users",name:"Logged in Users" },{ id:"agents",name:"Agents" },{ id:"logged_in_users_and_agents",name:"Logged in Users And Agent" }],
	
			visible_to: "",
	
			buttonVisible : false,//buttons config for create and edit page
	
			publish_time: new Date(),
	
			timeOptions : { start: '00:00', step: '00:30', end: '23:30' },//date picker time options
	
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
	
			title :'create_new_article', iconClass:'fas fa-save',btnName:'save',//page title and btn classes for create and edit page
			article_id :'',
	
			modal_title :'',

			tag_id : [],

			showTemplateModal: false,

			is_comment_enabled : true,

			showComments : false
		}),
	
		computed:{
	
					...mapGetters(['getUserData'])
		},
	
		watch:{
	
			getUserData(newValue,oldValue){
	
				const location = window.location.pathname.split('/');      	 
	
				if(location[location.length-1] !== 'edit') {
	
					this.author = {
	
						id : newValue.user_data.id, 
	
						name: newValue.user_data.first_name + ' ' + newValue.user_data.last_name, 
				
						email : newValue.user_data.email, 
						
						profile_pic : newValue.user_data.profile_pic 
					};
				}

				return newValue
			}
		},

		beforeMount() {
			
			const path = window.location.pathname;

			this.getValues(path);

			this.getCommentStatus()
		},

		created() {
			window.eventHub.$on('applyTemplate', this.applyTemplate);
		},
		
		methods: {

			getValues(path){

				const articleId = getIdFromUrl(path);
			
				if (path.indexOf("edit") >= 0) {
			
					this.title = 'edit_article';
			
					this.iconClass = 'fas fa-sync'
			
					this.btnName = 'update'
			
					this.buttonVisible = true
			
					this.hasDataPopulated = false;
			
					this.getInitialValues(articleId);
				} else {

					this.loading = false;

					this.hasDataPopulated = true;

				}
			},
			
			getCommentStatus() {

				axios.get('/kb/settings/getvalue').then(res=>{
				
					this.showComments = res.data.data.kbsettings.is_comment_enabled;

				}).catch(res=>{
					
					this.showComments = false;
				});
			},

			getInitialValues(articleId) {
		
				this.loading = true;			
		
				axios.get('/api/edit/article/'+articleId).then(res => {
					
					this.loading = false;

					this.hasDataPopulated = true;

					this.article_id  = articleId
		
					this.updateStatesWithData(res.data.message);

					this.tag_id = res.data.message.article.tags;
		
				}).catch(err => {			
					
					this.loading = false;

					errorHandler(err)
				});
			},
		
			updateStatesWithData(articleData) {
			
				var articleDetails = articleData.article
		
				this.link = articleDetails.slug
		
				this.seoTitleProgress(articleDetails.seo_title)
		
				this.metaProgress(articleDetails.meta_description);
		
				const self = this;
		
				const stateData = this.$data;

				Object.keys(articleData.article).map(key => {
		
					if (stateData.hasOwnProperty(key)) {
		
						self[key] = articleData.article[key];
					}
				});

				this.publish_time = new Date(this.publish_time).toString()

				this.seo_title = articleDetails.seo_title === '' ? articleDetails.name : articleDetails.seo_title

				this.meta_description = articleDetails.meta_description === '' ? articleDetails.description : articleDetails.meta_description
			},
		
			isValid() {
		
				const { errors, isValid } = validateArticleSettings(this.$data);
		
				if (!isValid) {
		
					return false;
				}
					return true;
			},
			
			modalMethod(name){
			
				this.showModal = true
			
				this.modal_title = name
			
				this.$store.dispatch('unsetValidationError');
			},	
			
			onSubmit() {
			
				this.classname = this.description === '' ? 'form-group has-error'  : 'form-group'
			
				this.show_err = this.description === '' ? true  : false
			
				if (this.isValid() && this.description !== '') {

					this.loadingSpeed = 8000;
			
					this.loading = true;
			
					var fd = new FormData();
			
					if(this.article_id != ''){
			
						fd.append('articleid', this.article_id);
					}
			
					fd.append('name', this.name);
			
					fd.append('slug', this.link);
			
					fd.append('description', this.description);
			
					fd.append('category_id',this.category.map(a => a.id));

					if(this.showComments){

						fd.append('is_comment_enabled',this.is_comment_enabled ? 1 : 0);
					}

					if(this.tag_id.length > 0){
						
						fd.append('tag_id',this.tag_id.map(a => a.id));								
					}
			
					fd.append('author', this.author.id);
			
					if(this.template != '' && this.template != null ){
			
						fd.append('template', this.template.id);
					} else {
						
						fd.append('template', '');
					}				
			
					fd.append('publish_time', new Date(this.publish_time).toUTCString());
			
					fd.append('visible_to', this.visible_to);
			
					fd.append('type', this.type);
			
					fd.append('seo_title', this.seo_title);
			
					fd.append('meta_description', this.meta_description);
			
					const config = { headers: { 'Content-Type': 'multipart/form-data' } };
			
					axios.post('/article', fd,config).then(res => {
			
						this.loading = false;
			
						successHandler(res,'article-create');
			
						if(this.article_id === ''){
							
							this.redirect('/article')
						} else {
			
							this.getInitialValues(this.article_id)
						}
					}).catch(err => {
			
						this.loading = false;
			
						errorHandler(err,'article-create');
					});
				}
			},
			
			newCategory(value){
			
				this.category.push(value);
			},

			newTag(value){
				
				this.tag_id.push(value)
			},
			
			onChange(value, name) {
				
				this[name] = value;
				
				/*
				* Updating article link based on name(Text field) and slug(Text field)
				* By default article link is Article Name... also given option to update the link in Slug field
				* Description : link should not contain any spaces or special characters for that i am using Regex pattern
				*/
				if(!this.buttonVisible){

					if(name === 'name' || name==="slug"){
			
						var regex = value.replace(/[^\w\s]/gi, '').toLowerCase();
				
						var regex1 = regex.replace(/_+/g,'')
				
						this.link= regex1.replace(/\s+/g,"-");
				
						this.slug = value
					}
				}

				if(name === 'publish_time') {

					this.publish_time = moment(value).format('YYYY-MM-DD HH:mm:ss')
				}
			
				if(name === 'seo_title'){ this.seoTitleProgress(value) }
			
				if(name === 'meta_description'){ this.metaProgress(value) }

				if(name === 'author' ){ this[name] = value === null ? '' : value }
			},

			templateData(data){

				this.description = data;
			},
			
			seoTitleProgress(value){
			
				this.seoStyle.width = value.length/0.6+'%'
				
				// SEO title length
				var len = value.length;
				
				/**
				 * progDan(danger (red)), progSuc(success (green)), progWarn(warning (yellow)) => Progress bar classes
				 *
				 * Based on SEO title length this will show Progrees bar
				 * If SEO title length is more than 60 progress bar in red color
				 * If SEO title length is more than 25 and less than or equal to 50 progress bar in green color
				 * If SEO title length is more than 50 and less than or equal to 60 progress bar in yellow color
				 */
				this.seoClass = len > 60 ? this.progDan : 
												len > 25 && len <= 50 ? this.progSuc : 
												len > 50 && len <= 60 ? this.progWarn :  this.progStart;
			},
			
			metaProgress(value){
			
				this.seoDesStyle.width = value.length/1.6+'%'
				
				// SEO description length
				var len = value.length;
				
				/**
				 * progDan(danger (red)), progSuc(success (green)), progWarn(warning (yellow)) => Progress bar classes
				 *
				 * Based on SEO description length this will show Progrees bar
				 * If SEO description length is more than 160 progress bar in red color
				 * If SEO description length is more than 80 and less than or equal to 120 progress bar in green color
				 * If SEO description length is more than 120 and less than or equal to 160 progress bar in yellow color
				 */
				this.seoDesClass = len > 160 ? this.progDan : 
														len > 80 && len <= 120 ? this.progSuc : 
														len > 120 && len <= 160 ? this.progWarn :  this.progStart;
			},
			
			onClose(){
			
				this.$store.dispatch('unsetValidationError');
			
				this.showModal = false
			
			},

			/**
			 * Apply template description to the ckeditor content
			 * data.operation = {
			 * 'append': 'Append the data.data to the ckeditor content',
			 * 'overwrite': 'Overwrite the content of ckeditor with the data.data
			 * }
			 */
			applyTemplate(data) {
				if(data.operation === 'append') {
					this.description += data.data;
				} else if(data.operation === 'overwrite') {
					const isConfirmed = confirm('You are going to overwrite the description. Are you sure?');
					if(!isConfirmed) {
						return;
					}
					this.description = data.data;
				}
				this.onTemplateModalClose();
			},

			onTemplateModalClose() {
				this.showTemplateModal = false;
			}
		},
		components: {
			
			"text-field": require("components/MiniComponent/FormField/TextField"),
			
			"perma-link": require("components/MiniComponent/FormField/PermaLink"),
			
			"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),
			
			"add-media": require("components/MiniComponent/FormField/AddMedia"),
			
			'alert': require("components/MiniComponent/Alert"),
			
			"custom-loader": require("components/MiniComponent/Loader"),
			
			'radio-button':require('components/MiniComponent/FormField/RadioButton'),
			
			'static-select':require('components/MiniComponent/FormField/StaticSelect'),
			
			'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),
			
			'article-modal': require('components/Agent/kb/article/Create/ArticleModal'),

			'template-modal': require('components/Agent/kb/article/Create/TemplateModal'),

			'status-switch':require('components/MiniComponent/FormField/Switch'),
		}
	};	
</script>

<style scoped>
  #article__template__button {
    margin-top: -13px;
  }
</style>