<template>

	<div>

		<a v-if="data.edit_url" class="btn btn-primary" :href="data.edit_url" v-tooltip="lang('edit')">

			<i class="fas fa-edit"></i>
		</a>

		<div v-if="data.role != 'user'" class="btn-group">
		
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" v-tooltip="lang('view')">
			
				<i class="fas fa-eye"></i>
			</button>
		
			<div class="dropdown-menu dropdown-menu-right">
				
				<a class="dropdown-item" :href="data.view_url"> {{trans('view_user')}} </a>

				<a class="dropdown-item" :href="data.agent_view_url"> {{trans('view_agent')}} </a>
			</div>
		</div>

		<a v-if="data.role === 'user'" class="btn btn-primary" :href="data.view_url" 
			v-tooltip="lang('view')">

			<i class="fas fa-eye"></i>
		</a>
	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';

	import {boolean} from 'helpers/extraLogics'

	export default {

		name:"user-table-actions",

		description: "User table edit componnet",

		props: {

			data : { type : Object, required : true },
		},
	};
</script>
