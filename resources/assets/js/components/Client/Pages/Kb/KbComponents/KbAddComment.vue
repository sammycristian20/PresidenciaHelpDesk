<template>

	<div id="respond" class="comment-respond form-border">
		
		<alert/>

		<h3 id="reply-title" class="comment-reply-title section-title">

			<i class="line" :style="lineStyle"></i>{{ lang('leave_a_reply') }}
		</h3>
		
		<form-with-captcha btnClass="btn btn-custom btn-lg float-right" :btnStyle="buttonStyle" 
			btnName="post_comment" iconClass="fa fa-save" :formSubmit="postMessage" 
			page="comments_page" componentName="KbComment">

			<div class="row" slot="fields">
						
				<div class="col-sm-4"  v-if="Array.isArray(auth.user_data)">
							
					<text-field :label="lang('name')" :value="name" type="text" name="name"
						:onChange="onChange" :required="true" :inputStyle="inputStyle">
								
					</text-field>

					<text-field :label="lang('email')" :value="email" type="text" name="email"
						:onChange="onChange" :required="true" :inputStyle="inputStyle">
							
					</text-field>

					<text-field :label="lang('website')" :value="website" type="text" name="website"
						:onChange="onChange" :inputStyle="inputStyle">
							
					</text-field>
				</div>

				<div v-else  class="col-sm-4" >

					<div class="banner-wrapper user-data text-center clearfix" id="ban_ner" :style="borderStyle">

						<faveo-image-element id="user_avatar" :source-url="image" :classes="['avatar']" alternative-text="User image" :img-height="70" :img-width="70"/>

						<div><strong>{{lang('hello')}}</strong></div>
					
						<p class="banner-title ellipsize_first_name h4" :title="name">{{name}}</p>
						
						<p>If you are not? </p>

						<div class="banner-content" id="dropdown_content">
							
							<button class="btn btn-custom btn-sm text-white logout_btn" :style="buttonStyle" @click="logout()">
								{{ lang('log_out') }}

							</button>
						</div>
					</div>	
				</div>

				<div class="col-sm-8">
							
					<text-field :label="lang('comment')" :value="comment" type="textarea" name="comment"
						:onChange="onChange" :required="true" :columns="30" :rows="8" 
						:inputStyle="inputStyle">
							
					</text-field>
				</div>
			</div>
		</form-with-captcha>
	</div>
</template>
<script>

	import axios from 'axios'
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import { kbCommentValidation } from "helpers/validator/kbCommentRules";
	
	export default {
		
		name : "kb-add-comment",

		description : "This component shows the comment box for the article",

		props : {
			 
			 slug : { type : String, default : ''},

			 auth : { type :  Object, default : ()=>{}},

			 layout : { type : Object, default : ()=>{}}
		},

		data() {
		
		return {
		
			base : this.auth.system_url,
		
			lineStyle: {
				
				borderColor : this.layout.portal.client_header_color,
			},
			
			buttonStyle: {
				
				borderColor : this.layout.portal.client_button_border_color,
				
				backgroundColor : this.layout.portal.client_button_color
			},

			borderStyle : { 
				border : '1px solid ' + this.layout.portal.client_header_color,
				borderTopWidth : '5px'
			},

			inputStyle: {
			
				borderColor : this.layout.portal.client_input_field_color
			},

			name : '',

			email : '',

			comment : '',

			website : '',
			
			path : '',
			
			image:'',
			
			lang_locale : this.layout.language,

			loginData : this.auth,
		}
	},

	beforeMount(){

		if(!Array.isArray(this.auth.user_data)){

			this.name = this.auth.user_data.first_name;

			this.email = this.auth.user_data.email;

			this.image = this.auth.user_data.profile_pic;
		}
	},

	components : {

		 "text-field": require("components/MiniComponent/FormField/TextField"),

		 'faveo-image-element': require('components/Common/FaveoImageElement'),

		 "form-with-captcha": require("components/Common/Form/FormWithCaptcha.vue"),
	},

	created() {

		this.path=location.pathname.split('/');
	},

	methods: {

		isValid() {

			const { errors, isValid } = kbCommentValidation(this.$data);
				
			if (!isValid) {
				
				return false;
			}
			return true;
		},

		logout(){

			this.$Progress.start();

			axios.post('/auth/logout').then(res=>{

				localStorage.setItem('redirectPath','');

				this.$Progress.finish();

				this.redirect('/');

			}).catch(error=>{

				this.$Progress.fail();

				this.redirect('/');
			})
		},

		onChange(value,name){

			this[name] = value;
		},

		postMessage(key,value) {
			
			if(this.isValid()){

				this.$Progress.start();

				const slug = this.path[this.path.length-1];
			
				const data={};
				
				data['name']=this.name;
				
				data['email']=this.email;
				
				data['website']=this.website;
				
				data['comment']=this.comment;

				data[key] = value;
				
				axios.post('/postcomment/'+slug,data).then(res=>{
					
					this.comment='';

					if(Array.isArray(this.auth.user_data)){

						this.name = '';

						this.website = '';

						this.email = '';
					}
					
					successHandler(res,'KbComment')
					
					window.eventHub.$emit('forComment',this.slug);

					this.$Progress.finish();

				}).catch(error=>{

					errorHandler(error,'KbComment');

					if(error.response.data.message['g-recaptcha-response']) {

						this.$store.dispatch('setAlert',{
							type:'danger',message:'Invalid ReCaptcha', component_name : 'KbComment'
						})
					}

					this.$Progress.fail();
				})
			}
		},
	}
};
</script>

<style scoped>

	#dropdown_content{ : 0 !important; }

	#user_avatar{
		border-radius: 35px;
		height: 55px !important;
	}

	#ban_ner{ margin-top: 5px; }

	#reply-title{ text-align: left; }

	.logout_btn { padding-bottom: 4px !important; }
</style>