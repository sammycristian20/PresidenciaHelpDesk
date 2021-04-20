<template>

    <div class="card">

      <ul class="nav nav-tabs">

        <li v-for="section in tabs" class="nav-item" :key="section.id">

          <a  class="nav-link" :class="{ active: category === section.id }" data-toggle="pill" id="changes_tab" 
            @click="syncTabs(section.id)">

            {{lang(section.title)}}
          </a>
        </li>
      </ul>

      <div class="card-body">
      <div class="tab-content">

        <div class="active tab-pane" id="activity">

          <div>

              <div class="form-group">
                  <textarea v-if="category == 'description'" 
                    class="form-control"
                    v-model="task.task_description"
                    readonly
                    rows="10"
                  ></textarea>
              </div>

              <div v-if="category == 'assignees'">

                  <template v-if="assignees.length == 0">
                    <h2 class="text-center">{{ lang('no_assignees') }}</h2>
                  </template>

                  <ul class="list-group">
                    <li class="list-group-item" v-for="agent in assignees" :key="agent">
                      {{ agent }}
                    </li>
                  </ul>
                  
              </div>
            
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

  export default {

    props : {

      taskId : { type : String | Number, default : '' },

      task : { type : Object, default : ()=>{} },
    },

    data(){

      return {

        tabs:[
          {id : 'description', title : 'description'},
          {id : 'assignees', title : 'assignees'},
        ],

        category : 'description',

        assignees: []


      }
    },

    beforeMount() {
      this.getAssignees();
    },

    methods : {

      syncTabs(category){
        this.category = category;
      },

      getAssignees() {
        var users = [];
        var promises = [];
        for(const element of this.task.assigned_to) {
          promises.push(
            axios.get('/api/admin/agent/' + element.user_id).then(response => {
              let names = response.data.data.agent.first_name+" "+response.data.data.agent.last_name;
              users.push(names);
            })
          )
        }
        Promise.all(promises).then(() => this.assignees = users);
      }

    },

    components : {

    }
  };
</script>

<style scoped>
  
  #changes_tab{
    cursor: pointer;
  }

  textarea { resize: vertical; }

</style>