<template>
		
	<div>
		
		<alert componentName="UserReport"/>

		<div class="card card-light ">
		
			<div class="card-header">
			
				<h3 class="card-title" v-tooltip="lang('report')">{{lang('report')}}</h3>
			</div>
			
			<div class="card-body">
			
					<div class="row">
						
						<date-time-field :label="lang('select_date_range')"
							:value="date"
							type="date"
							name="date"
							:required="false"
							:onChange="onChange" range
							:currentYearDate="false"
							format="YYYY-MM-DD" classname="col-md-6 col-sm-12 col-12" :confirm="true"
							:clearable="true" :editable="true" :disabled="false" :after="new Date()">
						</date-time-field>
					</div>

					<div class="row">
					
						<div v-if="loading" class="col-md-12 col-sm-12 col-12">

							<loader :animation-duration="4000" :size="60"/>
						</div>
							
						<div v-else class="col-md-12 col-sm-12 col-12">
						
							<canvas id="trafficBar"></canvas>
						</div>
						
						<hr class="visible-xs-block">
					</div>
			</div>
		</div>
	</div>
</template>

<script>
	
	import Chart from 'chart.js';
	
	import moment from 'moment';

	import axios from 'axios';

	export default {

		name : 'user-report',

		description : 'User report page',

		props : { 

			id : { type : String | Number, default : '' },
		},

		data() {

			return {

				chartData : '',

				start_date : '',
				
				end_date : '',
				
				date  :'',
				
				apiUrl : '/user-chat-report',
				
				loading : true
			}
		},

		computed : {

			isMobile () {
	     
	      return (window.innerWidth <= 800 && window.innerHeight <= 600)
	    }
		},

		beforeMount(){

			this.getChartData();
		},

		created(){

			window.eventHub.$on('refreshOrgReport',this.getChartData)
		},

		methods : {

			onChange(value,name){

				this.start_date = value[0] !== null ? moment(value[0]).format('YYYY-MM-DD') : '';
				
				this.end_date =  value[1] !== null ? moment(value[1]).format('YYYY-MM-DD') : '';
				
				if(value){
				
					this.getChartData();	
				}
			},

			getChartData(){

				this.loading = true;
				
				axios.get(this.apiUrl,{
					params : {
					'org_id[0]' : this.id,
					'start_date' : this.start_date,
					'end_date' : this.end_date,
				}}).then(res=>{
						
					this.chartData = res.data.data;
				
					this.showChart(this.chartData);
					
				}).catch(error=>{
				
					this.loading = false
				})
			},

			showChart(data){
				
				this.loading = false;
				
				var newset = data.datasets.map(el=>{
				
					el = {
				
						label: el.label,
        
            fill: false,
        
            borderColor: el.borderColor,
        
            pointBackgroundColor: el.pointBackgroundColor,
        
            backgroundColor: el.backgroundColor,
        
            data: el.data
					}
					return el
				})
				
				this.$nextTick(() => {
				
					var ctx = document.getElementById('trafficBar').getContext('2d')
					
					var config = {
					
						type: 'line',
					
						data: {
				
							labels: data.labels,
						
							datasets: newset
						},
				
						options: {
				
							responsive: true,
							
							maintainAspectRatio: !this.isMobile,
				
							scales: {
				
				        yAxes: [{

				          ticks: {
				
				            stepSize: 1,
				
				            min: 0,
				          }
				        }]
				      },
							
							legend: {
							
								position: 'bottom',
							
								display: true
							},
							tooltips: {
								
								mode: 'label',
								
								xPadding: 10,
								
								yPadding: 10,
								
								bodySpacing: 10
							}
						}
					}
					new Chart(ctx, config)
				})
			}
		},

		components : {

			"alert": require("components/MiniComponent/Alert"),

			'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>

<style scoped>
	
.fullCanvas {
	width: 100%;
}
</style>