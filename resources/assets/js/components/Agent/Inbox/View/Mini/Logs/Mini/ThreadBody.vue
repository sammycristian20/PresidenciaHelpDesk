<template>
	
	<div>
	
		<i :class="thread.thread_type_icon_class" v-tooltip="thread.thread_type_text"></i> 
	
		<div class="timeline-item">
	
			<span class="time thread_right">
				
				<span v-tooltip="lang('reply_rating')" class="thread_rating">

					<rating-component v-for="rating in ratings" :key="rating.id" :rating="rating" :labelStyle="labelStyle">
					
					</rating-component>
				</span>
				
				<span v-if="ratings.length > 0" class="mt_4">&nbsp;&#124;&nbsp;&nbsp;</span>

				<a v-if="thread.is_internal !== 1" class="pointer" v-tooltip="lang('reply_to_this_thread')"
					@click="replyThread(thread.body,thread.user.name,thread.created_at)">

					<i class="fas fa-reply"></i>
				</a>
			</span>
			
			<h3 class="timeline-header">
				
				<img :src="thread.user.profile_pic" class="img-circle img-bordered-sm" alt="User Image" width="25" 
					height="25" id="thread_img">

				<a :href="basePath()+'/user/'+thread.user.id" v-tooltip="thread.user.name">{{thread.user.name}} </a>

				<span class="thread_time"> - <i class="far fa-clock"></i> {{formattedTime(thread.created_at)}}</span>
			</h3>

			<div class="timeline-body">
								
				<span v-html="thread.body" id="thread_desc"></span>
			</div>

			<div v-if="thread.attach.length > 0" class="timeline-footer">

				<ul class="mailbox-attachments align-items-stretch clearfix">

					<template v-for="(attachment,index) in thread.attach">
						
						<template v-if="attachment.poster=='ATTACHMENT'">
							
							<attachment :attachment="attachment"></attachment>
						</template>
					</template>
				</ul>
			</div>
		</div>
	</div>
</template>

<script>
	
	import { mapGetters } from 'vuex';

	export default { 

		name : 'thread-body',

		description : 'Thread Body Component',

		props : {

			thread : { type : Object, default : ()=> {} },

			index : { type : String | Number, default : '' },
		},

		data() {

			return {

				ratings:[],

				labelStyle : { display : 'none' }
			}
		},

		computed:{

			...mapGetters(['formattedTime','getRatingTypes'])
		},

		beforeMount() {
			
			this.ratingTypes(this.getRatingTypes)
		},

		methods : {

			replyThread(data,name,date) {

				const quote = 'On ' + this.formattedTime(date) + ' ' +'<b>'+ name + '</b> ' + 'wrote :';
				
				var content;

				content = "<p>&nbsp;</p><blockquote>"+quote+"<figure  id='mine'>"+data+"</figure></blockquote>";

				this.thread['content'] = content;

				window.eventHub.$emit('threadReply', this.thread);
			},

			ratingTypes(types) {

				this.ratings = types;

				var ratingArr=[];

				if(this.thread.ratings.length != 0){

					for(var i in this.ratings){

						for(var j in this.thread.ratings){

							if(this.ratings[i].id == this.thread.ratings[j].rating_id){

								if(this.ratings[i].rating_area == 'Comment Area'){

									this.ratings[i]['rating_value']=this.thread.ratings[j].rating_value;

									this.ratings[i]['ticket_id']=this.thread.ticket_id;

									ratingArr.push(this.ratings[i])

								}
							}
						}
					}
				}
				this.ratings=ratingArr;
			},
		},

		components : {

			'rating-component':require('components/MiniComponent/RatingComponent'),

			'attachment':require('components/MiniComponent/AttachmentBlock'),
		}
	};
</script>

<style scoped>

#thread_img{ margin-top: -3px; }

.pointer { cursor: pointer;color: #999 !important; }

.thread_rating { margin-top: -13px; }

.thread_right { display: flex; margin-top: 5px; }

.mt_4 { margin-top: -4px; }

.rating_dropdown { 
	background: transparent !important;
	 border: none !important;
	 box-shadow: unset !important;
	 margin-top: -5px;
	 font-size: 14px !important;
}

.rating_body {

	width: max-content;
	 left: unset !important;
	 right: -1px !important;
}

.thread_time { 
	font-size: 12px;
    color: #999999;
}
</style>

<style>
	
	blockquote p { font-size: 14px !important; }
</style>