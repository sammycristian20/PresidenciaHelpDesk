<template>
	
	<transition name="page" mode="out-in">
	
	<div>
	
		<div :class="classname">
	
			<div class="widget-area">
		
				<section id="section-tags" class="section" :class="{tagsidebar: lang_locale == 'ar'}">
					
					<template v-if="tagsList">
						
						<h2 class="section-title h4 clearfix" >

							<i class="line" :style="lineStyle"></i>{{ lang('tags') }}

							<small :class="{left: lang_locale == 'ar'}" class="pull-right">

								<i class="fa fa-hdd-o fa-fw"></i>
							</small>
						</h2>

						<div class="tagcloud">
							
							<template v-for="tag in tagsList.slice(0,10)">
								
								<router-link :title="tag.name" :to="{ name:'Tags',params:{tag_id:tag.id}}" 
								class="btn btn-tag btn-sm tag">
									
									{{ subString(tag.name)}}
								</router-link>
							</template>
						</div>
					</template>

					<template v-else>
						
						<div class="row" style="margin-top:30px;margin-bottom:30px">
						
							<loader :color="layout.portal.client_header_color" :animation-duration="4000" :size="30"/>
						</div>
					</template>
				</section>
			</div>
		</div>
	</div>
</transition>
</template>

<script>
	
	import { getSubStringValue } from 'helpers/extraLogics';
	
	import axios from 'axios'
	
	export default {
	
		name : "tags-sidebar",
	
		description : "This component shows the top 10 tags in the tags list ",
	
		props:{
			
			classname:{type:String},
	
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
	
				lang_locale : this.layout.language,
	
				tagsList : ''
			}
		},
	
		beforeMount(){
	
			this.getTags()
		},
	
		methods : {
	
			getTags(){
	
				axios.get('/api/tag-list').then(res=>{
	
					this.tagsList = res.data.data.tags;
	
				}).catch(error=>{
					
					this.tagsList = ''
				})
			},

			subString(value){
			
				return getSubStringValue(value,20)
			}
		}
	};
</script>

<style>
	.side{
		margin-bottom: 2px;
	}
	.tagsidebar{
		direction :rtl;
	}
	.tag {
		margin-right: 4px;
	}
</style>