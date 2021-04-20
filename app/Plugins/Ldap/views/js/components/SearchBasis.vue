<style scoped>
  .ldap-import-query {
    margin-bottom: 10px;
  }

  .delete-search-base-modal-title {
	  padding-top: 1rem;
    padding-bottom: 2rem;
  }

</style>

<template>
  <!-- ROOT DIV HOLDER -->
  <div>
      <!-- ALERT COMPONENT -->

      <alert componentName="searchbase" />

      <!-- ALERT COMPONENT END -->
 <div class="row" v-if="loading === true">
    <custom-loader :duration="loadingSpeed"></custom-loader>
  </div>

      <!-- MODAL COMPONENT -->
      <modal v-if="deletePopup"  :classname="'modal-sm'" :containerStyle="containerStyle" :showModal="deletePopup" :onClose="onClose">

        <!-- SLOT TITLE -->

        <div slot="title">
          <h4 class="modal-title">{{lang('delete_search_base')}}</h4>
        </div>
        <!-- SLOT TITLE END -->
        <!-- SLOT FIELD  -->
          <div slot="fields">
             <span>{{ trans('delete_confirmation_message') }}</span>
          </div>
        <!-- SLOT TITLE END -->

        <!-- SLOT CONTROLS -->
          <div slot="controls">
            <button type="button" @click = "onSubmitDelete()" class="btn btn-danger"><i :class="iconClass" aria-hidden="true"></i>  {{lang('delete')}}</button>
          </div>
        <!-- SLOT CONTROLS END -->
      </modal>
      <!-- MODAL COMPONENT END -->

      <!-- SEARCH BASE QUERY UI -->
        <faveo-box :title="lang('import_settings')">
            <!--SEARCH BASIS UI  -->
            <div>
              <!-- <pre>{{ search_bases}}</pre> -->
              <div  v-for="(user,index) in searchBaseArray" :key="index" class="clearfix ldap-import-query">

                <div class="row">

                  <!-- STATE BASIS USER -->
                  <text-field  :id="'searchbase-' +index"  :label="lang('search_base')" :value="user.search_base" type="text" :name="'searchbase'+ index"
                          :onChange="onSearchBase" classname="col-sm-4" :required="true">
                  </text-field>
                  <!-- STATE BASIS USER END -->

                  <!-- STATE BASIS USER -->
                  <text-field  :id="'filter-' +index"  :label="lang('filter')" :value="user.filter" type="text" :name="'filter-'+ index"
                          :onChange="onFilter" classname="col-sm-6" :hint="lang('ldap_filter_hint')">
                  </text-field>
                  <!-- STATE BASIS USER END -->

                  <!-- IMPORT BUTTON -->
                  <div class="form-group col-sm-2">
                      <label for="actions">Actions</label>
                      <div>
                          <span :id="'ping-'+index" class="btn btn-primary" @click="confirmLdapPing(user.search_base, user.filter)" title="Ping User" v-if="user.search_base"><i class="fa fa-paper-plane" ></i></span>
                          <span :id="'delete-'+index" class="btn btn-danger" title="Delete User" @click="deleteUser(user.id ,index)"><i class="fa fa-trash"></i></span>
                      </div>
                  </div>
                </div>
                <!-- IMPORT BUTTON END -->
              <div class="row">
                <static-select :id="'user-type-'+index" v-if="showRole" :label="lang('user_type')"   :elements="usersArray"
                     :name="index" :hideEmptySelect="true" :value="user.user_type" classname="col-sm-4" :onChange="onHandle"
                     :required="true">
                </static-select>
                <!-- USER TYPE END -->

                <!-- DEPARTMENTS -->
                <template v-if="user.user_type !=='user'  && showDepartment">
                  <dynamic-select :key="'search-base-'+index+'-department'" :id="'search-base-'+index+'-department'" apiEndpoint="/api/dependency/departments"  :label="lang('department')"  :multiple="true" :name="index" :prePopulate="false"
                  classname="col-sm-6"  :value="user.departments" :onChange="onDepartmentChange">
                  </dynamic-select>
                </template>

                <!-- DEPARTMENTS END -->

                <!-- ORGANIZATIONS -->
                <template v-if="user.user_type==='user' &&  showOrganization">
                    <dynamic-select :key="'search-base-'+index+'-organization'" :id="'search-base-'+index+'-organization'" apiEndpoint="/api/dependency/organizations"   :label="lang('organizations')"  :multiple="true" :name="index" :prePopulate="false"
                      classname="col-sm-6"  :value="user.organizations" :onChange="onOrganizationChange">
                    </dynamic-select>
                </template>
              </div>
                <!-- ORGANIZATIONSEND -->

              </div>
            </div>
            <!--SEARCH BASIS UI END  -->
          <!--SAVE BUTTONS  -->
          <div slot="actions" class="card-footer">
            <button class="btn btn-primary update-btn" id="query" @click="saveandImport(searchBaseArray , false)" :disabled="loading"><span class="fas fa-save"></span>&nbsp;{{lang('save')}} </button>
            <button class="btn btn-primary update-btn" id="importquery" @click="saveandImport(searchBaseArray,true)" :disabled="loading"><span class="fas fa-save"></span>&nbsp;{{lang('save_and_import')}}</button>
            <button class="btn btn-primary update-btn" id="importquery" @click="addQuery" :disabled="loading"><span class="fas fa-plus"></span>&nbsp;{{lang('add_more')}}</button>
          </div>

        </faveo-box>
  </div>
  <!-- ROOT DIV HOLDER END -->
