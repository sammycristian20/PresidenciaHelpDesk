<template>
	
	<div :class="classname">
				
		<div class="widget-area">
			
			<section id="section-categories" class="section">
				
				<h2 class="section-title h4 clearfix" :class="{align1: lang_locale == 'ar'}">

					<i class="line" :style="lineStyle"></i>{{ lang('articles') }}  ({{articlelength}})
				
					<small :class="{left: lang_locale == 'ar'}" class="float-right">

						<i class="far fa-hdd fa-fw"></i>
					</small>
				</h2>

				<ul class="nav nav-pills nav-stacked nav-categories" :class="{padd: lang_locale == 'ar'}">
					
					<li v-for="article in articleList.slice(0,10)" class="d-flex justify-content-between align-items-center">

						<router-link :title="article.name" :to="{ name:'Articles', params:{slug:article.slug} }" 
							class="list-group-item list-group-item-action" id="art_link" :class="{align1: lang_locale == 'ar'}">
									
							{{subString(article.name)}}
						</router-link>
					</li>
				</ul>
			</section>
		</div>
	</div>
</template>

<script>
	
	import { getSubStringValue } from 'helpers/extraLogics';

	export default {

		name : "kb-article-sidebar",

    description : "This component shows the articles list which belongs to the same category of the opened article in the article detail page",

		props:{
			
			classname : { type : String},
			
			articleList : { type : Array, required:true},
			
			articlelength:{ type : Number, required:true},

			layout : { type : Object, default : ()=>{}}
		},

		data() {
		
			return {
		
				lineStyle : {
		
					borderColor : this.layout.portal.client_header_color,
				},
				
				lang_locale : this.layout.language
			}
		},

		methods : {
			
			subString(value){
			
				return getSubStringValue(value,20)
			}
		},
	};
</script>

<style scoped>
.side{
	margin-bottom: 2px;
}
.article_side{
	direction : rtl;
}
#art_link{
	padding: 5px !important;
}
</style>