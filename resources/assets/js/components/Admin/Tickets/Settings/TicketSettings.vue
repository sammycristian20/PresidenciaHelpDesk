<template>
	
	<div>
		
		<alert componentName="TicketSettings"></alert>

		<faveo-box :title="trans('ticket-setting')">
			
			<div class="row" v-if="!hasDataPopulated || loading">

				<loader :animation-duration="4000" :size="60"/>
			</div>
			
			<template v-if="hasDataPopulated">
				
				<faveo-box :title="trans('general_settings')">
					
					<div class="row">

						<dynamic-select :label="trans('default_status')" :multiple="false" name="status" classname="col-sm-4"
							apiEndpoint="/api/dependency/statuses?supplements[purpose_of_status][]=1" :value="status" :onChange="onChange" 
							:strlength="30" :required="false" :clearable="false">

						</dynamic-select> 

						<number-field :label="trans('agent_collision_avoidance_duration_minutes')" :value="collision_avoid"  name="collision_avoid"
								:onChange="onChange" type="number" :required="false" placeholder="n" classname="col-sm-4"
							>
							
						</number-field>

						<static-select :label="trans('lock_ticket_frequency')" :elements="lockOptions" name="lock_ticket_frequency" 
							:onChange="onChange" :value="lock_ticket_frequency" :hideEmptySelect="true" classname="col-sm-4" >

						</static-select>
	            </div>

	            <div class="row">
	                 
          			<text-field :label="trans('ticket_number_prefix')" :value="ticket_number_prefix" type="text" name="ticket_number_prefix" 
          				:onChange="onChange" classname="col-sm-4" :required="true" :hint="trans('ticket_number_prefix_description')">

						</text-field> 	

						<static-select :label="trans('show_ticket_per_page')" :elements="pageOptions" name="record_per_page" 
							:onChange="onChange" :value="record_per_page" :hideEmptySelect="true" classname="col-sm-4" 
							:hint="trans('ticket-perpage-tooltip')">

						</static-select>

						<number-field :label="trans('waiting_time_hours')" :value="waiting_time"  name="waiting_time" :onChange="onChange" 
							type="number" :required="false" placeholder="n" classname="col-sm-4" :hint="trans('waiting_time_for_client_reply')"
							>
							
						</number-field>
        			</div>
				</faveo-box>

				<faveo-box :title="trans('inbox_settings')">
					
					<div class="row">
						
						<radio-button name="count_internal" classname="form-group col-sm-4" :options="inboxOptions"
            			:label="trans('include_internal_note_in_thread_count')" :value="count_internal" :onChange="onChange">
            		
            		</radio-button>

            		<radio-button name="show_status_date" classname="form-group col-sm-4" :options="inboxOptions"
            			:label="trans('show_status_update_date')" :value="show_status_date" :onChange="onChange">
            		
            		</radio-button>

            		<radio-button name="show_org_details" classname="form-group col-sm-4" :options="inboxOptions"
            			:label="trans('show_org_details')" :value="show_org_details" :onChange="onChange">
            		
            		</radio-button>
					</div>

					<div class="row">
						
						<radio-button name="show_user_location" classname="form-group col-sm-4" :options="inboxOptions"
            			:label="trans('ticket_settings_inbox_settings_display_location')" :value="show_user_location" 
            			:onChange="onChange">
            		
            			</radio-button>
					</div>

					<div class="row" v-if="customOptions.length > 0">
						
						<check-box :options="customOptions" name="custom_field_name" :value="custom_field_name" 
							:label="trans('add_custom_fields')" :onChange="onChange" classname="col-sm-12" class_name="col-sm-12"
							spanClass="check_grid">
							
						</check-box>
					</div>
				</faveo-box>	

				<div class="card-footer" slot="actions">
					
					<button class="btn btn-primary" type="button" @click="onSubmit">
						
						<i class="fas fa-save"> </i> {{trans('save')}}
					</button>
				</div>		
			</template>
		</faveo-box>	

		<div class="row" v-if="pageLoad">

			<custom-loader :duration="4000"></custom-loader>
		</div>
	</div>
</template>

