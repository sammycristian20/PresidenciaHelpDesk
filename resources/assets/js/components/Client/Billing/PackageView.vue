<template>

  <div id="main-div" :class="{rtl : lang_locale == 'ar'}">
    
    <meta-component :dynamic_title="lang('package')" :layout="layout" >
        
    </meta-component>

    <div class="row" v-if="renderingData">

      <loader :animation-duration="4000" :size="60" :color="layout.portal.client_header_color"/>
    </div>
    
    <alert componentName="Packageview"/>

    <div v-if="!renderingData" id="content" class="site-content col-md-12">
      
      <header class="archive-header" :class="{align1 : lang_locale === 'ar'}">
        
        <h1 class="archive-title">{{lang('selected_package')}}</h1>
      </header>

      <div class="archive-list archive-news">

        <article class="hentry">
      
          <header class="entry-header">
            
            <img :src="packDetail.package_pic.path ? packDetail.package_pic.path : defaultImage"
              alt="" class="pack_img entry-thumbnail float-left" :class="{float1 : lang_locale === 'ar'}">
            
            <h2 class="entry-title h3" :class="{align1 : lang_locale === 'ar'}">
              <a :style="linkStyle" rel="bookmark">{{packDetail.name}}</a>
            </h2>
          </header>

          <footer class="entry-footer" :class="{align1 : lang_locale === 'ar'}">
            
            <div class="entry-meta text-muted">
              
              <small class="date"><i class="far fa-calendar fa-fw"></i> 

                <time datetime="2013-10-22T20:01:58+00:00">{{formattedTime(packDetail.updated_at)}}</time>
              </small>

              <small :title="lang('validity')" class="date"><i class="far fa-clock fa-fw"></i> 

                <time>{{lang(packDetail.validity ? packDetail.validity : 'one_time')}}</time>
              </small>

              <small :title="lang('allowed_tickets')" class="date"><i class="fa fa-ticket-alt fa-fw"></i> 

                <time>{{packDetail.allowed_tickets}}</time>
              </small>

              <small :title="lang('price')" class="date"><i class="far fa-money-bill-alt"></i> 

                <time datetime="2013-10-22T20:01:58+00:00">{{getFormattedCurrency(packDetail.price)}}</time>
              </small>
              
            </div>
          </footer>

          <div class="entry-summary clearfix">
            
            <p :class="{align1 : lang_locale === 'ar'}">{{packDetail.description}}</p>
            
            <p :class="[(lang_locale === 'ar') ? 'text-right' : 'text-left']">
              
              <template v-if="packDetail.kb_link">

                <input type="checkbox" v-on:click="checkboxClickEvent()" title="Accept terms & conditions" 
                v-model="checkboxStatus" id="terms-condition">
              
                <a :href="packDetail.kb_link" target="_blank" :style="linkStyle">{{lang("terms_conditions")}}</a>
  
              </template>
              
              <router-link tag="button" :disabled="!checkboxStatus" class="btn btn-custom float-right check_btn" 
                :to="'/checkout/' + 'CO-' + packageId " :style="btnStyle" :class="{left : lang_locale == 'ar'}">{{lang("checkout")}}&nbsp;

                <i class="fa fa-forward"></i>
              </router-link>
            </p>
          </div>
        </article>
      </div>
    </div>    
    </div>
  </div>
</template>

<script>
  
  import axios from 'axios';
  
  import { errorHandler } from "helpers/responseHandler";
  
  import { currencyFormatter, lang } from '../../../helpers/extraLogics';

  import { mapGetters } from 'vuex';

  export default {

    props : {

      layout : { type : Object, default : ()=>{}},

      auth : { type : Object, default : ()=>{}},

      someUnrelatedVar : { type : String | Number, default : ''}
    },

    components: {
     
      "custom-loader": require("components/MiniComponent/Loader"),
     
      "alert": require("components/MiniComponent/Alert"),
    },

    data(){
     
      return {
     
        packageId: this.$route.params.id,
     
        renderingData: true,
     
        packDetail: '',
     
        checkboxStatus: false,

        defaultImage : this.auth.system_url + '/themes/default/client/images/package.png',

        lang_locale : this.layout.language,

        btnStyle : {

          borderColor : this.layout.portal.client_button_border_color,

          backgroundColor : this.layout.portal.client_button_color,
        },

        linkStyle : {
          color : this.layout.portal.client_header_color,
        }
      }
    },

    computed : {

      ...mapGetters(['formattedTime'])
    },

    beforeMount() {
      
      this.$Progress.start();

      this.getDataFromAPi();
    },

    methods: {
      
      getDataFromAPi(){

        axios.get('bill/package/user-package-info/' + this.packageId, {}).then(res => {

          this.packDetail = res.data.data;
          
          if(!this.packDetail.kb_link){
            
            this.checkboxStatus = true;
          }
          
          this.renderingData = false;

          this.$Progress.finish();
        }).catch(err => {
          
          this.renderingData = false;
          
          errorHandler(err,'PackageList');
          
          this.$router.push({ path:'/billing-package-list'});

          this.$Progress.fail();
        })
      },

      getFormattedCurrency(value) {
        
        return value==0 ? lang('free'): currencyFormatter(value,this.layout.billing_settings.currency,this.lang_locale,)
      },

      checkboxClickEvent(){
        
        this.checkboxStatus = this.checkboxStatus;
      }
    },
  };
</script>

<style scoped>

  .img-circle{
    border: 2px solid #ccccc8 !important;
    width: 130px !important;
    height: 130px !important;
    border-radius: 0px !important;
  }
  .control-label {
    font-weight: 500;
    text-align: left;
    word-break: break-all;
  }

  .pack_img{
    width: 200px;
    height: 200px;
  }

  .check_btn{
    margin-bottom: 20px;
  }
</style>


