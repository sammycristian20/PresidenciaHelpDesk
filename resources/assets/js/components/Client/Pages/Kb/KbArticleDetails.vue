<template>
	
	<div>

		<div v-if="pageChange" class="row">

			<client-panel-loader :size="60" :color="layout.portal.client_header_color"></client-panel-loader>
		</div>

		<alert componentName="articleDetails"/>

		<div v-if="!hasDataPopulated || loading" class="row">

			<loader :animation-duration="4000" :size="60" :color="layout.portal.client_header_color"/>
		</div>

		<div v-if="hasDataPopulated && !not_found" class="row">
				
			<meta-component :dynamic_title="articleName" :dynamic_description="lang('article_lists-page-description')" 
				:layout="layout" >
					
			</meta-component>

			<div id="content" :class="{article_data: lang_locale == 'ar'}" class="site-content col-md-9">
			
				<article class="hentry">

					<header class="entry-header" :class="{align1: lang_locale == 'ar'}">

						<h1 class="entry-title">{{articleName}}</h1>

						<div :class="{time: lang_locale == 'ar'}" class="entry-meta text-muted">
							
							<span class="date"><i class="far fa-clock fa-fw"></i> 

								<time datetime="2013-09-19T20:01:58+00:00">{{publishDate(articleCreatedAt)}}</time>
							</span>
							
							<span class="category" v-for="category in CategoryList"><i class="far fa-folder-open fa-fw"></i> 

								<router-link  :to="{ name:'Category', params:{slug:category.slug} }" :title="category.name">
									{{subString(category.name)}}
								</router-link>
							</span>

							<template v-for="tag in articleData.tags">
								
								<router-link :title="tag.name" :to="{ name:'Tags',params:{tag_id:tag.id}}" 
								class="btn btn-tag btn-sm tag">
									
									{{ subString(tag.name)}}
								</router-link>
							</template>
						</div>
					</header>

					<div class="entry-content ck-content clearfix" :class="{align1: lang_locale == 'ar'}">
						
						<p v-html="articleDescription" id="p_desc"></p>
					</div>

					<footer class="entry-footer" id="entry"></footer>
				</article>

				<div id="comments" class="comments-area" :class="{align1: lang_locale == 'ar'}">
					
					<ol class="comment-list">

						<comments-list v-for="comment in commentsList"  :key="comment.id" :comment="comment" 
						:base="base" :layout="layout">
								
						</comments-list>
					</ol>
						
					<div class="align_end" v-if="records > 10">
							
						<uib-pagination :total-items="records" v-model="pagination" class="pagination" :boundary-links="true" :items-per-page="perpage" @change="pageChanged()" :rotate="true" :max-size="3" :force-ellipses="true">
								
						</uib-pagination><br>
					</div>

					<alert componentName="KbComment"></alert>

					<article-comment v-if="articleData.is_comment_enabled" :layout="layout" :auth="auth"></article-comment>
				</div>
			</div>

			<div id="sidebar" :class="{left: lang_locale == 'ar'}" class="site-sidebar col-md-3">
					
				<category-sidebar :layout="layout" :categoryList="categoryList" :categorylength="category_count" classname="col-md-12 col-sm-12 col-xs-12"/>
					
				<article-sidebar :articleList="articleList" :articlelength="articles_count" 
					:layout="layout" classname="col-md-12 col-sm-12 col-xs-12"/>
			</div>
		</div>
	</div>
