<template>

		<div  class="box-container">
			<div :class="headerClass">
				<h3 class="box-title">{{lang(title)}}</h3>
			</div>
			<div class="box-body">
				<div class="row">
					<slot name="fields"></slot>
				</div>
			</div>
		</div>

</template>

<script>
	export default {
		props:{
			/**
			 * box header class from parent component
			 * @type {String}
			 */
			headerClass:{type:String},
			/**
			 * box title
			 * @type {String}
			 */
			title:{type:String}
		}
	};
</script>

<style scoped>
 input .form-control{
        border-radius : 30px !important
    }

    .box-header.with-border{
        padding-bottom:4px;
        background-color: #eee;
    }

    .box-container{
        margin: 10px 10px 20px 10px;
        border: 1px solid #ddd;
    }

    .with-switch{
        padding-bottom: 0px !important;
        margin-bottom:-3px;
    }

    .with-switch >h3{
        margin-top: 2px !important;
    }

    .box-header >h3{
        font-family: Tahoma;
    }

    .update-btn{
        margin:20px 0 30px 15px;
    }

    .pointer-none{
        pointer-events:none;
    }
</style>