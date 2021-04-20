<template>
	
	<div :class="classname">
	
		<div class="widget-area">
		
			<section id="section-categories" class="section">
				
				<h2 class="section-title h4 clearfix" :class="{align1: lang_locale == 'ar'}">

					<i class="line" :style="lineStyle"></i>{{ lang('categories') }}  ({{categorylength}})
				
					<small :class="{left: lang_locale == 'ar'}" class="float-right">

						<i class="far fa-hdd fa-fw"></i>
					</small>
				</h2>

				<ul class="nav nav-pills nav-stacked nav-categories" :class="{padd: lang_locale == 'ar'}">
					
					<li v-for="category in categoryList.slice(0,10)" class="d-flex justify-content-between align-items-center">

						<router-link :title="category.name" :to="{ name:'Category', params:{slug:category.slug} }" 
							class="list-group-item list-group-item-action" id="cat_link" :class="{align1: lang_locale == 'ar'}">
									
							<span :class="{left: lang_locale == 'ar'}" class="badge badge-pill pull-right float-right"  
							id="side_badge" :style="badgeStyle">{{category.articles_count}}
							</span>

							{{ subString(category.name)}}
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

		name : "category-sidebar",

		description : "This component shows the top 10 categories in the category list ",

		props:{
			
			classname:{type:String},
		
			categoryList:{type:Array,required:true},
			
			categorylength:{type:Number,required:true},

			layout : { type : Object, default : ()=>{}},
		},

		data() {
			return {

				lineStyle: {
					
					borderColor : this.layout.portal.client_header_color,
				},

				badgeStyle : {

					backgroundColor : this.layout.portal.client_header_color
				},

				lang_locale : this.layout.language
			}
		},

		methods : {

			subString(value){
			
				return getSubStringValue(value,18)
			}
		}
	};
</script>
<style>
#cat_link{
	padding: 5px !important;
}
#side_badge{
	margin-top: 2px;
}
.side{
	margin-bottom: 2px;
}
</style>