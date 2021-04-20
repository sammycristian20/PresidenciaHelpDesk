<template>

	<div class="btn-group">
		
		
		<a id="status">
			<span title="Is_default" :class="{'btn btn-success btn-xs':default_val !== false,'btn btn-danger btn-xs':default_val === false}" id="edit_btn">{{default_val ? lang('yes') : lang('no')}}</span>
		</a>

	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';
	import {boolean} from 'helpers/extraLogics'

	export default {
		
		name:"data-table-is-default",

		description: "Contains yes and no status",		
		
		props: {
			
			/**
			 * component will display buttons based on the indexes present in the `data` object
			 * For eg : if you want this component to display delete button, `data` should have 
			 * an index `delete` which should be the API endpoint which can be used to delete 
			 * the data.
			 * @type {Object}
			 */
			data : { type : Object, required : true }
		
		},

		data() {
			return {
				default_val : ''
			}
		},

		created(){
			this.default_val = boolean(this.data.is_default)
		},

		watch : {
			data(newValue,oldValue){
				this.default_val = boolean(newValue.is_default) 
				return newValue
			}
		},
		
		methods:{

		}
	};

</script>

<style type="text/css">

</style>