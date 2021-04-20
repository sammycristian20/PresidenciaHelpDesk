<template>
	
  <select @change="filterApi(search)" v-model="search.month" class="form-control" style="border-radius: 0px">
    
    <option value="">{{lang('Month')}}</option>

    <option v-for="(month,index) in months" :value="month.value" :key="index">{{lang(month.name)}}</option>
  </select>
</template>

<script>

  export default{

  	props:['search'],

  	data(){

  	 	return{

        months : [
          { value : '01', name : 'january'},
          { value : '02', name : 'february'},
          { value : '03', name : 'march'},
          { value : '04', name : 'april'},
          { value : '05', name : 'may'},
          { value : '06', name : 'june'},
          { value : '07', name : 'july'},
          { value : '08', name : 'august'},
          { value : '09', name : 'september'},
          { value : '10', name : 'october'},
          { value : '11', name : 'november'},
          { value : '12', name : 'december'},
        ]
  	 	}
  	},

    methods:{
    
      filterApi(x){
    
        this.$emit('filtering',x);
      }
    }
  };
</script>