</template>
<script>
	
	import axios from 'axios'
	
	import { mapGetters } from 'vuex'
	
	import { findObjectByKey } from 'helpers/extraLogics'

	import { getSubStringValue } from 'helpers/extraLogics'

	import moment from 'moment'
	
	export default {

		name : 'kb-article-details',

		description : 'Knowledgebase article details component',

		props :{

			layout : { type : Object, default : ()=>{}},

			auth : { type : Object, default : ()=>{}}
		},

		data() {

			return {
			
				base : this.auth.system_url,
			
				ArticleSlug:'',
			
				CategoryId:0,
			
				CategoryList:[{}],
			
				articleData:{},
			
				articleName : '',
			
				articleSlug : '',
			
				articleCreatedAt : '',
			
				articleDescription : '',
			
				category_count :0,
			
				categoryList:[],
			
				articleList:[],
			
				articles_count:0,
			
				commentsList:[],
			
				loading:true,

				hasDataPopulated : false,

				perpage:0,
			
				pagination:{currentPage: 1},
			
				records:0,
			
				lang_locale : this.layout.language,
			
				path:'',
			
				art_slug:'',
			
				paramsObj:{},
			
				kb_status : this.layout.kb_settings.status,

				pageChange : false,

				not_found : false,

				lineStyle: {
					
					borderColor : this.layout.portal.client_header_color,
				},
			}
		},
		
		created() {
		
			this.lang_locale = localStorage.getItem('LANGUAGE');
	
			window.eventHub.$on('loaderOn', this.loaderStart);
		
			window.eventHub.$on('forComment', this.addComment);
		
			this.path=location.pathname.split('/');
			
			this.art_slug=this.path[this.path.length-1];
			
			if(this.kb_status){
				
				this.getSiderData();

			} else {

				this.$router.push({name:'Home'})
			}
		},
		
		computed:{
		
			...mapGetters(['formattedTime','timeFormat'])
		},
		methods : {

			publishDate(value) {

				let utcFormat = new Date(value+' GMT').toString();

				return moment(utcFormat).format(this.timeFormat)
			},

			getSiderData(){

				this.$Progress.start();

				axios.get('api/category-list-with-article-count').then(response=> {
			
					this.categoryList = response.data.data.categories;
			
					this.category_count = this.categoryList.length;

					this.$Progress.finish();

					this.articleId();
			
				}).catch(error=>{
			
					this.loading = false;

					this.hasDataPopulated = true;

					this.$Progress.fail();
				});
			},

			subString(value){
			
				return getSubStringValue(value,35)
			},

			articleId(x){

				this.$Progress.start();

				this.ArticleSlug = this.art_slug;

				var params = x;

				axios.get('api/article/'+this.ArticleSlug,{params}).then(response => {

					if(response.data.data.article){

						this.articleData = response.data.data.article;

						window.eventHub.$emit('articleDetailsLoaded',response.data.data.article)

						this.articleName = this.articleData.name;

						this.articleSlug = this.articleData.slug;

						this.articleCreatedAt = this.articleData.publish_time;

						this.articleDescription = this.articleData.description;

						this.commentsList = (response.data.data.comments.data).reverse();

						this.records =  response.data.data.comments.total;

						this.perpage =  response.data.data.comments.per_page;

						this.CategoryList = this.articleData.categories;

						this.CategoryId = this.CategoryList.map(a => a.id);

						if(!this.pageChange){

							this.getCategoryData(this.CategoryId);
						} else {

							this.loading = false;

							this.pageChange = false;

							this.hasDataPopulated = true;
						}
					} else {

						this.$router.push({name:'NotFound'})
					}

					this.$Progress.finish();

				}).catch(error=>{
					
					this.$store.dispatch('setAlert', {
						type: 'danger', 
						message: error.response.data.message, component_name : 'articleDetails'
					});

					this.$Progress.fail();

					this.loading = false;

					this.pageChange = false;

					this.hasDataPopulated = true;

					this.not_found = true;

					this.$router.push({name:'NotFound'})
				})
			},

			getCategoryData(id){

				this.$Progress.start();

				axios.get('api/category-list/'+id).then(response=> {

					this.loading = false;

					this.hasDataPopulated = true;

					this.articleList =response.data.data.article.data;

					this.articles_count = this.articleList.length;

					this.$Progress.finish();

				}).catch(error=>{

					this.loading = false;

					this.hasDataPopulated = true;
					
					this.$Progress.fail();
				})
			},
			
			pageChanged() {
				
				this.pageChange = true;

				this.paramsObj['page']=this.pagination.currentPage;
			
				this.articleId(this.paramsObj);

				var elmnt = document.getElementById('comments');
				
				elmnt.scrollIntoView({ behavior : "smooth"});
			},
			
			addComment() {
				
				this.$Progress.start();

				this.pagination.currentPage = 1;
			
				const data = 'page=' + 1;
			
				axios.get('api/article/'+this.ArticleSlug+'?'+data).then(response => {
			
					this.commentsList = (response.data.data.comments.data).reverse();
			
					this.records =  response.data.data.comments.total;
			
					this.perpage =  response.data.data.comments.per_page;

					this.$Progress.finish();
				})
			},
			
			loaderStart() {
			
				this.loading = true;
			},
		},

		components:{
		
			'article-comment': require('components/Client/Pages/Kb/KbComponents/KbAddComment'),
			
			'comments-list': require('components/Client/Pages/Kb/KbComponents/KbCommentsList'),
			
			'category-sidebar': require('components/Client/Pages/Kb/KbComponents/KbCategorySidebar'),
			
			'article-sidebar': require('components/Client/Pages/Kb/KbComponents/KbArticleSidebar'),
			
			'client-panel-loader' : require('components/Client/ClientPanelLayoutComponents/ReusableComponents/Loader.vue'),

			'alert' : require('components/MiniComponent/Alert'),
		},
	}; 
</script>
<style>
	.clearfix table {
		
		border-collapse: separate; 
	}
	.side{
		margin-bottom: 2px;
	}
	#pre{
		background-color: #eee;
		border: 1px solid #999;
		display: block;
		padding: 20px;
	}
	.list-enter-active, .list-leave-active {
		transition: all 1s;
	}
	.list-enter, .list-leave-to {
		opacity: 0;
		transform: translateY(30px);
	}
	.article-remark{
		background-color: #9acd32;
	}
	.hentry {
		margin : auto !important;
	}
	.article_data{
		direction: rtl;
		float: right !important;
		padding-right: 0px;
	}
	#p_desc{
		word-wrap: break-word;overflow: auto;
	}
	#p_desc pre{
		padding: 10px;
    border-radius: 3px;
    line-height: 2;
	}
	#entry{
		padding-top: 1.5em !important;
	}

	.table{
		overflow-x: auto;
	}
	.entry-content table,.entry-content table td,.entry-content table tr,.entry-content table th {  
	  border: 1px solid #ddd;
	  text-align: left;
	}

	.entry-content table {
	  border-collapse: collapse;
	  width: 100%;
	}

	.entry-content table th, .entry-content table td {
	  padding: 15px;
	}

	.btn-tag {
    margin-bottom: .5em;
    font-size: 12px;
    margin-right: 4px;
    color: #333;
    border-color: rgba(0,0,0,.15) rgba(0,0,0,.15) rgba(0,0,0,.25);
    text-shadow: 0 1px 1px rgba(255,255,255,.75);
    background: linear-gradient(to bottom,#fff 0,#e6e6e6 100%);
    box-shadow: inset 0 1px 0 rgba(255,255,255,.2), 0 1px 2px rgba(0,0,0,.05);
	}

	.align_end { text-align: end; }
</style>