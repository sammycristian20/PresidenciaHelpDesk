<template>
   <form-field-template
      :name="name"
      :classname="classname"
      :label="label"
      :hint="hint"
      :required="required"
      :labelStyle="labelStyle"
      :showNewButton="showNewButton" 
      :onClickEvent="getFieldName"
			:actionBtn="actionBtn"
			:isInlineForm="isInlineForm"
			:showPreview="showPreview"
      >
			<ValidationProvider :name="name" :rules="rules">
				<template slot-scope="{ failed, errors, classes }">
					<div class="dynamic-select">
						<v-select
						:name="name"
						:class="['faveo-dynamic-select', classes]"
						ref="faveoDynamicSelect"
						:options="listElements"
						v-model="selectedValue"
						:label="optionLabel"
						:multiple="multiple"
						:placeholder="placeholder"
						:autoscroll="autoscroll"
						:disabled="disabled"
						:clearable="clearable"
						:searchable="searchable"
						:transition="transition"
						:closeOnSelect="closeOnSelect"
						:taggable="taggable"
						:pushTags="pushTags"
						:filterable="false"
						:noDrop="noDrop"
						:inputId="id"
						:dir="dir"
						@input="onValueChange"
						@search="onSearch"
						@open="connectObserver"
						@close="disconnectObserver"
						@search:blur="clearSearchQuery"
						>
						<template slot="no-options" slot-scope="{search, searching}">
								<template v-if="searching">No results found for <em>{{ search }}</em>. </template>
								<template v-else>
									<span v-if="!isLoading && !hasNextPage">No options found.</span>
									<loader v-if="isLoading && !hasNextPage" :duration="4000" :size="25"></loader> 
								</template>
						</template>
						<template slot="option" slot-scope="option">
								<div class="d-center" :title="option[optionLabel]">
									<!-- For images -->
									<faveo-image-element v-if="option.profile_pic" id="faveo-dynamic-select-img" :source-url="option.profile_pic" />
									<!-- For Icons -->
									<i v-if="option.icon_class" :class="option.icon_class" aria-hidden="true"></i>
									{{ subString(option[optionLabel]) }}
								</div>
						</template>
						<template slot="selected-option" slot-scope="option">
								<div class="selected d-center" :title="option[optionLabel]">
									<!-- For images -->
									<faveo-image-element v-if="option.profile_pic" id="faveo-dynamic-select-img" :source-url="option.profile_pic" />
									<!-- For Icons -->
									<i v-if="option.icon_class" :class="option.icon_class" aria-hidden="true"></i>
									{{ subString(option[optionLabel]) }}
								</div>
						</template>
						<template slot="list-footer" v-if="hasNextPage">
								<li ref="dynamicSelectLoader" class="loader-area">
									<loader :duration="4000" :size="25"></loader>
								</li>
						</template>
						</v-select>
					</div>
					<span v-show="failed" class="error-block is-danger">{{errors[0]}}</span>
				</template>
			</ValidationProvider>
			<slot name="dsHelpText"></slot>
   </form-field-template>
</template>

<script>
import vSelect from "vue-select";

import axios from "axios";

import FormFieldTemplate from "./FormFieldTemplate";

import { errorHandler } from "helpers/responseHandler";
import { getSubStringValue, boolean } from "helpers/extraLogics";
import { convertMapToSubmitableData } from 'helpers/formUtils';

import _ from 'lodash-core';

