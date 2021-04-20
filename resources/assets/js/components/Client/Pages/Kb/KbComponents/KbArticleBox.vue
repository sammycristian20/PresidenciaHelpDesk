<template>
	
	<div id="articles">
		
		<div v-if="pageChange" class="row">

			<client-panel-loader :size="60" :color="layout.portal.client_header_color"></client-panel-loader>
		</div>

		<div v-if="!hasDataPopulated || loading">

			<h3 style="text-align : center">{{lang('loading_data')}}</h3>
		</div>
      
		<template v-if="hasDataPopulated">
			
			<header v-if="from" class="archive-header">
						
				<h1 class="archive-title">{{lang('article_tagged')}} : {{tag_name}}</h1>
			</header>

			<div v-if="articleList.length > 0" class="archive-list archive-article" >
					
				<article class="hentry" :class="{article: lang_locale == 'ar'}" v-for="article in articleSorted">
			
					<header class="entry-header" :class="{align1: lang_locale == 'ar'}">
						
						<i :class="{float1: lang_locale == 'ar'}" class="fa fa-list-alt fa-2x fa-fw float-left text-muted"></i>
						
						<h2 class="entry-title h4">

							<router-link :class="{art_name: lang_locale == 'ar'}"  :style="linkStyle"
								:to="{ name:'Articles', params:{slug:article.slug} }">{{article.name}}
							</router-link>
						</h2>
					</header>			
					
					<blockquote :class="{art_design: lang_locale == 'ar'}" class="blockquote archive-description" id="block">
						
						<p v-html="convert(article.description)" id="p_tag"></p>

						<a>
							
							<router-link :to="{ name:'Articles', params:{slug:article.slug} }" :style="linkStyle">{{ lang('read_more') }}
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

			<div v-else>
			
				<h3 style="text-align:center">{{lang('no-data-to-show')}}</h3>
			</div>
			
			<div v-if="records > 10" class="float-right" :class="{left: lang_locale == 'ar'}">
			
				<uib-pagination :total-items="records" v-model="pagination" class="pagination" :boundary-links="true" :items-per-page="perpage" @change="pageChanged()" :rotate="true" :max-size="3" :force-ellipses="true"></uib-pagination>
			</div>
		</template>
	</div>
</template>

<script>
	
	import axios from 'axios'
	
	import { mapGetters } from 'vuex'
	
	export default {
	
		name : "kb-article-box",


		description : "This component shows the article with publish time and description",
		
		props:{
			
			layout : { type: Object, default : ()=>{}},

			classname:{type:String},

			from : { type : String, default : ''},

			tag_id : { type : String | Number, default : ''},
		},

		data() {

			return {

				articleList:'',

				paramsObj:{},

				loading:true,
				
				perpage:0,
				
				pagination:{currentPage: 1},
				
				records:0,
				
				lang_locale : this.layout.language,
				
				all_art:'',

				hasDataPopulated : false,

				pageChange : false,

				linkStyle : {

					 color : this.layout.portal.client_header_color
				},

				tag_name : ''
			}
		},
		
		beforeMount(){

			if(this.from){
					
				this.paramsObj['tag_id'] = this.tag_id;						
			}

			this.articleApi(this.paramsObj);
		},

		computed:{

			articleSorted: function() {

				return _.orderBy(this.articleList, 'publish_time', 'desc');
			},

			...mapGetters(['formattedTime','formattedDate'])
		},

		methods : {

			articleApi(x) {
				
				this.$Progress.start();

				var params = x;
			
				axios.get('api/article-list-with-categories',{params}).then(response => {
			
					this.loading=false;

					this.pageChange=false;

					this.hasDataPopulated = true
			
					this.articleList = response.data.data.articles.data;
					
					this.records =  response.data.data.articles.total;
					
					this.perpage =  response.data.data.articles.per_page;

					this.tag_name = response.data.data.tag.name;

					this.$Progress.finish();
				
				}).catch(res=>{
				
					this.loading=false;

					this.pageChange=false;

					this.hasDataPopulated = true;

					this.$Progress.fail();
				})
			},
			
			pageChanged() {
				
				this.pageChange = true;
				
				if(this.from){
					
					this.paramsObj['tag_id'] = this.tag_id;						
				}

				this.paramsObj['page']=this.pagination.currentPage;
				
				this.articleApi(this.paramsObj);
				
				var elmnt = document.getElementById('articles');
	  		
	  		elmnt.scrollIntoView({ behavior : "smooth"});
			},
			
			convert(x) {
			
				if(x){
					
					if(x.length>25){
								
						return x.replace(/(<\/?(?:img)[^>]*>)|<[^>]+>/ig, '$1').substring(0,250) + '....';
					} else {
						
						return x.replace(/(<\/?(?:img)[^>]*>)|<[^>]+>/ig, '$1');
					}
				}
			}
		},

		components : {

			'client-panel-loader' : require('components/Client/ClientPanelLayoutComponents/ReusableComponents/Loader.vue'),
		}
	};
</script>

<style scoped>
#p_tag{
	word-wrap: break-word;
}
.date {
	margin-right:0px;
}
blockquote {
	font-size: 14px !important;
}
.date_align{
	margin-right: 0em !important
}
#block{
	margin-bottom: 10px !important;
	margin-top: 10px !important;
}
#block_link{
	text-decoration: underline;
}
</style>