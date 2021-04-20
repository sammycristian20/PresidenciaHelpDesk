<template>
  	
  	<navigation-layout>
	
		<nav class="mt-2">
			
			<div v-if="loading" class="admin-navigation">

				<loader></loader>
			</div> 

			<ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" 
				data-accordion="false">

				<!-- for loop from here itself -->
						
				<template v-for="(navigationCategory, index) in navigationArray">

					<li class="nav-header">{{navigationCategory.name.toUpperCase()}}</li>

					<template v-for="(navigation, index) in navigationCategory.navigations">
				              
				      <navigation v-if="navigation.name" :navigation="navigation" :key="navigation.name">

				      </navigation>
				   </template>
				</template>
			</ul>
		</nav>
	</navigation-layout>
</template>

<script>

	import axios from 'axios';
	
	import {errorHandler} from 'helpers/responseHandler';
	
	import NavigationLayout from './NavigationLayout';

	export default {
  	
  		name : 'admin-navigation-bar',

  		description : 'Admin Navigation Bar on admin panel',

  		data(){
	 		
	 		return {
				
				navigationArray : [],
				
				loading: true,
	  		}
  		},
 	
 		created() {
	 	
	 		window.eventHub.$on('update-sidebar', this.refreshSidebar);
  		},

  		beforeMount(){
			
			// this.loading = true;
			
			this.getDataFromServer();
  		},

  		methods:{

	 	/**
	  	* Gets data from server and populate in the component state
	  	* NOTE: Making it a diffent method to improve readablity
	  	* @return {Promise}
	  */
	 	refreshSidebar(){
			
			this.getDataFromServer();
	 	},

	 	/**
	  	* Gets data from server and populate in the component state
	  	* @return {Promise}
	  	*/
	 	getDataFromServer(){
			
			axios.get("/api/admin/navigation").then(res => {

				this.loading = false;
				
		  		this.navigationArray = res.data.data

			}).catch(err => {

				this.loading = false;

		  		errorHandler(err);

			});
	 	}
  	},

  	components: {
		
		'navigation' : require('components/Navigation/Navigation'),
	 	
	 	'navigation-layout' : NavigationLayout,
	 	
	 	'loader': require('components/Client/Pages/ReusableComponents/Loader'),
  	},
};
</script>

<style>
	
	.admin-navigation{
	    margin-top : 200px !important;
	  }
</style>
