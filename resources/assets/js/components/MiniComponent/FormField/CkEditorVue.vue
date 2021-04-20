<template>

	<form-field-template :label="label" :labelStyle="labelStyle" :name="name" :classname="classname" :hint="hint" 
		:required="required" :isInlineForm="isInlineForm">

		<ValidationProvider :name="name" :rules="rules">

      <template slot-scope="{ failed, errors, classes }">
		
			<ckeditor id="editor" :name="name" :editor="editor" v-model="changedValue" :config="editorConfig" v-on:input="onChange(changedValue, name)"
			tag-name="textarea" :class="classes">
			</ckeditor>

			<span v-show="failed" class="error-block is-danger">{{errors[0]}}</span>

      </template>
		</ValidationProvider>

	</form-field-template>
</template>

<script>

	export default{

		props : {
			
			name : {type : String ,default : 'reply_content'},
			
			value : {type : String ,required:true},

			label : {type : String ,required:true},

			noDropdown : { type : Boolean , default : false},

			onChange: { type: Function, Required: true },

			required: { type: Boolean, default: false },

			labelStyle:{type:Object},

			hint: { type: String, default: "" }, //for tooltip message

			classname: { type: String, default: "" },

			lang : { type : String, default  : 'en'},

			isInlineForm: { type: Boolean, default: false },

			rules: { type: String, default: '' }
		
		},

		data() {
			
			return {

				csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
				
				editor: ClassicEditor,

				changedValue : this.value,
				
				editorConfig: {

					ckfinder: {

						uploadUrl: ''
					},

					language: {},

					allowedContent: true,
				}
			};
		},

		created() {

			this.editorConfig.ckfinder.uploadUrl = this.basePath()+'/img_upload?_token='+this.csrf;

			this.editorConfig.language = { ui : 'en', content : this.isRtlLayout ? 'ar' : 'en' }
		},

		 mounted() {

			this.changedValue = this.value;
		 },

		watch: {
			
			value(newVal) {
				
				this.changedValue = newVal;
			}
		},
		
		components: {

			"form-field-template": require("./FormFieldTemplate")
		}
	};
</script>

<style>
	.ck.ck-editor__main>.ck-editor__editable {
		border-color: var(--ck-color-base-border);
		min-height: 200px;
		max-height: 200px;
	}

	.table{
		overflow-x: auto;
	}

	.ck-content table,.ck-content table td,.ck-content table tr,.ck-content table th {  
	  border: 1px solid #ddd;
	  text-align: left;
	}

	.ck-content table {
	  border-collapse: collapse;
	  width: auto !important; 
	}

	.ck-content .table {
    margin: 1em auto;
    display: table;
     width: auto !important; 
	}

	.ck-content table th, .ck-content table td {
	  padding: 15px;
	}

	.ck.ck-balloon-panel { z-index: 9999; }
</style>
