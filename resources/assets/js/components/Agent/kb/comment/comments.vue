<template>

	<div>

		<alert componentName="CommentsIndex"/>

		<div class="card card-light ">
			
			<div class="card-header">
				
				<h3 class="card-title">{{lang('list_of_comments')}}</h3>
			</div>

			<div class="card-body" id="kb-comments">
				
				<div class="row"  v-if="!loading">

					<div class="col-md-4">
						
						<template v-if="records > 0">
							
							<data-per-page :ticketper="perpage" v-on:message="handlePageCount"/>
						
							<kb-sorting name1="Created time" value1="created_at" v-on:sort="sorting"/>
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

					<div class="callout callout-info" :style="callOutStyle">
						
						<span id="call"><p>{{calloutText}}.&nbsp;&nbsp;&nbsp;</p>
							<a id="all" href="javascript:;" @click="seeAll">{{lang('click_to_see_all')}}</a>
						</span>
					</div>
					
					<ul class="nav nav-tabs" role="tablist">
					
						<li v-for="section in tabs" class="nav-item">
							
							<a class="nav-link" :class="{ active: filter === section.id }" data-toggle="pill" role="tab" href="javascript:;" 
								@click="comments(section.id)" id="comment_tab">

								{{lang(section.title)}} <span class="badge bg-yellow"> {{section.count}} </span>
							</a>
						</li>
					</ul>

					<div class="tab-content">
					
						<div class="active tab-pane mt-4">
							
							<template v-if="records > 0">

								<comments-box v-for="comment in comments_List"  :key="comment.id" :comment="comment"
									:updateList="updateData">
									
								</comments-box>
						
								<div class="float-right" v-if="records >  10">
							
									<uib-pagination :total-items="records" v-model="pagination" class="pagination" :boundary-links="true" 
										:items-per-page="perpage" @change="pageChanged()" :rotate="true" :max-size="3" :force-ellipses="true">
									
									</uib-pagination>
								</div>
							</template>

							<div v-if="show && records == 0">
					
								<p class="p-3" style="text-align:center">{{lang('no-data-to-show')}}</p>
							</div>

							<div class="row" v-if="pageLoader">
				
								<custom-loader></custom-loader>
							</div>
						</div>			
					</div>
				</div>

				<div class="row" v-if="loading">
				
					<loader></loader>
				</div>
			</div>
		</div>
	</div>  
