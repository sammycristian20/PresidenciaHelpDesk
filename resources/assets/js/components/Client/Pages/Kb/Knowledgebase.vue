<template>
	
	<div id="categories">
				
		<meta-component :dynamic_title="lang('knowledgebase-title')" :dynamic_description="lang('knowledgebase-description')" :layout="layout">
				
		</meta-component>

		<template v-if="hasDataPopulated">
				
			<div class="row" v-if="category_count > 0">
					
				<div id="content" :class="{float1: lang_locale == 'ar'}" class="site-content col-md-9">
					
					<category-box :layout="layout" :auth="auth" classname="col-md-6 col-sm-12 col-xs-12"/>
					
					<section class="section">
						
						<div class="banner-wrapper banner-horizontal clearfix" :class="{banner_align : lang_locale ==='ar'}" 	:style="lineStyle">
							
							<h4 id="banner_title" class="banner-title h3" :class="{float1: lang_locale == 'ar'}">
							{{ lang('need_more_support') }}&rlm;?&rlm;</h4>
								
							<div class="banner-content" :class="{float1: lang_locale == 'ar'}">
									
								<p>{{ lang('if_you_did_not_find_an_answer_please_raise_a_ticket_describing_the_issue') }}</p>
							</div>
								
							<p>
								
								<router-link v-if="(create_ticket ===1 || (create_ticket  === 0 && !Array.isArray(auth.user_data)))"
									class="btn btn-custom" :class="{submit_tic : lang_locale === 'ar'}" to="/create-ticket" 
									:style="buttonStyle">
									
									{{ lang ('submit_a_ticket') }}		
								</router-link>
							</p>
						</div>
					</section>
				</div>
					
				<div id="sidebar" :class="{left: lang_locale == 'ar'}" class="site-sidebar col-md-3">
				
					<category-sidebar :layout="layout" :auth="auth" :categoryList="categoryList" 
						:categorylength="category_count" classname="col-md-12 col-sm-12 col-xs-12"/>

					<tags-sidebar :layout="layout" classname="col-md-12 col-sm-12 col-xs-12"/>

					</tags-sidebar>
				</div>
			</div>
		
			<div v-else>
				
				<h3 style="text-align:center">{{lang('no-data-to-show')}}</h3>
			</div>
		</template>

		<div v-if="!hasDataPopulated || loading" class="row">

			<loader :color="layout.portal.client_header_color" :animation-duration="4000" :size="60"/>
		</div>
	</div>
</template>

<script>

	import axios from 'axios'
	
	export default {
		
		name : 'knowkwdgebase-index',

		description : 'Knowledgebase index component',

		props : {

			layout : { type : Object, default : ()=>{}},

			auth : { type : Object, default : ()=>{}}
		},

		data() {
			
			return {
				
				base:'',
				
				categoryList : '',
				
				category_count :'',
				
				buttonStyle: {
					
					borderColor : this.layout.portal.client_button_border_color,
					
					backgroundColor : this.layout.portal.client_button_color
				},
				
				lineStyle: {
					
					borderColor : this.layout.portal.client_header_color,
				},
				
				create_ticket : parseInt(this.layout.allow_users_to_create_ticket.status),
				
				loading : true,

				hasDataPopulated : false,
				
				lang_locale : this.layout.language,
	
				kb_status : this.layout.kb_settings.status,
			}
		},

		beforeMount(){

			if(this.kb_status){
				
				this.getData();

			} else {

				this.$router.push({name:'Home'})
			}
		},

		methods : {

			getData(){

				this.$Progress.start();
			
				axios.get('api/category-list-with-article-count').then(response=> {
					
					this.$Progress.finish();
					
					this.loading = false;

					this.hasDataPopulated = true;
					
					this.categoryList = response.data.data.categories;
					
					this.category_count = this.categoryList.length;
					
				}).catch(res=>{
					
					this.$Progress.fail();
					
					this.loading = false;

					this.hasDataPopulated = true;
				}) 
			}
		},
		
		components:{
			
			'category-box': require('components/Client/Pages/Kb/KbComponents/KbCategoryBox'),
			
			'category-sidebar': require('components/Client/Pages/Kb/KbComponents/KbCategorySidebar'),
			
			'tags-sidebar': require('components/Client/Pages/Kb/KbComponents/KbTagsSideBar'),
			
			'client-panel-loader' : require('components/Client/ClientPanelLayoutComponents/ReusableComponents/Loader.vue'),
		},
	};
</script>

<style scoped>
	
	#banner_title{
		font-size: 15px !important;
	}

	.more_support{
		direction: rtl;
    float: right;
	}
	.submit_tic{
		float : left !important;
	}
	.banner_align {
		border-left-width: 1px !important;
    border-right-width: 5px !important;
	}
</style>