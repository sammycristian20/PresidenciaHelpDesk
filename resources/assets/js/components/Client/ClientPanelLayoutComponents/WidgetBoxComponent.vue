<template>
	
	<transition name="page" mode="out-in">
	
	<!--<div id="corewidgetbox" class="wid">

		<div id="wbox" :class="{box_align: lang_locale == 'ar'}" class="widgetrow text-center">
			
			<widget-box v-if="$route.path !== '/auth/register' && allow_register == 1 && data_type === false " route='/auth/register'  classname='register' :label="lang('register')" :layout="layout" :auth="auth"/>

			<widget-box v-if="$route.path === '/auth/register' && data_type === false " route='/auth/login'  classname='register' :label="lang('login')" :layout="layout" :auth="auth"/>
			
			<widget-box  v-if="allow_create_ticket || data_type" route='/create-ticket' classname='submitticket' :label="lang('submit_a_ticket')" :layout="layout" :auth="auth"/>
			
			<widget-box route='/mytickets' classname='mytickets' :label="lang('my_tickets')" :auth="auth" :layout="layout"/>
			
			<widget-box v-if="status" route='/knowledgebase' classname='knowledgebase' :label="lang('knowledge_base')" :layout="layout" :auth="auth"/>
		</div>
	</div>-->
	</transition>	
</template>

<script>

	export default {

		name : 'widget-component',

		description : 'Widget box component',

		props : {

			layout : { type : Object, default : ()=>{}},

			auth : { type : Object, default : ()=>{}}
		},

		components:{

		'widget-box': require('components/Client/ClientPanelLayoutComponents/WidgetBox'),
		},

		data() {
			return {

				allow_register : this.layout.user_registration.status,

				allow_create_ticket : parseInt(this.layout.allow_users_to_create_ticket.status),

				lang_locale : this.layout.language,

				status : this.layout.kb_settings.status,

				data_type : Array.isArray(this.auth.user_data) == true ? false : true,
			}
		},

		beforeMount(){

		},
	};
</script>
<style scoped>

	#wbox{
		margin-right:-5px !important;
		margin-bottom: 50px;
	}
	.wid {
		margin-top: 2em !important; margin-bottom: 1.5em !important;
	}
	.box_align {
		direction: rtl;
	}

</style>