</template>
<script>

	import axios from 'axios'
	
	import { mapGetters } from 'vuex'
	
	export default {
		
		data() {
			
			return {
				
				comments_List:[],
				
				paramsObj:{},
				
				length:0,
				
				// pagination
				perpage:10,
				
				pagination:{currentPage: 1},
				
				records:0,
				
				// loader
				loading:true,
				
				// search
				search:'',

				show:false,
				
				tab:'',
				
				calloutText:'',
				
				callOutStyle:{display:'none'},
				
				filter:2,
				
				tabs:[{id:2,title:'All',count:0},{id:1,title:'Approved',count:0},{id:0,title:'Unapproved',count:0}],
				
				commentFilterData:{},
				
				pageLoader : false
			}
		},

		created() {
			
			window.eventHub.$on('updateCommentsList',this.updateData);

			if(this.getCommentFilterData){
				
				if(this.getCommentFilterData.id !== undefined){
					
					this.commentFilterData = this.getCommentFilterData
					
					if(this.commentFilterData.to === 'all'){
						
						this.filter = 2
						
						this.callOutStyle.display = 'block';
					
					} else if(this.commentFilterData.to === 'unapprove') {
						
						this.filter = 0
						
						this.callOutStyle.display = 'block';
					
					} else {
					
						this.filter = 1
					}
				}
			}
		},

		beforeMount(){
		
			if (performance.navigation.type == 1) {
		
				this.seeAll()
		
			} else {
		
				if(this.getCommentFilterData){
		
					if(this.getCommentFilterData.id !== undefined){
		
						this.calloutText = 'Comments of ' + this.getCommentFilterData.name
		
						const data ={ condition: this.getCommentFilterData.to, article_id: this.getCommentFilterData.id, filter_by:this.filter }
						
						this.commonApi(data);
					
					} else {
						
						this.commonApi()
					}
				
				}else {
					
					this.commonApi();
				}
			}
		},

		computed: {
			
			searchFilter : {
			
				get() {
			
					return this.search;
				},
			
				set(value) {
				
					this.pageLoader = true;

					this.search=value;
			
					this.paramsObj['search-option']=value;
			
					if(this.getCommentFilterData){
			
						if(this.getCommentFilterData.id !== undefined){
			
							this.commonFunction('search-option',value)
			
						} else {
			
							this.commonApi(this.paramsObj);
						}
					} else {
						
						this.commonApi(this.paramsObj);
					}
				} 
			},

			...mapGetters(['getCommentFilterData'])
		},

		methods: {
			
			updateData() {

				this.pagination.currentPage = 1;

				this.search = '';

				this.pageLoader = true;

				this.paramsObj = {};

				this.pageLoader = true;

				this.commonApi(this.paramsObj)				
			},

			commonApi(obj) {
				
				var params = obj;
				
				axios.get('/get-comment',{params}).then(res=>{
					
					this.loading = false;

					this.pageLoader = false;
					
					this.comments_List=res.data.message.data;
					
					if( res.data.message.total == 0){
						
						this.show=true;
					}
					
					this.records = res.data.message.total;
					
					this.perpage = res.data.message.per_page;
					
					this.tabs[0].count = res.data.data.all;
					
					this.tabs[1].count = res.data.data.approved;
					
					this.tabs[2].count= res.data.data.pending;
					
					this.length =  res.data.message.total;
				
				}).catch(res=>{

					this.pageLoader = false;

					this.loading = false;
				});
			},

			//per page change function
			handlePageCount(payload){
				
				this.pageLoader = true;

				this.pagination.currentPage = 1;
			
				this.paramsObj['pagination']=payload;
			
				setTimeout(()=>{
			
					if(this.getCommentFilterData){
			
						if(this.getCommentFilterData.id !== undefined){
			
							this.commonFunction('pagination',payload)
			
						} else {
			
							this.commonApi(this.paramsObj);
						}
					} else {

						this.commonApi(this.paramsObj);
					}
				},100)
			},

			//sorting
			sorting(payload){
				
				this.pageLoader = true;

				this.show=false;
			
				this.paramsObj['sort-by']=payload.value;
			
				if(this.getCommentFilterData){
			
					if(this.getCommentFilterData.id !== undefined){
			
						this.commonFunction('sort-by',payload.value)
			
					} else {
			
						this.commonApi(this.paramsObj);
					}
				} else {
					
					this.commonApi(this.paramsObj);
				}
			},

			//search option
			refresh() {
				this.pagination.currentPage = 1;
			},

			// for showing approved onapproved and all comments
			comments(x) {
				
				this.pageLoader = true;

				this.tab =x;
			
				this.paramsObj['filter_by']=x;
			
				this.pagination.currentPage = 1;
			
				this.paramsObj['page']=1;
			
				if(this.getCommentFilterData){
			
					if(this.getCommentFilterData.id !== undefined){
			
						this.commonFunction('filter-by',x)
			
					} else {
			
						this.commonApi(this.paramsObj);
					}
				} else {
					
					this.commonApi(this.paramsObj);
				}	
			},

			// for pagination
			pageChanged() {
				
				this.pageLoader = true;

				var elmnt = document.getElementById('kb-comments');
				
				elmnt.scrollIntoView({ behavior : "smooth"});

				this.paramsObj['page']=this.pagination.currentPage;
				
				let filter = '';
				
				if(this.getCommentFilterData){
				
					if(this.getCommentFilterData.id !== undefined){
				
						this.commonFunction('page',this.pagination.currentPage)
				
					} else {
				
						this.commonApi(this.paramsObj);
					}
				} else {
				 
				  	this.commonApi(this.paramsObj);
				}
			},

			commonFunction(param,value){
			
				if(param === 'filter-by'){
			
					this.filter = value
				}
			
				const data = { condition: this.getCommentFilterData.to, article_id: this.getCommentFilterData.id, filter_by:this.filter}
				
				if(param === 'filter-by'){
					
					data['page']= 1
				
				} else {
				
					data[param]= value
				}
				
				this.commonApi(data);
			},

			seeAll(){
				
				this.pageLoader = true;
				
				this.$store.dispatch('commentFilterData',{});
				
				this.paramsObj['filter_by']=this.filter;
				
				this.pagination.currentPage = 1;
				
				this.paramsObj['page']=1;
				
				this.commonApi(this.paramsObj);
				
				this.callOutStyle.display = 'none';
			}
		},

		components : {

			'comments-box': require('components/Agent/kb/comment/ReusableComponents/commentBox'),

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
#call{
	display:inline-flex;margin-bottom: -10px;
}
#all{
	color:#007bff !important;
}
</style>