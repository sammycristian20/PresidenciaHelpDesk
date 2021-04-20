<template>
     <div id="alert" >
        <div class="row">
                <div class="col-md-12">
                        <div class="alert alert-success alert-dismissable" v-bind:style="{display:successShow}"><i class="fa  fa-check-circle"></i>&nbsp;{{lang(successMsg)}}
                          <button type="button" class="close" data-dismiss="alert">x</button>
                        </div>
                        <div id="merge-err-alert" class="alert alert-danger alert-dismissable" v-bind:style="{display:errorShow}">
                          <button type="button" class="close" data-dismiss="alert">x</button>
                            <span v-if="errorMsg.length < 5">
                              
                                   <p v-for="err in errorMsg"><i class="fa  fa-warning"></i>&nbsp;{{lang(err)}}</p>
                            </span>
                            <span v-if="errorMsg.length > 5">
                              
                                   <p id="bottom"><i class="fa  fa-warning"></i>&nbsp;{{lang(errorMsg)}}</p>
                            </span>
                            <div v-html="extraData"></div>
                          </div>
                        </div>
                </div>
        </div>
</template>
<script>
   export default{
       props:['successMsg','errorMsg','successShow','errorShow','extraData','baan'],
       data(){
          return{
           
          }
       },
       created() {
          window.eventHub.$on('startSlide', this.SlideUp);
       },
       watch:{
           successMsg(newValue,oldValue){
                  return newValue;
           },
           errorMsg(newValue,oldValue){
                  return newValue;
           },
           successShow(newValue,oldValue){
                  return newValue;
           },
           errorShow(newValue,oldValue){
                  return newValue;
           },
           extraData(newValue,oldValue){
                  return newValue;
           },
           ban(newValue,oldValue){
            console.log(newValue);
                  return newValue;
           },
       },
      
        
       
   };
</script>
<style scoped>
#err{
  padding-top:0px !important;
}
#ban {
  float: left !important;
    margin-left: -2px;
}
#bottom{
  margin-bottom: 0px !important;
}
</style>