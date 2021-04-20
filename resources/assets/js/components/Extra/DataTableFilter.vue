<template>
	
	<faveo-box box-class="card card-light " :title="lang('filter')">
		
		<template slot="customActions">
			
			<custom-filter v-if="showCustomFilter" :updateFilterFields="updateFields" :selectedFilterFields="selectedFields">
				
			</custom-filter>
		</template>

		<div v-if="loading" class="card-body">
	
			<div class="row">
				
				<loader :animation-duration="4000" color="#1d78ff" :size="60"/>
			</div>
		</div>

		<div v-else id="table-filter">
		
			<div class="row" v-for="section in metaData">
				
				<template v-for="item in section.section">
					
					<dynamic-select v-if="item.type !== 'date' && item.type !== 'number' && item.type !== 'text'"
						:key="item.name"
						:id="item.name"
						:name="item.name"
						:apiEndpoint="item.url"
						:classname="item.className"
						:elements="item.elements"
						:multiple="item.isMultiple"
						:label="lang(item.label)"
						:value="item.value"
						:onChange="onChange"
						strlength="35"
						>
					</dynamic-select>

					<number-range-field v-if="item.type === 'number'"
						:label="lang(item.label)"  
						:formStyle="formStyle" 
						:value="item.value" 
						:key="item.name"
						:id="item.name"
						:name="item.name"
						:classname="item.className"  
						type="text" 
						:onChange="onChange" 
						:min_placeholder="item.min_placeholder"
						:max_placeholder="item.max_placeholder"
						:required="false">
					</number-range-field>

					<text-field v-if="item.type === 'text'" 
						:label="lang(item.label)"  
						:value="item.value" 
						:key="item.name"
						:id="item.name"
						:name="item.name" 
						:onChange="onChange" 
						:classname="item.className"   
						:required="false">

					</text-field>

					<date-time-field v-if="item.type === 'date'" 
						:label="item.label"
						:value="item.value" 
						type="datetime" 
						:key="item.name"
						:id="item.name"
						:name="item.name"
			         :onChange="onChange" 
			         :range="item.range" 
			         :required="false" 
			         format="MMMM Do YYYY, h:mm a" 
			         :classname="item.className"
			         :clearable="false" 
			         :disabled="false" 
			         :editable="true"
			         :pickers="item.pickers"
			         :currentYearDate="false" 
			         :time-picker-options="item.timeOptions"
			         :isDateSelector="false"
			         :numberStyle="numberStyle" 
			         :confirm="true"
	          	>
	        		</date-time-field>
				</template>
			</div>
		</div>

		<div slot="actions" class="card-footer">
			
			<button id="apply-btn" class="btn btn-primary" type="button" @click="onApply">
					
				<span class="fa fa-check"></span>&nbsp; {{ lang('apply')}}
			</button>

			<button id="apply-btn" class="btn btn-primary" type="button" @click="onReset">
				
				<span class="fa fa-undo"></span>&nbsp; {{ lang('reset')}}
			</button>
		
			<button v-if="from !== 'report'" id="apply-btn" class="btn btn-danger" type="button" @click="onCancel">
					
				<span class="fa fa-times"></span>&nbsp; {{ lang('cancel')}}
			</button>
		</div>
	</faveo-box>
</template>

<script>

	export default {
		
		name : "table-filter",

		description : "Table filter component",

		props:{
			
			metaData: { type:Array, required: true },

			appliedFilters : { type : Object, default : ()=>{}},
			
			from : { type : String, default  :''},

			showCustomFilter : { type : Boolean, default : false },

			updateFields : { type : Function, default : ()=>{}},

			selectedFields : { type : Array, default : ()=> []}
		},

		data(){
			
			return{
				
				selectedFilters: this.appliedFilters,

				close_on_select: !this.multiple,

				isShowFilter: false,

				componentMetaData: this.metaData,

				loading : false,

				formStyle : { width:'49.5%' },

				numberStyle : { width : '20%' },
			}
		},

		beforeMount() {

			this.updateSelectedFieldValues()
		},

		methods:{

			updateSelectedFieldValues() {

				for(var i in this.selectedFields){

					this.onChange(this.selectedFields[i].value, this.selectedFields[i].name)
				}
			},

			onChange(value, name){

				this.selectedFilters[name] = value;
			},

			onCancel() {
				
				this.$emit('selectedFilters', 'closeEvent');
			},

			onApply() {

				this.$emit('selectedFilters',this.selectedFilters);
			},
			
			onReset() {

				this.selectedFilters = {};

				this.loading = true;

				setTimeout(()=>{
					this.loading = false
				},1000)

				this.$emit('selectedFilters', 'resetEvent');
			},
		},

		components: {
			
			'dynamic-select': require("components/MiniComponent/FormField/DynamicSelect"),

			'loader':require('components/MiniComponent/Loader.vue'),

			'date-time-field': require('components/MiniComponent/FormField/TimeRangeField'),

			'number-range-field': require("components/MiniComponent/FormField/NumberRangeField"),

			'text-field': require("components/MiniComponent/FormField/TextField"),

			'faveo-box' : require('components/MiniComponent/FaveoBox'),

			'custom-filter' : require('./CustomFieldFilter'),
		}
	};
</script>

<style>
	#table-filter #range_count, #table-filter #range_option{
		background: transparent;
	}
</style>