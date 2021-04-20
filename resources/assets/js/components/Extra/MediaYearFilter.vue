<template>
	
	<select @change="filterApi(search)" v-model="search.year" class="form-control" style="border-radius: 0px">
																										
		<option value="">{{lang('Year')}}</option>
		
		<option v-for="(year,index) in years" :value="year.value" :key="year.index">
      {{ year.name }}
    </option>
	
	</select>
</template>
<script>
	export default{
			props:['search'],
			 data(){
						return{
							years : []
						}
			 },

			 beforeMount(){
			 		axios.get('/api/get-storage-directories').then(res=>{
			 			this.years = res.data.data
			 		}).catch(error=>{
			 			console.log(error)
			 		})
			 },

			 methods:{
						filterApi(x){
								 this.$emit('filtering',x);
						}
				}
	}
</script>