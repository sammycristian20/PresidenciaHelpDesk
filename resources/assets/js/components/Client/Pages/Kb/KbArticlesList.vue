<template>

	<transition name="page" mode="out-in">
		<div>

			<meta-component v-if="!from" :dynamic_title="lang('article-list-title')" :layout="layout" 
				:dynamic_description="lang('article-list-description')">
				
			</meta-component>

			<div v-if="!hasDataPopulated || loading" class="row">

				<loader :color="layout.portal.client_header_color" :animation-duration="4000" :size="60"/>
			</div>

			<div v-if="hasDataPopulated" class="row">

				<div id="content" class="site-content col-md-9" :class="{article_box : lang_locale === 'ar'}">

					<header v-if="!from" class="archive-header" :class="{align1: lang_locale == 'ar'}">
						
						<h1 class="archive-title">{{lang('articles')}}</h1>
					</header>
											
					<article-box :layout="layout" :from="from" :tag_id="tag_id" classname="col-md-12 col-sm-12 col-xs-12"/>
						
				</div>

				<div :class="{left: lang_locale == 'ar'}" id="sidebar" class="site-sidebar col-md-3">

					<category-sidebar :layout="layout" :categoryList="categoryList" :categorylength="category_count" 
						classname="col-md-12 col-sm-12 col-xs-12"/>

					<tags-sidebar :layout="layout" classname="col-md-12 col-sm-12 col-xs-12"/>
								
					</tags-sidebar>
				</div>
			</div>
		</div>
	</transition>
</template>
<script>

	import axios from 'axios'

	export default {

		name : 'kb-articles-list',

		description : 'Knowledgebase articles list component',
		
		props : {

			layout : { type : Object, default : ()=>{}},

			from : { type : String, default : ''},

			tag_id : { type : String | Number, default : ''}
		},

		data() {
		
			return {
				
				categoryList : [],
				
				category_count :0,
				
				loading:true,

				lang_locale : this.layout.language,
				
				kb_status : this.layout.kb_settings.status,

				hasDataPopulated : false,
			}
		},
		
		beforeMount(){

			if(this.kb_status){
				
				this.getData();

			} else {

				this.$router.push({name:'Home'})
			}
		},

		methods: {

			getData(){

				this.loading =true;

				this.$Progress.start();
				
				axios.get('api/category-list-with-article-count').then(response=> {
				
					this.$Progress.finish();
				
					this.loading =false;

					this.hasDataPopulated = true;
				
					this.categoryList = response.data.data.categories;
				
					this.category_count = this.categoryList.length;

				}).catch(error=>{
				
					this.$Progress.fail();
				
					this.loading=false;

					this.hasDataPopulated = true;
				});
			},
		},
		
		components:{
		
			'article-box': require('components/Client/Pages/Kb/KbComponents/KbArticleBox'),
			
			'category-sidebar': require('components/Client/Pages/Kb/KbComponents/KbCategorySidebar'),

			'client-panel-loader' : require('components/Client/ClientPanelLayoutComponents/ReusableComponents/Loader.vue'),

			'tags-sidebar': require('components/Client/Pages/Kb/KbComponents/KbTagsSideBar'),
		}
	};
</script>
<style scoped>
	.article_box{
		float: right !important;
		direction: rtl;
	}
</style>