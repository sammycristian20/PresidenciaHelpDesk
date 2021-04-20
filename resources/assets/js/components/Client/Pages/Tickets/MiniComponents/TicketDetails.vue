<template>
	
	<div class="row" :class="{align1: lang_locale == 'ar'}">

		<template>
			
			<div class="col-md-6" style="min-height: 100px;">

				<table class="table">
					
					<tbody>
					
						<tr>

							<td><b>{{ lang('status') }} :</b></td>

							<td style="color:orange"> {{ ticket.status }} </td>
						</tr>
					
						<tr>
											
							<td><b>{{ lang('department') }} :</b></td>

							<td :title="ticket.department"> {{ ticket.department }} </td>
						</tr>
					
						<tr>
											
							<td><b>{{ lang('created_date') }} :</b></td>
											
							<td title="Created Date">{{formattedTime(ticket.created_at)}}</td>
						</tr>
					</tbody>
				</table>

				<div class="clearfix"></div>
			</div>

			<div class="col-md-6" style="min-height: 100px;">
									
				<table class="table">
								
					<tbody>
								
						<tr>

							<td><b>{{ lang('help_topic') }} :</b></td>
											
							<td :title="ticket.help_topic"> {{ ticket.help_topic }} </td>
						</tr>
								
						<tr>

							<td><b>{{ lang('last_message') }} :</b></td>

							<td> {{ ticket.threads[ticket.threads.length-1].user.first_name}} &nbsp;
										{{ ticket.threads[ticket.threads.length-1].user.last_name}}
							</td>
						</tr>

						<tr>

							<td><b>{{ lang('last_response') }} :</b></td>

							<td> {{ formattedTime(ticket.threads[ticket.threads.length-1].created_at) }}</td>
						</tr>
					</tbody>
				</table>
				<div class="clearfix"></div>				
			</div>
		</template>

		<template v-if="loading">
						
			<loader :color="layout.portal.client_header_color" :animation-duration="4000" :size="60"/>
		</template>
	</div>
</template>

<script>
	
	import { mapGetters } from 'vuex';

	export default {

		name : 'ticket-details',

		description : 'Ticket deatils page',

		props : {

			layout : { type : Object, default : ()=>{}},

			auth : { type : Object, default : ()=>{}},

			ticket : { type : Object, default : ()=>{}}
		},

		data(){

			return {

				loading : false,

				lang_locale : this.layout.language
			}
		},

		computed: {
			
			...mapGetters(['formattedTime','formattedDate'])
		},

		components :{

			'client-panel-loader' : require('components/Client/ClientPanelLayoutComponents/ReusableComponents/Loader.vue'),

			'alert' : require('components/MiniComponent/Alert'),
		}

	};

</script>

<style scoped>
	
	.content {

		margin-top: 5px !important
	}
</style>