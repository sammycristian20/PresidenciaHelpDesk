<template>

  <div  v-if="tabs" id="task-associates" class="card card-light card-tabs">

    <alert componentName="taskAssociates"/>

    <div v-if="!loading" class="card-header p-0 pt-1">

      <ul class="nav nav-tabs">

        <template v-for="section in tabs">

          <li v-if="section.show" class="nav-item">

            <a id="asset_tab" class="nav-link" :class="{ active: category === section.id }" data-toggle="tab" @click="associates(section.id)">
              {{trans(section.title)}}
            </a>

          </li>
        </template>
      </ul>
    </div>

    <div class="card-body">
      
      <div class="tab-content">

        <div class="active tab-pane" id="activity">

          <div>
            <task-activity v-if="category === 'activity'" :taskId="taskId" />
          </div>

        </div>

      </div>
    </div>

    <div v-if="loading" class="row">
      <loader :animation-duration="4000" :size="60"/>
    </div>

  </div>

</template>

<script>

export default {

  name : 'TaskAssociates',

  description : 'Task associates page',

  props : {

    taskId : { type : String | Number, default : '' }
  },

  data(){

    return {

      tabs: [{id: 'activity', title: 'task-plugin-activity-log', show: true}],

      category : 'activity',

      loading : false
    }
  },

  methods : {

    associates(category){

      this.loading = true;

      setTimeout(()=>{ this.loading = false; },1)

      this.category = category;
    }
  },

  components : {

    'alert' : require('components/MiniComponent/Alert'),

    'loader':require('components/Client/Pages/ReusableComponents/Loader'),

    'task-activity':require('./TaskActivity'),

  }
};
</script>

<style scoped>

#asset_tab{
  cursor: pointer;
}
</style>