<template>
	
	<optgroup label="Billing">  
			    
		<option value="/billing-package-list">&nbsp;{{lang('purchage')}}</option>
					
		<option v-if="!Array.isArray(auth.user_data)" value="/billing-user-packages">&nbsp;{{lang('my_packages')}}
		</option>
					
		<option v-if="!Array.isArray(auth.user_data)" value="/biling-user-invoices">&nbsp;{{lang('my_invoices')}}
		</option>
	</optgroup>
</template>

<script>
	
	export default {

		name : 'billing-mobile-menu',

		description : 'Billing mobile menu component',

		props : {

			auth : { type : Object, default :()=>{}}
		}
	};
</script>