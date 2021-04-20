<template>

  <div class="row" id="activity_log_div">

    <div class="col-sm-12 mb-2">

      <div id="select_limit" v-if="!loading && activity_log.length > 0">

        <select class="form-control" v-model="perPage" @change="logLimit(perPage)">

          <option value="10">10</option>

          <option value="25">25</option>

          <option value="50">50</option>

          <option value="100">100</option>
        </select>
      </div>

      <div id="choose_order" v-if="!loading && activity_log.length > 0">

        <button id="sort_btn" type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">{{lang('created_time')}} - {{lang(sort_key)}}
        </button>

        <div class="dropdown-menu" style="z-index: 9999;">

          <a href="javascript:;" class="dropdown-item" @click="orderBy('asc')">

            <i class="fas fa-sort-amount-up"></i> {{lang('asc')}}
          </a>
        
          <a href="javascript:;" @click="orderBy('desc')" class="dropdown-item">

            <i class="fas fa-sort-amount-down"></i> {{lang('desc')}}
          </a>
        </div>
      </div>
      
      <div class="float-right">
        
        <div id="page_div" v-if="!loading && total > 10">

          <uib-pagination id="page2" :total-items="Records" v-model="pagination" :max-size="maxSize" class="pagination" 
            :boundary-links="false" :items-per-page="PerPage" ></uib-pagination>
        </div>
      </div>
    </div>

    <div v-if="filtering" class="col-md-12">

      <custom-loader :duration="4000"></custom-loader>
    </div>

    <div v-if="loading" class="col-md-12" id="loader_log">

      <loader :animation-duration="4000" :size="60"/>
    </div>

    <div class="col-md-12">
      
      <div class="timeline" v-if="!loading && activity_log.length > 0">
        
        <template v-for="(log,index) in activity_log">

          <div class="time-label" v-if="checkDate(index)">
            
            <span class="bg-green">{{formattedDate(log.created_at)}}</span>
          </div>
          
          <div>
            
            <i class="far fa-dot-circle"></i>

            <div class="timeline-item">
              
              <span class="time"><i class="far fa-clock"></i>  {{formattedTime(log.created_at)}}</span>

              <h3 class="timeline-header">

                <faveo-image-element id="problem_img" :source-url="log.creator.profile_pic" :classes="['img-circle', 'img-bordered-sm']" alternative-text="User Image" :img-width="25" :img-height="25"/>

                <a :href="basePath()+'/user/'+log.creator.id">{{log.creator.full_name}}</a>
              </h3>

              <div class="timeline-body break">
                
                <span v-html="log.message" id="log_desc"></span>
              </div>
            </div>
          </div>

          <div v-if="showThreadEnd(index)">
            
            <i class="far fa-clock bg-gray"></i>
          </div>
        </template>
      </div>

      <div class="float-right" id="page_div" v-if="!loading && total > 10">

        <uib-pagination id="page2" :total-items="Records" v-model="pagination" :max-size="maxSize" class="pagination" :boundary-links="false" :items-per-page="PerPage" ></uib-pagination>
      </div>
    </div>

    <div class="col-sm-12" v-if="!filtering && !loading && activity_log.length === 0">
        
      <h6 class="text-center">{{lang('no_data_found')}}</h6>
    </div>
  </div>
</template>

<script>

  import { mapGetters } from 'vuex';

  import Vue from 'vue';

  import axios from 'axios'

  Vue.use(require("vuejs-uib-pagination"));

  export default {

    name : 'problem-activity',

    description : 'Problem activity page',

    props : {

      taskId : { type : String | Number, default : '' },
    },

    data(){

      return {

        loading : true,

        apiUrl : '/tasks/api/activity/'+this.taskId,

        activity_log : '',

        perPage:'10',

        total:0,

        maxSize:5,

        pagination:{currentPage: 1},

        paramsObj : {},

        filtering : false,

        sort_key : 'desc'
      }
    },

    created(){

      window.eventHub.$on('updateAssetLogs',this.getUpdateActivity)
    },

    beforeMount() {

      this.paramsObj['page'] = this.pagination.currentPage

      this.getValues(this.paramsObj);
    },

    watch:{

      "pagination.currentPage"(newValue,oldValue){

        this.filtering = true;

        var elmnt = document.getElementById('activity_log_div');

        elmnt.scrollIntoView({ behavior : "smooth"});

        this.paramsObj['page'] = newValue;

        this.getValues(this.paramsObj);

        return newValue
      }
    },

    computed : {

      PerPage: function() {
         return this.perPage ? parseInt(this.perPage) : 10;
      },

      Records: function() {
          return this.total ? parseInt(this.total) : 0;
      },

      ...mapGetters(['formattedTime','formattedDate'])
    },

    methods : {

      getUpdateActivity(){

        this.activity_log = [];

        this.filtering = true;

        this.pagination.currentPage = 1;

        this.sort_key = 'desc';

        this.perPage = 10 ;

        this.total = 0;

        this.paramsObj['page'] = 1;

        this.paramsObj['limit'] = 10;

        this.paramsObj['sort_order'] = 'desc';

        setTimeout(()=>{

          this.getValues(this.paramsObj);
        },1000)
      },

      commonFilter(){

        this.filtering = true;

        this.pagination.currentPage = 1;

        this.paramsObj['page'] = 1;

        this.getValues(this.paramsObj);
      },

      logLimit(limit){

        this.paramsObj['limit'] = limit;

        this.commonFilter();
      },

      orderBy(order){

        this.sort_key = order;

        this.paramsObj['sort_order'] = order;

        this.commonFilter();
      },

      getValues(params){

        axios.get(this.apiUrl,{params}).then(res=>{

          this.loading = false;

          this.filtering = false;

          this.activity_log = res.data.data.data;

          this.total = res.data.data.total;

          this.perPage = res.data.data.per_page;

        }).catch(error=>{

          this.loading = false;

          this.filtering = false;
        })
      },

      checkDate(x){

        if(x==0){

          return true;

        }else{

          var date1=this.formattedDate(this.activity_log[x-1].created_at);

          var date2=this.formattedDate(this.activity_log[x].created_at);

          if(date1!=date2){

            return true;
          }
        }
      },

      showThreadEnd(x){

        return x === this.activity_log.length-1 ? true : false
      }
    },

    components : {

      'loader':require('components/Client/Pages/ReusableComponents/Loader'),

      'custom-loader' : require('components/MiniComponent/Loader'),
      
      'faveo-image-element': require('components/Common/FaveoImageElement')
    }
  };
</script>

<style scoped>

  #loader_log{
    margin-top:30px;margin-bottom:30px;
  }

  #select_limit{
    display: inline-block; float: left;
  }

  #choose_order{
    display: inline-block; margin-left: 10px;
  }

  #page_div{
    margin-top: -20px;
  }

  #sort_btn{
    background-color: rgb(255, 255, 255);
  }

  #log_desc strong p {

    display: inherit !important;
  }

  #problem_img{
    margin-top: -3px;
  }

  .break { word-break: break-all; }
</style>
