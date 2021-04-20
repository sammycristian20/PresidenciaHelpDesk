<template>

	<transition name="page" mode="out-in">

		<div id="categories">
			
			<meta-component :dynamic_title="lang('category-list-title')" :dynamic_description="lang('category-list-description')" :layout="layout">
				
			</meta-component>

			<div v-if="!hasDataPopulated || loading" class="row">

				<loader :color="layout.portal.client_header_color" :animation-duration="4000" :size="60"/>
			</div>

			<div v-if="hasDataPopulated">

				<div v-if="categoryList.length > 0">
				
					<div id="content cnt" class="site-content col-md-12">
				
						<category-box :layout="layout" classname="col-md-6 col-sm-12 col-xs-12"/>
					</div>
				</div>
				
				<div v-else>
				
					<h3 style="text-align:center">{{lang('no-data-to-show')}}</h3>
				</div>
			</div>
		</div>
	</transition>
</template>
<script>
	
	import axios from 'axios'
	
	export default {

		props : {

			layout : { type : Object, default : ()=>{}}
		},
		
		data() {
			
			return {
			
				categoryList:[],
				
				loading:true,

				hasDataPopulated : false,
				
				lang_locale : this.layout.language,
				
				kb_status : this.layout.kb_settings.status,
			}
		},

		beforeMount(){

			if(this.kb_status){
				
				this.getData();

			} else {

				this.$router.push({name:'Home'})
			}
		},
		
		methods:{
			
			getData(){

				this.loading=true;
				
				this.$Progress.start();
				
				axios.get('api/category-list-with-articles').then(response => {
				
					this.$Progress.finish();
				
					this.loading=false;

					this.hasDataPopulated = true;
				
					this.categoryList = response.data.data.categories.data;

				}).catch(error=>{
				
					this.$Progress.fail();
				
					this.loading=false;

					this.hasDataPopulated = true;
				})
			},	
		},

		components:{
			
			'category-box': require('components/Client/Pages/Kb/KbComponents/KbCategoryBox'),

			'client-panel-loader' : require('components/Client/ClientPanelLayoutComponents/ReusableComponents/Loader.vue'),
		},
	};
</script>
<style scoped>
	#cnt {
		margin-top:-25px;
	}
</style>