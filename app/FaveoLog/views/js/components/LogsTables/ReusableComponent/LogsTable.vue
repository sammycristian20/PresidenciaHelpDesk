<template>	
	<div class="col-md-12">
		<data-table id="logs_table" v-if="apiEndpoint !== ''" :url="apiEndpoint" :dataColumns="columns"  :option="options" 
			:scroll_to ="id" :componentTitle="componentTitle"/>			
	</div>
</template>

<script>
	
	export default {

		props : {

			category_ids : { type : Array, default : ()=>[] },

			created_at_start : { type : String, default : '' },

			created_at_end : { type : String, default : '' },

			cron_start_time_start : { type : String, default : '' },

			cron_start_time_end : { type : String, default : '' },

			sender_mails : { type : Array, default : ()=>[] },

			reciever_mails : { type : Array, default : ()=>[] },

			columns : { type : Array, default : ()=> [] },

			options : { type : Object, default : ()=> {} },

			apiUrl :  { type : String, default : '' },

			id : { type : String , default : ''},

			componentTitle: { type: String, default: '' }
		},

		data(){

			return {

				apiEndpoint : this.apiUrl+'?category_ids='+this.category_ids+
											'&created_at_start='+this.created_at_start+'&created_at_end='+this.created_at_end+
											'&cron_start_time_start='+this.cron_start_time_start+'&cron_start_time_end='+this.cron_start_time_end+
											'&sender_mails='+this.sender_mails+ '&reciever_mails='+this.reciever_mails
			}
		},

		watch : {
			created_at_start(newValue,oldValue){

				let categories = this.category_ids.map(a => a.id);

				let s_mails = this.sender_mails.map(a => a.email);

				let r_mails = this.reciever_mails.map(a => a.email);

				this.apiEndpoint = this.apiUrl+'?category_ids='+categories+
												'&created_at_start='+newValue+'&created_at_end='+this.created_at_end+
												'&cron_start_time_start='+this.cron_start_time_start+'&cron_start_time_end='+this.cron_start_time_end+
												'&sender_mails='+s_mails+'&reciever_mails='+r_mails;
				return newValue
			},

			created_at_end(newValue,oldValue){

				let categories = this.category_ids.map(a => a.id);

				let s_mails = this.sender_mails.map(a => a.email);

				let r_mails = this.reciever_mails.map(a => a.email);

				this.apiEndpoint = this.apiUrl+'?category_ids='+categories+
												'&created_at_start='+this.created_at_start+'&created_at_end='+newValue+
												'&cron_start_time_start='+this.cron_start_time_start+'&cron_start_time_end='+this.cron_start_time_end+
												'&sender_mails='+s_mails+'&reciever_mails='+r_mails;
				return newValue
			},

			cron_start_time_start(newValue,oldValue){
				
				let categories = this.category_ids.map(a => a.id);

				let s_mails = this.sender_mails.map(a => a.email);

				let r_mails = this.reciever_mails.map(a => a.email);

				this.apiEndpoint = this.apiUrl+'?category_ids='+categories+
												'&created_at_start='+this.created_at_start+'&created_at_end='+this.created_at_end+
												'&cron_start_time_start='+newValue+'&cron_start_time_end='+this.cron_start_time_end+
												'&sender_mails='+s_mails+'&reciever_mails='+r_mails;
				return newValue
			},

			cron_start_time_end(newValue,oldValue){
				
				let categories = this.category_ids.map(a => a.id);

				let s_mails = this.sender_mails.map(a => a.email);

				let r_mails = this.reciever_mails.map(a => a.email);

				this.apiEndpoint = this.apiUrl+'?category_ids='+categories+
												'&created_at_start='+this.created_at_start+'&created_at_end='+this.created_at_end+
												'&cron_start_time_start='+this.cron_start_time_start+'&cron_start_time_end='+newValue+
												'&sender_mails='+s_mails+'&reciever_mails='+r_mails;
				return newValue
			},

			category_ids(newValue,oldValue){
				
				let s_mails = this.sender_mails.map(a => a.email);

				let r_mails = this.reciever_mails.map(a => a.email);

				let result = newValue.map(a => a.id);

				this.apiEndpoint = this.apiUrl+'?category_ids='+result+
												'&created_at_start='+this.created_at_start+'&created_at_end='+this.created_at_end+
												'&cron_start_time_start='+this.cron_start_time_start+'&cron_start_time_end='+this.cron_start_time_end+
												'&sender_mails='+s_mails+'&reciever_mails='+r_mails;
				return newValue
			},

			sender_mails(newValue,oldValue){

				let categories = this.category_ids.map(a => a.id);

				let r_mails = this.reciever_mails.map(a => a.email);

				let result = newValue.map(a => a.email);
				
				this.apiEndpoint = this.apiUrl+'?category_ids='+categories+
												'&created_at_start='+this.created_at_start+'&created_at_end='+this.created_at_end+
												'&cron_start_time_start='+this.cron_start_time_start+'&cron_start_time_end='+this.cron_start_time_end+
												'&sender_mails='+result+'&reciever_mails='+r_mails;
				return newValue
			},

			reciever_mails(newValue,oldValue){

				let categories = this.category_ids.map(a => a.id);

				let s_mails = this.sender_mails.map(a => a.email);

				let result = newValue.map(a => a.email);
				
				this.apiEndpoint = this.apiUrl+'?category_ids='+categories+
												'&created_at_start='+this.created_at_start+'&created_at_end='+this.created_at_end+
												'&cron_start_time_start='+this.cron_start_time_start+'&cron_start_time_end='+this.cron_start_time_end+
												'&sender_mails='+s_mails+'&reciever_mails='+result;
				return newValue
			},

			apiEndpoint(newValue,oldValue){
				return newValue
			}
		},

		components:{
			
			'data-table' : require('components/Extra/DataTable'),
		}
	};
</script>

<style scoped>
		.pad{
			border-top: 1px solid #efefef;
    	padding: 8px;
		}
		#logs_table{
			padding-bottom: 60px
		}
</style>