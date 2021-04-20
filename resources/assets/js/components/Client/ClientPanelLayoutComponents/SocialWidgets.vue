<template>

	<ul class="list-inline hidden-print">

		<li v-for="widget in social_widgets" class="list-inline-item">

			<a v-if="widget.name=='facebook'" :href="widget.value" class="btn btn-social btn-facebook" target="_blank"><i class="fab fa-facebook-f fa-fw"></i></a>
			
			<a v-if="widget.name=='google'"  :href="widget.value" class="btn btn-social btn-google-plus" target="_blank"><i class="fab fa-google-plus-g fa-fw"></i></a>
			
			<a v-if="widget.name=='twitter'"  :href="widget.value" class="btn btn-social btn-twitter" target="_blank"><i class="fab fa-twitter fa-fw"></i></a>
			
			<a v-if="widget.name=='linkedin'"  :href="widget.value" class="btn btn-social btn-linkedin" target="_blank"><i class="fab fa-linkedin fa-fw"></i></a>
			
			<a v-if="widget.name=='vimeo'"  :href="widget.value" class="btn btn-social btn-vimeo" target="_blank"><i class="fab fa-vimeo-square fa-fw"></i></a>
			
			<a v-if="widget.name=='youtube'"  :href="widget.value" class="btn btn-social btn-youtube" target="_blank"><i class="fab fa-youtube fa-fw"></i></a>
			
			<a v-if="widget.name=='pinterest'" :href="widget.value" class="btn btn-social btn-pinterest" target="_blank"><i class="fab fa-pinterest fa-fw"></i></a>
			
			<a v-if="widget.name=='dribbble'"  :href="widget.value" class="btn btn-social btn-dribbble" target="_blank"><i class="fab fa-dribbble fa-fw"></i></a>
			
			<a v-if="widget.name=='flickr'"  :href="widget.value" class="btn btn-social btn-flickr" target="_blank"><i class="fab fa-flickr fa-fw"></i></a>
			
			<a v-if="widget.name=='instagram'"  :href="widget.value" class="btn btn-social btn-instagram" target="_blank"><i class="fab fa-instagram fa-fw"></i></a>
			
			<a v-if="widget.name=='rss'"  :href="widget.value" class="btn btn-social btn-rss" target="_blank"><i class="fas fa-rss fa-fw"></i></a>
			
			<a v-if="widget.name=='skype'"  :href="widget.value" class="btn btn-social btn-facebook" target="_blank"><i class="fab fa-skype fa-fw"></i></a>
			
			<a v-if="widget.name=='deviantart'"  :href="widget.value" class="btn btn-social btn-facebook" target="_blank"><i class="fab fa-deviantart fa-fw"></i></a>
			
			<a v-if="widget.name=='stumble'" :href="widget.value" class="btn btn-social btn-pinterest" target="_blank"><i class="fab fa-stumbleupon fa-fw"></i></a>
		</li>
	</ul>
</template>

<script>
	
	export default {

		name : 'client-panel-social-widgets',

		description : 'Client panel social widgets component',

		props : {

			layout : { type : Object, default : ()=>{}}
		},

		data(){

			return {

				social_widgets : this.layout.social_widgets,

				footerButtonStyle : {
					
					borderColor : this.layout.portal.client_button_border_color,
					
					backgroundColor : this.layout.portal.client_button_color
				},
			}
		}
	};
</script>

<style scoped>

	.btn-social {
    padding: 4px 5px !important;
    border-color: #ccc !important;
    border-radius: 1em !important;
	}

	.list-inline-item:not(:last-child) {
    margin-right: 1px !important;
	}
</style>