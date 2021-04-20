<template>
	<div draggable="false">
		<modal v-if="isShowSettingModal" :showModal="isShowSettingModal" :onClose="onCloseSettingModal"
			:containerStyle="containerStyle">
			<div slot="title">
				<h4 class="modal-title">{{lang(title) + '(' + fieldSettingData.labels_for_form_field[0].label + ')'}}</h4>
			</div>
			<div slot="fields" class="modal-width">
				<alert componentName="form-builder-setting-modal" />

				<ul class="nav nav-tabs" role = "tablist" v-if="tabsToShow.length">
					<li class="nav-item" v-for="tab in tabsToShow" :key="tab.id" :id="tab.id">
						<a href="javascript:void(0)" :class="['nav-link', currentTab === tab.value ? 'active' : '']" @click="onTabChange(tab.value)" data-toggle="pill" role="tab">{{lang(tab.value)}}</a>
					</li>
				</ul>

				<div v-if="tabsToShow.length">

					<FB-configuration-settings v-if="currentTab === 'configuration'" :form-field-data="fieldSettingData" />

					<div v-if="currentTab === 'addOption'">

						<!-- Add options view -->
						<div>
								<label class="mt-2 mb-2">{{lang('options')}}:<small style="color: orangered;" v-if="addOptionInputHelp">({{addOptionInputHelp}})</small></label>

								<div class="table">
									<ul id="horizontal-list">
										<div class="col-sm-12">
										<draggable-element v-model="fieldSettingData.options">
											
											<li v-for="(item, index) in fieldSettingData.options" :key="item.id">
												<nested-field-option :option-index="index" v-model="item.labels[0].label" @on-delete="deleteOption" @on-update="updateOptionValue"/>
											</li>
										
										</draggable-element>
										</div>
										<li class="input-box-li">
											<input ref="addOptionInput" type="text" :placeholder="lang('add_new_option')" class="form-control add-option" v-model.trim="newOption" @keyup="addNewOption($event)">
										</li>
										<li class="add-option-loader float-right" v-if="isLoading">
											<relative-loader :size="20" />
										</li>
									</ul>
								</div>
						</div>
					</div>

					<FB-field-validation v-if="currentTab === 'validation'" :form-field-data="fieldSettingData" />

					<FB-add-language v-if="currentTab === 'language'" :form-field-data="fieldSettingData" />

				</div>
				<div v-else>
					<div class="alert alert-info" role="alert">{{ lang('this_field_is_not_editable') }}</div>
				</div>
			</div>

			<div slot="controls" v-if="tabsToShow.length">
				<button type="button" class="btn btn-primary pull-right" @click="onApply()">
					<i class="fas fa-check"> </i> {{ lang('apply') }}
				</button>
			</div>
		</modal>
	</div>
</template>

