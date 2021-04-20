<template>
	
	<div class="card card-light">
	
		<div class="card-header">
			
			<h3 class="card-title">{{lang('filter')}}</h3>
		</div>
		<div class="card-body">
			
			<div v-if="loading"  class="row">
				
				<loader :animation-duration="4000" color="#1d78ff" :size="60"/>
			</div>

			<template  v-else>
				
				<div class="row" v-for="section in metaData" :key="section.section.name">
					
					<dynamic-select
						v-for="item in section.section"
						:key="item.name"
						:id="item.name"
						:name="item.name"
						:apiEndpoint="item.url"
						:classname="item.className"
						:elements="item.elements"
						:multiple="item.isMultiple"
						:prePopulate="item.isPrepopulate"
						:label="lang(item.label)"
						:value="item.value"
						:onChange="onChange"
						strlength="35"
						>
					</dynamic-select>
				</div>
			</template>
		</div>

		<div class="card-footer">
			
			<span class="single-btn">
				
				<button id="apply-btn" class="btn btn-primary round-btn" type="button" @click="onApply">
					
					<span class="fas fa-check"></span>&nbsp; {{ lang('apply')}}
				</button>

			</span>
			
			<span class="single-btn">
				
				<button id="apply-btn" class="btn btn-primary single-btn round-btn" type="button" @click="onReset">
					
					<span class="fas fa-undo"></span>&nbsp; {{ lang('reset')}}
				</button>
			</span>
			
			<span class="single-btn">
				
				<button id="apply-btn" class="btn btn-danger single-btn round-btn" type="button" @click="onCancel">
					
					<span class="fas fa-times"></span>&nbsp; {{ lang('cancel')}}
				</button>
			</span>
		</div>
	</div>
</template>

<script>

	export default {
		
		props:{
			
			metaData: { type:Array, required: true },

			appliedFilters : { type : Object, default : ()=>{}},
		},

		data(){
			
			return{

				selectedFilters: this.appliedFilters,

				close_on_select: !this.multiple,

				isShowFilter: false,

				componentMetaData: this.metaData,

				sections : [1,2,3],

				loading : false
			}
		},

		methods:{

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

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>