</template>

<script type="text/Javascript">
import axios from "axios";
import { extractOnlyId, fetchNameAsPerId } from "helpers/extraLogics.js";
import { errorHandler, successHandler } from "helpers/responseHandler";
import { store } from "store";
import { validateLdapSettingsSearchBase } from "./../validator/ldapSearchBaseRules.js";
import FaveoBox from 'components/MiniComponent/FaveoBox';

export default {
  props: {

    ldapId: { type: String, default: '' },

    /**
     * Add User is function which helps to add a new query in the searchbases table
     */
    addUser: {type: Function, default: function() { return null }},

    /**
     * List of query in the search_bases
     */
    searchBaseArray: {type: Array, default: []},

    /**
     * get ldap setting api call , function is passed in as  a prop because when the user delete the
     * searchbase then after the modal get closed new data should be reflected
     */
    getLdap: {type: Function, default: function() { return null }},

    /**
     * decides if organization shold be visible or not
     */
    showOrganization: {type: Boolean, default: true},

    /**
     * decides if department shold be visible or not
     */
    showDepartment: {type: Boolean, default: true},

    /**
     * if role field should be visible
     */
    showRole: {type: Boolean, default: true},

    loadingValue: {type: Boolean, default: true}

  },
  data: function() {
    return {
      /**
       * Component Name
       */
      name: "searchbase",

      iconClass: "fa fa-trash",
      is_valid: "", // parameter used for showing the search base query the valid i default set to false and would be true once the save configuration is done
      search_base: "",
      loading: this.loadingValue, //to show the loader
      loadingSpeed: 4000, // loader speed
      usersArray: [
        {
          id: "user",
          name: "user"
        },
        {
          id: "admin",
          name: "admin"
        },
        {
          id: "agent",
          name: "agent"
        }
      ], // static user type
      deletePopup: false,
      containerStyle: {
        width: "500px"
      },

      searchValueId: "", //state used to store the id send by the backend
      indexValue: "", //state used to store the index of the single item row in searchbases table
      organization_ids: [] //organization id
    };
  },
  watch: {
    searchBaseArray(newvalue) {
      return newvalue;
    }
  },
  methods: {
    /**
     * Addquery method would add a new user in the searchbases table with help of addUser function,
     * which is being passed as prop
     */
    addQuery() {
      this.addUser({
        id: "",
        search_base: "",
        filter: "",
        user_type: "user",
        departments: [],
        organizations: []
      });
    },

    /**
     * populate the states corresponding to 'name' ith 'value'
     * here we have been sending the index to ensure the two way binding with the search_base tag
     * index helps us to update that value in an array
     * @param {string} value
     * @param {index} name
     * @returns {void}
     */

    onSearchBase(value, name) {
      let newindexValue = name.replace("searchbase", "");
      this.searchBaseArray[newindexValue].search_base = value;
    },

    /**
     * populate the states corresponding to 'name' ith 'value'
     * here we have been sending the index to ensure the two way binding with the search_base tag
     * index helps us to update that value in an array
     * @param {string} value
     * @param {index} name
     * @returns {void}
     */

    onFilter(value, name) {
      let newindexValue = name.replace("filter-", "");
      this.searchBaseArray[newindexValue].filter = value;
    },

    /**
     * populates the state corresponding to 'name' with 'value'
     * @param {string} value
     * @param {[type]} name
     * @return {void}
     */
    onOrganizationChange(value, name) {
      this.searchBaseArray[name].organizations = value;
    },
    /**
     * populates the states corresponding to 'name' with 'value'
     * @param  {string} value
     * @param  {[type]} name
     * @return {void}
     */
    onDepartmentChange(value, name) {
      this.searchBaseArray[name].departments = value;
    },
    /**
     * populate the states corresponding to 'name' with 'value'
     * here we have been sending the index to ensure the two way binding with the user_type tag
     * index helps us to updat that value in an array
     * @param {string} value
     * @param {index} name
     * @returns {void}
     */
    onHandle(value, name) {
      this.searchBaseArray[name].user_type = value;
    },

    /**
     * method to get the server response for the single search base query
     * @param {string}
     * @returns {void}
     */
    confirmLdapPing(searchBase, filter) {
      this.loading = true;
      axios
        .get("api/ldap/search-base/ping/" + this.ldapId, {
          params: {
            search_base: searchBase,
            filter: filter
          }
        })
        .then(res => {
          this.loading = false;
          successHandler(res, "searchbase");
        })
        .catch(err => {
          this.loading = false;
          errorHandler(err, "searchbase");
        });
    },

    /**
     * Mehtod will make the modal pop vairiable true and assign the searchId ie (value send through backend) to indexValue
     * @param {Number}
     * ie Number is the searchId value of the element which needs to be deleted from the array
     */
    deleteUser(searchId, index) {
      this.deletePopup = true;
      if (searchId) {
        this.deletePopup = true;
        this.searchValueId = searchId;
      } else {
        this.indexValue = index;
      }
    },

    /**
     * Method helps to delete the particular row in the searchbase table, with help
     * of searchId(ie it is an id which is being sent by backend)
     */
    onSubmitDelete() {
      if (this.searchValueId) {
        axios
          .delete("api/ldap/search-base/" + this.searchValueId)
          .then(res => {
            this.deletePopup = false;
            successHandler(res, "searchbase");
            this.getLdap();
            this.searchValueId = "";
          })
          .catch(err => {
            errorHandler(err, "searchbase");
            // console.log("error");
          });
      } else {
        this.deletePopup = false;
        store.dispatch("setAlert", {
          type: "success",
          message: "Successfully Deleted",
          component_name: "searchbase"
        });
        this.searchBaseArray.splice(this.indexValue, 1);
      }
    },

    /**
     * Method helps to close the modal pop
     */
    onClose() {
      this.deletePopup = false;
      this.searchValueId = "";
    },

    //method use to validate the data
    isValidSearchBase() {
      const { errors, isValid } = validateLdapSettingsSearchBase(
        this.searchBaseArray
      );
      if (!isValid) {
        return false;
      }
      return true;
    },

    /**to save the query search base
     * @param {Array}
     * @param {Boolean}
     * @returns {void}
     */
    saveandImport(data, status) {
      this.loading = true;
      let newdata = _.each(data, function(key) {
        if (key.user_type === "user") {
          key.department_ids = [];
        }
        if (key.user_type !== "user") {
          key.organization_ids = [];
        }
        if (key.departments) {
          key.department_ids = extractOnlyId(key.departments);
        }
        if (key.organizations) {
          key.organization_ids = extractOnlyId(key.organizations);
        }
      });
      if (this.isValidSearchBase()) {
        const SEARCH_BASE_API = '/api/ldap/search-bases/' + this.ldapId;
        axios
          .post(SEARCH_BASE_API, {
            search_bases: newdata,
            import: status
          })
          .then(res => {
            this.loading = false;
            successHandler(res, "searchbase");
            this.getLdap();
          })
          .catch(err => {
            this.loading = false;
            errorHandler(err, "searchbase");
          });
      } else {
        this.loading = false
      }
    }
  },

  /**
   * Components required in this vue file
   */
  components: {
    "static-select": require("components/MiniComponent/FormField/StaticSelect"),
    "text-field": require("components/MiniComponent/FormField/TextField"),
    "dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),
    alert: require("components/MiniComponent/Alert"),
    "custom-loader": require("components/MiniComponent/Loader"),
    modal: require("components/Common/Modal.vue"),
    'faveo-box': FaveoBox,
  }
};
</script>
