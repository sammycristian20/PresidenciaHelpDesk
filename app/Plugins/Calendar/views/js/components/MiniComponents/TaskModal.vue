<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title">
			
			<h4 class="modal-title">{{lang(title)}}</h4>
		</div>

		<div v-if="!loading" slot="fields" class="row">
		
				<text-field 
					:label="lang('project_name')" id="project_name" 
         	:onChange="onChange" :value="name" 
          type="text" name="name" 
          :required="true"
          classname="col-sm-12"
        />
		</div>

		<div v-if="loading" class="row" slot="fields" >
			
			<loader :animation-duration="4000" color="#1d78ff" :size="60"/>
		</div>

		<div slot="controls">
			
			<button type="button" @click="onSubmit" class="btn btn-primary" :disabled="name ? false : true">

				<i class="fas fa-check"></i> {{lang('proceed')}}
			</button>
		</div>
	</modal>
</template>

<script type="text/javascript">

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {

		name : 'task-modal',

		description : 'Task Modal component',

		props:{

			title : { type : String, default : '' },

			showModal:{type:Boolean,default:false},
			
			onClose:{type: Function},

			alertComponentName : { type : String, default : 'dataTableModal' },

			componentTitle : { type : String, default : '' },

			containerStyle : { type : Object, default : ()=>{ width : '700px'} },

			data : { type : Object, default : ()=>{} },
		},

		data(){

			return {

				loading:false,

				name : '',

				project_id : ''
			}
		},

		beforeMount() {

			this.updateSubject()
		},

		methods:{

			updateSubject() {

				if(this.title === 'edit_project') {

					this.name = this.data.name;

					this.project_id = this.data.id;
				}
			},

			onChange(value,name) {

				this[name] = value;
			},

			onSubmit(){

				this.loading = true;

				const data = {};

				data['name'] = this.name;

				if(this.title === 'edit_project'){

					axios.put('/tasks/api/project/edit/'+this.project_id,data).then(res=>{

						successHandler(res,this.alertComponentName);

						this.afterRespond();

					}).catch(err => {

						errorHandler(err,this.alertComponentName);

						if(err.response.status === 412){

							this.loading = false;
						
						} else {

							this.afterRespond();
						}
					})
				} else {

					axios.post('/tasks/api/project/create',data).then(res=>{

						successHandler(res,this.alertComponentName);

						this.afterRespond();

					}).catch(err => {

						errorHandler(err,this.alertComponentName);

						if(err.response.status === 412){

							this.loading = false;
						
						} else {

							this.afterRespond();
						}
					})
				}
			},

			afterRespond(){

				window.eventHub.$emit(this.componentTitle+'refreshData');

				this.onClose();

				this.loading = false;
			}
		},

		components:{

			'modal':require('components/Common/Modal.vue'),
				
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'text-field' : require('components/MiniComponent/FormField/TextField')
		}
	};
</script>

<style type="text/css">

</style>
