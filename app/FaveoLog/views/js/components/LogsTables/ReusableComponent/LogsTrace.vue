<template>

	<div>

		<p id="logs_trace">{{data.trace | subStringFilter}}

			<a  id="logs_read_more" href="javascript:;" @click="showModal = true">
			
				<span class="label label-default">{{lang('read-more')}}</span>
			</a>
		</p>
		<transition name="modal">
			<logs-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :data="data" title="trace"></logs-modal>
		</transition>
	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';

	export default {
		
		name:"logs-trace",

		description: "Logs trace component",		
		
		props: {
		
			data : { type : Object, required : true }
		
		},

		data(){

			return {
			
				showModal:false,
			}
		},


		filters : {
			
			subStringFilter(value) {
				
				if(value && value.length>40){ 
					
					return value.substring(0,40) + '...';
				}else { return value; }
			}
		},

		methods:{
			
			onClose(){
				
				this.showModal = false;
			},
		},

		components:{

			'logs-modal': require('./LogsModal')
		}
	};

</script>
<style>
	.trace {
		padding: 1rem;
		color: #ffffff;
		background-color: black;
		height: 50vh;
		overflow: auto !important;
		line-height: 1.5 !important;
		white-space: nowrap !important;
	}
</style>