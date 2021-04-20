<template>
	<div class="ml_10">

	<div v-if="list.length > 0 && !loading">
		 <vddl-list  :list="list" :horizontal="false" :drop="handleDrop">
                  <vddl-draggable  v-for="(item, index) in list" :key="item.id" 
                    :draggable="item"
                    :index="index"
                    :wrapper="list"
                    effect-allowed="move">
                    <workflow-menu :item="item" :indexValue="index" :formList="list"></workflow-menu>
                  </vddl-draggable>
                  <vddl-placeholder class="drop-placeholder"></vddl-placeholder>
              </vddl-list>
		<div class="col-sm-12 mt-4 ml-1">
			<button class="btn btn-primary" @click="reOrder()" href="javascript:;">
			<span class="fas fa-save"> </span> {{lang('save')}}
		</button>
		<button  @click="showTable()" class="btn btn-default" href="javascript:void(0);">
			<span class="fas fa-times"> </span> {{lang('cancel')}}
		</button>
		</div>
	</div>
	<div v-if="loading" class="row" style="margin-bottom:30px">
		<loader :animation-duration="4000" color="#1d78ff" :size="60" :class="{spin: lang_locale == 'ar'}" />
	</div>
</div>
</template>

<script>
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	export default {
		data(){
			return {
				list : [],

				base :  window.axios.defaults.baseURL,

				lang_locale : '',

				loading : true
			}
		},

		props : {
			
			url : { type : String, default : ''},

			onClose : { type : Function , default : ()=>[]},

			reorder_type : { type : String, default : 'workflow'},
		},

		components: {
            'workflow-menu': require('components/Admin/Workflow/ReorderMenuBlock.vue'),
            'loader':require('components/Client/Pages/ReusableComponents/Loader'),
      	},

      	beforeMount(){
      		this.lang_locale = localStorage.getItem('LANGUAGE');
      		axios.get(this.url).then(res=>{
		      this.list = res.data.data
		      this.loading = false
		    }).catch(err=>{ 
		      this.loading = false
		    })
      	},

      	methods : {
		    handleDrop(data) {
		      const { index, list, item } = data;
		      let id = item.id;
		      item.id = null;
		      list.splice(index, 0, item);
		      setTimeout(() => {
		        for (var i in list) {
		          if (list[i].id == null) {
		            list[i].id = id;
		          }
		        }
		      }, 100);
		      this.list = list;
		    },

		    reOrder(list){
		    	this.loading = true
		    	var fd = new FormData();
		    	for(var i in this.list){
					fd.append('enforcers['+i+'][id]', this.list[i].id);
					fd.append('enforcers['+i+'][name]', this.list[i].name);
				}
				fd.append('type', this.reorder_type);
				const config = { headers: { 'Content-Type': 'multipart/form-data' } };
		    	axios.post('/api/reorder-enforcer-list',fd,config).then(res=>{
		      		this.loading = false
		    		this.onClose()
		    		successHandler(res,'dataTableModal')
		    	}).catch(err=>{
		    		this.loading = false
		    		this.onClose()
		    		errorHandler(err,'dataTableModal')
		    	})
		    },
		    showTable(){
		    	this.onClose()
		    }
      	}
	};
</script>

<style scoped>

	.ml_10 { margin-left: -10px; }
</style>