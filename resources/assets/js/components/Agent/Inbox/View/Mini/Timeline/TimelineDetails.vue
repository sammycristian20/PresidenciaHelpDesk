<template>
	
	<div class="timeline_details">

		<div class="callout" v-if="data.priority != null" :style="{backgroundColor:data.priority.priority_color,color:'#F9F9F9'}">

			<div class="row">

				<div class="col-md-4" v-tooltip="formattedTime(data.created_at)">

					<b>{{ lang('created_date') }}: </b> {{formattedTime(data.created_at)}}

				</div>

				<div class="col-md-4" v-tooltip="formattedTime(data.duedate)">

					<b>{{ lang('due_date') }}: </b> {{formattedTime(data.duedate)}}

				</div>

				<div class="col-md-4" v-tooltip="formattedTime(data.updated_at)">

					<b>{{ lang('updated_date') }}: </b> {{formattedTime(data.updated_at)}}

				</div>
			</div>
		</div>

		<div class="col-md-12">

			<div class="row">

				<div v-if="data.status" class="col-md-6 info-row">

					<div class="col-md-6"><label>{{ lang('status') }}</label></div>

					<div class="col-md-6">

						<a v-tooltip="data.status.name" target="_blank" :href="data.status.href">{{subString(data.status.name)}}</a>
					</div>
				</div>

				<div v-if="data.source" class="col-md-6 info-row">

					<div class="col-md-6"><label>{{ lang('source') }}</label></div>

					<div class="col-md-6">

						<a v-tooltip="data.source.name" target="_blank" :href="data.source.href">{{subString(data.source.name)}}</a>
					</div>
				</div>

				<div v-if="data.type" class="col-md-6 info-row">

					<div class="col-md-6"><label>{{ lang('type') }}</label></div>

					<div class="col-md-6">

						<a v-tooltip="data.type.name" target="_blank" :href="data.type.href">{{subString(data.type.name)}}</a>
					</div>
				</div>

				<div v-if="data.priority" class="col-md-6 info-row">

					<div class="col-md-6"><label>{{ lang('priority') }}</label></div>

					<div class="col-md-6">

						<a v-tooltip="data.priority.name" target="_blank" :href="data.priority.href">{{subString(data.priority.name)}}</a>
					</div>
				</div>

				<div v-if="data.departments" class="col-md-6 info-row">

					<div class="col-md-6"><label>{{ lang('departments') }}</label></div>

					<div class="col-md-6">

						<a v-tooltip="data.departments.name" target="_blank" :href="data.departments.href">{{subString(data.departments.name)}}</a>
					</div>
				</div>

        <div v-if="data.sla_plan" class="col-md-6 info-row">

					<div class="col-md-6"><label>{{ lang('sla_plan') }}</label></div>

					<div class="col-md-6">

						<a v-tooltip="data.sla_plan.name" target="_blank" :href="data.sla_plan.href">{{subString(data.sla_plan.name)}}</a>
					</div>
				</div>

				<div v-if="data.helptopic" class="col-md-6 info-row">

					<div class="col-md-6"><label>{{ lang('helptopic') }}</label></div>

					<div class="col-md-6">

						<a v-tooltip="data.helptopic.name"  target="_blank" :href="data.helptopic.href">{{subString(data.helptopic.name)}}</a>
					</div>
				</div>

				<div v-if="data.last_replier" class="col-md-6 info-row">

					<div class="col-md-6"><label>{{ lang('last_replier') }}</label></div>

					<div class="col-md-6">

						<a v-tooltip="data.last_replier.full_name" :href="basePath()+'/user/'+data.last_replier.id" target="_blank">
							{{subString(data.last_replier.full_name)}}
						</a>
					</div>
				</div>

				<div v-if="data.user" class="col-md-6 info-row">

					<div class="col-md-6"><label>{{ lang('requester') }}</label></div>

					<div class="col-md-6">

						<a v-tooltip="data.user.full_name" target="_blank" :href="data.user.href">{{subString(data.user.full_name)}}</a>
					</div>
				</div>

				<div v-if="data.assignee" class="col-md-6 info-row">

					<div class="col-md-6"><label class="text-capitalize">{{ lang('assignee') }}</label></div>

					<div class="col-md-6">

						<a v-tooltip="data.assignee.name" target="_blank" :href="data.assignee.href">{{subString(data.assignee.name)}}</a>
					</div>
				</div>

				<div v-if="data.creator" class="col-md-6 info-row">

					<div class="col-md-6"><label>{{ lang('creator') }}</label></div>

					<div class="col-md-6">

						<a v-tooltip="data.creator.full_name" :href="data.creator.href" target="_blank">
							{{subString(data.creator.full_name)}}</a>
					</div>
				</div>

				<div v-if="data.location" class="col-md-6 info-row">

					<div class="col-md-6"><label>{{ lang('location') }}</label></div>

					<div class="col-md-6">

						<a v-tooltip="data.location.name" target="_blank" :href="data.location.href">{{subString(data.location.name)}}</a>
					</div>
				</div>

        <div v-if="boolean(data.organizations)" class="col-md-6 info-row">

          <div class="col-md-6"><label>{{lang('organization')}}</label></div>

          <div class="col-md-6">
            <div v-for="organization in data.organizations" >
              <a v-tooltip="organization.name" target="_blank" :href="organization.href">{{subString(organization.name)}}</a>
            </div>
          </div>

        </div>

        <template v-if="timelineData.custom_field_values && timelineData.custom_field_values.length > 0">
					
					<div v-for="customField in timelineData.custom_field_values"  v-if="boolean(customField.value)" class="col-md-6 info-row">

						<div class="col-md-6">

							<label>{{ customField.label }}</label>

						</div>

						<div class="col-md-6">

							<div v-if="Array.isArray(customField.value)">

								<a  v-for="(value, index) in customField.value" target="_blank" :href="customField.href[index]" v-tooltip="value">{{subString(value)}}&nbsp;</a>

							</div>

							<div v-else>
								<a target="_blank" v-if="customField.field_type === 'date'" :href="customField.href"  v-tooltip="formattedTime(customField.value)">{{ formattedTime(customField.value) }}</a>

								<a target="_blank" v-else :href="customField.href"  v-tooltip="customField.value">{{ subString(customField.value) }}</a>
							</div>

							
						</div>
					</div>
				</template>

				<!-- this is a div which can be used to inject plugin components to it -->
				<div id="timeline-meta-list-box" class="col-md-6 info-row">{{timelineMetaListBoxMounted()}}</div>
			 </div>
		 </div>
	</div>
</template>

<script>
	
	import { mapGetters } from 'vuex'

	import { getSubStringValue } from 'helpers/extraLogics'

	export default {

		name : 'timeline-details',

		description : 'Timeline Details Component',

		props : {

			data : { type : Object, default : ()=> {} },
		},

		data() {

			return {

				timelineData : this.data
			}
		},

		computed:{

			...mapGetters(['formattedTime'])
		},

		watch : {

			data(newValue,oldValue) {

				this.timelineData['custom_field_values'] = [];

				setTimeout(()=>{

					this.timelineData = newValue;
				},1000);
			}
		},

		methods :{

			subString(value,length = 15){
	 
			  return getSubStringValue(value,length)
			},

			/**
         * For any external script to be able to inject some javascript on loginBox visiblity
         * @return {undefined}
         */
        timelineMetaListBoxMounted(){
            window.eventHub.$emit('timeline-meta-box-mounted', this.data);
        },
		}
	};
</script>

<style scoped>
	
	.timeline_details { margin-top: 10px; }

	 .info-row { border-top: 1px solid #f4f4f4; padding: 10px; display: flex; }
</style>