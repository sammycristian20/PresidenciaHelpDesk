<template>
	<div style="display: inline-block;float: left;">
            <select class="form-control" v-model="ticketPer" @change="ticketCount(ticketPer)">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
</template>
<script>
		export default{
			props:['ticketper'],
			data(){
				return{
	                 ticketPer:this.ticketper,
				}
			},
			methods:{

				//ticket per page
		            ticketCount(x){
		                 this.$emit('message', x);
		            }
			}
		}
</script>