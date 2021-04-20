<template>
   <div>
      <alert :componentName="this.$options.name"/>
      <div class="card card-light">
         <div class="card-header">
            <h3 class="card-title">{{lang('user-import-mapper')}}</h3>
            <tool-tip :message="lang('user-import-mapper_instructions')" size="large"></tool-tip>

            <div class="card-tools">
              
              <!--  clicking on this should call the API function instead of mount -->
            <a id="toggle-list-button" v-on:click="toggleList" class="btn-tool">
            <span v-if="!hasDataPopulated" class="glyphicon glyphicon-menu-down" v-tooltip="lang('expand')"></span>
            <span v-else class="glyphicon glyphicon-menu-up" v-tooltip="lang('collapse')"></span>
            </a>
            </div>
         </div>
         <div class="card-body">
            <custom-loader v-if="loading"></custom-loader>
            <div :class="['toggle',{'toggle-expand' : hasDataPopulated}]">
               <table v-if="hasDataPopulated" id="faveo-attribute-list" class="faveo-table table">
                  <thead class="thead-default">
                  <!-- TODO AVINASH WILL CHANGE THE TOOLTIP MESSEGES -->
                  <tr>
                     <th>
                        {{ lang(tableHeadings.faveo.header) }}
                        <tool-tip :message="lang(tableHeadings.faveo.description)"></tool-tip>
                     </th>
                     <th>
                        {{lang(tableHeadings.thirdParty.header)}}
                        <tool-tip :message="lang(tableHeadings.thirdParty.description)"></tool-tip>
                     </th>
                     <th>
                        {{lang(tableHeadings.overwrite.header)}}
                        <tool-tip :message="lang(tableHeadings.overwrite.description)"></tool-tip>
                     </th>
                  </tr>
                  </thead>
                  <tbody v-for="(faveoAttribute, faveoIndex) in faveoAttributeList" class="faveo-attribute-elements">
                  <tr>
                     <td class="padding-top-12">
                        <span :id="'faveo-attribute-label-'+faveoIndex">{{faveoAttribute.label}}</span>
                        <tool-tip v-if="faveoAttribute.description" :message="faveoAttribute.description" size="small" ></tool-tip>
                     </td>
                     <!-- active directory available attributes dropdown -->
                     <td>
                        <select v-model="faveoAttribute.mapped_to" :id="'attribute-list-'+faveoIndex" :disabled="faveoAttribute.editable == 0"
                                class="attribute-list form-control" @change="onChange(faveoAttribute.mapped_to, 'attribute-list-'+faveoIndex)">
                           <!-- if faveo attribute is user_name then display only loginable fields -->
                           <option v-if="displayAttribute(faveoAttribute.name, attribute.is_loginable)" :id="'attribute-element-'+faveoIndex" v-for="attribute in thirdPartyAttributes" :key="attribute.id" :value="attribute.id">
                              {{attribute.name}}
                           </option>
                        </select>
                     </td>
                     <td>
                        <checkbox :value="faveoAttribute.overwrite" :name="'overwrite-'+faveoIndex" label=""
                                  :onChange="onChange" :disabled="disableOverwrite(faveoAttribute.overwriteable)">
                        </checkbox>
                     </td>
                  </tr>
                  </tbody>
               </table>
            </div>
         </div>

        <div class="card-footer">
          
            <button v-if="hasDataPopulated" id="user-import-mapper-submit" type="button" v-on:click="onSubmit" :disabled="loading" class="btn btn-primary update-btn">
            <span class="fas fa-save"></span>&nbsp;{{lang(buttonText)}}
            </button>   
        </div>
      </div>
   </div>
