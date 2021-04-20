<template>
	
	<div class="sidebar">
		
		<div class="user-panel mt-3 pb-3 mb-3 d-flex">
			
			<div class="image">

				<faveo-image-element id="sidebar-profile-img" :source-url="profilePic" :classes="['img-circle elevation-2']" 
					alternative-text="User Image"/>
        </div>

        <div class="info">
          <a class="d-block" :title="fullName" :href="basePath()+'/profile'">{{subString(fullName)}}</a>
        </div>
		</div>

		<slot></slot>
	</div>
</template>

<script>

	import { getSubStringValue } from 'helpers/extraLogics'

	export default {

		computed : {
			
			sidebarStyle(){
				
				if(this.isRtlLayout){
					
					return {'margin-right': '-8px'}
				}
					
					return {'margin-left': '-8px'}
			},

			fullName(){
				
				return sessionStorage.getItem('full_name');
			},

        	profilePic(){
            return sessionStorage.getItem('profile_pic');
        	}
    	},

		methods : {

			subString(value){

				return getSubStringValue(value,20)
			},
		},

		components: {
			
			'faveo-image-element': require('components/Common/FaveoImageElement')
		}
	};
</script>