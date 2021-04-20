<template>
	<div class="btn-group">
		<div :class="['div-elem', data.role]">{{formatedRole}}</div>
    <!-- 'fa fa-user' -->
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';

	export default {
		
		name: "user-role",

		description: "Display user role(Admin/Agent/User...)",	
		
		props: {
			data : { type : Object, required : true }
		},

		computed: {
			formatedRole: function() {
				return this.data.role.charAt(0).toUpperCase() + this.data.role.slice(1);
			}
		}
	};
 
</script>

<style type="text/css" scoped>

.div-elem {
/*	text-align: center;
*/  border-radius: 3px;
    width: 4pc;
}
.admin {
/*	border: 1px solid #008080;
*/	color: #008080
}
.agent {
/*	border: 1px solid #3c8dbc;
*/	color: #3c8dbc;
}
.user {
/*	border: 1px solid #000;
*/	color: #000;
}
</style>