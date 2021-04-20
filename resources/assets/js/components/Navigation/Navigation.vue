<template>

	<li v-if="!navigation.hasChildren" class="nav-item">
					
		<a :class="['nav-link',{'active active-navigation-element' : active}]"
			:href="getLink(navigation)" @click="onNavigationClick(navigation)">
							
			<i :class="'nav-icon '+navigation.iconClass"></i>
							
			<p> {{(navigation.hasCount) ? subStr(navigation.name,16) : navigation.name}}
									
				<span v-if="boolean(navigation.hasCount)"class="right badge badge-success" id="nav_count">{{navigation.count}}

				</span>
			</p>
		</a>
	</li>

	<li v-else :class="['nav-item has-treeview',{'menu-open' : menuOpened}]">
							
		<a href="javascript:;" :class="['nav-link',{'active' : menuOpened }]" id="nav_child">
								
			<i :class="'nav-icon '+navigation.iconClass"></i>
								
			<p>{{navigation.name}}
					
				<i class="right fas fa-angle-left"></i>
			</p>
		</a>
							
		<ul class="nav nav-treeview" :style="[menuOpened ? {'display': 'block !important'} : {'display': 'none !important'}]">
								
			<navigation v-for="(nav, index) in navigation.children" :navigation="nav" :key="index"
				:toggleParent="toggleActive">

			</navigation>
		</ul>
	</li>
</template>

<script>

import axios from "axios";

import { boolean,getSubStringValue } from 'helpers/extraLogics';

export default {

	name : 'navigation',

	props:{

		navigation : { type : Object, default :()=>{}},

		toggleParent : {type : Function, default : ()=>{} },
	},

	data(){
		
		return {
		
			menuOpened : false,
		
			active : false,
		}
	},

	mounted(){
    	
    	this.markNavigationActiveIfRequired()
  	},

	methods : {

		/**
     	* Marks active and menuOpened as true.
     	* NOTE: this method is passed to children so that chilren can call this method and mark parent as active
     	* @return {undefined}
     	*/
    	toggleActive(){
      
      	this.active = true;
      
      	this.menuOpened = true;
      
      	this.toggleParent();
    	},

		isActive(navigation){
			
			if(this.getCurrentRouteUrl() == navigation.redirectUrl ){

				return true
			}
		},

		/**
		 * Redirects if has no child elements
		 * @param  {String} redirectUrl
		 * @return {String}
		 */
		onNavigationClick(navigation){

			// for logout
			if(navigation.routeString == 'auth/logout'){

				axios.post('/auth/logout').then(res=>{

					 window.location.replace(res.data.data);

				}).catch(error=>{})
			}

		 // if hasChildren is false, then redirect
      	if(boolean(navigation.hasChildren)){
        		
        		this.menuOpened = !this.menuOpened;
      	}
		},

		 /**
	     * Gets redirect link for the anchor tag
	     * @return {String}
	     */
	    getLink(navigation){
	      
	      if(!boolean(navigation.hasChildren)){
	      
	        return navigation.redirectUrl;
	      }
	      return 'javascript:void(0);';
	    },

		/**
		 * Gets current url
		 * @return {String}
		 */
		getCurrentRouteUrl(){
			return window.location.href;
		},


		subStr(name,count) {
			return getSubStringValue(name,parseInt(count));
		},

		/**
	     * Checks if navigation is active, if yes mark that as active
	     * @return {undefined}
	   */
    	markNavigationActiveIfRequired(){
      
      	if(this.getCurrentRouteUrl() == this.navigation.redirectUrl){
        	
        		this.active = true;
        		
        		this.toggleParent();
      	}
    	},
	},

	watch : {
    	
    	active(newVal){
      	
      	if(newVal){
        	// waiting for the DOM to render completely so that active-navigation-element can be present
        		setTimeout(()=>{

          		let activeElements = document.getElementsByClassName('active-navigation-element');
          		
          		if(activeElements !== undefined){
            		
            		activeElements[activeElements.length - 1].scrollIntoView({behavior: "smooth"});
          		}
        		},10)
      	}
      	
      	return newVal;
    	}
  	}
};
</script>