</template>
<script>

   import { errorHandler, successHandler } from 'helpers/responseHandler';
   import axios from 'axios';
   import { boolean } from 'helpers/extraLogics'

   export default {

      name: 'user-import-mapper',

      props: {

         /**
          * API Endpoint used for getting/posting component data
          */
         apiEndpoint: {
            type: String,
            required: true
         },

         updateParent: {
            required: true,
            type: Function
         },

         tableHeadings: {
            required: true,
            type: Object
         },

         buttonText: {
            required: false,
            type: String,
            default: 'save'
         },

         importId: {
            required: false,
            type: String
         },

         expandByDefault: {
            required: false,
            type: Boolean,
            default: false
         }

      },
      //advanced settings has to a seperate post API
      data() {
         return {

            /**
             * will be an array with key and value as object keys.
             * for eg. [{faveo_attribute : 'email', third_party_attribute: '', remember: false}]
             * @type {Array}
             */
            faveoAttributeList: [],

            /**
             * active-directory attribute list
             * @type {Object}
             */
            thirdPartyAttributes: [],

            /**
             * If an API request has been made whose response is pending
             * @type {Boolean}
             */
            loading: false,

            /**
             * if the data is populated
             * @type {Boolean}
             */
            hasDataPopulated: false,

            /**
             * If the list is minimized
             * @type {Boolean}
             */
            minimized: true,

         }
      },

      created() {
         window.eventHub.$on('refreshData', this.getDataFromBackend)
      },

      beforeMount() {
         if (this.expandByDefault) {
            this.toggleList();
         }
      },

      methods: {

         /**
          * Toggles the list view
          * @return {undefined}
          */
         toggleList() {
            this.minimized = !this.minimized
            if (!this.minimized) {
               this.loading = true;
               this.hasDataPopulated = false;
               this.getDataFromBackend();
            }
            else {
               this.hasDataPopulated = false;
            }
         },

         /**
          * Gets data from backend
          * @return {undefined}
          */
         getDataFromBackend() {
            axios.get(this.apiEndpoint).then(res => {
               this.faveoAttributeList = res.data.data.faveo_attributes;
               this.thirdPartyAttributes = res.data.data.third_party_attributes;
               this.loading = false;
               this.hasDataPopulated = true;
            }).catch(err => {
               errorHandler(err);
               this.loading = false;
            })
         },

         /**
          * updates change in overwrite into the `faveoAttributeList` array
          * @param  {Boolean} value  true/false of the checkbox
          * @param  {string} name    name in format `overwrite-id`
          * @return {undefined}
          */
         onChange(value, name) {
            let key = name.includes('overwrite') ? 'overwrite' : 'mapped_to';
            //identify the data-type by name prefix and push to faveoAttributeList accordingly
            let nameArray = name.split('-')
            let index = nameArray[nameArray.length - 1]
            this.faveoAttributeList[index][key] = value;
         },

         /**
          * Submits the whole data to backend for saving
          * @return {undefined} [description]
          */
         onSubmit() {
            this.loading = true;
            let params = {
               faveo_attributes: this.faveoAttributeList
            };

            if (this.importId) {
               params['import_id'] = this.importId
            }

            axios.post(this.apiEndpoint, params)
                    .then(res => {
                       successHandler(res, this.$options.name)
                       this.loading = false;
                       this.updateParent();
                    }).catch(err => {
               errorHandler(err, this.$options.name)
               this.loading = false;
            })
         },

         /**
          * Verifies if overwrite has to be disabled
          * @param  {Boolean} $overwritable
          * @return {Boolean}
          */
         disableOverwrite($overwritable) {
            return !boolean($overwritable)
         },

         /**
          * If option should display or not
          * @param  {String}  faveoAttributeName
          * @param  {Boolean} isLogginable
          * @return {Boolean}
          */
         displayAttribute(faveoAttributeName, isLogginable) {
            // if faveoAttributeName is username and isLogginable is false then don't display
            if (faveoAttributeName == 'user_name' && !boolean(isLogginable)) {
               return false;
            }
            return true;
         }

      },

      components: {
         checkbox: require("components/MiniComponent/FormField/Checkbox"),
         alert: require("components/MiniComponent/Alert"),
         "custom-loader": require("components/MiniComponent/Loader"),
         "tool-tip": require("components/MiniComponent/ToolTip")
      }
   }
</script>
<style>
   #faveo-attribute-list{
      border: 1px solid #eee;
      margin-top: 10px;
      margin-bottom: 10px;
   }

   .attribute-list{
      width: 90%;
      margin-bottom: 0;
   }

   .toggle {
      transition: max-height 0.25s ease-out;
   }

   .toggle.toggle-expand {
      transition: max-height 0.25s ease-in;
   }
   #toggle-list-button {
      cursor: pointer;
   }
</style>