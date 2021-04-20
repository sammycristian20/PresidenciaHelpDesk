<template>
	
	<div :class="[(lang_locale === 'ar') ? 'float-left' : 'float-right']">
	
		<template v-if="getRatingTypes">
	
			<table>
	
				<tbody>
	
					<tr v-for="(rating,index) in all_ratings">
	
						<td id="rating_align" class="star-rating-control">
		
							<div class="ticketratingtitle text-right"><b>{{ rating.name }}</b>&nbsp;&nbsp;</div>

							<i v-if="from !== 'show-ticket'" :class="{float1: lang_locale == 'ar'}" class="fa fa-minus-circle cancel" 
								@click="setCurrentSelectedRating(rating,0,index)">
											
							</i>
								
							<div v-if="from === 'show-ticket'">
									
								<star-rating v-model="rating.rating_value" :star-size="18" :show-rating="false" :max-rating="rating.rating_scale"  @rating-selected="setCurrentSelectedRating(rating,rating.rating_value)" :increment="0.5" :read-only="true">
									
								</star-rating>
							</div>

							<div v-else>
									
								<star-rating v-model="rating.rating_value" :star-size="18" :show-rating="false" :max-rating="rating.rating_scale"  @rating-selected="setCurrentSelectedRating(rating,rating.rating_value)" :increment="0.5" :read-only="rating.allow_modification === 0 ? true : false">
									
								</star-rating>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</template>

		<template>
			
			<div v-if="loading" class="row">

				<client-panel-loader :size="60" :color="layout.portal.client_header_color"></client-panel-loader>
			</div>
		</template>
	</div>
</template>
<script>

import { findObjectByKey } from 'helpers/extraLogics'

import { mapGetters } from 'vuex'

import {errorHandler, successHandler} from 'helpers/responseHandler'

	export default {

		props:{

			url : {type : String},

			AreaRatings : {type : Array, default : ()=>[]},

			layout : {type : Object, default : ()=>{}},

			auth : {type : Object, default : ()=>{}},

			department : { type : String, default :''},

			ticketId : { type : String | Number, default : ''},
			
			commentId : { type : String | Number, default : ''},

			area : { type : String, default :''},

			from : { type : String, default : ''}

		},

		data() {
			return {

				matched_rating:{},

				rating_value:0,

				lang_locale : this.layout.language,

				all_ratings : '',

				default_ratings : '',

				loading : true,

				request_data : ''
			}
		},

		computed: { 

			...mapGetters(['getRatingTypes'])
		},

		beforeMount(){
			
			this.$Progress.start();

			this.ratingTypes();
		},

		methods: {

			ratingTypes() {

				setTimeout(()=>{

					this.all_ratings = this.getRatingTypes;

					this.default_ratings = this.getRatingTypes;

					var ratingArr=[];
					
					for(var i in this.all_ratings){
						
						if(this.all_ratings[i].rating_area === this.area){
						
							if(this.all_ratings[i].restrict === '' || this.all_ratings[i].restrict === this.department 
								|| this.all_ratings[i].restrict === 'General'){
								
								this.all_ratings[i]['rating_value']=0;
								
								this.all_ratings[i]['ticket_id']=this.ticketId;
								
								ratingArr.push(this.all_ratings[i])
							}
						}
					}
				
					if(this.AreaRatings.length != 0){ 
						
						for(var i in this.all_ratings){
							
							for(var j in this.AreaRatings){
								
								if(this.all_ratings[i].id == this.AreaRatings[j].rating_id){
								
									if(this.all_ratings[i].rating_area === this.area){
										
										if(this.all_ratings[i].restrict == '' || this.all_ratings[i].restrict == this.department 
											|| this.all_ratings[i].restrict == 'General'){
											
											this.all_ratings[i]['rating_value']=this.AreaRatings[j].rating_value;
											
											this.all_ratings[i]['ticket_id']=this.ticketId;
										}
									}
								}
							}
						}
					}
					this.all_ratings=ratingArr;

					this.loading = false;

					this.$Progress.finish();
				},2000)
			},	

			setCurrentSelectedRating(rating,value,index) {
				
				this.$Progress.start();

				this.loading = true;

				if(value === 0) {

					this.all_ratings[index].rating_value = 0;
				}
				
				if(rating.rating_area=='Helpdesk Area'){

					this.request_data = rating.name+'='+ value;
					
					this.submitRatings();

				} else {

					this.request_data = rating.name +','+ this.commentId +'='+ value ;

					this.submitRatings();
				}
			},

			submitRatings(){

				this.$Progress.start();
				
				axios.post(this.url+this.ticketId,this.request_data).then(res=>{

					successHandler(res,'statusBox')
		
					this.$Progress.finish();

					this.loading = false;

				}).catch(res=>{
						
					this.loading = false;

					this.$Progress.fail();
				})
			}
		},

		components :{
			
			'client-panel-loader' : require('components/Client/ClientPanelLayoutComponents/ReusableComponents/Loader.vue'),
		}

	};
</script>

<style scoped>
.cancel{
	color: red;
	font-size: medium;
	margin-right: 3px;
	cursor: pointer;
	margin-top: 11px;
}
#rate{
	margin-bottom: 15px !important
}
#rating_align{
	display: inline-flex !important;
}
.comment-meta .cancel {
	margin-top: 5px !important;
}
.vue-star-rating{
	margin-top: -2px !important;
}
</style>
