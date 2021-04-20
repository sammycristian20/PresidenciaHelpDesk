<template>

  <div class="btn-group right">
    <button
      id="btn-click"
      type="button"
      :title="lang(title)"
      :class="[ btnColorClass, 'form-control' ]"
      @click = "onClick"
      data-toggle="dropdown">
        <span v-if="btnIconClass != ''" :class="btnIconClass"></span>
        <span v-if="isShowCaretIcon" class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <li id="list-item" v-for="item in componentElements" @click="onValueChange(item)">{{ item.name }}</li>
    </ul>
  </div>

</template>

<script>

    import axios from 'axios'
    import { errorHandler } from "helpers/responseHandler";
    import {flatten} from "helpers/extraLogics";

    export default {

        name : "button-dropdown",

        description : "",

        props:{

          /**
           * list of the elements that has to be displayed
           * when element is not passed as prop, it is assumed that elements will be fetched using an API call
           * @type {Array}
           */
          elements:{ type:Array, default: ()=>[] },


          /**
           * selected value of the field.
           * list of already selected element ids that has to be displayed
           * @type {Number|Boolean}
           */
          value:{ type:String|Number, default: null },


          /**
           * Api endpoint which will return the list of elements which are needed to be displayed
           * if api endPoint is null, we use elements to display
           * @type {String}
           */
          apiEndpoint : { type: String, default: null },

          /**
           * The function which will be called as soon as value of the field changes
           * It should have two arguments `value` and `name`
           *     `value` will be the updated value of the field
           *     `name` will be thw name of the state in the parent class
           *
           * An example function :
           *         onChange(value, name){
           *             this[name]= selectedValue
           *         }
           *
           * @type {Function}
           */
          onChange : {type: Function, Required: true },


          /** Button color class 
          * This can be any valid class
          */
          btnColorClass: {type: String, default: 'btn-primary'},

          /** Button icon class 
          * This can be any valid icon class
          */
          btnIconClass: {type: String, default: ''},


          /** Tooltip on the dropdown button*/
          title: { type: String, default: ''},


          /** Identity flag is for which purpose this component is used.
          * This will be used in `componentElements` data modification 
          */
          identityFlag: { type: String, Required: true},

          /** This will decide whether to show `caret` icon.
          * Default is false 
          */
          isShowCaretIcon: { type: Boolean, default: false},

          /**
           * whether api call should be made on clicking on the field or as soon as field is mounted
           * if passed as true, options will be populated as soon as component mounts else will populate if clicked
           * @type {Boolean}
           */
          prePopulate : { type:Boolean, default:false },

        },

        data(){
          return{
            /**
             * elements that has to be shown in the field as dropdown
             * @type {Array}
             */
            componentElements: this.elements,
            }
        },

        mounted(){
          if(this.prePopulate && this.elements.length === 0 ){
              this.getValuesFromAPI()
          }
        },

        methods:{

            /**
             * Makes APi call to the server and populate local state with the list of elements
             * @param  {Object} params  parameters that can be passed along with the API call
             * @return {void}
             */
            getValuesFromAPI(params = {}){
              axios.get(this.apiEndpoint, {params: params}).then(res => {
                this.componentElements = flatten(res.data.data);
                if(this.identityFlag == "departmentList") {
                  this.modifyResp();
                }
              }).catch(err=>{
                  errorHandler(err)
              })
            },

            modifyResp() {
              let allDeptObj = {
                id: 'all_departments',
                name: 'All departments'
              };
              this.componentElements.push(allDeptObj);
              this.componentElements.reverse();
              this.componentElements.forEach(function(element) { element.queryPerm = 'dept-ids=' + element.id; });
            },

            /**
             * gets values from the API on click, if prePopulate is OFF
             * @return {void}
             */
            onClick(){
              if(!this.prePopulate){
                  this.getValuesFromAPI()
              }
            },

            /**
             * if value changes, it updates parent state
             * @param  {Array} newValues   the selected field(s)
             * @return {void}
             */
            onValueChange(newValues){
              this.onChange(newValues, this.name)
            }
        },
    };

</script>

<style type="text/css" scoped>

  .dropdown-menu {
    background-color: #f1f1f1;
  }
  .dropdown-list {
    cursor: default;
    background-color: #FFF;
    border-radius: 4px;
    text-align: left;
  }
  button {
    cursor: pointer;
  }

  li {
    margin: 6px;
    cursor: pointer;
    padding-left: 4px;
  }
  li:hover {
    background-color: #3c8dbc;
    color: white;
  }

</style>