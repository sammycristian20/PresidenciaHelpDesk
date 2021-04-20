<template>

  <div>
    <alert componentName="approval-workflow"/>

    <div class="row" v-if="hasDataPopulated === false || loading === true">
        <custom-loader></custom-loader>
    </div>

    <div>

      <div class="card card-light" v-if="hasDataPopulated">

        <div class="card-header">

            <h3 class="card-title">{{lang('approval_workflow_settings')}}</h3>
        </div>

        <!-- approval workflow body -->
        <div class="card-body">

          <div class="row margin-0" id="approval-workflow-name">

            <!-- workflow name -->
            <text-field :label="lang('name')" :value="name" name="name" :onChange="onChange" :required="true" classname="col-md-4">
            </text-field>

            <!--status on approve-->
            <dynamic-select :label="lang('status_on_approve')" name="action_on_approve" :multiple="false"
                :value="action_on_approve" classname="col-sm-4" :onChange="onChange"  apiEndpoint="/api/dependency/statuses"></dynamic-select>

            <!--status on deny-->
            <dynamic-select :label="lang('status_on_deny')" name="action_on_deny" :multiple="false"
                :value="action_on_deny" classname="col-sm-4" :onChange="onChange" apiEndpoint="/api/dependency/statuses"></dynamic-select>

          </div>

          <div id="list-of-levels">
            <!-- list of levels -->
            <template v-for="(level, levelIndex) in levels">  

            <div class="margin-10">
                <alert :componentName="'approval-level-'+levelIndex"/>
              </div>

              <div class="margin-10 card card-light">

              <div class="card-header">

                <h3 class="card-title">{{lang('level')}}&nbsp;{{levelIndex + 1}}</h3>

                <!-- delete button on top right of the bix -->
                <div class="card-tools">
                  <button v-if="levels.length > 1" :id="'approval-workflow-level-delete-'+levelIndex" 
                    @click="deleting(levelIndex, level.id)" class="btn btn-tool">
                    <span class="fas fa-times" aria-hidden="true"></span>
                  </button>
              </div>
              </div>

              <div class="card-body">

              <!-- first row of level which contains name, matcher and delete button -->
              <div class="row margin-auto margin-top-10">
                <text-field :label="lang('name')" :value="level.name" :name="'level-name-'+levelIndex" :onChange="onChange"
                  :required="true" classname="col-md-6">
                </text-field>

                <static-select :label="lang('matcher')" :elements="matcherOptions" :name="'level-match-'+levelIndex"  :onChange="onChange"
                  :required="true" classname="col-md-6" :value="level.match">
                </static-select>
              </div>

              <!-- second row of level which contains approvers -->
              <div class="row margin-auto" :id="'approval-workflow-level-'+levelIndex">
                <dynamic-select :label="lang('users')" :value="level.approvers.users" :name="'approver-agent-'+levelIndex" :onChange="onChange"
                  apiEndpoint="/api/dependency/users?meta=true"  :multiple="true" classname="col-md-6">
                </dynamic-select>

                <dynamic-select :label="lang('user_types')" :value="level.approvers.user_types" :name="'approver-user-type-'+levelIndex" :onChange="onChange"
                  apiEndpoint="/api/dependency/user-types" :multiple="true" classname="col-md-6">
                </dynamic-select>
              </div>
            </div>

              <!-- delete pop-up -->
              <modal v-if="showDeletePopUp" :showModal="true" :onClose="()=> showDeletePopUp = false">

                  <!-- if mode is delete, we only show the confirmation message that if they really want to delete -->
                  <div slot="title">
                    <h4 class="modal-title">{{lang('delete_workflow_level')}}</h4>
                  </div>

                  <!-- if mode is delete, we only show the confirmation message that if they really want to delete -->
                  <div slot="fields">
                      <span>
                        {{lang('you_are_about_to_delete_a_workflow_level')}}.&nbsp;{{lang('please_confirm')}}
                      </span>
                  </div>

                  <div slot="controls">
                    <button :id="'confirm-delete-button-'+levelIndex" type="button" @click = "onDelete()" class="btn btn-danger">
                      <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                      &nbsp;{{lang('delete')}}
                    </button>
                  </div>

                </modal>
            </div></template>
          </div>
        </div>

         <div class="card-footer">

            <!-- add levels -->
            <button type="button" id="add-level" @click="addLevel" class="btn btn-primary">
              <span class="glyphicon glyphicon-plus"></span>&nbsp;{{lang('add_level')}}
            </button>

            <!-- submit button -->
            <button type="button" id="submit-workflow" @click="onSubmit" :disabled="loading" class="btn btn-primary">
              <span class="fas fa-save"></span>&nbsp;{{lang('save')}}
            </button>

          </div>

      </div>
    </div>
  </div>
