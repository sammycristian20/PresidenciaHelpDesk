<template>

 <span>

  <a class="user_name pop" :href="showUser(from)" data-container="body" data-toggle="popover" data-placement="right" 
    :data-content="hoverContent" target="_blank">
    
    <span class="emphasize"> {{ subString(fullName) }}
      
      <span>{{ subString(getUserName) }}</span>
    </span>
  </a>

  <span v-if="isUnassigned" style="color: red">{{lang('unassigned')}}</span>
</span>

</template>

<script>
  
  import { boolean,getSubStringValue } from 'helpers/extraLogics';

  export default{
    
    props : {

      from : { type : Object, default : ()=> {} },

      tableHeader : { type : String, default : '' },

    },

    computed:{
      
      fullName() {
        
        return boolean(this.from) ? `${this.from.first_name} ${this.from.last_name}` : '';
      },
      
      getUserName() {
        
        return (boolean(this.from) && (this.from.first_name=='' || this.from.first_name==null) && this.from.user_name) ? this.from.user_name : '';
      },
      
      isUnassigned() {
        
        return (this.fullName === '' && this.getUserName === ''); 
      },
      
      hoverContent(){

        if(boolean(this.from)){

          return `
            <div class='card card-light card-widget mb-0'>

              <div class='card-header pad-7'>

              <div class="user-panel d-flex">
                
                <div>
                  
                  <img src=${this.from.profile_pic} class="img-circle elevation-2" alt="User Image">
                </div>
                
                <div class="info">
                  
                  <a href=${this.basePath()+'/user/'+this.from.id} class="d-block">
                    ${this.from.first_name !== '' ?  this.from.first_name+" "+this.from.last_name : this.from.user_name}
                  </a>
                </div>
              </div>
              </div>

              <div class='card-body info-body'>

                <ul class='nav nav-stacked'>

                  <li id='b_bottom'>
                    <a id='cus-link' title=${this.from.email} >

                      <i class='fas fa-envelope'></i>&nbsp;
                        ${this.from.email  ? this.subString(this.from.email) : "---"}
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          `
        } else { return '' }
      }
    },
   
    methods:{

      subString(value,length = 25){

        return getSubStringValue(value,length)
      },
      
      //show user page
      showUser(x){
        
        return boolean(x) ? this.basePath()+'/user/'+x.id : '';
      }
    }
  };
</script>

<style>
  /*Globally required*/
  .popover-body { padding : 0 !important; }

  .pad-7 { padding: 7px !important; }

  .info-body { padding: 15px !important; padding-left: 7px !important;}
</style>