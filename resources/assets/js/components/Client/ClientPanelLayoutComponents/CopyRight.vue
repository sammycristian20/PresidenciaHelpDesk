<template>
	<p class="text-muted">{{ lang('copyright') }} © {{moment().year()}}. 

		<a :href="layout.company.website" target="_blank">

			<b>{{ layout.company.company_name }} </b>

		</a> {{ lang('all_rights_reserved') }}. 

		<span v-if="layout.copyright"> {{ lang('powered_by') }} </span>
		
		<a :href="layout.link" target="_blank">
			
			<b> {{ layout.copyright }}</b>
		</a>
	</p>		
</template>

<script>
	
	import moment from 'moment'

	export default {

		name : 'client-panel-copyright',

		description : 'Client panel copyright component',

		props : {

			layout : { type : Object, default : ()=>{}}
		},

		data(){

			return {

				moment:moment
			}
		}
	};
</script>