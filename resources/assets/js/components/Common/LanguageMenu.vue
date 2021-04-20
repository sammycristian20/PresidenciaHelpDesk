<style scoped>
      .inline{
         display: inline-block;
      }
</style>
<template>
    <div v-if="showMenu" class="language-menu inline dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="Choose Language" @click="getLanguageMenu()">
             <img   :src="getLocalFlag(languageFlag.language)">&nbsp;
             <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
            <li class="col-md-offset-4" v-if="langMenu.length==0"><fulfilling-bouncing-circle-spinner :animation-duration="4000" :size="30" color="#1d78ff"/></li>
            <li v-for="lang in langMenu">
                <a href="javascript:void(0)"  @click="changeFlag(lang.locale)" >
                    <img :src='getLocalFlag(lang.locale)'>&nbsp;
                    {{lang.name}}
                </a>
            </li>
        </ul>
    </div>
</template>

<script>

  import { FulfillingBouncingCircleSpinner } from 'epic-spinners';
export default {
  props:['languageFlag'],
  data(){
    return {
         langMenu:[],
         showMenu:true,
      }
  },
  mounted(){
       $(".dropdown-toggle").dropdown();
  },
  components: {
            'fulfilling-bouncing-circle-spinner':FulfillingBouncingCircleSpinner,
  },
  methods:{
      //getting language
      getLanguageMenu(){
         var vm=this;
            axios.get('api/dependency/languages?meta=true').then(function (resp) {
                vm.langMenu=resp.data.data.languages;
            }).catch(function(resp){

            });
      },
      //get local flag
      getLocalFlag(x){
          return  window.axios.defaults.baseURL+'/themes/default/common/images/flags/'+x+'.png'
      },
      //changes flag name
      changeFlag(x){
          this.languageFlag['language']=x;
           this.$emit('getflag')
      },
  }

};
</script>
