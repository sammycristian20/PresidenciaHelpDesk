<template>
	
	<div>

		<a href="javascript:;"  ref="right_click_menu"  v-on:contextmenu=" openContextMenu($event)" v-tooltip="details.title"
			:class="{'fw-400': details.isanswered}">
			
			<span class="ticket-no" :data-pjax="details.id">[#{{details.ticket_number}}]&nbsp;&rlm;</span>
				
				{{subString(details.title,50)}}&nbsp;&rlm;(&lrm;{{details.thread_count}}) 
		</a>

		<a href="javascript:;" v-popover="{ name: 'details' + details.id }" @click="getPopData(details.id)">
		
			<i class="fas fa-question-circle" style="{fontSize:'medium', color:'#3c8dbc'}" 
				v-tooltip="lang('click_to_see_details')">
					
			</i>
		</a>
		<span v-if="details.attachment_count !== 0"> &nbsp;<i class="glyphicon glyphicon-paperclip"></i></span>

		<span v-if="details.collaborator_count !== null">&nbsp;<i class="fas fa-users"></i></span>

		<template v-for="(label,index) in details.labels">
								
			<a href="javascript:;" v-tooltip="lang(label.value)" class="badge"  
				:style="{'backgroundColor' : label.color, 'color': '#FFF'}">{{subString(label.value)}}
								
			</a>&nbsp;
		</template>

		<popover :name="'details'+details.id" :pointer="true" event="click">
			
			<template v-if="hasData">

				<div class="card card-light card-widget m_5">
              	
              	<div class="card-header pad-10">
                	
                	<div class="user-block">
                  	
                  	<faveo-image-element id="popover-img" :source-url="details.from.profile_pic" :classes="['img-circle']"/>
                  	
                  	<div> 
                  	<span class="username text-capitalize mb-6" 
                  		v-tooltip="popUpData.user_name ? popUpData.user_name : popUpData.email">
                  		
                  		<a href="javascript:;">
                  			{{popUpData.user_name ? popUpData.user_name : popUpData.email }}
                  		</a>
                  	</span>
                  	
                  	<span class="description fw-400">{{lang('created_at')}} :  {{formattedTime(popUpData.created_at)}}

                  	</span></div>
                	</div>
              	</div>
              
              	<div class="card-body pb-0 pad-10">
                	
                	<p v-html="popUpData.body"></p>
              	</div>
            </div>
			</template>

			<template v-else>
				
				<div class="row" id="load_margin">
					
					<loader :animation-duration="4000" color="#1d78ff" :size="30"/>
				</div>
			</template>
		</popover>
	</div>
</template>

<script>
	
	import { getSubStringValue } from 'helpers/extraLogics'

	import { mapGetters } from 'vuex'

	import axios from 'axios'

	export default {

		name : 'ticket-popover',

		props : {

			details : { type : Object, default : ()=> {} },

			tableHeader : { type : String, default : ''},

			onTicketClick : { type : Function }
		},

		data () {

			return {

				bgStyle : { background : this.tableHeader },

				hasData : false,

				popUpData : '',

				delay: 300,
				
				clicks: 0,
				
				timer: null
			}
		},

		computed:{
				
			...mapGetters(['formattedTime'])
		},

		methods : {

			subString(value,length = 10){

				return getSubStringValue(value,length)
			},

			getPopData(id) {

				this.hasData = false;

				axios.get('/ticket/tooltip?ticketid='+id).then(res=>{

					this.hasData = true;

					this.popUpData = res.data;

				}).catch(err=>{

					this.hasData = false;
				})
			},

			onClick(id){
				
				this.$store.dispatch('setTicketId', id);

				this.clicks++ 
				
				if(this.clicks === 1) {

					this.timer = setTimeout(()=> {
						
						this.clicks = 0

						this.onTicketClick(id)

					}, this.delay);
				} else{
					
					this.redirectMethod(id);
			 }        	
		  },

		  redirectMethod(id) {

		  		this.clicks = 0;

				clearTimeout(this.timer);  

				window.open(this.basePath()+'/thread/' + id, "_blank");
		  },

			openContextMenu(e){
				
				let titleEl=this.$refs.right_click_menu;
				
				titleEl.href = this.basePath()+"/thread/"+this.details.id;
			},
		},

		components : {

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'faveo-image-element': require('components/Common/FaveoImageElement')
		}
	};
</script>

<style scoped>
	
	.vue-popover{ width: 60% !important; top:auto !important; left:auto !important; }
	
	#load_margin { margin-top: 15px;margin-bottom: 15px; }

	.m_5 { margin : -5px; }

	.ticket_label{ font-size: 10px; }

	.mb-6 { margin-bottom: 6px; }

	.fw-400 { font-weight: 400; }

	.pad-10 { padding: 10px; }
</style>