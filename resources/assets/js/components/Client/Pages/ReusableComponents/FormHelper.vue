<template>

	<div class="col-md-6 offset-md-3 form-helper">
		
		<div class="login-box" style="width:auto;" valign="center">
		
			<div class="form-border">
		
				<slot name="title"></slot>
		
				<slot name="fields"></slot>
		
				<slot name="controls"></slot>
		
				<slot name="links"></slot>
			</div>
		</div>
	</div>
</template>

<script>

	export default {

	};
</script>

<style>
	h3 {	
		text-align: center;
	}
	.form-helper {
		margin-bottom: 50px !important;
		display: inline-block;
	}
</style>