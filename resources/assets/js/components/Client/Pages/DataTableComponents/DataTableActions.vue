<template>

	<div class="btn-group">

		<router-link v-if="data.edit_url" :to="data.edit_url">

			<span title="Edit" class="btn btn-primary btn-sm" id="edit_btn"><i class="fa fa-edit"></i></span>

		</router-link>


		<a v-if="data.deactivate_url" id="delete-button" @click="showModalMethod" href="javascript:;">
			<span title="Deactivate" class="btn btn-danger btn-sm" id="delete_btn" :disabled="disabled"><i class="fas fa-trash"></i></span>
		</a>

		<transition  name="modal">
		 
		 	<deactivate-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :layout="data.layout" 
			 	:deleteUrl="'/org/delete/user/'+data.id">

			</deactivate-modal>
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

				disabled : ''
			}
		},

		created(){
			this.disabled = boolean(this.data.is_default)
		},

		watch : {
			data(newValue,oldValue){
				this.disabled = boolean(newValue.is_default) 
				return newValue
			}
		},
		
		methods:{

			showModalMethod(){
				if(this.data.is_default){
					if(boolean(this.data.is_default)){
					this.showModal = false
				} else {
					this.showModal = true
					}
				} else {
					this.showModal = true
				}
			},

			onClose(){
		        this.showModal = false;
		        this.$store.dispatch('unsetValidationError');
		    },
		},

		components:{
			'deactivate-modal' : require('../Tickets/MiniComponents/DeactivateModal'),
		}
	};

</script>

<style type="text/css">
	#edit_btn,#edit_modal_btn {
		/* border: 1px solid #f2ebeb !important; */
	}
	#edit-button,#edit-modal-button {
		color: black !important;
	}
</style>