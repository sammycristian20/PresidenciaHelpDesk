<template>
	
	<div class="card card-light">
		
		<div class="card-header">
			
			<h3 class="card-title">{{lang('set_sla_reminders')}}</h3>

			<tool-tip :message="lang('What_happens_when_this_SLA_due_time_approaches')" size="medium"></tool-tip>
		</div>

		<div class="card-body">
			
			<div>
				
				<p v-html="lang('set_reminder_rule_when_a_ticket_response_time_approaches')" id="desc"></p>

				<dynamic-sla-field :fieldsArr="response" fieldGroup="response" :onFieldChange="getFields" from="approaching">
					
				</dynamic-sla-field>
			</div><br>

			<div>
				
				<p v-html="lang('Set_reminder_rule_when_a_ticket_resolution_time_approaches')" id="desc"></p>

				<dynamic-sla-field :fieldsArr="resolution" fieldGroup="resolution" :onFieldChange="getFields" from="approaching">
					
				</dynamic-sla-field>
			</div>
		</div>	
	</div>
</template>

<script>
	
	export default {

		name  : 'sla-remindeers',

		description : 'SLA Reminders settings page',

		props : {

			getUpdateArray : { type : Function },

			approach : { type : Array, default : ()=>[]}
		},

		data(){

			return { 

				response : [],

				resolution : [],
			}
		},

		beforeMount(){
			
			if(this.approach.length > 0){

				for(var i in this.approach){

					var reminder = this.approach[i].reminder_delta.split('~');

					var delta = reminder[reminder.length -2].split('::');

					this.approach[i]['option'] = reminder[reminder.length -1];

					this.approach[i]['reminder_delta'] = delta[delta.length -1];
					
					if(this.approach[i].type === 'response'){

						this.response.push(this.approach[i])
					
					} else {

						this.resolution.push(this.approach[i])

					}
				}
			}
		},

		methods : {

			getFields(name,array){

				this[name] = array;

				this.getUpdateArray(this.response, this.resolution)
			}
		},

		components : {

			"tool-tip": require("components/MiniComponent/ToolTip"),

			"dynamic-sla-field": require("./MiniComponents/DynamicSlaField"),
		}
	};
</script>

<style scoped>
	
	#desc {
		margin: 5px 0px 10px 0px !important;
	}
</style>