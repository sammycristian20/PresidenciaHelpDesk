<template>

	<div>

		<div class="row" v-if="hasDataPopulated === false || loading === true">

			<custom-loader :duration="loadingSpeed"></custom-loader>

		</div>

		<alert componentName="category-create"/>

		<div class="card card-light " v-if="hasDataPopulated === true">

			<div class="card-header">
				
				<h3 class="card-title">{{lang(title)}}</h3>

				<div class="card-tools">
	
					<a v-if="buttonVisible" id="view_category" class="btn-tool" :disabled="!status" v-tooltip="lang('view')"
						:href="status ? basePath()+'/category-list/'+link :'javascript:;'" :target="status ? '_blank' : ''">
								
						<i class="fa fa-eye" ></i> 
					</a>
							
					<a v-if="buttonVisible" id="delete_category" class="btn-tool" @click="showModal = true" href="javascript:;"
						v-tooltip="lang('delete')">
							
						<i class="fas fa-trash">  </i>	
					</a>
				</div>
			</div>
					
			<div class="card-body">
		
				<div class="row">
			
					<text-field label="Name" :value="name" type="text" name="name" :onChange="onChange" classname="col-sm-6" 
						:required="true">
						
					</text-field>
					
					<number-field :label="lang('display_order')" :value="display_order" name="display_order" classname="col-sm-3"
                  :onChange="onChange" type="number" :required="true">
            
            	</number-field>

					<radio-button :options="radioOptions" :label="lang('status')" name="status" :value="status":onChange="onChange" 
						classname="form-group col-sm-3" >
						
					</radio-button>
				</div> 

				<div class="row">
					
					<text-field :label="lang('description')" :value="description" type="textarea" name="description"
						rows="8" :onChange="onChange" classname="col-sm-12" :required="true">
									
					</text-field>
				</div>
			</div>

			<div class="card-footer">
				
				<button type="button" id="submit_btn" v-on:click="onSubmit()" :disabled="loading" 
					class="btn btn-primary">
						
					<i :class="iconClass"></i>&nbsp;{{lang(btnName)}}
					
				</button>
			</div>
		</div>

		<transition  name="modal">

			<category-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :apiUrl="'/category/delete/'+category_id"
				alert="category-create" redirectUrl="/category">

			</category-modal>
		</transition>
	</div>

</template>

<script>

	import axios from "axios";
	
	import { errorHandler, successHandler } from "helpers/responseHandler";
	
	import { validateCategoryCreateSettings } from "helpers/validator/categoryCreateRules";
	
	import {getIdFromUrl} from 'helpers/extraLogics';
	
	import { mapGetters } from 'vuex'

	export default {

		name : 'article-template',

		description : 'Article Template component',

		data(){
			return {

				name : '',

				status : 1,

				description : '',

				display_order : '',

				radioOptions:[{name:'active',value:1},{name:'inactive',value:0}],

				//ck editor
				classname : 'form-group',

				show_err : false,
				
				title :'create_new_category', 

				iconClass:'fas fa-save',

				btnName:'save',

				loading: false,//loader status
				
				loadingSpeed: 4000,
				
				hasDataPopulated: true,

				category_id :'',

				buttonVisible : false,

				link : '',

				showModal : false

			}
		},
		
		beforeMount() {

			const path = window.location.pathname;
			
			this.getValues(path);
		},

		methods : {

			getValues(path){

				const categoryId = getIdFromUrl(path);
			
				if (path.indexOf("edit") >= 0) {
					
					this.title = 'edit_category';
							
					this.iconClass = 'fas fa-sync-alt'
					
					this.btnName = 'update'

					this.hasDataPopulated = false;
					
					this.buttonVisible = true;

					this.getInitialValues(categoryId);
				 
				 } else {

					this.loading = false;

					this.hasDataPopulated = true
				}
			},

			getInitialValues(id) {

				this.loading = true;			
				
				axios.get('/api/edit/category/'+id).then(res => {

					this.category_id  = id

					this.hasDataPopulated = true;
					
					this.loading = false;

					this.updateStatesWithData(res.data.message.category);
				
				}).catch(err => {
					
					errorHandler(err)
					
					this.hasDataPopulated = true;
					
					this.loading = false;
				
				});
			
			},
			
			updateStatesWithData(categoryData) {
				
				this.link = categoryData.slug

				const self = this;
				
				const stateData = this.$data;
				
				Object.keys(categoryData).map(key => {

					if (stateData.hasOwnProperty(key)) {
					
						self[key] = categoryData[key];
					
					}
				
				});
			
			},
			
			isValid() {

				const { errors, isValid } = validateCategoryCreateSettings(this.$data);
			
				if (!isValid) {
			
					return false;
			
				}
			
					return true;
			
			},

			onChange(value, name) {
				
				this[name] = value;
			},

			onSubmit() {
			
				if (this.isValid()) {
			
					this.loadingSpeed = 8000;
			
					this.loading = true;
			
					const data = {};
			
					if(this.category_id != ''){

						data['id'] = this.category_id;
					
					}

					data['name'] = this.name;

					data['status'] = this.status;

					data['description'] = this.description;

					data['display_order'] = this.display_order;

					axios.post('/api/category/create', data).then(res => {

						this.loading = false;
						
						successHandler(res,'category-create');
						
						if(this.category_id === ''){
						
						 	this.redirect('/category')
						
						}else {
							this.getInitialValues(this.category_id)
						}
					
					}).catch(err => {

						this.loading = false;
					
						errorHandler(err,'category-create');
					});
				}
			},

			onClose(){
			
				this.$store.dispatch('unsetValidationError');
			
				this.showModal = false
			},
		},

		components:{
			
			"text-field": require("components/MiniComponent/FormField/TextField"),
			
			'number-field':require('components/MiniComponent/FormField/NumberField'),		

			'alert': require("components/MiniComponent/Alert"),
			
			"custom-loader": require("components/MiniComponent/Loader"),
			
			'radio-button':require('components/MiniComponent/FormField/RadioButton'),
			
			'category-modal': require('components/Agent/kb/KbDeleteModal')
		}
	};
</script>