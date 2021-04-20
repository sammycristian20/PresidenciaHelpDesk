<template>

	<form-field-template :label="label" :labelStyle="labelStyle" :name="name" :classname="classname" :hint="hint" 
		:required="required">

		<input ref="fileInput" type="file" @change="onFileSelected" style="display:none" :disabled="is_default">
				
		<div>

			<faveo-image-element id="profile-pic" :classes="['profile-user-img', 'img-responsive', 'img-circle', 'img-click']" :source-url="imageSrc" :title="lang(tooltip)" :style-object="styleObj"/>

			<h6 class="text-center font-weight-normal mt-2" :style="labelCss">{{label}}</h6>
		</div>  

		<div class="text-center">
			
			<button class="btn btn-primary btn-xs text-center" :disabled="is_default" data-toggle="modal" 
				@click="$refs.fileInput.click()"> 
				<i v-if="!buttonName" class="fas fa-exchange-alt"></i>
				{{buttonName ? lang(buttonName) : btnName ? lang('change_icon') : lang('change_logo') }}
			</button>
		</div>
	</form-field-template>
</template>

<script type="text/javascript">

import { mapGetters } from 'vuex'

import {boolean} from 'helpers/extraLogics'

export default {

	name: "file-upload",

	description: "file upload component along with error block",

	props: {
		
		label: { type: String, required: true },

		hint: { type: String, default: "" }, //for tooltip message

		name: { type: String, required: true },
		
		onChange: { type: Function, Required: true },

		classname: { type: String, default: "" },

		required: { type: Boolean, default: false },

		labelStyle:{type:Object},

		id : {type: String|Number, default:'text-field'},

		value : { type: Object|String , default : '' },

		is_default : { type :  Boolean|Number, default : false },

		btnName : { type : String, default : ''},

		buttonName : { type : String, default : ''},

		labelCss : { type : Object, default : ()=>{}}

	},
	
	data() {
		
		return {
			
			selectedFile:this.value,
			
			imageSrc:this.value,
			
			tooltip : '',

			styleObj : { background : 'none' }
		};
	},

	beforeMount(){

		this.tooltipValue(this.selectedFile);

		this.selectedFile = this.value;

		this.imageSrc = this.selectedFile
	},

	methods:{
		
		onFileSelected(event) {

			if (!event.target.files[0].type.includes('image/')) {

				alert('Please select an image file');
				
				return;
			}

			if(event.target.files[0].size < 2097152) { 

				this.selectedFile = event.target.files[0];

				var element=this.$refs.fileInput;
								
				var input = event.target;
				
				if (input.files && input.files[0]) {
									
					var reader = new FileReader();
					
					reader.onload = (e) => {
						this.imageSrc = e.target.result;
					}
					reader.readAsDataURL(input.files[0]);
				}
				
				element.value="";
							
				this.onChange(this.selectedFile,this.name);

			} else {
				alert('Maximum File upload size is 2MB.')
			}
			
		},

		tooltipValue(selectedFile){

			this.tooltip = selectedFile !== null && selectedFile.name === undefined ? selectedFile.split('/')[selectedFile.split('/').length-1] : selectedFile ? selectedFile.name : 'no_file' 

			this.styleObj.background = this.tooltip === 'logo.png' ? 'black' : 'none'
		}
	},

	components: {
		"form-field-template": require("./FormFieldTemplate"),
		'faveo-image-element': require('components/Common/FaveoImageElement')
	}
};
</script>

<style>
.img-click {
	width: 100px !important;
		height: 100px !important;
}
.profile-user-img:hover{
	border: 3px solid #3c8dbc;
}
</style>