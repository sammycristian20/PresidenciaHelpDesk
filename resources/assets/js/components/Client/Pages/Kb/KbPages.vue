<template>
	
	<div>
			
		<meta-component :dynamic_title="lang('pages-title')" :dynamic_description="lang('pages-description')" 
			:layout="layout">
				
		</meta-component>

		<div v-if="!hasDataPopulated || loading" class="row">

			<loader :color="layout.portal.client_header_color" :animation-duration="4000" :size="60"/>
		</div>
		
		<div v-if="hasDataPopulated" class="row">
			
			<div id="content" class="site-content col-sm-12">
						
				<article class="hentry">
					
					<header class="entry-header" :class="{align1 : lang_locale == 'ar'}">
						
						<h1 class="entry-title">{{name}}</h1>
					</header>

					<div class="entry-content ck-content clearfix" id="page_desc" :class="{align1 : lang_locale == 'ar'}">
						
						<p v-html="description" class="page-content"></p>					
					</div>
				</article>
			</div>
		</div>
	</div>
</template>

<script>
	export default {

		name : 'kb-pages',

		description : 'Knowledgebase pages component',

		props : {

			layout : { type : Object, default : ()=>{}}
		},

		data() {
			
			return {
			
				loading:true,

				hasDataPopulated : false,

				buttonStyle : {
			
					borderColor : this.layout.portal.client_button_border_color,
			
					backgroundColor : this.layout.portal.client_button_color
				},
			
				lineStyle: {
			
					borderColor : this.layout.portal.client_header_color,
				},

				location:'',

				path:'',

				name:'',

				description:'',

				lang_locale : this.layout.language,
				
				kb_status : this.layout.kb_settings.status,

				page_data:''
			}
		},

		beforeMount(){

			this.page_data = this.layout.pages;
			
			var result = this.page_data.map(a => a.slug);
			
			const includesSlug = result.includes(this.$route.params.slug);
			
			if(!includesSlug){
				
				this.$router.push({name:'NotFound'})
			} else {

				if(this.kb_status){
				
					this.pageData();

				} else {

					this.$router.push({name:'Home'})
				}
			}
		},
			
		methods: {
			
			pageData(){
			
				this.$Progress.start();
				
				this.loading= true;
				
				this.path=location.pathname.split('/');
				
				this.location = 'slug='+this.path[this.path.length-1]
				
				axios.get('page/show?'+this.location).then(res=>{
				
					this.$Progress.finish();
					
					this.loading = false;
					
					this.hasDataPopulated = true;

					this.name = res.data.data.name;
					
					this.description = res.data.data.description;

				}).catch(res=>{
					
					this.$Progress.fail();

					this.loading = false;

					this.hasDataPopulated = true;

				})
			}
		},

		components : {

			'client-panel-loader' : require('components/Client/ClientPanelLayoutComponents/ReusableComponents/Loader.vue'),
		}
	};
</script>
<style scoped>
	.banner{
		margin-top: 70px;
	}
	.page_align{
		direction: rtl;
	}
	#page_desc{
		overflow-x: scroll;
	}
	.entry-title{
		word-break: break-all;
	}
</style>
<style>
	.table{
		overflow-x: auto;
	}
	.page-content table,.page-content table td,.page-content table tr,.page-content table th {  
	  border: 1px solid #ddd;
	  text-align: left;
	}

	.page-content table {
	  border-collapse: collapse;
	  width: 100%;
	}

	.page-content table th, .page-content table td {
	  padding: 15px;
	}
</style>