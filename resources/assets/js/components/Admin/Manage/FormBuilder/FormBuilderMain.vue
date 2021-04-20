<template>
  <div>
    <alert componentName="form-builder-main" />

    <div class="card card-light">

      <div class="card-header">

        <loader v-if="showLoader" />
        
        <h3 class="card-title" id="box-title-1">{{lang(pageHeading)}}</h3>

        <div class="card-tools">

          <button class="btn btn-tool btn-default form-save-btn" @click="onSaveForm()">
          
              <span class="fas fa-save"></span> {{lang('save')}}
          </button>
        </div>
      </div>

      <div class="card-body" id="form-builder-body">
        <faveo-form-builder-menu v-if="hasMenuLoaded" :form-group-list="formGroupList"/>

        <div v-if="hasFormFieldsLoaded && hasMenuLoaded">
          <div class="row" v-if="isCreateFormGroup || isEditFormGroup">

            <text-field :id="localFormNameObj.id" :classname="localFormNameObj.className" :label="lang(localFormNameObj.label)" :value="localFormNameObj.value" type="text" :name="localFormNameObj.id" :onChange="(value) => localFormNameObj.value = value" :placehold="lang(localFormNameObj.placeholder)" :required="true"/>

            <dynamic-select v-for="item in localFormLinker" :key="item.id" :id="item.id" :name="item.id" :classname="item.className" :apiEndpoint="item.apiEndpoint" :label="lang(item.label)" :value="item.value" :required="false" :multiple="true"  :onChange="(value) => item.value = value"/>

          </div>

          <ul class="nav nav-tabs" role="tablist">
                
            <li class="nav-item" v-for="tab in formCategoryTypes" :key="tab.category" :id="tab.category">
              
              <a :class="['nav-link', activeFormCategoryType === tab.category ? 'active' : '']" data-toggle="pill" href="javascript:;" role="tab" @click="onTabClick(tab.category)">{{tab.name}}</a>
            </li>  
          </ul>

          <div class="mt-2">
            
            <draggable-form-field-item v-model="formFieldsStoreValue" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import _ from 'lodash-core';
import { errorHandler, successHandler } from "helpers/responseHandler";
import { getIdFromUrl } from 'helpers/extraLogics';
import axios from 'axios';
import { validateFormGroup } from 'helpers/validator/formGroupRules';
import { boolean } from 'helpers/extraLogics';

