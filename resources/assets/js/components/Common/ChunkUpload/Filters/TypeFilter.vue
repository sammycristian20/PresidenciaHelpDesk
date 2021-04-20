<template>
	
	<select @change="filterApi(search)" v-model="search.type" class="form-control" style="border-radius: 0px">
		
		<option value="">{{lang('All Media Items')}}</option>
		
		<option value="image">{{lang('Image')}}</option>
		
		<option value="video">{{lang('Video')}}</option>
		
		<option value="Audio">{{lang('Audio')}}</option>

		<option value="doc">{{lang('Document')}}</option>
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