export default {
	name: "dynamic-select",

	props: {
		/**
		 * the name of the state in parent class
		 * @type {String}
		 */
		name: {
			type: String | Number,
			Required: true
		},

		/**
		 * Hint regarding what the field is about (it will be shown as tooltip message)
		 * @type {String}
		 */
		hint: {
			type: String,
			default: ""
		},

		/**
		 * Whether the given field is required or not.
		 * If passed yes, an asterik will be displayed after the label
		 * @type {Boolean}
		 */
		required: {
			type: Boolean,
			default: false
		},

		/**
		 * classname of the form field. It can be used to give this component any bootstrap class or a custom class
		 * whose css will be defined in parent class
		 * @type {String}
		 */
		classname: {
			type: String,
			default: ""
		},

		/**
		 * Id of the field
		 * @type {String|Number}
		 */
		id: {
			type: String | Number,
			default: "dynamic-select"
		},

		/**
		 * For showing Field label.
		 * If passed display as none, the label will not be displayed
		 * @type {Object}
		 */
		labelStyle: {
			type: Object,
			default: function () {
				return {};
			}
		},

		/**
		 * The function which will be called as soon as value of the field changes
		 * It should have two arguments `value` and `name`
		 *     `value` will be the updated value of the field
		 *     `name` will be thw name of the state in the parent class
		 *
		 * An example function:
		 *   onChange(value, name){
		 *     this[name]= selectedValue
		 *   }
		 *
		 * @type {Function}
		 */
		onChange: {
			type: Function,
			required: true
		},

		/**
		 * When true, the dropdown will automatically scroll to ensure that the option highlighted is fully within the dropdown viewport when navigating with keyboard arrows.
		 */
		autoscroll: {
			type: Boolean,
			default: true
		},

		/**
		 * Contains the currently selected value.
		 * Very similar to a value attribute on an <input>.
		 * You can listen for changes using 'change' event using v-on.
		 */
		value: {
			type: Object | String,
			default: null
		},

		/**
		 * An array of strings or objects to be used as dropdown choices.
		 * If you are using an array of objects, vue-select will look for a label key (ex. [{label: 'Canada', value: 'CA'}]).
		 * A custom label key can be set with the label prop.
		 */
		elements: {
			type: Array,
			default () {
				return [];
			}
		},

		/**
		 * Disable the dropdown.
		 */
		disabled: {
			type: Boolean,
			default: false
		},

		/**
		 * Can the user clear the selected property?
		 */
		clearable: {
			type: Boolean,
			default: true
		},

		/**
		 * Enable/disable filtering the options.
		 */
		searchable: {
			type: Boolean,
			default: true
		},

		/**
		 * Equivalent to the multiple attribute on a <select> input.
		 */
		multiple: {
			type: Boolean,
			default: false
		},


		/**
		 * Api endpoint which will return the list of elements which are needed to be displayed
		 * if api endPoint is null, we use elements to display
		 * @type {String}
		 */
		apiEndpoint: {
			type: String,
			default: null
		},

		/**
		 * API parameters needs to get added in the api calls
		 */
		apiParameters: {
			type: Object | Map,
			default: () => {}
		},

		/**
		 * Equivalent to the placeholder attribute on an <input>.
		 */
		placeholder: {
			type: String,
			default: "Search or Select"
		},

		/**
		 * Sets a Vue transition property on the .dropdown-menu.
		 */
		transition: {
			type: String,
			default: "fade"
		},

		/**
		 * Enables/disables clearing the search text when an option is selected.
		 */
		clearSearchOnSelect: {
			type: Boolean,
			default: true
		},

		/**
		 * Close a dropdown when an option is chosen.
		 * Set to false to keep the dropdown open (useful when combined with multi-select, for example)
		 */
		closeOnSelect: {
			type: Boolean,
			default: true
		},

		/**
		 * Tells vue-select what key to use when generating option labels when each option is an object.
		 */
		optionLabel: {
			type: String,
			default: "name"
		},

		/**
		 * Form field label text
		 */
		label: {
			type: String,
			default: ''
		},

		/**
		 * Enable/disable creating options from searchInput.
		 */
		taggable: {
			type: Boolean,
			default: false
		},

		/**
		 * When true, newly created tags will be added to the options list.
		 */
		pushTags: {
			type: Boolean,
			default: false
		},

		/**
		 * Used to truncate a string
		 */
		strlength: {
			type: Number | String,
			default: 40
		},

		/**
		 * Disable the dropdown entirely.
		 */
		noDrop: {
			type: Boolean,
			default: false
		},

		/**
		 * Sets the id of the input element.
		 */
		inputId: {
			type: String
		},

		/**
		 * Sets RTL support. Accepts ltr, rtl, auto.
		 */
		dir: {
			type: String,
			default: "auto"
		},

		/**
     * The new button is need to show or not.
     * @type {Boolean}
     */
    showNewButton: { type: Boolean, default: false },

    /**
     * for getting field name
     */
    onClickEvent : { type : Function },
    
    /**
     * This method will return field name to parent component.
     */
		onButtonClick: { type : Function},

		isInlineForm: { type: Boolean, default: false },

		rules: { type: String, default: '' },

		actionBtn: { type: Object, default: () => null },

		showPreview : { type : String | Object, default : '' }

	},

	data: () => {
		return {
			listElements: [],
			page: 0,
			observer: null,
			nextPageUrl: '',
			searchQuery: undefined,
			isLoading: false,
			selectedValue: null
		};
	},

	beforeMount() {
		this.listElements = Boolean(this.elements) ? this.elements : [];
	},

	mounted() {
		if (this.apiEndpoint) {
			this.flushAndRestart();
		}
		this.selectedValue = this.value;
	},

	computed: {
		hasNextPage() {
			return Boolean( this.nextPageUrl !== null && this.apiEndpoint);
		}
	},

	methods: {

		getFieldName(name){
						
			this.onButtonClick(name)
		},

		subString(value) {
			return getSubStringValue(value, parseInt(this.strlength))
		},


		flushAndRestart() {
			this.disconnectObserver();
			this.resetProperties();
			this.observer = new IntersectionObserver(this.infiniteScroll);
		},

		/**
		 * gets value from the API based on the search string
		 * @param  {String} searchQuery  text that is typed in the field (for searching purpose)
		 * @return {void}
		 */
		onSearch(searchQuery) {
			this.searchQuery = searchQuery;
			this.page = 1;
			this.search();
		},

		/**
		 * Listens the change event from v-select if any
		 */
		onValueChange(value) {
			this.onChange(value, this.name);
		},

		search: _.debounce(function () {
			if (this.apiEndpoint) {
				this.getListFromServer(true);
			} else {
				// Search elements locally
				this.filterListElements();
			}
		}, 350),

		/** Filter list elements */
		filterListElements() {
			this.listElements = this.elements.filter((element) => element[this.optionLabel].toLowerCase().includes(this.searchQuery.toLowerCase()));
		},

		getListFromServer(isRefresh, target) {

			if (!boolean(this.apiEndpoint)) return;

			this.isLoading = true;
			axios.get(this.apiEndpoint, {
					params: this.getApiParams()
				})
				.then(response => {
					this.postApiResponseOperations(response.data.data, isRefresh, target);
				})
				.catch(error => {
          // if request fails, there won't be any next page, so nextPageUrl will be marked as null
				  this.nextPageUrl = null
					errorHandler(error);
				})
				.finally(() => {
					this.isLoading = false;
				})
		},

		/*
		 * Ensure that the scroll position remains where it was before the list size changed.
		 * It's important to wait for $nextTick here so that the DOM elements have been inserted before setting the scroll position.
		 */
		async postApiResponseOperations(responseData, isRefresh, target) {
			try {
				this.nextPageUrl = responseData.next_page_url;
				if (isRefresh) {
					this.listElements = responseData.data;
				} else {
					const ul = target.offsetParent;
					const scrollTop = target.offsetParent.scrollTop;
					this.listElements.push(...responseData.data);
					await this.$nextTick();
					ul.scrollTop = scrollTop;
				}
			} catch (error) {
				console.error(error);
			}
		},

		getApiParams() {
			let apiParams = boolean(this.apiParameters) ? this.apiParameters : {};

			if (apiParams instanceof Map) {
				apiParams = convertMapToSubmitableData(apiParams);
			}

			let params = JSON.parse(JSON.stringify(apiParams));
			params['search-query'] = this.searchQuery;
			params['page'] = this.page || undefined;
			params['paginate'] = 1;
			return params;
		},

		/**
		 * Clear search query on search:blur event
		 * https://github.com/sagalbot/vue-select/issues/287
		 */
		clearSearchQuery() {
			this.$refs.faveoDynamicSelect.onEscape()
		},

		/**
		 * The observer is set to call infiniteScroll when the <li> is completely visible within the list.
		 * Some fancy destructuring is done here to get the first ObservedEntry, and specifically the isIntersecting & target properties.
		 * If the <li> is intersecting, we increase the limit
		 */
		infiniteScroll([{ isIntersecting, target }]) {
			if (isIntersecting) {
				this.page += 1;
				this.getListFromServer(false, target);
			}
		},

		/**
		 * When Vue Select opens, the open event is emitted and connectObserver will be called.
		 * We wait for $nextTick() so that the $ref we need will exist, then begin observing it for intersection.
		 */
		async connectObserver() {
			if (this.hasNextPage) {
				await this.$nextTick();
				this.observer.observe(this.$refs.dynamicSelectLoader);
			} else {
				this.getListFromServer(true)
			}
		},

		disconnectObserver() {
			if (this.observer) {
				this.observer.disconnect();
			}
		},

		/**
		 * Reset properties
		 * Need to reset properties if `apiEndpoint` OR `apiParameters` changes 
		 */
		resetProperties() {
			this.listElements = [];
			this.page = 0;
			this.observer = null;
			this.nextPageUrl = '';
			this.searchQuery = undefined;
			this.isLoading = false;
			this.selectedValue = null;
		}

	},

	watch: {

		elements(newValue, oldValue) {
			if (newValue) {
				this.listElements = newValue;
			}
		},

		apiEndpoint(newValue, oldValue) {
			if (boolean(newValue)) {
				this.flushAndRestart();
			}
		},

		apiParameters(newValue, oldValue) {
			if (boolean(newValue)) {
				this.flushAndRestart();
			}
		},


		// TODO This `value` watcher needs refactoring
		value(newValues, oldValues) {
			this.selectedValue = newValues;

			// if taggable is there, handle value seperatey
			// loop over all values and see if a value is string
			// if it is a string make an object out of it with name as value and id as value
			if (this.taggable) {
				if (newValues === null) return;
				let modifiedValues = newValues.map(value => {
					let valueObj;
					if (typeof value === 'string') {
						valueObj = {}
						valueObj.id = value;
						valueObj[this.optionLabel] = value;
					}
					return valueObj || value;
				});
				// call onChange only if value if 2 objects are different to avoid infinite loop
				if (JSON.stringify(modifiedValues) !== JSON.stringify(newValues)) {
					this.onChange(modifiedValues, this.name)
				}
			}
		}
	},

	components: {
		'v-select': vSelect,
		'form-field-template': FormFieldTemplate,
		'faveo-image-element': require('components/Common/FaveoImageElement'),
		'loader': require('components/Extra/Loader'),
	}
};
</script>

<style scoped>
#faveo-dynamic-select-img {
	border: 1px solid transparent;
	width: 18px;
	border-radius: 10px;
	margin-top: -1px;
}
.loader-area {
	padding-top: 3px;
	height: 37px;
}
</style>