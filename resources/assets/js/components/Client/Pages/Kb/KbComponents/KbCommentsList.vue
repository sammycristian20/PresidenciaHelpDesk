<template>
	
	<li class="comment">
		
		<article class="comment-body" :class="{bod: lang_locale == 'ar'}">
			
			<footer class="comment-meta" :class="{meta: lang_locale == 'ar'}">
				
				<div class="comment-author">

					<faveo-image-element id="user_avatar" :source-url="comment.profile_pic" :classes="['avatar', lang_locale == 'ar' ? img : '']" alternative-text="User image" :img-height="50" :img-width="50"/>

					<b class="fn"><a rel="external" class="url" :style="linkStyle">{{comment.name }}</a></b>
				</div>

				<div class="comment-metadata">
					
					<small class="date text-muted">
						
						<time datetime="2013-10-23T01:50:50+00:00"> <i class="fa fa-clock-o"> </i> {{formattedTime(comment.created_at)}}</time>
					</small>
				</div>
			</footer>

			<div class="comment-content">
				
				<p v-html="convert(comment.comment)" style="word-wrap: break-word;"></p>
			</div>
		</article>
	</li>
</template>

<script>
	
	import axios from 'axios'
	
	import { mapGetters } from 'vuex'
	
	export default { 

		name : 'kb-comments',

		description : 'Knowledgebase comments list page',
	
		props:{
	
			comment : { type : Object, default : ()=>{}},
			
			layout : { type : Object, default : ()=>{}},

			base : { type : String, default : ''}
		},

		data() {

			return {

				lang_locale : this.layout.language,

				linkStyle : {

					 color : this.layout.portal.client_header_color
				}
			}
		},

		methods : {
		
			convert(x) {

				if(x){
					
					return $("<p />", { html: x }).text().replace(/(<\/?(?:img)[^>]*>)|<[^>]+>/ig, '$1');
				}
			},
		},

		computed: {
			
			...mapGetters(['formattedTime','formattedDate'])
		},

		components: {
			'faveo-image-element': require('components/Common/FaveoImageElement')
		}
	};
</script>
<style scoped>
	#com_time {
		margin-left : -3px;
	}
	.comment-author img{
		height: 50px !important;
	}
</style>