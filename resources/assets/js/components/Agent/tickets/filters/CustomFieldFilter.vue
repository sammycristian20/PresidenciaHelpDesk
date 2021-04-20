<template>

	<div class="card-tools">
			
		<a href="javascript:;" class="btn-tool dropdown-toggle" data-toggle="dropdown" v-tooltip="trans('add_custom_field')" 
			@click="getCustomFields($event)">
			
			<i class="fas fa-plus"> </i>
		</a>
			
		<div class="dropdown-menu dropdown-menu-right dropdown-menu-lg custom_filter_menu" role="menu" 
			@click="$event.stopPropagation()" style="">
			
			<span class="dropdown-header">{{trans('filter_custom_field')}}</span>

			<div class="px-2">

				<input type="text" class="form-control" placeholder="filter here" v-model="search">
			</div> 

			<template v-if="visibileCustomFields.length && !loading">
				
				<template v-for="(field,index) in visibileCustomFields">
					
					<a :id="'form-field-'+index" role="menuitem" tabindex="-1" href="javascript:;" class="dropdown-item" 
						@click="toggleFormFieldVisibility(field)" :class="{ 'selected-field' : field.selected }">
						
						<span v-if="field.selected==1">
								
							<span aria-hidden="true" class="glyphicon glyphicon-ok"></span>
						</span>&nbsp;
							
						<span style="position: absolute;margin-top: -1px">{{field.label}}</span>
					</a>
				</template>
			 </template>
			
			<a v-if="!visibileCustomFields.length && !loading" class="dropdown-item">{{ trans('no_results_found') }}</a>

			<div v-if="loading" style="padding :35px;">
	       	
	       	<loader :size="30" :duration="4000" :color="'#1d78ff'"></loader>
	     </div>
		</div>
	</div>
</template>
<script>

	import axios from 'axios';
	import $ from 'jquery';

	export default{

					data(){
						return{
							allCustomFields:[],
							selectedCustomFields: this.selectedFilters,
							searchedCustomFields:[],
							search:'',
							loading: true,
						}
					},

					props:{
						selectedFilters: {type: Array, required: true},
					},

					mounted(){
							$('.dropdown-menu>.unbind-click').click(function(event){
									 event.stopPropagation();
							});
					},

					methods:{
						//set selected custom filed
						setSelectedField(data){

								if(data.data.length>0){

									for(var i in data.data){

										// if key `selected` is not present in the object,
										// we initialise it with zero
										if(data.data[i].selected == undefined){
											data.data[i].selected = 0;
										}

										for(var j in this.selectedCustomFields){
											if(this.selectedCustomFields[j].id == data.data[i].id){
												data.data[i].selected = 1;
												data.data[i].value=this.selectedCustomFields[j].value;
											}
										}
									}

									this.allCustomFields = data.data;
									this.showloader=false;
									this.nextUrl=data;
								}
						},

						//get custom fields
						getCustomFields(x){

									this.toggleOptions();
									this.loading = true;
									axios.get('api/ticket-custom-fields-flattened')
									 .then(res => {
											 this.setSelectedField(res.data);
											 this.loading = false;
									 }).catch(err => {
											 errorHandler(err)
											 this.loading = false;
									 });

									this.unbindClickInContainer(x)
						},

						/**
						 * * NOTE: this has been moved to a seperate method because jquery has been
						 * defined as a global object and cannot be mocked in test cases
						 * @param  {Object} x DOM element reference
						 * @return {undefined}
						 */
						unbindClickInContainer(x){
							$(x.target).attr('@click',null).unbind('click');
						},

						/**
						 * Toggles option in the dropdown
						 * NOTE: this has been moved to a seperate method because jquery has been
						 * defined as a global object and cannot be mocked in test cases
						 * @return {undefined}
						 */
						toggleOptions(){
							$(".custom-menu").dropdown("toggle");
						},

						/**
						 * Toggles form field visiblilty
						 * @param  {Object} formFieldObject
						 * @return {undefined}
						 */
						toggleFormFieldVisibility(formFieldObject){
							if(!formFieldObject.selected){
								 formFieldObject.selected=1;
								 formFieldObject.value='';
								 this.selectedCustomFields.push(formFieldObject);
							 }
							 else{
									formFieldObject.selected=0;
									this.selectedCustomFields = this.selectedCustomFields.filter(function(el) {
										 return el.id != formFieldObject.id;
									 });
							 }
							this.$emit('custom',this.selectedCustomFields);
						},

						/**
						 * Filters fields which matches the query
						 * @param  {string} searchQuery
						 * @return {Array}
						 */
						searchFilter(searchQuery){
							return this.allCustomFields.filter(field => {
								searchQuery = searchQuery.toLowerCase();
								let label = field.label.toLowerCase();
								return label.indexOf(searchQuery) !== -1;
							})
						},
					},

				watch : {
					search(val){
						this.searchedCustomFields = this.searchFilter(val);
					}
				},

				computed :{
					visibileCustomFields(){
						return this.search == '' ? this.allCustomFields : this.searchedCustomFields;
					}
				},

				components : {

					'loader':require('components/Extra/Loader.vue'),
				}
	};
</script>

<style scoped>

	.selected-field{ background: #f7f8f9; }

	/*.custom_filter_menu { max-height: 250px; overflow-x: hidden;overflow-y: auto; transform: translate3d(-124px, 31px, 0px) !important;}*/
</style>