</template>

<script>

  import {getIdFromUrl, extractOnlyId} from 'helpers/extraLogics';
  import {successHandler, errorHandler} from 'helpers/responseHandler';
  import {validateApprovalWorkflow} from 'helpers/validator/approvalWorkflowRules'
  import axios from 'axios';

  export default {
    name: 'approval-workflow',

    description: 'handles create/edit part of approval workflow',

    data(){
        return {

          /**
           * Id of the workflow
           * @type {Number}
           */
          id: 0,

          /**
           * Name of the workflow
           * @type {String}
           */
          name:'',

          /**
           * Status that should be enforced when ticket is approved
           * @type {Number}
           */
          action_on_approve: '',

          /**
           * Status that should be enforced when ticket is denied
           * @type {Object}
           */
          action_on_deny: '',

          /**
           * Levels in the workflow
           * @type {Array}
           */
          levels:[{
              /**
               * Level Id
               * @type {Number}
               */
              id:0,

              /**
               * level name
               * @type {String}
               */
              name:'',

              /**
               * Order in which levels are displayed
               * NOTE: a drag functionality can be added as enhancement in future using this
               * @type {String}
               */
              order: 1,

              /**
               * It can be any or all
               * @type {String}
               */
              match:'all',

              /**
               * Approvers of the level
               * @type {Object}
               */
              approvers:{

                    /**
                     * Selected user_types in the level to approve
                     * @type {Array}
                     */
                    user_types:[],

                    /**
                     * Selected users in the level to approve
                     * @type {Array}
                     */
                    users:[],
              }
          }],

          /**
           * If API call responses are pending
           * @type {Boolean}
           */
          loading: false,

          /**
           * If the delete pop-up should be visible
           * @type {Boolean}
           */
          showDeletePopUp: false,

          /**
           * Avaible matcher options
           * @type {Array}
           */
          matcherOptions: [{id: 'any', name: 'Any'}, {id: 'all', name: 'All'}],

          /**
           * If data from get api has populated (in case of edit)
           * @type {Boolean}
           */
          hasDataPopulated: true,

          /**
           * Index of the level which is getting deleted
           * @type {Number}
           */
          deletingLevelIndex: null,

          /**
           * Id of the level which is getting deleted
           * @type {Number}
           */
          deletingLevelId: null,
        }
    },

    mounted(){
      const path = window.location.pathname;
      const workflowId = getIdFromUrl(path);

      //if it is edit page, we get initial data to begin with
      if (path.indexOf("edit") >= 0) {
        this.hasDataPopulated = false;
        this.getInitialValues(workflowId);
      }
    },

    methods:{

      loadWorkflowData(){
        const path = window.location.pathname;
        const workflowId = getIdFromUrl(path);

        //if it is edit page, we get initial data to begin with
        if (path.indexOf("edit") >= 0) {
          this.getInitialValues(workflowId);
        }
      },

      /**
       * gets initial values of the selected workflow
       * @param  {Number} workflowId
       * @return {undefined}
       */
      getInitialValues(workflowId){
          this.loading = true;
          axios.get('api/admin/approval-workflow/'+workflowId)
          .then(res => {
            this.id = res.data.data.id;
            this.name = res.data.data.name;
            this.action_on_approve = res.data.data.action_on_approve;
            this.action_on_deny = res.data.data.action_on_deny;
            this.levels = res.data.data.levels;
            this.loading = false;
            this.hasDataPopulated = true;
          })
          .catch(err => {
            this.loading = false;
            this.hasDataPopulated = true;
          })
      },

      addLevel(){
        // adds more empty entries into level
        this.levels.push({
            id:0,
            name:'',
            order: this.levels.length + 1,
            match: 'all',
            approvers:{
              user_types:[],
              users:[],
            }
          })

        this.scrollToSubmitButton();
      },

      scrollToSubmitButton(){
        // scrolling to the bottom of the div
        let x = document.getElementById("submit-workflow");
        setTimeout(() => {
          x.scrollIntoView({behavior: "smooth", block: "center", inline:"center"});
        }, 2);
      },

      onChange(value, name){
        //in case of wprkflow name
        let nameArray = name.split('-')

        if(nameArray.length == 1){
          this[name] = value;
        }

        let index = nameArray[nameArray.length - 1]

        //level-name is the prefix that is given to the name of dynamic component of level
        if(name.includes('level-name-')){
            this.levels[index].name = value
        }

        //approver-user_type- is the prefix that is given to the name of dynamic component of approver user_type
        if(name.includes('approver-user-type-')){
          this.levels[index].approvers.user_types = value
        }

        //approver-agent- is the prefix that is given to the name of dynamic component of approver agent
        if(name.includes('approver-agent-')){
          this.levels[index].approvers.users = value
        }
        //level-match- is the prefix that is given to static field component that represents matcher
        if(name.includes('level-match-')){
          this.levels[index].match = value
        }
      },

      /**
       * if data is valid
       * @return {Boolean} [description]
       */
      isValid(){
        const { errors, isValid } = validateApprovalWorkflow(this.$data);
        if (!isValid) {
          return false;
        }

        return true;
      },

      onSubmit(){
          if(this.isValid()){

            let actionOnApprove = this.action_on_approve == null ? null : this.action_on_approve.id;
            let actionOnDeny = this.action_on_approve == null ? null : this.action_on_deny.id;

            this.loading = true;
            axios.post('/api/admin/approval-workflow',
            {id: this.id, name: this.name, action_on_approve: actionOnApprove,
              action_on_deny: actionOnDeny, levels : this.getFormattedLevelData()})
            .then(res =>{
              successHandler(res, 'approval-workflow');
              this.loading = false;

              //if it is in create mode it should redirect to index page
              if (!(window.location.pathname.indexOf("edit") >= 0)) {
                  window.location = window.axios.defaults.baseURL + '/approval-workflow';
              }

            }).catch(err => {
              errorHandler(err, 'approval-workflow')
              this.loading = false;
            }).finally(res => {
              this.loadWorkflowData();
            })
          }
      },


      getFormattedLevelData(){
        let levels = this.$data.levels.map(level => {
          return {
            id: level.id,
            name: level.name,
            order: level.order,
            match: level.match,
            approvers :{
              users: extractOnlyId(level.approvers.users),
              user_types: extractOnlyId(level.approvers.user_types)}
            }
        })

        return levels;
      },

      /**
       * Removes selected level from list and sends a delete request to the server if exists there
       * @param {Number} index  index of the level that has to be deleted
       * @param {Number} id     id of the level that has to be deleted
       * @return {undefined}
       */
      onDelete(){
        //if id is zero, removes it from the array
        if(this.deletingLevelId != 0){
          this.loading = true;
          // send a delete request to the server
          axios.delete('api/admin/approval-workflow/'+ this.deletingLevelId +'/level').then(res =>{
            successHandler(res,'approval-workflow');
          }).catch(err => {
            errorHandler(err,'approval-workflow');
          }).finally(res => {
            this.loadWorkflowData();
            this.loading = false;
          })
        }

        //remove that particular index from the array
        this.levels.splice(this.deletingLevelIndex, 1);
        this.showDeletePopUp = false;
      },

      deleting(index, id){
        this.deletingLevelIndex = index;
        this.deletingLevelId = id;
        this.showDeletePopUp = true;
      }
    },

    watch : {
      showDeletePopUp(val){
        if(!val){
          this.deletingLevelIndex = null;
          this.deletingLevelId = null;
        }
      }
    },

    components: {
      "text-field": require("components/MiniComponent/FormField/TextField"),
      "dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),
      "static-select": require("components/MiniComponent/FormField/StaticSelect"),
      "modal": require('components/Common/Modal'),
      "alert": require("components/MiniComponent/Alert"),
      "custom-loader": require("components/MiniComponent/Loader"),
    }
  }

</script>

<style>
  .approval-workflow-levels{
    border: 1px solid #eee;
    padding: 10px;
    margin: 5px;
  }
  .box-header .close {
    margin-top: -4px;
    font-size: x-large;
  }
</style>
