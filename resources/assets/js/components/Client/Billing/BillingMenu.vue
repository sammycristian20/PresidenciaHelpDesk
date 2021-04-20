<template>
	
	<li class="nav-item dropdown" :class="[{'active': subIsActive('billing')},{float: language == 'ar'}]">
					
		<a class="dropdown-toggle nav-link"  id="navbarDropdown" role="button" 
			data-toggle="" aria-haspopup="true" aria-expanded="false" :class="{'active': subIsActive('billing')}">{{lang('billing')}} 
		</a>

		<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" :class="{nav_dropdown : language == 'ar'}">
						
			<li>
						
				<router-link class="dropdown-item" to="/billing-package-list" :class="{align: language == 'ar'}">
					{{ lang('purchage') }}
				</router-link>
			</li>
						
			<li v-if="!Array.isArray(auth.user_data)"> 

				<router-link class="dropdown-item" to="/billing-user-packages" :class="{align: language == 'ar'}">
					{{lang('my_packages')}}
				</router-link>
			</li>
				
			<li v-if="!Array.isArray(auth.user_data)">
							
				<router-link class="dropdown-item" to="/billing-user-invoices" :class="{align: language == 'ar'}">
					{{ lang('my_invoices') }}
				</router-link>
			</li>
		</ul>
	</li>
</template>

<script type="text/javascript">

	export default {

		props:{

			layout : { type : Object, default : ()=>{}},

			auth : { type : Object, default : ()=>{}},

			language : { type : String, default : ''},
		},

		methods : {

			subIsActive(input) {
		   	
		   	if(input === 'billing'){

		   		let page_path = this.$route.path.split('/');
		   		
		  		return page_path[page_path.length - 1].includes(input) 		
		   	
		   	} 
		  },
		}
	};
</script>

<style scoped>
	.sub-menu:hover{
		display:block !important;
	}
	#biling:hover ~ .sub-menu {
		display: block !important;
	}
</style>