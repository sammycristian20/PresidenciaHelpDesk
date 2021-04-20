<template>
	
	<div id="logo" :class="{float: layout.language == 'ar'}" class="navbar-brand brand site-logo text-center">

			<router-link  v-if="layout.company.use_logo == '1'" to="/" rel="home" id="logo_img">

				<img :src="layout.company.logo" alt="User Image" width="200px" height="200px">
			</router-link>
			
			<router-link to="/" id="logo_title" v-else style="color: #FFFFFF">
				{{layout.system.name !== '' ? layout.system.name : 'SUPPORT CENTER'}}
			</router-link>
		</div>
</template>

<script>
	
	export default {

		name : 'client-panel-logo',

		description : 'Client panel logo component',

		props : {

			layout : { type : Object | String, default : ()=>{}},
		},

		data() {

			return {

				link_color : ''
			}
		},

		beforeMount(){

			this.link_color = this.layout.portal.client_header_color
		}
	};
</script>

<style scoped>
	
	#logo_title {
		font-size: 32px !important;
		font-weight : 600 !important;
		text-decoration: none !important;
	}

	#logo{
		margin-bottom: 10px !important;
	}
</style>