export default {
  
  name: 'form-builder-main',

  components: {
    'faveo-form-builder-menu': require('./FormBuilderMenu'),
    'alert': require('components/MiniComponent/Alert'),
    'loader': require('components/MiniComponent/Loader'),
    'text-field': require('components/MiniComponent/FormField/TextField'),
    'dynamic-select': require('components/MiniComponent/FormField/DynamicSelect'),
  },

  props: {

    /**
     * Form name object
     *
     * example:- { id: 'name' , className: 'col-md-4', label: 'form_group_name', placeholder: 'name_your_form_group', value: '', submitFormParamKey: 'name' }
     */
    formNameObj: {
      type: Object,
      default: () => {}
    },

    /**
     * form linker object
     * used for linking form with some field element
     * 
     * example:- [{ id: 'departments', className: 'col-md-4', apiEndpoint: '/api/dependency/departments?config=true', label: 'department', value: [], submitFormParamKey: 'department_ids' }]
     */
    formLinker: {
      type: Array,
      default: () => []
    },

    // Api endpoint for edit form group
    editFormGroupApi: {
      type: String,
      default: () => ''
    },

    // Api endpoint for posting form group
    postFromGroupApi: {
      type: String,
      default: () => ''
    },

    // form category type -- can be 'ticket', 'user', 'group', etc
    formCategoryType: {
      type: String,
      default: ''
    }

  },

  data: () => {
    return {
      pageHeading: 'create-form', // text to show on the page header
      hasMenuLoaded: false, // has form menu json loaded?
      hasFormFieldsLoaded: false, // has form fields loaded?
      showLoader: true, // show loader or not?
      formGroupList: [], // array of form groups if any
      isCreateFormGroup: false, // component will behave like create from if true
      isEditFormGroup: false, // component will behave like edit form if true
      formGroupId: null, // Id of form group -- used in case of edit form
      activeFormCategoryType: '', // may be `ticket` OR `user` OR `organisation`, etc
      formCategoryTypes: [],
      localFormNameObj: '',
      localFormLinker: ''
    }
  },

  beforeMount() {
    this.localFormNameObj = this.formNameObj;
    this.localFormLinker = this.formLinker;
    this.activeFormCategoryType = this.formCategoryType;
    this.setInitialValue();
  },

  computed: {
    formFieldsStoreValue: {
      get() {
        return this.$store.getters.getFormFields;
      },
      set(value) {
        this.$store.dispatch('updateFormFields', value);
      }
    }
  },

  created() {
    window.eventHub.$on('formFieldSettingsApplied', this.applySettingsChanges);
    window.eventHub.$on('onRemoveElement', this.onRemoveElement);
    window.eventHub.$on('getFormDataFromServer', this.getFormDataFromServer);
  },

  methods: {

    /**
     * Set initial values
     * check if form create page
     * check if form edit page
     * get form group list if category is ticket
     * get form menu json
     */
    setInitialValue() {
      const currentRoute = this.getCurrentWindowLocation();

      if (currentRoute.indexOf('create') !== -1 && currentRoute.indexOf('forms/create') === -1) { // create form group case
        this.isCreateFormGroup = true;
        this.hasFormFieldsLoaded = true;
        this.pageHeading = 'create-form';
        this.showLoader = false;
      }
      else if (currentRoute.indexOf('edit') > -1) { // Edit form group case
        this.formGroupId = getIdFromUrl(currentRoute);
        this.isEditFormGroup = true;
        this.pageHeading = 'edit-form';
        this.showLoader = false;
      }
      
      if(!this.isCreateFormGroup && !this.isEditFormGroup) {
        this.getFormCategories();
      } else {
        this.getMenuJson();
        this.assignFormFields();
      }
    },

    getFormCategories() {

      axios.get('api/dependency/form-categories')
      .then(resp => {
        this.formCategoryTypes = resp.data.data.form_categories;
        this.activeFormCategoryType = this.formCategoryTypes[0].category;
        this.getFormDataBasisOnCategory();
      })
      .catch(error => {
        errorHandler(error, 'form-builder-main');
      })
    },

    getFormDataBasisOnCategory() {
      this.getFormGroupList();
      this.getMenuJson();
      this.assignFormFields();
    },

    getCurrentWindowLocation() {
      return window.location.pathname;
    },

    // Fetch json of available types of form fields
    getMenuJson() {
      axios.get('api/get-form-menu/' + this.activeFormCategoryType)
      .then(resp => {
        this.$store.commit('updateFormMenu', resp.data.data);
        this.hasMenuLoaded = true;
      });
    },

    // fetch form-fields from server and assign it to component properties
    assignFormFields() {
      if (this.isCreateFormGroup) {
        this.$store.commit('updateFormFields', []);
      } else {
        this.getFormDataFromServer()
        .then((resp) => {
          if (this.isEditFormGroup) {
            this.localFormNameObj.value = resp.data.data[this.localFormNameObj.id] ? resp.data.data[this.localFormNameObj.id] : '';
            this.localFormLinker.forEach(element => {
              element.value = resp.data.data[element.id] ? resp.data.data[element.id]: [];
            });
          }
          this.hasFormFieldsLoaded = true;
        })
      }
    },

    onTabClick(newTab) {
      if(this.activeFormCategoryType === newTab) {
        return;
      }
      this.activeFormCategoryType = newTab;
      this.getMenuJson();
      this.getFormGroupList();
      this.getFormDataFromServer();
    },

    // get form-fields from server
    getFormDataFromServer() {
      this.showLoader = true;
      this.$store.commit('updateFormCategoryType', this.activeFormCategoryType);
      let apiEndpoint = 'api/get-form-data';
      let params = {
        category: this.activeFormCategoryType,
        config: true
      };

      if (this.isEditFormGroup) {
        apiEndpoint = this.editFormGroupApi + '/' + this.formGroupId;
        params.config = false;
      }

      return axios.get(apiEndpoint , { params }).then((response) => {
        this.$store.commit('updateFormFields', response.data.data.form_fields);
        this.showLoader = false;
        return response
      }).catch((error) => {
        this.showLoader = false;
        errorHandler(error, 'form-builder-main');
      })
    },

    // If the case of create/edit form check form name is valid or not
    isValidFormName() {
      if(this.isCreateFormGroup || this.isEditFormGroup){
        const { errors, isValid } = validateFormGroup(this.$props);
        return isValid;
      } else {
        return true;
      }
    },

    // fetch form-groups from server
    getFormGroupList() {
      if (this.activeFormCategoryType === 'user' || this.activeFormCategoryType === 'organisation') {
        return;
      }
      this.showLoader = true;
      const parameters = { limit: 2000, supplements: this.activeFormCategoryType }; // a big number so that all(may be) form-groups can be listed in the dropdown
			axios.get('api/dependency/form-groups', { params: parameters})
				.then(res => {
					this.formGroupList = res.data.data.form_groups.map(s => {
            return {
              id: s.id,
              title: s.name,
              name: s.name,
              type: 'group',
              edit_url: s.edit_url,
              is_deletable: 1
            }
          });
        })
        .catch(err => {
					console.error('Error in getting form group list ', err);
				}).finally(() => {
          this.showLoader = false;
        })
    },
    
    // Save button click event
    async onSaveForm() {
      if (this.isValidFormName()) {
        const formBuilderData = this.$store.getters.getFormFields;
        this.assignSortOrder(formBuilderData);
        this.saveForm(formBuilderData);
      }
    },

    /**
     * Assign sort_order to each field 
     * `sort_order` will be the array index of the element in which the element presents
     */
    assignSortOrder(array) {
      for (let i = 0; i < array.length; i++) {
        array[i].sort_order = i;
        if(boolean(array[i].options)) {
          this.assignSortOrder(array[i].options);
        }
        if(boolean(array[i].labels)) {
          this.assignSortOrder(array[i].labels);
        }
        if(boolean(array[i].nodes)) {
          this.assignSortOrder(array[i].nodes);
        }
        if(boolean(array[i].labels_for_form_field)) {
          this.assignSortOrder(array[i].labels_for_form_field);
        }
      }
    },

    // make api call to save form fields in DB
    saveForm(formBuilderData) {
      this.showLoader = true;
      let api = (this.isCreateFormGroup || this.isEditFormGroup) ? this.postFromGroupApi : 'api/post-form-data';

      axios.post(api, this.getSaveFormParams(formBuilderData))
        .then(resp => {
          successHandler(resp, 'form-builder-main');
          if(this.isCreateFormGroup || this.isEditFormGroup) {
            this.showLoader = false;
							setTimeout(() => {
								window.location.href = window.axios.defaults.baseURL + '/form-groups';
							}, 500);
						} else {
							  this.getFormDataFromServer().then( () => {
                this.showLoader = false;
              })
						}
        }).catch(error => {
          this.showLoader = false;
          errorHandler(error, "form-builder-main");
        });
    },


    // returns parameters for making api call to save form
    getSaveFormParams(formBuilderData) {
      let params = {};
      params['category'] = this.activeFormCategoryType;
      params['form-fields'] = formBuilderData;

      if (this.isCreateFormGroup || this.isEditFormGroup) {
        params[this.localFormNameObj.submitFormParamKey] = this.localFormNameObj.value;

        this.localFormLinker.forEach(element => {
          params[element.submitFormParamKey] = element.value.map(e => e.id);
        });
      }
      if (this.isEditFormGroup) {
        params['id'] = this.formGroupId;
      }
      return params;
    },

    // Remove element from db/local
    onRemoveElement(fieldData, reference_id, reference_type) {
      let isConfirmed = confirm('Are you sure you want to delete?');
      if(isConfirmed) {
        if(this.isDeleteFromDB(fieldData)) {
          this.deleteElementFormDB(fieldData.id, reference_id, reference_type);
        } else {
          this.deleteElementLocally(fieldData.form_identifier);
        }
      }
    },

    /**
     * Delete formfield locally if the successfully deleted from db  
     */
    deleteElementLocally(form_identifier) {
      const data = this.$store.getters.getFormFields;
      const isResolved = this.removeAt(data, form_identifier)
      if(isResolved) {
        this.$store.dispatch('updateFormFields', data);
      }
    },

    // make api call to delete form field
    deleteElementFormDB(id, reference_id, reference_type) {
      this.showLoader = true;
      let api = 'api/delete-form-data/form-field/' + id;
      if (reference_id) { // if reference_id is truthy(condition for form-group)
        api = 'api/unlink-form-group/' + reference_type + '/' + reference_id;
      }
      axios
      .delete(api)
      .then(resp => {
        successHandler(resp, 'form-builder-main');
        this.getFormDataFromServer();
        this.showLoader = false;
      })
      .catch(error => {
        this.showLoader = false;
        errorHandler(error, 'form-builder-main');
      });
    },


    // check whether formfield have to removed from db or locally
    isDeleteFromDB(fieldData) {
      /**
       * If group type, check for groupid
       * If it has `groupid` property that means it's being created locally not present in DB
       */
      if(fieldData.type === 'group') {
        return typeof fieldData.groupid === 'undefined';
      }
      /**
       * We are assigning timestamp to the id of newly dropped items
       * At this time, timestamp length will be greater or equal to 13
       * So, if it is less than 13, remove the element from db
       **/
      return fieldData.id.toString().length < 13;
    },

    // recursively find element which need to be removed and remove the element by it's ID -- considered `nodes` and `options` cases
    removeAt(array, form_identifier) {
      let idx = _.findIndex(array, (element) => element.form_identifier === form_identifier);
      if(idx !== -1) {
        array.splice(idx, 1);
      } else {
          for (let i = 0; i < array.length; i++) {
            if(boolean(array[i].options)) {
              this.removeAt(array[i].options, form_identifier);
            }
            if(boolean(array[i].nodes)) {
              this.removeAt(array[i].nodes, form_identifier);
            }
          }
        }
      return new Promise(resolve => {
        resolve(true);
      });
    },


    // Listen event emmited from form-setting component to change form fields properties
    async applySettingsChanges(changedValue) {
      const data = this.$store.getters.getFormFields;
      const isResolved = await this.assignNewValue(data, changedValue.form_identifier, changedValue);
      if(isResolved) {
        this.$store.dispatch('updateFormFields', data);
      }
    },

    // recursively finds the index of the property that has been altered and replace old object with new object -- considered `nodes` and `options` cases
    assignNewValue(array, affectedId, newObj) {
      let idx = _.findIndex(array, (element) => element.form_identifier === affectedId);
      if(idx !== -1) {
        array[idx] = newObj;
      } else {
        for (let i = 0; i < array.length; i++) {
          if(boolean(array[i].options)) {
            this.assignNewValue(array[i].options, affectedId, newObj);
          }
          if(boolean(array[i].nodes)) {
            this.assignNewValue(array[i].nodes, affectedId, newObj);
          }
        }
      }
      return new Promise(resolve => {
        resolve(true);
      });
    },

  }

}
</script>

<style lang="css" scoped>
  .active {
    border-top: 2px solid #3c8dbc;
    border-right: 1px solid #dedede;
    border-bottom: 0px;
  }
</style>

