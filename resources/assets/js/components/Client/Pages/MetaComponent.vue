<template>
	<div v-if="layout">

 		<vue-headful :title="title" :description="description" />
	</div>
</template>

<script>
	export default {
		
		props:['dynamic_title','dynamic_description','name', 'layout'],
		
		data() {
		
			return {
		
				layout_data:'',
				
				system_name: this.layout.company.company_name,
				
				title:'',
				
				description:''
			}
		},

		created() {

			if(this.dynamic_title){
				
				if(this.system_name != undefined){
					
					this.title  = this.dynamic_title + ' - ' + this.system_name;
				} else {
					
					this.title  = this.dynamic_title + ' - ';
				}
			}	
			this.description  = this.dynamic_description;
		},

		watch : {
		
			title() {
		
				if(this.dynamic_title){
			
					if(this.system_name != undefined){
				
						this.title  = this.dynamic_title + ' - ' + this.system_name;
					} else {
					
						this.title  = this.dynamic_title + ' - ';
					}
				}
					this.description  = this.dynamic_description;
			}
		},
	};
</script>

<style scoped>

</style>
