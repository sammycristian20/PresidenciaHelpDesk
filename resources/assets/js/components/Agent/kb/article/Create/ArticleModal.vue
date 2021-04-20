<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="modalStyle">

		<div slot="title">
				
			<h4 class="modal-title">{{lang(title)}}</h4>
		</div>

		<div slot="fields">
				
			<div slot="alert" class="row">
			
				<alert/>
			</div>
			
			<template v-if="title === 'new_category'">
					
				<div class="row">

					<text-field :label="lang('name')" :value="name" 
						type="text" name="name"
						:onChange="onChange" classname="col-sm-6" :required="true">
					</text-field>

					<radio-button :options="radioOptions" :label="lang('status')" 
						name="status" :value="status"
						:onChange="onChange" classname="form-group col-sm-4" >
					</radio-button>
				</div>

				<div class="row">

					<number-field :label="lang('display_order')" :value="display_order" 
						name="display_order" classname="col-sm-12"
						:onChange="onChange" type="number" :required="true">
					</number-field>

					<text-field :label="lang('description')" :value="description" 
						type="textarea" name="description"
						:onChange="onChange" classname="col-sm-12" :required="true">						
					</text-field>
				</div>
			</template>

			<div v-if="title === 'new_tag'" class="row">
					
				<text-field :label="lang('name')" :value="name" type="text" name="name" :onChange="onChange" 
					classname="col-sm-12" :required="true">
					
				</text-field>
			</div>

			<div v-if="title === 'delete'">
					
				<span>{{lang('are_you_sure_you_want_to_delete')}}</span>
			</div>
		</div>
				
		<div slot="fields" class="row" v-if="loading === true">
			
			<custom-loader :duration="4000"></custom-loader>
		</div>
							
		<div slot="controls">
			
			<button type="button" id="submit_btn"  @click = "onSubmit()" :class="btnClass" :disabled="isDisabled">
				
				<i :class="iconClass"></i> {{lang(btnName)}}
			</button>
		</div>
	</modal>	
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import {validateCategoryCreate} from "helpers/validator/categoryCreateValidation.js"

	import { validateKbTagSettings } from "helpers/validator/kbTagRules";

	import { mapGetters } from 'vuex'

	import { findObjectByKey } from 'helpers/extraLogics'

	import axios from 'axios'

	export default {

		name:'article-modal',

		description:'Article modal Component',

		props:{

			showModal:{type:Boolean,default:false},

			onClose:{type: Function},

			newCategory:{type: Function},

			newTag : { type : Function },

			title: { type : String },

			id : { type : String | Number , default : ''}
		},

		data:()=>({

			btnName:'save',

			iconClass:'fas fa-save',

			btnClass:'btn btn-primary',
			
			isDisabled:false,

			description:'',

			loading:false,

			size: 60,

			color: '#1d78ff',

			apiUrl:'',

			lang_locale:'',

			radioOptions:[{name:'active',value:1}],

			status : 1 ,

			name : '' ,

			display_order : '' ,

			modalStyle : { width : '850px'}

		}),

		beforeMount(){
			
			this.iconClass = this.title === 'delete' ? 'fas fa-trash' : 'fas fa-save'
			
			this.btnClass = this.title === 'delete' ? 'btn btn-danger' : 'btn btn-primary'
			
			this.btnName = this.title === 'delete' ? 'delete' : 'save'
			
			this.modalStyle.width = this.title === 'delete' ? '500px' : '850px'

		},

		methods:{
		
		isValid(){
		
			const {errors, isValid} = validateCategoryCreate(this.$data)
		
			if(!isValid){
		
				return false
			}
			return true
		},

		isTagValid(){
			
			const {errors, isValid} = validateKbTagSettings(this.$data)
			
			if(!isValid){
			
				return false
			}
			return true
		},
		
		onChange(value, name,){
		
				this[name]= value;	
		},

		initialState(){

			this.loading = false;
			
			this.isDisabled = false
			
			this.name = ''
			
			this.description = ''
			
			this.display_order = ''
		},

		onSubmit(){
			
			switch (this.title) {

			  	case 'delete':
			    	this.deleteMethod();
			    	break;

			  	case 'new_tag':
			   	this.createNewTag();
			   	break;

			   case 'new_category':
			   	this.createNewCategory();
			   	break;
			}
		},

		deleteMethod(){
			
			this.loading = true
			
			this.isDisabled = true
			
			axios.delete('/article/delete/'+this.id).then(res=>{					
				
				this.afterSuccess(res);
				
				this.redirect('/article');
			
			}).catch(err => {
						
				this.afterFailure(err)
			})
		},

		createNewTag(){
			
			if(this.isTagValid()){
				
				this.loading = true
			
				this.isDisabled = true
				
				const data = {};
				
				data['name'] = this.name;

				data['kb'] = 1;
				
				axios.post('/tag', data).then(res => {
					
					this.getNewTag(this.name)
					
					this.afterSuccess(res);
				}).catch(err => {
						
						this.afterFailure(err)
				});
			}
		},

		createNewCategory(){
			
			if(this.isValid()){
					
				this.loading = true
			
				this.isDisabled = true
				
				axios.post('/api/category/create',{
			
					name : this.name,
					
					description : this.description,
					
					status : this.status,
					
					display_order : this.display_order
				
				}).then(res=>{
						
						this.getNewCategory(this.name)
						
						this.afterSuccess(res)
				
				}).catch(error=>{
					
					this.afterFailure(error)
				})
			}
		},

		afterSuccess(res){
			
			this.loading = false;
						
			this.isDisabled = true;
			
			successHandler(res,'article-create');
				
			this.initialState()
			
			this.onClose();
		},
		
		afterFailure(error){
			
			this.loading = false;
					
			this.isDisabled = false
					
			errorHandler(error);
		},

		getNewTag(name){
			
			axios.get('/api/dependency/kb-tags?search-query='+name).then(res=>{
				
				let tag = findObjectByKey(res.data.data.kbtags, 'name',name);
				
				this.newTag(tag);
			})
		},

		getNewCategory(name){

			axios.get('/api/dependency/categories?search-query='+name).then(res=>{
			
				let category = findObjectByKey(res.data.data.categories, 'name',name);
			
				this.newCategory(category);
			})
		}
	},

	components:{
		
		'modal':require('components/Common/Modal.vue'),
		
		'text-field': require('components/MiniComponent/FormField/TextField'),
		
		'number-field': require('components/MiniComponent/FormField/NumberField'),
		
		'radio-button':require('components/MiniComponent/FormField/RadioButton'),
		
		'alert' : require('components/MiniComponent/Alert'),
		
		"custom-loader": require("components/MiniComponent/Loader")
	}
};
</script>