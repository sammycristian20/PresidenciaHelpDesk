<template>

	<transition name="page" mode="out-in">

		<div :class="{login_align : layout.language === 'ar'}">
			
			<meta-component :dynamic_title="lang('auth-login-title')" :dynamic_description="lang('auth-login-description')" 
				:layout="layout" >
				
			</meta-component>

			<div id="content" class="site-content col-md-12 ng-scope">
				
				<alert componentName="page-login"/>

				<widget-component :layout="layout" :auth="auth"></widget-component>
				<loader v-if="isLoading"/>
				<template v-else>
					
					<login slot="fields" :layout="layout" :auth="auth"></login>
				</template>
			</div>
		</div>
	</transition>
</template>
<script>
	
	import { mapGetters } from 'vuex'
	import axios from 'axios'
	import { errorHandler, successHandler } from "helpers/responseHandler"

	export default {

		name : 'login-page',

		description : 'Login page component',

		props : {

			layout : { type : Object, default : ()=>{}},

			auth : { type : Object, default : ()=>{}}
		},
		data: () => {
			return {
				isLoading: false,
			}
		},
		beforeMount(){

			if(!Array.isArray(this.auth.user_data)){
				this.$router.push({name:'Home'})
			}
			const CURRENT_ROUTE = this.$router.currentRoute;

			if (CURRENT_ROUTE && CURRENT_ROUTE.name === 'SocialCallback') {
				this.isLoading = true;
				this.handleSocialCallback(CURRENT_ROUTE)
			}
		},
		methods: {
			handleSocialCallback(currentRoute) {
				axios.get(`api${currentRoute.fullPath}`)
					.then((res)=> {

						successHandler(res, 'page-login')

						let redirectUrl = localStorage.getItem('redirectPath')

						if(redirectUrl) {
							window.location.href = this.base + redirectUrl
						}

						redirectUrl = res.data.data.redirect_url
						
						//Ganda hai par dhandha hai ye
						if (redirectUrl != "/") {
							redirectUrl = "/"+redirectUrl;
						}

						this.redirect(redirectUrl)

						window.eventHub.$emit('login-success', res)

					})
					.catch((err) => {
						errorHandler(err, 'page-login')
						this.isLoading = false
					})
			}
		},
		components:{
			
			'widget-component': require('components/Client/ClientPanelLayoutComponents/WidgetBoxComponent'),

			'alert' : require('components/MiniComponent/Alert'),

			'login' : require('components/Client/Pages/Auth/MiniComponents/LoginComponent'),

			'loader': require('components/MiniComponent/Loader')
		},
	};
</script>

<style scoped>

	.login_align{
		direction: rtl;
	}
</style>