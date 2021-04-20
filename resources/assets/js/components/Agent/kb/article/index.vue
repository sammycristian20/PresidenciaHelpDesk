<template>

<div>

	<alert componentName="ArticlesIndex"/>

	<div class="card card-light ">
		
		<div class="card-header">
			
			<h3 class="card-title">{{lang('list_of_articles')}}</h3>

			<div class="card-tools">
       		
       		<a :href="basePath()+'/article/create'" class="btn-tool" v-tooltip="lang('create_article')">

       			<i class='fas fa-plus'> </i> 
       		</a>
    		</div>
		</div>

		<div class="card-body" id="kb-articles">
			
			<div class="row"  v-if="!loading">

				<div class="col-md-4">
					
					<template v-if="records > 0">
						
						<data-per-page :ticketper="perpage" v-on:message="handlePageCount"/>
					
						<kb-sorting name1="Published time" value1="publish_time" name2="Article name" value2="name" 
							v-on:sort="sorting"/>
					</template>
				</div>

				<div class="col-md-8">
					
					<div class="float-right">

						<span style="line-height: 2;">{{ lang('search') }}:&nbsp;</span>
						
						<input id="search" type="text"  class="form-control search-field" v-model="searchFilter" 
							placeholder="search here" @keyup="refresh">
					</div>
				</div>
			</div>

			<div v-if="!loading">

				<template v-if="records > 0">
					
					<articles-box v-for="article in articleList"  :key="article.id" :article="article" :status="status"></articles-box>
					
					<div class="float-right" v-if="records >  10">
						
						<uib-pagination :total-items="records" v-model="pagination" class="pagination" :boundary-links="true" :items-per-page="perpage" @change="pageChanged()"  :rotate="true" :max-size="3" :force-ellipses="true">
							
						</uib-pagination>
					</div>
				</template>

				<div v-if="show && records == 0">
				
					<p style="text-align:center">{{lang('no-data-to-show')}}</p>
				</div>
			</div>

			<div class="row" v-if="loading">
			
				<loader></loader>
			</div>

			<div class="row" v-if="pageLoader">
			
				<custom-loader></custom-loader>
			</div>
		</div>
	</div>
</div>  
</template>
<script>

	import axios from 'axios'
	
	export default {
		
		data() {
			
			return {
				
				articleList:'',
				
				paramsObj:{},
				
				length:0,
				
				// for pagination
				perpage:10,
				
				pagination:{currentPage: 1},
				
				records:0,
				
				// for loader
				loading:true,

				pageLoader : false,
				
				// for search
				search:'',
				
				status : 1,
				
				show:false
			}
		},

		created () {

			window.eventHub.$on('updateKbList',this.updateList);
		},

		beforeMount() {
			
			this.commonApi();
			
			this.getKbStatus();
		},

		computed: {
			
			// for search
      	searchFilter : {
	
				get() {
          	
          		return this.search;
        		},
        		
        		set(value) {
          		
          		this.search=value;
          			
          		this.pageLoader = true;

          		this.paramsObj['search-query']=value;
          		
          		this.commonApi(this.paramsObj);
        		}
      	},
		},

		methods: {

			updateList() {

				this.pagination.currentPage = 1;

				this.pageChanged();
			},

			getKbStatus() {

				axios.get('kb/settings/getvalue').then(({data})=>{
				
				this.status = data.data.kbsettings.status;

				}).catch(res=>{
					
					this.status = 0;
				})
			},

			commonApi(obj) {
				
				var params = obj;
				
				axios.get('/get-articles',{params}).then(res=>{
					
					this.loading = false;

					this.pageLoader = false;
					
					this.articleList=res.data.message.data;
					
					if(res.data.message.total == 0){
						
						this.show =true;
					}
					
					this.records = res.data.message.total;
					
					this.perpage = res.data.message.per_page;
					
					this.length = res.data.message.total;

				}).catch(err=>{
               
               this.loading = false;

					this.pageLoader = false;

               errorHandler(err,'ArticlesIndex')
            })
			},

			//per page change function
			handlePageCount(payload){
				
				this.pageLoader = true;

				this.pagination.currentPage = 1;
				
				this.paramsObj['pagination']=payload;
				
				setTimeout(()=>{
	         
	            this.commonApi(this.paramsObj);
				},100)
			},

			//sorting
        	
        	sorting(payload){
	      	
				this.pageLoader = true;

	        	this.paramsObj['sort-by']=payload.value;
	      
	        	this.commonApi(this.paramsObj);
	    	},

	    	// refresh 
	    	refresh() {
	    		
	    		this.pagination.currentPage = 1;
	    	},
	      
	      // for page changing
			pageChanged() {
				
				this.pageLoader = true;

				var elmnt = document.getElementById('kb-articles');
				
				elmnt.scrollIntoView({ behavior : "smooth"});

				this.paramsObj['page']=this.pagination.currentPage;
				
				this.commonApi(this.paramsObj);
			},
		},

		components : {

			'articles-box': require('components/Agent/kb/article/ReusableComponents/indexBox'),

			'kb-sorting': require('components/Agent/kb/common/kbSorting'),

			'data-per-page': require('components/Agent/kb/common/dataPerPage'),

			'alert': require("components/MiniComponent/Alert"),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			"custom-loader": require("components/MiniComponent/Loader"),
		}
	};
</script>

<style scoped>
	#search {
		width: 78%;display:inline;
	}
</style>