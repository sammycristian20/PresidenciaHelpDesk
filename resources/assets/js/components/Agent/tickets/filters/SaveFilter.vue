<template>

  <!-- A filter pop-up that should appear while saving a filter -->
  <modal :showModal="true" :onClose="onClose">

    <!-- if mode is delete, we only show the confirmation message that if they really want to delete -->
    <div slot="title">
      <!-- should change according to saving or sharing -->
      <h4 class="modal-title" v-if="mode !== 'delete'">{{lang('save_filter')}}</h4>
      <h4 v-else class="modal-title">{{lang('are_you_sure_you_want_to_delete')}}</h4>
    </div>

    <div slot="alert">

      <alert :componentName="this.$options.name"/>
    </div>

    <div v-if="mode !== 'delete'" slot="fields" class="save-filter-modal-body">
      <custom-loader v-if="loading"></custom-loader>

      <div class="row">
        <text-field name="name" :label="lang('name')" :value="filterName" :onChange="onChange" :required="true" classname="col-md-6">
        </text-field>
      </div>

      <section>
        <div class="row">
          <div class="col-sm-4" style="line-height: 2">
            <label for="isDisplayOnDashboard">
              <input type="checkbox" name="isDisplayOnDashboard" id="isDisplayOnDashboard" v-model="isDisplayOnDashboard">
              {{lang('display_this_filter_on_dashboard')}}
            </label>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6">
            <label for="dashboard-widget-bg-color"> {{ lang('icon_color') }} </label>
             <label class="is-danger" v-if="isDisplayOnDashboard">*</label>
             <tool-tip :message="lang('dashboard_icon_background_color_hint')" size="small"></tool-tip>
            <color-picker id="dashboard-widget-bg-color" :color="iconColor" v-model="iconColor" :disabled="!isDisplayOnDashboard" />
          </div>

          <dynamic-select name="iconClass" :label="lang('icon_class')" :value="iconClass" :onChange="onChange"
            classname="col-md-6" :elements="iconList" :disabled="!isDisplayOnDashboard" :multiple="false" option-label="icon_class" :required="isDisplayOnDashboard" :clearable="false" :hint="lang('dashboard_icon_class_hint')">
          </dynamic-select>
        </div>
      </section>

      <section>
        <div class="row">
          <div class="col-sm-4" style="line-height: 2">
            <span><strong>{{lang('share_with') + ':'}}</strong></span><tool-tip :message="lang('share_filter_tooltip_message')" size="small"></tool-tip>
          </div>
        </div>

        <div class="row">
          <dynamic-select name="agents" :label="lang('agents')" :value="agents" :onChange="onChange"
            classname="col-md-6" apiEndpoint="/api/dependency/agents" :multiple="true">
          </dynamic-select>

          <dynamic-select name="departments" :label="lang('departments')" :value="departments" :onChange="onChange"
            classname="col-md-6" apiEndpoint="/api/dependency/departments" :multiple="true">
          </dynamic-select>
        </div>
      </section>

    </div>

    <!-- body for deleting confirmation -->
    <div v-else slot="fields">
      <span>You are about to delete a filter with name <strong>{{filterName}}</strong>
        created by you. Please confirm</span>
    </div>


    <div slot="controls">
      <button v-if="showSave()" type="button" @click = "saveFilter('update')" class="btn btn-primary">
        <i class="fas fa-sync" aria-hidden="true"></i>&nbsp;{{lang('update')}}</button>
      <button v-if="showSaveAsNew()" type="button" @click = "saveFilter('create')" class="btn btn-primary">
        <i class="fas fa-save" aria-hidden="true"></i>&nbsp;{{lang('save_as_new')}}</button>
      <button v-if="showDelete()" type="button" @click = "deleteFilter()" class="btn btn-danger"><i class="fas fa-trash" aria-hidden="true"></i>&nbsp;{{lang('delete')}}</button>
    </div>

  </modal>
