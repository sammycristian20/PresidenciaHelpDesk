<template>

		<div>
			
			<meta-component :dynamic_title="lang('home')" :dynamic_description="lang('home-page-description')" :layout="layout" >
				
			</meta-component>

			<div id="content" class="site-content col-md-12">
			
				<alert componentName="home"/>
				
				<div class="login100-form-avatar mb-4">
					<div class="logo_presidencia"></div>
				</div>
				<h1 class="py-4 text-center text-white" style="font-weight:400;">prueba</h1>
				<div class="col">
					<div class="row justify-content-center">
						<div class="col-4">
							<button  class="login100-form-btn red" style="max-width:400px; margin:0 auro;" @click="onRegister" :style="buttonStyle" :disabled="isDisabled">
								{{ lang('create_an_account') }}
							</button><br>
						</div>
					</div>
				</div>

				<!-- <widget-component :layout="layout" :auth="auth"></widget-component>  -->

			</div> 
		</div>
</template>
<script>

export default {
	
	name : 'home-page',

	description : 'Client panel home page Component',

	props : {

		layout : { type : Object, default : ()=>{}},

		auth : { type : Object, default : ()=>{}}
	},

	components:{

		'widget-component': require('components/Client/ClientPanelLayoutComponents/WidgetBoxComponent'),
		
		'alert' : require('components/MiniComponent/Alert'),
		
	},

	created(){
		this.$Progress.start();
	},

	mounted(){
		this.$Progress.finish();
	},

};
</script>