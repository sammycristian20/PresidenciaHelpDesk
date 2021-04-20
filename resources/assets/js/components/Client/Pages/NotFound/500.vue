<template>
	
	<div class="row">

		<meta-component :dynamic_title="lang('oops_page_not_found')" :layout="layout"></meta-component>

		<div id="content" class="site-content col-md-8 offset-md-2">

			<article class="hentry error500 text-center">
				
				<h1 class="error-title">5<i class="far fa-frown text-danger" :style="linkStyle"></i><span class="visible-print text-danger"></span><i class="far fa-frown text-danger" :style="linkStyle"></i><span class="visible-print text-danger"></span></h1>
				
				<h2 class="entry-title text-muted">{{lang('sorry_something_went_wrong')}}</h2>
								
				<div class="entry-content clearfix">
					
					<p><router-link to="/" replace :style="linkStyle">{{ lang('go_to_home') }}</router-link></p>

				</div>
			</article>
		</div>
	</div>
</template>
<script>
export default {

	name : 'bad-request-component',

	description : '500 Error Component',

	props : { 

		layout : { type : Object, default : ()=>{}},
	},
	
	data () {

		return {

			linkStyle : { color : this.layout.portal.client_header_color + ' !important'}
		}
	}
};
</script>
<style scoped>
	
	.error-title{
		margin-bottom: 0px !important
	}
</style>