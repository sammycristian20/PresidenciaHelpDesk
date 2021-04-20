<template>
	
	<div>

		<div class="timeline mt-20">

			<div id="article_div">
									
				<div class="timeline-item" id="article_item">
				  
				  <span class="time"><i class="far fa-clock"></i> {{publishDate(article.publish_time)}}</span>

				  <h3 class="timeline-header">

						<a :href="basePath()+'/show/'+article.slug" v-tooltip="article.name">{{article.name}}</a> 

						<i class="fas fa-question-circle cursor text-primary"  v-tooltip="lang('click_to_see_details')" 
							v-popover="{ name: 'article' + article.id }">
									
						</i>
					</h3>

					<popover :name="'article'+article.id" :pointer="true" event="click">
			
						<div class="p-3">
	                	
	                	<template v-if="article.author">
	                		
	                		<strong><i class="fas fa-user mr-1"> </i> {{lang('author')}} </strong> &nbsp;

			                <p class="text-muted" v-tooltip="article.author.full_name ? article.author.full_name : article.author.user_name"> {{article.author.full_name ? article.author.full_name : article.author.user_name}}</p>

			               <hr>
	                	</template>

	                	<template v-if="article.categories.length > 0">
	                		
	                		<strong><i class="fas fa-newspaper mr-1"> </i> {{lang('categories')}} </strong> &nbsp;

			                <p class="text-muted" v-for="(category,index) in article.categories.slice(0,20)" v-tooltip="category.name"> 

			                	<i class="far fa-circle "> </i> {{subString(category.name,25)}}
			                </p>

			               <hr>
	                	</template>

	                	<template v-if="article.tags.length > 0">
	                		
	                		<strong><i class="fas fa-tags mr-1"> </i> {{lang('tags')}} </strong> &nbsp;

			                <p class="text-muted" v-for="(tag,index) in article.tags.slice(0,20)" v-tooltip="tag.name"> 

			                	<i class="far fa-circle "> </i> {{tag.name}}
			                </p>

			               <hr>
	                	</template>
		             </div>
					</popover>

					<div class="timeline-body" v-html="convert(article.description)"></div>
				  
				 	<div class="timeline-footer bt-1">
					
						<a :href="basePath()+'/article/'+article.id+'/edit'" class="btn btn-xs btn-default text-dark" v-tooltip="lang('edit')">

							<i class="fas fa-edit"></i> {{lang('edit')}}
						</a>
					
						<a v-if="status" target="_blank" :href="basePath()+'/show/'+article.slug" class="btn btn-xs btn-default text-dark" 
							@click="viewArticle(article.id)" v-tooltip="lang('view')">

							<i class="fas fa-eye"></i> {{lang('view')}}
						</a>
					
						<a href="javascript:;" class="btn btn-xs btn-default text-dark" @click="deleteMethod(article.slug)"
							v-tooltip="lang('delete')">

							<i class="fas fa-trash"></i> {{lang('delete')}}
						</a>

						<span class="float-right">
							
							<a :href="basePath()+'/comment'" @click="pendingComments(article.id,article.name)" v-tooltip="lang('pending_comments')">

								<i class="far fa-comment-dots"></i>

								<span class="badge badge-danger article-badge">{{article.pending_comments_count}}</span>
							</a>&nbsp;&nbsp;

							 <a :href="basePath()+'/comment'" @click="allComments(article.id,article.name)" 
							 	v-tooltip="lang('total_comments')">
							
								<i class="far fa-comments"></i> ({{article.all_comments_count}}) {{lang('comments')}}
							</a>
						</span>
					</div>
				</div>
			</div> 
		</div>

		<transition name="modal">

			 <delete-modal v-if="showModal" :onClose="onClose" :showModal="showModal"
	        alert="ArticlesIndex" :apiUrl="'/article/delete/'+slug">

	     </delete-modal>
		</transition>
	</div>
</template>
<script>

	import Vue from 'vue'
	
	import { mapGetters } from 'vuex'

	import { getSubStringValue } from 'helpers/extraLogics'

	import moment from 'moment'
	
	export default {
		
		props : {

			article : { type : Object, default : () => {} },

			status : { type : String | Number | Boolean, default : 1 }
		},

		data () {

			return {

				slug : '',

				showModal : false,
			}
		},

		created(){

			this.$store.dispatch('commentFilterData',{});
		},

		computed: {
	
			...mapGetters(['formattedTime','timeFormat'])
		},

		methods: {

			publishDate(value) {

				let utcFormat = new Date(value+' GMT').toString();

				return moment(utcFormat).format(this.timeFormat)
			},

			subString(value,length = 10){

				return getSubStringValue(value,length)
			},
	
			viewArticle(article) {
			
				this.$store.dispatch('setArticleId', article);
			},

			deleteMethod(value) {

				this.slug = value;

				this.showModal = true;
			},

			onClose() {

				this.showModal = false;
			},

			// showing only 250 characters in article description
			convert(x) {
		
				if(x){
				
					if(x.length>250){
					
						return x.replace(/(<\/?(?:a|p|blockquote)[^>]*>)|<[^>]+>/ig, '').substring(0,250) + '....';
			 	
			 		} else {
					
						return x.replace(/(<\/?(?:a|p|blockquote)[^>]*>)|<[^>]+>/ig, '');
					}
				}
			},

			allComments(id,name){
		
				const data = {to:'all',id:id ,name:name}
		
				this.$store.dispatch('commentFilterData',data);
			},
		
			pendingComments(id,name){
		
				const data = {to:'unapprove',id:id ,name:name}
		
				this.$store.dispatch('commentFilterData',data);
			}
		},

		components : {

			'delete-modal' : require('components/Agent/kb/KbDeleteModal')
		}
	};
</script>

<style scoped>

	#article_item { 
	/*	box-shadow: none !important;
    	border-top: 1px solid #dee2e6;
    	border-bottom: 1px solid #dee2e6;
    	border-radius: 0; */
    	margin-left: 2px !important;  margin-right: 4px!important; 
	}

	.bt-1 { border-top: 1px solid #dee2e6!important; }

	#article_div { margin-bottom: 0 !important; margin-right: 0 !important; }

	.article-badge { font-size: .6rem; font-weight: 300;  position: absolute; margin-left: -5px; }

	.cursor { cursor: pointer; }

	.vue-popover{ top:auto !important; left:auto !important;width: 250px !important; }

	.mt-20 { margin-bottom: 20px !important; }

</style>