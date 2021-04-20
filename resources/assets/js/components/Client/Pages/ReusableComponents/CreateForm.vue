<template>

	<div class="col-md-12" v-bind:class="{float: lang_locale == 'ar'}">

		<div class="box box-primary">

			<div class="box-header with-border">

				<slot name="title"></slot>
			</div>

			<div class="box-body">

				<slot name="fields"></slot>

				<slot name="controls"></slot>
			</div>

			<div class="box-footer">

				<slot name="links"></slot>
			</div>
		</div>
	</div>
</template>

<script>
	export default {

		props:{

			layout_data:{type:Object}
		},
		
		data() {
		
			return {
		
				lang_locale:'',
		
				buttonStyle: {
						borderColor:'',
						backgroundColor:''
					},
			}
		},
		created() {
		
			this.buttonStyle.borderColor   				= this.layout_data.portal.client_button_border_color;
			
			this.buttonStyle.backgroundColor   	= this.layout_data.portal.client_button_color;
			
			this.lang_locale = localStorage.getItem('LANGUAGE');
		},

	};
</script>

<style>
</style>