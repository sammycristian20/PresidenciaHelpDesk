<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" @close="showModal = false" 
		:containerStyle="containerStyle">
		
		<div slot="title">
			
			<h4 class="modal-title">{{lang('settings')}}</h4>
		</div>
			
		<div v-if="!loading" slot="fields">
			
			<div class="row">

				<dynamic-select :label="lang('department')" :multiple="false" name="department" :prePopulate="true"
					classname="col-sm-6" apiEndpoint="/api/dependency/departments" :value="department" 
					:onChange="onChange" :clearable = "department ? true : false">

				</dynamic-select>

				<dynamic-select :label="lang('helptopics')" :multiple="false" name="helptopic" :prePopulate="true"
					classname="col-sm-6" apiEndpoint="/api/dependency/help-topics" :value="helptopic" 
					:onChange="onChange" :clearable = "helptopic ? true : false">

				</dynamic-select>
			</div> 

			<div class="row">
					
				<div class="col-sm-12">

					<label>Department Url</label>

					<div class="input-group">

				      <input type="text" class="form-control" ref="dept_text" :value="dept_url" readonly>
				      
				      <div class="input-group-btn">
				      
				        <button class="btn btn-default telephony_btn" @click="copyDeptUrl" v-tooltip="lang('copy')">

				        		<i class="fas fa-copy"></i>
				        </button>
				      </div>
					</div>
				</div>
			</div>

				<div class="row">

					<div class="col-sm-12">		
					
						<label>Helptopic Url</label>

						<div class="input-group">

				      <input type="text" class="form-control" ref="topic_text" :value="topic_url" readonly>
				      
				      <div class="input-group-btn">
				      
				        <button class="btn btn-default telephony_btn" @click="copyTopicUrl" v-tooltip="lang('copy')">

				        	<i class="fas fa-copy"></i>
				        </button>
				      </div>
				    </div>
				 </div>
				</div>
			</div>
		</div> 
			
		<div v-if="loading" class="row" slot="fields">
			
			<loader :animation-duration="4000" color="#1d78ff" :size="size" :class="{spin: lang_locale == 'ar'}" />
		</div>
	</modal>
</template>

<script type="text/javascript">
	
	export default {
		
		name : 'telephony-settings-modal',

		description : 'Telephony settings modal component',

		props:{
			
			showModal:{type:Boolean,default:false},

			onClose:{type: Function},

			data : { type : Object , default:()=>{}}

		},

		data(){
			
			return {

				department : '',
				
				helptopic : '',

				containerStyle : { width:'800px' },

				loading:false,

				dept_url : this.data.base_url,

				topic_url : this.data.base_url,
			}
		},

		methods : {

			onChange(value,name){

				this[name] = value;

				if(name === 'department'){

					if(value){
						
						this.dept_url = this.data.base_url + '/' + value.id + '/department'					
					} else {

						this.dept_url = this.data.base_url;
					}
				}

				if(name === 'helptopic'){

					if(value){
						
						this.topic_url = this.data.base_url + '/' + value.id + '/helptopic'					
					} else {

						this.topic_url = this.data.base_url;
					}
				}
			},

			copyDeptUrl() {

      	this.$refs.dept_text.select();
      	
      	document.execCommand('copy');
      },

      copyTopicUrl() {

      	this.$refs.topic_text.select();
      	
      	document.execCommand('copy');
      }
		},

		components:{

		'modal':require('components/Common/Modal.vue'),

		'loader':require('components/Client/Pages/ReusableComponents/Loader'),

		'text-field': require('components/MiniComponent/FormField/TextField'),

		'dynamic-select': require('components/MiniComponent/FormField/DynamicSelect'),
	}
};
</script>

<style scoped>
	.telephony_btn {
		border-top-left-radius: 0;
	   border-bottom-left-radius: 0;
	   padding-bottom: 7px;
	}
</style>