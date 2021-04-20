<template>

		<div>

			<meta-component :dynamic_title="lang('account-activation-title')" :layout="layout"
				:dynamic_description="lang('account-activation-description')" >
				
			</meta-component>

			<div id="content" class="site-content col-md-12 " >
				<!-- alert message which only gets mounted when vuex has non empty alert values -->
				<alert componentName="activateEmail"/>

				<div v-if="loading">
					<loader :animation-duration="4000" :color="layout.portal.client_header_color" :size="50"/>
				</div><br>

			</div>         
		</div>
</template>

<script>

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import {mapGetters} from 'vuex'

	import axios from 'axios'

	export default {

		name : 'activate-email',

		description : 'Email Activation Component',

		props : {

			layout : { type : Object, default : ()=>{}}
		},

		data() {
			return {

				// loader status
				loading:true,
				
				// for language locale
				lang_locale : this.layout.language,

				hash:''

			}
		},

		beforeMount(){

			this.getResponse();
		},

		methods: {
			
			getHashFromUrl(url){
		    
		    let urlArray = url.split("/");
		    
		    return urlArray[urlArray.length - 1];
			},

			getResponse(){
				
				const path = window.location.pathname;
		   	
		   	this.hash = this.getHashFromUrl(path);
				
				this.$Progress.start();
				
				this.loading=true;
				
				axios.post('/api/account/activate/'+this.hash).then(response =>{
				
					this.$Progress.finish();
				
					this.loading=false;
				
					successHandler(response,'activateEmail');
				
					setTimeout(()=>{
				
						this.$router.push({ path:'/auth/login',name: 'Login'});
				
					},6000)
				
				}).catch(error=>{
				
					this.loading=false;
				
					this.$Progress.fail();
				
					errorHandler(error,'activateEmail')
				
					if(error.response.data.message.redirect_to){
				
						window.location.href = error.response.data.message.redirect_to
				
					}
				})
			}
		},


		components:{
		
		'alert' : require('components/MiniComponent/Alert'),
		
		},
	};
</script>
<style scoped>
#h3 {
	margin-top: 0px;
	margin-bottom: 20px;
}
#or{
	margin :auto !important;
}
</style>