<script>
	import Modal from 'components/Common/Modal';
	import axios from 'axios';
	import { errorHandler, successHandler } from "helpers/responseHandler";
	import draggable from 'vuedraggable';
	import { lang } from 'helpers/extraLogics';

	export default {

		name: 'form-builder-setting-modal',

		props: {

			// Form field data object
			fieldData: {
				type: Object,
				required: true
			},

			// Boolean to check whether to show setting modal or not 
			isShowSettingModal: {
        type: Boolean,
        default: false
			},

			// Function to execute when close event emiited
			onCloseSettingModal: {
        type: Function
			},

			// Title to show in the header bar of the settign modal
			title: {
        type: String,
        default: ''
			}

		},

		data: () => {
			return {
				isLoading: false,
				containerStyle: { width: '800px' },
				fieldSettingData: null,
				newOption: '', // new option trimmed string
				currentTab: '', // active tab value string
				addOptionInputHelp: '',
			}
		},

		computed: {
			tabsToShow: function () {
				const MODAL_TABS = [
					{ id: 0, value: 'configuration', show: true },
					{ id: 1, value: 'addOption', show: true },
					{ id: 2, value: 'validation', show: true },
					{ id: 3, value: 'language', show: true }
				];
				if(this.isHideConfigurationTab()) {
					MODAL_TABS[0].show = false;
				}
				if(this.isHideAddOptionTab()) {
					MODAL_TABS[1].show = false;
				}
				if(this.isHideValidationTab()) {
					MODAL_TABS[2].show = false;
				}
				if(this.isHideLanguageTab()) {
					MODAL_TABS[3].show = false;
				}
				const validTabs = MODAL_TABS.filter((tabs) => tabs.show );
				this.currentTab = validTabs.length !== 0 ? validTabs[0].value : '';

				return validTabs;
			}
		},

		beforeMount() {
			this.fieldSettingData = JSON.parse(JSON.stringify(this.fieldData));
		},

		methods: {

			isHideLanguageTab() {
				return this.fieldSettingData.title === 'Captcha';
			},

			isHideConfigurationTab() {
				// If is_agent_config and is_user_config are false, hide conf tab
				if(!this.fieldSettingData.is_agent_config && !this.fieldSettingData.is_user_config) {
					return true;
				}
				// Hide this tab for some fields so that user can't able to hide all form-fields in the render form 
				const mandatoryFields = ['First Name', 'Last Name', 'Requester'];
				return mandatoryFields.includes(this.fieldSettingData.title);
			},

			isHideValidationTab() {
				return !Boolean((this.fieldSettingData.type === 'text' || this.fieldSettingData.type === 'textarea' || this.fieldSettingData.type === 'htmltextarea') && !this.fieldSettingData.default);
			},

			isHideAddOptionTab() {
				const deptHelptopic = ['Department', 'Help Topic'];
				const nestedTypes = ['select', 'radio', 'select2', 'checkbox', 'multiselect'];
				return !nestedTypes.includes(this.fieldSettingData.type) || deptHelptopic.includes(this.fieldSettingData.title) || this.fieldSettingData.default || this.fieldSettingData.title === 'Api'
			},

			/**
			 * Update the option label with the new value in case of editing
			 * data.index => the option index for which the to be updated
			 * data.newvalue => the update label for the option
			 */
			updateOptionValue(data) {
				if(this.isOptionAlreadyExist(data.newValue)) {
					return;
				}
				this.fieldSettingData.options[data.index].labels[0].label = data.newValue;
			},

			deleteOption(index) {
				/**
				 * Options shouldn't be empty 
				 * if only one option present don't allow to delete the option
				 * show alert message
				 **/ 
				if(this.fieldSettingData.options.length === 1) {
					this.$store.dispatch('setAlert', { type: 'danger', message: lang('option_can_not_be_empty_alert_msg'), component_name: 'form-builder-setting-modal' });
					return;
				}
				const selectedId = this.fieldSettingData.options[index].id;
				if(selectedId && selectedId.toString().length < 13) { // Case of new options(options not in DB)
					let isConfirmed = confirm('Are you sure you want to delete?');
					if(isConfirmed) {
						this.isLoading = true;
						axios
						.delete('api/delete-form-data/form-option/' + selectedId)
						.then(resp => {
							window.eventHub.$emit('getFormDataFromServer');
							this.fieldSettingData.options.splice(index, 1);
							successHandler(resp, 'form-builder-setting-modal');
						})
						.catch(resp => {
							errorHandler(resp, 'form-builder-setting-modal');
						})
						.finally(() => {
							this.isLoading = false;
						})
					}
				} else { // Case of old options(options present in DB)
					this.fieldSettingData.options.splice(index, 1);
				}
			},

			/**
			 * Add new option element to options property
			 */
			addNewOption(event) {
				if(!this.newOption){
					this.addOptionInputHelp = '';
					return;
				};
				this.addOptionInputHelp = 'Press enter to add';
				if(event.keyCode === 13) { // Listen ENTER event key, will push new option
					this.addOptionInputHelp = '';
					if(this.isOptionAlreadyExist(this.newOption)) {
						this.newOption = '';
						return;
					}
					const data = this.getOptionDataObj();
					if(this.fieldData.type === 'checkbox'){
						data.checked = false;
					}
					this.fieldSettingData.options.push(data);
					this.newOption = '';
				}
			},

			/**
			 * Get option object with the new option label
			 */
			getOptionDataObj() {
				const currentTimestamp = this.getCurrentTimestamp();
				return {
					id: currentTimestamp, // TimeStamp as id
          form_identifier: currentTimestamp,
          labels: [
            {
              id: null,
              language: 'en',
              label: this.newOption
            }
          ],
          nodes: [],
          sort_order: 0
				}
			},

			/**
			 * Check if option label already exist in the options list
			 * returns boolean accordingly
			 */
			isOptionAlreadyExist(newOptionLabel) {				
				for (let i = 0; i < this.fieldSettingData.options.length; i++) {
					const labels = this.fieldSettingData.options[i].labels;
					for (let j = 0; j < labels.length; j++) {
						if(labels[j].language === 'en' && labels[j].label.toLowerCase() === newOptionLabel.toLowerCase()) {
							this.$store.dispatch('setAlert', { type: 'danger', message: `Option [${newOptionLabel}] already present.`, component_name: 'form-builder-setting-modal' });
							return true;
						}
					}
				}
				return false;
			},

			onTabChange(newTabValue) {
				this.$store.dispatch('unsetAlert');
				this.currentTab = newTabValue;
			},

		// Emmit apply event with mutated data object
		onApply() {
			window.eventHub.$emit('formFieldSettingsApplied', this.fieldSettingData);
		},

		getCurrentTimestamp() {
      return new Date().getTime();
    },

	},

	beforeDestroy() {
		/**
		 * Unset alert box before leaving this component
		 */
		this.$store.dispatch('unsetAlert');
	},

	components: {
		'modal': Modal,
		'alert': require('components/MiniComponent/Alert'),
		'relative-loader': require('components/Extra/Loader'),
		'FB-configuration-settings': require('./FBConfigurationSettings'),
		'FB-add-language': require('./FBAddLanguage'),
		'FB-field-validation': require('./FBFieldValidation'),
		'draggable-element': draggable,
		'nested-field-option': require('./NestedFieldOption')
	}
};
</script>

<style type="text/css" scoped>
.modal-width {
	/*margin-left: 15px;
	margin-right: 15px;*/
	max-height: 400px;
	overflow-x: hidden;
	overflow-y: auto;
}
.tbm-checkbox {
	margin: 1rem;
}

input {
	border: none;
}

.content {
	min-height: 20vh !important;
}
li {
	list-style-type: none;
}

.table {
	display: table;
	margin: 0 auto;
}

ul#horizontal-list {
	border: 1px solid #b5b5b5;
	min-width: 696px;
	list-style: none;
	padding-left: 0;
	max-height: 100px;
	overflow-y: auto;
	border-radius: 0.25rem;
}

ul#horizontal-list li {
	display: inline-flex;
}
.add-option {
	margin-right: 10px;
   margin-left: -3px;
}
.input-box-li {
	padding-left: 0.25rem;
	padding-top: 1rem;
	width: 100%;
}
.add-option-loader {
	padding-right: 1rem;
  padding-top: 1rem;
}
.li-child {
	padding-left: 1rem;
	padding-top: 1rem; 
}
.active {
	border-top: 2px solid #3c8dbc;
	border-right: 1px solid #dedede; 
}
</style>