<script>
	
	import { errorHandler, successHandler } from "helpers/responseHandler";

	import { validateTicketSettings } from "helpers/validator/ticketSettingRules.js"

	import axios from 'axios';

	export default {

		name : 'ticket-settings',

		data() {

			return {

				loading : true,

				hasDataPopulated : false,

				pageLoad : false,

				settingsData : '',

				customData : [],

				status : '',

				collision_avoid : '',

				lock_ticket_frequency : '',

				lockOptions: [{id: 0, name: 'No'}, {id: 1, name: 'Only once'}, { id : 2, name : 'Frequently'}],

				ticket_number_prefix : '',

				record_per_page : '',

				pageOptions : [{id: 10, name: '10 tickets per page'}, {id: 25, name: '25 tickets per page'},{id: 50, name: '50 tickets per page'},
					{id: 100, name: '100 tickets per page'}],

				waiting_time : '',

				count_internal : '',

				show_status_date : '',

				show_org_details : '',

				show_user_location : '',

				inboxOptions: [{name:'Yes', value: 1}, {name:'No', value: 0 }],

				custom_field_name : [],

				customOptions : [],
			}
		},

		beforeMount() {

			this.getValues();

			this.getCustomFields();
		},

		methods : {

			getCustomFields() {

				axios.get('/api/ticket-custom-fields-flattened').then(res=>{

					this.customOptions = res.data.data;
				
				}).catch(err=>{

					this.customOptions = [];
				})
			},

			getValues() {

				axios.get('/api/getTicketSetting').then(res=>{

					this.settingsData = res.data.data.ticket;

					this.updateStatesWithData(this.settingsData)

					this.hasDataPopulated = true;

					this.loading = false;
				
				}).catch(err=>{

					this.hasDataPopulated = true;

					this.loading = false;
				})
			},

			updateStatesWithData(data){

				const self = this;
				
				const stateData = this.$data;
				
				Object.keys(data).map(key => {
					
					if (stateData.hasOwnProperty(key)) {
					
						self[key] = data[key];
					}
				});
			},

			onChange(value, name) {
			
				this[name] = value;
			
				if(value === null){
			
					this[name] = '';
				}
			},

			isValid () {

				const { errors, isValid } = validateTicketSettings(this.$data);

				return isValid;
			},

			onSubmit() {

				if(this.isValid()) {

					this.pageLoad = true;

					let data = {};

					data['ticket_number_prefix'] = this.ticket_number_prefix;
					
					data['status'] = this.status.id;

					data['collision_avoid'] = this.collision_avoid;

					data['record_per_page'] = this.record_per_page;

					data['lock_ticket_frequency'] = this.lock_ticket_frequency;

					data['waiting_time'] = this.waiting_time;

					data['count_internal'] = this.count_internal;

					data['show_status_date'] = this.show_status_date;

					data['show_user_location'] = this.show_user_location;

					data['show_org_details'] = this.show_org_details;

					data['_method'] = 'PATCH';

					if(this.custom_field_name.length > 0) {

						data['custom_field_value'] = this.custom_field_name;
					}

					axios.post('/api/postticket',data).then(res=> {

						this.pageLoad = false;

						successHandler(res,'TicketSettings');

						this.getValues();

					}).catch(err=>{

						this.pageLoad = false;

						errorHandler(err,'TicketSettings')
					})
				}
			}
		},

		components : {

			'faveo-box' : require('components/MiniComponent/FaveoBox'),

			'alert' : require('components/MiniComponent/Alert'),

			'custom-loader' : require('components/MiniComponent/Loader'),

			'loader' : require('components/Client/Pages/ReusableComponents/Loader'),

			"text-field": require("components/MiniComponent/FormField/TextField"),

			'dynamic-select':require('components/MiniComponent/FormField/DynamicSelect'),

			'static-select':require('components/MiniComponent/FormField/StaticSelect'),
			
			'number-field':require('components/MiniComponent/FormField/NumberField'),

			'radio-button':require('components/MiniComponent/FormField/RadioButton'),

			'check-box': require('components/MiniComponent/FormField/CheckBoxComponent'),
		}
	};
</script>