
<template>
<div id="app-admin">

<alert componentName="dataTableModal"/>



	<div class="card card-light">
		
			<div class="card-header" id="social-widget-list">
				
				<h3 class="card-title ">{{lang('social-widget-settings')}}</h3>
			</div>

			<div class="card-body">		
				
				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="social-widget-list"></data-table>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

    import Vue from 'vue'

	import { mapGetters } from 'vuex'

	Vue.component('social-widget-table-actions', require('./SocialWidgetTableActions.vue'));


	export default
	 {
		name : 'social-widget-list',
		description : 'Widget list table component',
		
 
		components:{"data-table" :require("components/Extra/DataTable") 
		
		},

		data: () => {
      return {
		  apiUrl : 'api/list-widgets/social-icon',

				columns: ['name','value','action'],

				options : {},
 
      }
	},
	 
	computed:{
    
      ...mapGetters(['formattedTime','formattedDate'])
    },
		
		beforeMount(){

			const self = this;
			
			this.options = {

				headings: {

					name: 'Name',

                    value: 'Link',

					
					action:'Action'

				
				},
columnsClasses : {

					name: 'widget-name',

					title: 'widget-title',

					action: 'widget-action',
				},
					texts: { filter: '', limit: '' },

					sortable: ['name','Link'],

				filterable: ['name', 'Link'],


			
				templates: {

					
					action : 'social-widget-table-actions',
					},

				requestAdapter(data) {
					return {
						'sort-field': data.orderBy ? data.orderBy : 'name',
						'sort-order': data.ascending ? 'asc' : 'desc',
						'search-query': data.query.trim(),
						page: data.page,
						limit: data.limit,
					}
				},
				responseAdapter({data}) {
					
					return {
						
						data: data.data.widgets.map(data => {
							data.edit_url =self.basePath()+ '/socialWidgetTableaction/' + data.id;
							
							data.view_url = self.basePath()+'/widgets/social-icon/' + data.id ;

							
							return data;
						}),
						count: data.data.total
					}
				},

					components : { 

			"data-table" : require("components/Extra/DataTable"),

			"alert": require("components/MiniComponent/Alert"),
		},
			};
		},
			};

</script>

<style type="text/css" scoped>
.widget-name{
		width:30% ;
		word-break: break-word;
	}
	.widget-title{
		width:30%;
		word-break: break-word;
	}

	.widget-value{
		white-space: nowrap;
	}
		.widget-action{
		white-space: nowrap;
	}
</style>