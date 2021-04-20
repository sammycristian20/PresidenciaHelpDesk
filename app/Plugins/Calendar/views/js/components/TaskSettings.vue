<template>

	<div>
	
		<alert componentName="dataTableModal"/>

		<div class="card card-light card-tabs">
			
			<div class="card-header p-0 pt-1">
				
				<ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
					
					<li v-for="section in tabs" :key="section.id" class="nav-item">
						
						<a  id="settings_tab" @click="associates(section.id)" class="nav-link" 
							:class="{ active: category === section.id }" data-toggle="pill" role="tab">
							{{lang(section.title)}}
						</a>
					</li>	
				</ul>
			</div>
			
			<div class="card-body">
				
				<div class="tab-content">
					
					<div class="tab-pane active" role="tabpanel">
						
						<component v-bind:is="currentComponent" :category="category"></component>					
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>

	export default {

		name : 'task-settings',

		description : 'Task Settings page',

		data(){

			return {

        tabs:[
          
          {id : 'project', title : 'project'},
          
          {id : 'tasklist', title : 'task-plugin-category'},
        ],
				category : 'project',

				loading: false,
			}
		},

		computed :{
			
			currentComponent(){
				
				let option = this.category;

        return option === 'project' ? 'task-project' : 'task-list'
      }
    },

		methods : {

			associates(category){

				this.category = category;
			}
		},

		components: {

			'task-project' : require('./MiniComponents/TaskProject'),

			'task-list' : require('./MiniComponents/TaskList'),
			
			'alert' : require('components/MiniComponent/Alert')
		},
	};
</script>

<style>
	#settings_tab{
		cursor: pointer;
		margin-bottom: -1px;
	}
</style>