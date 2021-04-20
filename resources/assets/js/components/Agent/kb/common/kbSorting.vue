<template>

	<div class="btn-group" style="display:inline-block;margin-left:10px;">
	
		<button class="btn btn-default dropdown-toggle sortval" type="button" data-toggle="dropdown"
			style="background-color: #fff;">{{ lang('sort-by') }}
			
		</button>
			
		<div class="dropdown-menu dropdown-menu-right" style="z-index:9999;">
			
			<span class="dropdown-header">{{ lang('sorting_menu') }}</span>

			<template v-for="(sorting,index) in sortingMenu">
				
				<a class="dropdown-item" @click="sort(sorting,index)" href="javascript:;" id="sort">

					<i :class="sorting.class"> </i> {{lang('sort-by')}} {{sorting.name}}
				</a>
			</template>
		</div>
	</div>
</template>

<script>

	export default{

	props:['name1','value1','name2','value2'],

	data(){

		return{

			sortingMenu:
			[{
					name:this.name1,
					value:this.value1,
					class:'fas fa-sort'
				},
				{
					name:this.name2,
					value:this.value2,
					class:'fas fa-sort'
			}]
		}
	},

	created(){
	
		if(this.name2 == undefined){
	
			this.sortingMenu=
			[{
					name:this.name1,
					value:this.value1,
					class:'fas fa-sort'
				}]
		}
	},

	methods:{
	//sorting function
		sort(x,y){
			this.$emit('sort', x);
		}
	}
};
</script>