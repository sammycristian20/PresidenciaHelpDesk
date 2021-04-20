<template>

	<div class="actions-row">

		<a v-if="data.edit_url" class="btn btn-primary" :href="data.edit_url" v-tooltip="trans('edit')"
			:target="data.tableName ? '_blank' : '' ">

			<i class="fas fa-edit"></i>
		</a>

		<a v-if="data.settings_url" class="btn btn-primary" :href="data.settings_url" target="_blank" 
			rel="noopener noreferrer" v-tooltip="trans('settings')">

			<i class="fas fa-cogs"></i>
		</a>

		<a v-if="data.edit_modal" class="btn btn-primary" @click="showEditModalMethod" href="javascript:;" 
			v-tooltip="disabled ? trans('default_field_is_not_editable') : trans('edit')" :disabled="disabled">

			<i class="fas fa-edit"></i>
		</a>

		<a v-if="data.download_url" class="btn btn-primary" :href="data.download_url" v-tooltip="trans('download')">

			<i class="fas fa-download"></i>
		</a>

		<a v-if="data.view_url" class="btn btn-primary" :href="data.view_url"
			:target="data.tableName ? '_blank' : '' " v-tooltip="trans('view')">
	
			<i class="fas fa-eye"></i>
		</a>

		<span v-tooltip="disabled ? trans('default_field_is_not_deletable') : trans('delete')">

			<button v-if="data.delete_url" class="btn btn-danger" @click="showModalMethod"
				:disabled="disabled">

				 <i class="fas fa-trash"></i>
			</button>
		</span>
		<transition name="modal">

		 <delete-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :deleteUrl="data.delete_url" :alertComponentName="alert" ></delete-modal>
		</transition>

		<transition name="modal">

		 	<data-table-modal v-if="showEditModal" title="edit" :onClose="onClose" :showModal="showEditModal"
		 		:data="data" :apiUrl="data.edit_modal">
		 	</data-table-modal>
		</transition> 
	</div>

</template>

<script type="text/javascript">
	import axios from 'axios';
	import {boolean} from 'helpers/extraLogics'
	export default {
		name:"data-table-actions",
		description: "Contains edit, delete and view buttons as group which can be used as a component as whole. It is built basically for displaying edit, delete and view button in a datable.",
		props: {
			data : { type : Object, required : true },
		},
		data(){
			return{
				showModal : false,
				showEditModal : false,
				location: this.data.delete_url,
				alert : '',
			}
		},

		computed : {

			disabled() {

				return boolean(this.data.is_default)
			}
		},

		created() {
			
			this.updateAlert()
		},

		methods:{

			updateAlert() {

				this.alert = this.data.alertComponentName ? this.data.alertComponentName : 'dataTableModal'; 
			},

			showModalMethod(){
				if(this.data.is_default){
					this.showModal = false
				} else {
					this.showModal = true
				}
			},
			showEditModalMethod(){
				if(this.data.is_default){
					this.showEditModal = false
				} else {
					this.showEditModal = true
				}
			},
			onClose(){
		    this.showModal = false;
		    this.showEditModal = false;
		    this.$store.dispatch('unsetValidationError');
		  },
		},
		components:{
			'delete-modal': require('components/MiniComponent/DataTableComponents/DeleteModal'),
			'data-table-modal': require('components/MiniComponent/DataTableComponents/DataTableModal')
		}
	};
</script>

<style scoped>
	
	.actions-row a { padding-right: 10px;
    padding-left: 10px; }
</style>

