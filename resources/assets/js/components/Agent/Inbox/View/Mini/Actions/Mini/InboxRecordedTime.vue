<template>

	<div>
	
		<table class="table  table-striped">
				
			<template v-if="total > 0">

				<template  v-for="track in data">
					
					<table-row  :key="track.id" :track="track" :current="pagination.currentPage" :reloadData="updateData">
					
					</table-row>	
				</template>	
			</template>
				
			<tbody v-if="total > 0">
				
				<tr style="background :none !important">
				
					<td class="mailbox-name">
				
						<div class="ticket_subject">
				
							<h4 style="font-weight:bold">{{lang('total')}}</h4>
				
							<div for="Time and amount details" style="margin-bottom:5px">
				
								<label>
				
									<a style="color:#878686">{{lang('worktime')}}
				
										<span style="color:#bab8b8;font-size: 12px;font-weight: normal;"> (hr:min) :</span>
				
									</a>
								</label>
				
								<span style="font-weight:bold">   {{(Math.floor(totalTime/60) < 10 ) ? ("0"+Math.floor(totalTime/60)):Math.floor(totalTime/60)}}:{{(totalTime%60 < 10) ? ("0" + totalTime%60): totalTime%60}}
								</span> &nbsp;
							</div>
						</div>
					</td>
				</tr>
			</tbody>
				
			<div v-if="total > 10" class="pull-right">
				
				<uib-pagination :total-items="total" v-model="pagination" class="pagination" :boundary-links="true" 
					:items-per-page="per_page" @change="pageChanged()" :rotate="true" :max-size="3" :force-ellipses="true">
							
				</uib-pagination>
			</div>
		</table>
			
		<div v-if="total == 0">
				
			<h3 style="text-align:center">{{lang('no-data-to-show')}}</h3>
		</div>

		<div v-if="loading" class="row load_margin">
			
			<loader :animation-duration="4000" :size="60"/>
		</div>
	</div>
</template>

<script>

	import axios from 'axios'
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	export default{
	
		name : 'timetrack-event',

		description : 'time track table page',

		props : {

			id : { type : String | Number, default : '' }
		},

		data () {
			
			return{
			
				loading:true,

				data:[],

				per_page:null,

				total:null,

				pagination:{currentPage: 1},

				paramsObj:{},

				totalTime:null,
			}
		},

		mounted(){
			
			this.getTableData();
		},

		created() {
			
			window.eventHub.$on('refreshTimeTrack',this.updateTableData)
		},

		methods:{

			updateData(){

				this.getTableData()
			},

			updateTableData() {

				this.loading = true;
				
				this.pagination.currentPage = 1;

				this.getTableData()
			},
			
			getTableData(y){

				var params = y;
				
				axios.get('ticket/'+this.id+'/time-track',{params}).then(res => {
			
					this.loading = false;
			
					this.data = res.data.data.data;
			
					this.per_page = res.data.data.per_page;
			
					this.total = res.data.data.total;
				
					var timesArr=this.data.map(a => a.work_time);
				
					this.totalTime = timesArr.reduce( (accumulator, currentValue) => accumulator + currentValue);
				
				}).catch(err =>{
			
					this.loading =false;
			
					errorHandler(err)
				})
			},

			pageChanged() {

				this.paramsObj['page']=this.pagination.currentPage;

				this.getTableData(this.paramsObj);

				var elmnt = document.getElementById('inbox-actions');
	  			
	  			elmnt.scrollIntoView({ behavior : "smooth"});
			},
		},

		components : {
	
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
	
			'table-row':require('./Child/RecordRow'),
	
			'alert' : require('components/MiniComponent/Alert'),
		},
	};
</script>

<style scoped>
	
	.load_margin { margin-top: 70px;margin-bottom: 70px; }
</style>