<template>
	
	<div id="search_data">

		<meta-component dynamic_title="Search results" dynamic_description="Search results"  :layout="layout">
				
		</meta-component>

		<div v-if="loading ==true" class="row" style="margin-top:30px;margin-bottom:30px">

			<loader :color="layout.portal.client_header_color" :animation-duration="4000" :size="60"/>
		</div>

		<div v-if="pageChange" class="row">

			<client-panel-loader :color="layout.portal.client_header_color" :size="60"></client-panel-loader>
		</div>

		<div v-if="!loading && !no_article && articleList.length > 0" class="col-md-12 col-sm-12 col-xs-12">
					
			<div class="archive-list archive-article" >
						
				<article :class="{article: lang_locale == 'ar'}" class="hentry" v-for="article in articleList">
							
					<header class="entry-header" :class="{align1: lang_locale == 'ar'}">
								
						<i :class="{float1: lang_locale == 'ar'}" class="fa fa-list-alt fa-2x fa-fw float-left text-muted"></i>
								
						<h2 class="entry-title h4">
									
							<router-link :class="{art_name: lang_locale == 'ar'}" :style="linkStyle" 
								:to="{ name:'Articles', params:{slug:article.slug} }">{{article.name}}
							</router-link>
						</h2>
					</header>
						
					<blockquote :class="{art_design: lang_locale == 'ar'}" class="blockquote archive-description" id="block">
								
						<p v-html="convert(article.description)" id="p_tag"></p>
						
						<a>

							<router-link :to="{ name:'Articles', params:{slug:article.slug} }" :styel="linkStyle">
								{{ lang('read_more') }}
							</router-link>
						</a>
					</blockquote> 

					<footer class="entry-footer" :class="{'art_name align1': lang_locale == 'ar'}">

						<div class="entry-meta text-muted">
									
							<span class="date"><i class="far fa-clock fa-fw"></i> 
									
								<span :class="{date_align : lang_locale === 'ar'}">{{formattedTime(article.publish_time)}}</span>
							</span>
						</div>
					</footer>
				</article>
			</div>

			<div v-if="articleList.length > 0 && records > 10" class="float-right" :class="{left: lang_locale == 'ar'}">
						
				<uib-pagination :total-items="records" v-model="pagination" class="pagination" :boundary-links="true" 
					:items-per-page="perpage" @change="pageChanged()" :rotate="true" :max-size="3" :force-ellipses="true">
								
				</uib-pagination>
			</div>
		</div>

		<div v-if="articleList.length == 0 || no_article" class="text-center">
							
			<h1>{{lang('sorry')}}!</h1>
							
			<p>{{lang('no-data-to-show')}}</p>
		</div>
	</div>
</template>

<script>

	import axios from 'axios'
	
	import { mapGetters } from 'vuex'
	
	export default {
		
		name : 'kb-search-result',

		description  : 'Knowledgebase search result component',

		props : {

			layout : { type : Object, default : ()=>{}}
		},

		data() {
			
			return {
			 
				search_data:'',
				
				articleList:[{}],
				
				paramsObj:{},
				
				path:'',
				
				loading:true,
				
				perpage:0,
				
				pagination:{currentPage: 1},
				
				records:null,
				
				lang_locale : this.layout.language,

				no_article : false,

				pageChange : false,

				linkStyle : {

					 color : this.layout.portal.client_header_color
				},

				kb_status : this.layout.kb_settings.status
			}
		}, 
 
		beforeMount(){
		
			this.search_data =location.search.substring(1).split('=')
		
			this.paramsObj['s']=this.search_data[this.search_data.length-1];

			if(this.kb_status){
				
				this.articleApi(this.paramsObj);

			} else {

				this.$router.push({name:'Home'})
			}
		},

		computed:{

			...mapGetters(['formattedTime','formattedDate'])
		},
		
		methods : {

			articleApi(x) {
				
				var params = x;
				
				this.$Progress.start();
				
				axios.get('api/search',{params}).then(response => {
					
					this.$Progress.finish();
					
					this.loading=false;

					this.pageChange = false;
					
					this.articleList = response.data.data.articles.data;
					
					this.records =  response.data.data.articles.total;
					
					this.perpage =  response.data.data.articles.per_page;
				}).catch(res=>{
					
					this.loading=false;

					this.pageChange = false;

					this.no_article = true;

					this.$Progress.fail();
				})
			},

			pageChanged() {
				
				this.pageChange = true;

				this.paramsObj['page']=this.pagination.currentPage;
			
				this.articleApi(this.paramsObj);

				var elmnt = document.getElementById('search_data');
				
				elmnt.scrollIntoView({ behavior : "smooth"});
			},

			convert(x) {
				
				if(x){
				
					if(x.length>25){
				
						return x.replace(/(<\/?(?:a|img)[^>]*>)|<[^>]+>/ig, '$1').substring(0,250) + '....';
					} else {
				
						return x;
					}
				}
			}
		},

		components : {

			'client-panel-loader' : require('components/Client/ClientPanelLayoutComponents/ReusableComponents/Loader.vue')
		} 
	};
</script>
<style scoped>
	.date {
		margin-right:0px;
	}
	blockquote {
		font-size: 14px !important;
	}
	#block{
		margin-bottom: 10px !important;
		margin-top: 10px !important;
	}
	#block_link{
		text-decoration: underline;
	}
	#p_tag{
		word-wrap: break-word;
	}
	.date_align{
		margin-right: 0em !important
	}
</style>
