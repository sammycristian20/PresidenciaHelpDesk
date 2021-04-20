<template>

	<tbody v-if="track">

		<tr  style="background:none !important" id="row" @mouseenter="showButton(track.id)" 
			@mouseleave="hideButton(track.id)">
			
			<td class="mailbox-name" style="border-bottom: 1px solid #f4f4f4;border-top:none !important">
			
				<div for="Time and amount details">
			
					<label>
			
						<a href="javascript:;" @click="editTimeTrack(track)">{{lang('worktime')}}
			
							<span style="color:#bab8b8;font-size: 12px;font-weight: normal;"> (hr:min) :
							</span>
						</a>
					</label>
					
					<span style="font-weight:bold">  {{(Math.floor(track.work_time/60) < 10 ) ? ("0"+Math.floor(track.work_time/60)):Math.floor(track.work_time/60)}}:{{(track.work_time%60 < 10) ? ("0" + track.work_time%60): track.work_time%60}}</span> &nbsp;
					
					<div v-if="show === track.id" class="float-right">
						
						<button id="btn" class="btn btn-primary btn-xs" @click="editTimeTrack(track)">

							<i class="far fa-edit"></i>
						</button>
						
						<button id="del" class="btn btn-danger btn-xs" @click="getDeleteId(track)">

							<i class="fas fa-trash"></i>
						</button>
					</div>
				</div>
				
				<div for="description" >
				
					<p style="word-wrap: break-word;">{{track.description}}</p>
				</div>		
				
				<div for="created time details"  style="margin-top:-15px">	
				
					<span id="span">Created on: {{formattedTime(track.created_at)}}</span>&nbsp;&nbsp;

					<span id="span" >Modified on: {{formattedTime(track.updated_at)}}</span>
				</div>										
			</td>
		</tr>
		
		<transition name="modal">
			
			<record-event v-if="showModal" :onClose="onClose" :showModal="showModal" :trackData="trackData"  
				:deleteTrack="deleteTrack" :editTrack="editTrack" :updateData="reloadData">
					
			</record-event>
		</transition>
	</tbody>	
</template>

<script>

	import { mapGetters } from 'vuex'

	export default {

		name : 'record-row',

		description : 'Time track table row component',

		props : {
			
			track : { type:Object, default : ()=> {} },

			current : { type:Number | String, default : '' },

			reloadData : { type : Function }
		},

		components:{

			'record-event':require('components/Agent/Inbox/View/Mini/Timeline/Mini/RecordTimeModal'),
		},

		data() {
			
			return {
				
				show:null,

				showModal:false,

				deleteTrack:false,

				editTrack : false,

				trackData:{},

				paramsObj:{},

				curPage:this.current
			}
		},

		computed:{
			
			...mapGetters(['formattedTime','formattedDate'])
		},

		methods:{

			showButton(x) {

				this.show = x;
			},

			hideButton(x) {
				this.show = null;
			},

			editTimeTrack(track){
			
				this.trackData = track;

				this.editTrack = true;
			
				this.showModal = true;
			},

			onClose(){
				
				this.paramsObj['page']=this.curPage;
				
				this.showModal = false;
				
				this.deleteTrack = false;

				this.editTrack = false;
				
				this.trackData = '';
				
				this.$store.dispatch('unsetValidationError');
			},

			getDeleteId(track){
				
				this.trackData = track;
				
				this.deleteTrack = true;
				
				this.showModal = true;
			}
		}
	};
</script>

<style scoped>
	
	.btn-xs{ background-image: none !important; }
	
	#span { color: #999;font-size: 11px;line-height:2; }
</style>