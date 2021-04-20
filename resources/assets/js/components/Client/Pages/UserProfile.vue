<template>

		<div class="nav-tabs-custom" :class="{pro_align : language === 'ar' }">
			
			<meta-component :dynamic_title="lang('client-profile-title')" :layout="layout"
				:dynamic_description="lang('client-profile-description')" ></meta-component>

			<alert componentName="edit_profile"/>	
			
			<ul class="nav nav-tabs">
			
				<li v-for="tab in tabs" class="nav-item">
			
					<a id="profile_tab" :class="{ active: current === tab.title, float1 : language === 'ar' }" 
					class="nav-link" data-toggle="tab" @click="onTabChange(tab.title)" href="javascript:;">
			
						{{lang(tab.title)}}
					</a>
				</li>
			</ul>
			
			<div class="tab-content">
			
				<div class="active tab-pane">
			
					<div class="row">
						
						<component :is="currentTabComponent" :layout="layout" :auth="auth"></component>

					</div>
				</div>
			</div>
		</div>
</template>

<script>

	export default{

		name : 'client-profile',

		description : 'Client profile component',

		props : {

			layout : { type : Object, default : ()=>{}},
			
			auth : { type : Object, default : ()=>{}},
		},

		data() {

			return {

				current : 'edit_profile',

				tabs : [{title:'edit_profile'},{title:'change_password'},{title:'2-step_verification'}],

				language : this.layout.language
			}
		},

		computed : {

			currentTabComponent(){
        
          return this.current === 'edit_profile' ? 'edit-profile' : this.current === 'change_password' ? 'change-password' : 'two-step'
        
        }
		},

		methods : {

     		onTabChange(value) {

				this.$store.dispatch('unsetAlert');

				this.current = value;
			},
     },

		components : {

			'alert' : require('components/MiniComponent/Alert'),

			'edit-profile' : require('components/Client/Pages/ProfileComponents/EditProfile'),
			
			'change-password' : require('components/Client/Pages/ProfileComponents/ChangePassword'),

			'two-step' : require('components/Client/Pages/ProfileComponents/TwoStepVerify'),
		}
	};
</script>

<style scoped>
	
	#profile_tab {

		cursor: pointer;
	}
	.pro_align{
		direction: rtl;
	}
</style>