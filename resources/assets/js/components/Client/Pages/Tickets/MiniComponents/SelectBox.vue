<template>
	
  <p class="sel">{{ lang('show') }}
	
  	<select v-model="pages" class="form-control input-sm" @change="ticketCount(pages)" :style="inputStyle">
	
  		<option value="10">10</option> 
	
  		<option value="25">25</option>
	
  		<option value="50">50</option>
	
  		<option value="100">100</option> 
	
  	</select> {{ lang('entries') }}
	</p>
</template>

<script>

  export default {

    props : {

      layout : { type : Object, default : ()=>{}},

      perpage : { type : String | Number, default : ''}
    },

  	data () {
  		
      return {
  	    
        pages:this.perpage,
        
        inputStyle: { borderColor : this.layout.portal.client_input_field_color }
  	  } 
  	},

    methods : {
      
      ticketCount(x){
        
        this.$emit('count', x);
      }
    }
  };
</script>
<style >
	.sel {
		margin-left: 12px;
    color:black !important;
	}
</style>