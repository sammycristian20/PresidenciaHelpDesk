<template>
	
	<transition name="page" mode="out-in">
		
		<div>
		
			<div v-if="loading" id="loader" class="row">
								
				<loader :animation-duration="4000" :size="60" :color="layout.portal.client_header_color"/>
			</div>

			<alert id="alert">
			</alert>

			<index v-if="!loading" :layout="layout" :auth="auth"></index>
		</div>
	</transition>
</template>

<script>
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default{

		props : { 

			layout : { type :  Object, default : ()=>{}},

			auth : { type : Object, default : ()=>{}}
		},

		data(){

			return{

				hash : '',

				rating : '',

				loading : true,
			}
		},

		beforeMount() {
			
			this.getValues();
		},


		methods:{
			
			getHashFromUrl(url){

		    let urlArray = url.split("/");

		    return urlArray[urlArray.length - 2];

			},

			getRatingFromUrl(url){

		    let urlArray = url.split("/");

		    return urlArray[urlArray.length - 1];

			},

			getValues(){
				
				this.$Progress.start();
				
				const path = window.location.pathname;
				
				this.hash = this.getHashFromUrl(path);

				this.rating = this.getRatingFromUrl(path);

				axios.get('/api/rating/'+this.hash+'/'+this.rating).then(res => {
					
					this.loading = false

					this.$Progress.finish();

					successHandler(res);

				}).catch(err => {
				
					this.loading = false

					this.$Progress.fail();

					errorHandler(err)
				})
			},
		},

		components : {

			'alert' : require('components/MiniComponent/Alert'),

			'index' : require('components/Client/Pages/Index'),
		}
	};
</script>