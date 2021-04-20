<template>
      <select @change="filterApi(search)" v-model="search.day" class="form-control" style="border-radius: 0px" >
                                                    <option value="">{{lang('day')}}</option>
                                                    <option value="01">{{lang('1')}}</option>
                                                <option value="02">{{lang('2')}}</option>
                                                <option value="03">{{lang('3')}}</option>
                                                <option value="04">{{lang('4')}}</option>
                                                <option value="05">{{lang('5')}}</option>
                                                <option value="06">{{lang('6')}}</option>
                                                <option value="07">{{lang('7')}}</option>
                                                <option value="08">{{lang('8')}}</option>
                                                <option value="09">{{lang('9')}}</option>
                                                <option value="10">{{lang('10')}}</option>
                                                <option value="11">{{lang('11')}}</option>
                                                <option value="12">{{lang('12')}}</option>
                                                <option value="13">{{lang('13')}}</option>
                                                <option value="14">{{lang('14')}}</option>
                                                <option value="15">{{lang('15')}}</option>
                                                <option value="16">{{lang('16')}}</option>
                                                <option value="17">{{lang('17')}}</option>
                                                <option value="18">{{lang('18')}}</option>
                                                <option value="19">{{lang('19')}}</option>
                                                <option value="20">{{lang('20')}}</option>
                                                <option value="21">{{lang('21')}}</option>
                                                <option value="22">{{lang('22')}}</option>
                                                <option value="23">{{lang('23')}}</option>
                                                <option value="24">{{lang('24')}}</option>
                                                <option value="25">{{lang('25')}}</option>
                                                <option value="26">{{lang('26')}}</option>
                                                <option value="27">{{lang('27')}}</option>
                                                <option value="28">{{lang('28')}}</option>
                                                <option value="29">{{lang('29')}}</option>
                                                <option value="30">{{lang('30')}}</option>
                                                <option value="31">{{lang('31')}}</option>
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