</template>

<script type="text/javascript">

  import {successHandler, errorHandler} from 'helpers/responseHandler';
  import {extractOnlyId, boolean} from 'helpers/extraLogics';
  import axios from 'axios';

  export default {

    name:'save-filter',

    description:'Save filter pop-up in ticket filters',

    props:{

      /**
       * Name of the filter. If passed from parent, it will reflect here. else it will update to parent
       * @type {Object}
       */
      filterName:{ type:String, default:'' },

      /**
       * Filter data that has to be sent to the backend in detailed format
       * for eg. for first helptopic it will be {id:1, name: 'test_helptopic'}
       * @type {Object}
       */
      filterObjects:{ type:Object, required:true },

      /**
       * Filter data that has to be sent to the backend with just the IDs or final value,
       * for eg. for first helptopic it will be 1
       * @type {Object}
       */
      filterValues:{ type:Object, required:true },

      /**
       * Id of the filter. if a new filter is getting created, it will be zero
       * @type {Object}
       */
      filterId:{ type:Number, default: 0 },

      /**
       * Toggles the visibility of the filter
       * @param showHidePopup
       * @param isClosePopupClicked
       */
      toggleFilterPopUp: { type:Function, required:true },

      /**
       * It can be either of `delete`, `share_only` or `save`
       * @type {Object}
       */
      mode: {type: String, default:''},

      /**
       * Initial lists of agents
       * @type {Array}
       */
      initialAgents:{type:Array, default:()=>[]},

      /**
       * Initial list of agents
       * @type {Array}
       */
      intitialDepartments:{type:Array, default:()=>[]},

      /**
       * Applies filter
       * @type {Object}
       */
      postFilterSaveAction: {type: Function, required: true},

      dashboardConfig: {
        type: Object,
        default: function () {
          return {
            displayOnDashboard: false,
            iconColor: '#00C0EF',
            iconClass: {
              "icon_class": "fas fa-ticket-alt"
            }
          }
        }
      }
    },

    data(){
      return{
        /**
         * Name of the filter
         * @type {String}
         */
        name:this.filterName,

        /**
         * agents with which filters are/will be shared
         * @type {Array}
         */
        agents: this.initialAgents,

        /**
         * Departments with which filters will be shared
         * @type {Array}
         */
        departments: this.intitialDepartments,

        /**
         * Whether filter should be visible or not
         * @type {Boolean}
         */
        shallFilterBeVisible:true,

        /**
         * If an Api response has been made and it is still loading
         * @type {Boolean}
         */
        loading:false,


        /**
         * If true this report will be displayed on the dashboard
         */
        isDisplayOnDashboard: false,

        iconColor: '',

        iconClass: '',

        iconList: []

      }
    },

    beforeMount() {
      this.isDisplayOnDashboard = this.dashboardConfig.displayOnDashboard;
      this.iconColor = this.dashboardConfig.iconColor;
      this.iconClass = this.dashboardConfig.iconClass;
    },

    methods:{

  			/**
  			 * Updates a filter or creates a new one at backend
  			 * @param  {String} type 	type can be either `create` or `update`
  			 * @return {undefined}
  			 */
  			saveFilter(type){

            //removing all previous validation errors
            this.$store.dispatch('unsetValidationError');

            this.loading = true;

  					axios.post('/api/agent/ticket-filter', this.getApiParams(type) )
  					.then(res=>{
  						// this.selectedFilter = res.data.data.id
              this.loading = false;
  						successHandler(res, this.$options.name);


              // takes post filter save actions after it gets saved successfully
              this.postFilterSaveAction();

              setTimeout(()=>{this.toggleFilterPopUp(false, false)}, 2000)

              // sidebar has to be updated as soon as filter gets updated
              window.eventHub.$emit('update-sidebar');

  					}).catch(err=>{
              this.loading = false;
              errorHandler(err,this.$options.name);
  					});
        },
        
        getApiParams(type) {
  				const ticketId = (type == 'create') ? 0 : this.filterId;
          let params = {
            id: ticketId,
            name: this.name ,
            fields: this.getFormattedFilterData(),
            agents: extractOnlyId(this.agents),
            departments: extractOnlyId(this.departments),
            display_on_dashboard: this.isDisplayOnDashboard,
            icon_class: this.iconClass.icon_class,
            icon_color: this.iconColor
          }

          return params;
        },


        /**
         * Fetch all the icon-classes 
         */
        getIconList() {
          axios.get('/json/icon-list.json')
          .then((response) => {
            this.iconList = response.data.data;
          })
          .catch((error) => {
            console.error('SaveFilter | getIconList | Error= ', error);
          })
        },

        getFormattedFilterData(){
          const timeRangeFilterOptions = ['due-on', 'created-at', 'updated-at'];

          return Object.keys(this.filterObjects)
          .filter(keyOne => {
            return boolean(this.filterObjects[keyOne])
          })
          .map(key => {
            let value = (this.filterValues[key] !== undefined)? this.filterValues[key] : this.filterObjects[key];
            return {key: key, value: value}
          })
        },

        deleteFilter(){
          //removing all previous validation errors
          this.$store.dispatch('unsetValidationError');

          this.loading = true;

          axios.delete('/api/agent/ticket-filter/' + this.filterId).then(res=>{
            // this.selectedFilter = res.data.data.id
            this.loading = false;
            successHandler(res, this.$options.name);
            setTimeout(()=>{this.toggleFilterPopUp(false, false)}, 2000)

            // redirect it to inbox after deleting the filter
            this.redirect('/tickets?show[]=inbox&departments[]=All');
          }).catch(err=>{
            this.loading = false;
            errorHandler(err,this.$options.name);
          });

        },

        /**
         * Updates local state variables
         * @param  {String|Object|Number} value   value of the changed field
         * @param  {String}               name    name of the changed field
         * @return {undefined}
         */
        onChange(value, name){
          this[name] = value
        },

        /**
         * Closes the ticket save filter pop-up
         * @return {String} [description]
         */
        onClose(){
          this.$store.dispatch('unsetValidationError');
          this.$store.dispatch('unsetAlert');
          this.toggleFilterPopUp(false, true);
        },

        /**
         * If `save as new` button should be visible
         * @return {Boolean}
         */
        showSaveAsNew(){
          return this.mode !== 'delete'
        },

        /**
         * If save button should be visible
         * @return {Boolean}
         */
        showSave(){
            return this.mode !== 'delete' && this.filterId != 0
        },

        /**
         * if delete button should be visible
         * @return {Boolean}
         */
        showDelete(){
          return this.mode === 'delete'
        }

    },

    watch: {

      /**
       * Watching `isDisplayOnDashboard` to decide whether to make an API call or not
       * if `isDisplayOnDashboard` is true and iconList is empty then only fetch the list json
       */
      isDisplayOnDashboard: function() {
        if(this.isDisplayOnDashboard && this.iconList.length === 0) {
          this.getIconList();
        }
      }
    },

    components:{
      'modal': require('components/Common/Modal'),
      'text-field':require('components/MiniComponent/FormField/TextField'),
      'dynamic-select': require('components/MiniComponent/FormField/DynamicSelect'),
      'alert': require("components/MiniComponent/Alert"),
      "custom-loader": require("components/MiniComponent/Loader"),
      "tool-tip": require("components/MiniComponent/ToolTip"),
      'checkbox' :require('components/MiniComponent/FormField/Checkbox'),
      'color-picker': require('components/MiniComponent/FormField/ColorPicker.vue'),
    }
  }

</script>

<style scoped>
  .center-of-page {
    position: absolute !important;
    left: 11% !important;
  }

.mailinbox input[type="checkbox"] {
  display: inline;
}
</style>
