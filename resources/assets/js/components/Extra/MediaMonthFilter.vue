<template>
	<select @change="filterApi(search)" v-model="search.month" class="form-control" style="border-radius: 0px">
                                                    <option value="">{{lang('Month')}}</option>
                                                    <option value="01">{{lang('january')}}</option>
                                                <option value="02">{{lang('february')}}</option>
                                                <option value="03">{{lang('march')}}</option>
                                                <option value="04">{{lang('april')}}</option>
                                                <option value="05">{{lang('may')}}</option>
                                                <option value="06">{{lang('june')}}</option>
                                                <option value="07">{{lang('july')}}</option>
                                                <option value="08">{{lang('august')}}</option>
                                                <option value="09">{{lang('september')}}</option>
                                                <option value="10">{{lang('october')}}</option>
                                                <option value="11">{{lang('november')}}</option>
                                                <option value="12">{{lang('december')}}</option>
                                                </select>
</template>
<script>
  export default{
  	props:['search'],
  	 data(){
  	 	return{

  	 	}
  	 },
     methods:{
            filterApi(x){
                 this.$emit('filtering',x);
            }
        }
  }
</script>