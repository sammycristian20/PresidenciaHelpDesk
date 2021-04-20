<template>
	
	<select @change="filterApi(search)" v-model="search.year" class="form-control" style="border-radius: 0px">
																										
		<option value="">{{lang('Year')}}</option>
		
		<option v-for="(year,index) in years" :value="year.value" :key="year.index">
			{{ year.name }}
		</option>
	</select>
</template>

<script>
	
	import axios from 'axios'

	export default{

		props:['search'],
		
		data(){
			
			return{
				
				years : []
			}
		},

		beforeMount(){
			
			this.getYears();
		},

		methods:{
			
			getYears(){

				axios.get('/api/get-storage-directories').then(res=>{
				
					this.years = res.data.data
					
				}).catch(error=>{
					
					this.years = [];
					
					console.log(error)
				})
			},

			filterApi(x){
				
				this.$emit('filtering',x);
			}
		}
	};
</script>