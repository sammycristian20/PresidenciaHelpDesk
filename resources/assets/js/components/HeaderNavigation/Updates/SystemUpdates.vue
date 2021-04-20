<template>

	<li class="nav-item dropdown">

		<a class="nav-link" data-toggle="dropdown" href="javascript:;" v-tooltip="trans('application_updates')">
			
			<i class="fas fa-sync-alt"></i>
			
			<span class="badge badge-warning navbar-badge update_badge">{{updatesCount}}</span>
		</a>
		
		<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
			
			<span v-if="!updatesCount" class="dropdown-header">You have {{updatesCount}} update(s)</span>
			
			<template  v-if="updatesCount" >
				
				<span class="dropdown-header">New version(s) available.</span>
				
				<div class="dropdown-divider"></div>

				<a href="javascript:;" class="dropdown-item">
					
					Please <a :href="basePath()+'/check-updates'"> click here </a> to update your system.
				</a>
			</template>
		</div>
	</li>
</template>

<script>
	
	export default {

		name : 'syatem-updates',

		props : {

			updatesCount : { type : String | Number, default : '' }
		}
	};
</script>

<style scoped>
	
	.update_badge {
		right: 1px !important;
	}
</style>