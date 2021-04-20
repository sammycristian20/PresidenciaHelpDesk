<template>
  <div id="custom-report-column">
    <modal :showModal="true" :onClose="closeView" modalBodyClass="custom-column-modal-body">

      <div slot="title">
        <h4 class="modal-title">{{lang('custom_column')}}</h4>
        <custom-loader v-if="loading"></custom-loader>
      </div>

      <div slot="alert">
        
        <alert componentName="custom-report-column"/>
      </div>

      <div slot="fields" id="link-container" refs="modalBodyRef">

        <div id="short-code-container">
          <div class="card card-light ">
            <div class="card-header"><h3 class="card-title">{{lang('list_of_available_shortcodes')}}</h3></div>
            <div class="card-body">
              <relative-loader v-if="!shortCodes.length"/>
              <ul v-else id="report-shortcode-list" class="row">
                <li v-for="shortcode in shortCodes" class="col col-sm-6">{{shortcode}}</li>
              </ul>
            </div>
          </div>
        </div>

        <text-field :label="lang('column_name')" :value="name" name="name" :onChange="onChange" classname="col-sm-12" :hint="lang('report_custom_column_name_hint')">
        </text-field>

        <text-field :label="lang('equation')" :value="equation" name="equation" :onChange="onChange" classname="col-sm-12" type="textarea" :hint="lang('report_custom_column_equation_hint')">
        </text-field>

        <checkbox name="isTimestamp" :value="isTimestamp" :label="lang('is_timestamp')"
                  :onChange="onChange" classname="col-sm-12">
        </checkbox>


        <div v-show="isTimestamp" class="row">
          <div class="col-sm-6">
            <label for="timestamp_format">{{lang('timestamp_format')}}</label>
            <select id="timestamp_format" class="form-control" name="timestamp_format" v-model="selectedTimestampFormat">
              <option v-for="(item, index) in timestampFormats" :key="index" :value="item">{{item}}</option>
            </select>
          </div>

          <text-field name="timestamp_format_example" :label="lang('preview')" :value="getTimestampExample()" classname="col-sm-6" :disabled="true" :onChange="() => {}">
          </text-field>
        </div>

      </div>

      <div slot="controls">

        <button type="button" id="custom-column-submit" @click="onSubmit" :disabled="loading" class="btn btn-primary update-btn">
          <span class="fas fa-sync"></span>&nbsp;{{lang('submit')}}
        </button>

      </div>

    </modal>
  </div>
</template>

<script>

  import FaveoBox from "components/MiniComponent/FaveoBox";
  import {errorHandler, successHandler} from "helpers/responseHandler";
  import { carbonToMomentFormatter, getCurrentFormattedTime } from "helpers/extraLogics";
  import {validateCustomColumn} from "../../helpers/validator/customColumnRules";
  import Modal from "components/Common/Modal";
  import axios from 'axios';

  export default {

    name: 'custom-report-column',

    description: 'handles adding custom columns in reports',

    props: {

      /**
       * handler for closing the component view
       */
      closeView: {type: Function, required: true},

      /**
       * The column which is getting edited
       */
      column: {type: Object, default: ()=>({})},

      /**
       * If a column is getting edited
       */
      isEditing: {type: Boolean, default: false},

      /**
       * Url endpoint for adding custom columns
       */
      addCustomColumnUrl: {
        type: String,
        required: true
      },

      /**
       * Url endpoint for getting short codes
       */
      shortCodeUrl: {
        type: String,
        required: true
      }
    },

    data(){
      return {

        /**
         * Id of the record (0 for a new record)
         */
        id: 0,

        /**
         * Name of the column
         */
        name: "",

        /**
         * airthmetic equation of the columns
         */
        equation: "",

        /**
         * If the column created is a timestamp
         */
        isTimestamp: false,

        // Timestamp formats if the column created is timestamp
        timestampFormats: ["F j, Y g:i  a", "Y-m-d g:i a", "d-m-Y g:i a", "m-d-Y g:i a", "F j, Y", "Y-m-d", "d-m-Y", "m-d-Y","g:i  a"],

        // Selected timestamp format
        selectedTimestampFormat: '',

        /**
         * if waiting for api call response
         */
        loading: false,

        /**
         * List of available short-codes in report
         */
        shortCodes: [],
      }
    },

    beforeMount(){
      this.getShortCodes();

      // Set default value for timestamp_format
      this.selectedTimestampFormat = this.timestampFormats[0];

      if(this.isEditing){
        this.populateColumnData();
      }

    },

    methods: {

      // get date-time example in human understandable form corresponding to it's carbon format
      getTimestampExample() {
        return getCurrentFormattedTime(carbonToMomentFormatter(this.selectedTimestampFormat));
      },

      /**
       * Populates local state with edit data
       * @return {Void}
       */
      populateColumnData(){
        this.id = this.column.id;
        this.name =  this.column.label;
        this.equation =  this.column.equation;
        this.isTimestamp =  this.column.is_timestamp;
        this.selectedTimestampFormat = this.column.timestamp_format;
      },

      /**
       * checks if custom column data is valid
       * @return {Boolean}
       */
      isValid(){

        const { errors, isValid } = validateCustomColumn(this.$data);

        if (!isValid) {
          return false;
        }
        return true;
      },

      /**
       * Submits column data to server
       * @return {Void}
       */
      onSubmit(){
        if(this.isValid()){

          this.loading = true;

          let params = {name: this.name, equation: this.equation,
            is_timestamp: this.isTimestamp, id: this.id};


          // If isTimestamp is true add selectedTimestampFormat to params
          if(this.isTimestamp) {
            params.timestamp_format = this.selectedTimestampFormat
          }

          axios.post(this.addCustomColumnUrl, params).then(res => {

            // giving 2 seconds to display success message
            setTimeout(() => this.closeView(), 2000)
            successHandler(res, "custom-report-column");
            window.eventHub.$emit('refresh-report');

          }).catch(err => {

            errorHandler(err, "custom-report-column");

          }).finally(res => {

            this.loading = false;
            this.hasDataPopulated = true;
          });
        }
      },

      /**
       * Gets shortcodes from server
       * @return {Void}
       */
      getShortCodes(){
        axios.get(this.shortCodeUrl)
                .then(res => {
                  this.shortCodes = res.data.data;
                })
                .catch(err => {
                  errorHandler(err);
                });
      },

      /**
       * handle state change through form
       * @param  {String} value
       * @param  {String} name
       * @return {Void}
       */
      onChange(value, name) {
        this[name] = value;
      }
    },

    components: {
      "text-field": require("components/MiniComponent/FormField/TextField"),
      "checkbox": require("components/MiniComponent/FormField/Checkbox"),
      "modal": Modal,
      "custom-loader": require("components/MiniComponent/Loader"),
      "relative-loader": require("components/Extra/Loader"),
      'dynamic-select': require('components/MiniComponent/FormField/DynamicSelect'),
    }
  };
</script>

<style>

  .custom-column-modal-body {
    max-height: 60vh;
    overflow-y: auto;
  }

  .form-control { box-shadow : none !important;}
</style>
