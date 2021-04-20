<template>
	
	<div>
		
		<meta-component  :dynamic_title="lang('kb_tag')" :layout="layout">
				
		</meta-component>

		<kb-article-list :layout="layout" from="tag" :tag_id="tag_id"></kb-article-list>
	</div>
</template>

<script>
	
	export default {

		name : 'kb-tags-articles',

		description : 'Knowledgebase tags articles list page',

		props : {

			layout : { type : Object, default : ()=>{}}
		},

		data() {

			return { 
				
				tag_id : this.$route.params.tag_id
			}
		},

		components : {

			'kb-article-list' : require('./KbArticlesList.vue')
		}	
	};
</script>