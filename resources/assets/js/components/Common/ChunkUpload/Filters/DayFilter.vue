<template>
    
    <select @change="filterApi(search)" v-model="search.day" class="form-control" style="border-radius: 0px" >
        
        <option value="">{{lang('day')}}</option>
        
        <option v-for="n in 31" :value="n">{{n}}</option>
    </select>
</template>

<script>
    
    export default{
        
        props:['search'],
        
        methods:{
            
            filterApi(x){
                
                this.$emit('filtering',x);
            }
        }
    };
</script>