<template>
	
	<div class="row" v-if="footer_list.length>0">
	
		<template v-for="footer in footer_list">

			<div class="col-md-3" :class="{float: language == 'ar'}">
				
				<div>
					
					<section id="section-about" class="section">
						
						<h2 v-if="footer.title && footer.title.trim()" class="section-title h4 clearfix" :title="footer.title" 
							:class="{align1: language == 'ar'}">

							<i class="line" :style="footerStyle"></i>{{ subString(footer.title) }}
						</h2>
							
						<div class="ck-content textwidget" style="word-wrap: break-word;overflow-x: auto;" v-html="footer.value" :class="{align1: language == 'ar'}">
								
						</div>
					</section><!-- #section-about -->
				</div>
			</div>
		</template>

		<div class="clearfix"></div>
			
		<hr>
	</div>
</template>

<script>
	
	import { getSubStringValue } from 'helpers/extraLogics';

	export default {

		name : 'client-panel-footer',

		description : 'Client panel footer component',

		props : {

			layout : { type : Object, default : ()=>{}},
		},

		data(){

			return {

				language : this.layout.language,

				footer_list : this.layout.footer,

				footerStyle: { borderColor: this.layout.portal.client_header_color},
			}
		},

		methods : {

			subString(value){
				return getSubStringValue(value,20)
			}
		},
	};
</script>

<style>
	caption {
   
    caption-side: top !important;
	}
</style>