<style scoped>
     .select-all{
     	display: inline-block;
      margin-left: 10px;
     }
</style>
<template>
       <div class="select-all">
       	    <input type="checkbox" id="all" v-model="selectAll">
            <label for="all" class="maxi-lab"></label>
       </div> 
</template>

<script>
export default {
  props:['selected','tickets'],
  data(){
    return {
        list:[],
      }
  },
  computed:{
  	 //slect all
             selectAll: {
                  get: function () {
                     if(this.tickets.length!=0){
                         return this.tickets ? this.selected.length == this.tickets.length : false;
                     }
                     else{
                        return false;
                     }
                      
                  },
                  set: function (value) {
                      if (value) {
                          window.eventHub.$emit('selectTicket',value);
                      }
                      else{
                          window.eventHub.$emit('selectTicket',false);
                      }
                  }
              },
  }
  
};
</script>
