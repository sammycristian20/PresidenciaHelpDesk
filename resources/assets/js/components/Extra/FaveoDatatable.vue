<template>
	<div class="faveo-datatable">	

		<!-- selects pagination -->
		<div class="select-pagination">
			<select v-model="pagination">
				<option value="10">10</option>
				<option value="25">25</option>
				<option value="50">50</option>
				<option value="100">100</option>
			</select>
			<label>{{lang('Records per page')}}</label>
		</div>

		<!-- search box  -->
		<div class="search">
			<label>{{lang('search')}}:</label>
			<input v-model="searchQuery" type="search">
		</div>
		
		<!-- Data part of the table -->
		<div class="table datatable">
			<thead>
				<tr>
					<th v-for="column in columns">
						<a v-if="column.sortable" @click="sort">
							<span class="fa fa-fw fa-sort" aria-hidden="true"/></a>
						<label v-bind:class="column.title" v-html="column.title">{{column.title}}</label>
						<!-- <a v-if="column.sortable" href="#"><span class="fa fa-fw fa-sort-down" aria-hidden="true"/></a> -->
					</th>
				</tr>			
			</thead>
			<tbody>
				<tr v-for="row in rows">
					<td v-for="column in columns" v-html="row[column.field]" v-bind:class="column.field">
						<a v-if="row.method" v-on:click="" v-on:hover=""></a>
						{{row[column.field]}}
					</td>
				</tr>
			</tbody>
		</div>

		<!-- Page navigation -->
		  <ul class="pagination">
		    <li>
		      <a href="#" aria-label="Previous">
		        <span aria-hidden="true">&laquo;</span>
		      </a>
		    </li>
		    
		    <li v-for="n in noOfPages" v-bind:class="{ active : n == currentPage}">
		    	<a v-if="n < currentPage + 5" href="">{{n}}</a>
		    	<a v-else-if="n > noOfPages - 5" href="">{{n}}</a>
		    </li>

		    <li>
		      <a href="#" aria-label="Next">
		        <span aria-hidden="true">&raquo;</span>
		      </a>
		    </li>
		  </ul>
	</div>
</template>

<script>
	export default 	{
		props:{
			rows:{
				type:Array,
				required:true
			},
			columns:{
				type:Array,
				required:true
			},
			noOfPages:{
				type: Number,
				required:true
			}
		},

		methods:{
			sort(){
				if(this.sort == 'NONE'){
					this.sort = 'DESC'
				}
				if(this.sort == 'DESC'){
					this.sort = 'ASC'
				}
				if(this.sort == 'ASC'){
					this.sort = 'NONE'
				}
			}
		},

		watch: {
			currentPage : function(){
			    this.$emit('update-page-number',this.pageNumber);
			},

			pagination : function(){
      		    this.$emit('update-pagination',this.pagination);
			},

			searchQuery : function(){
			    this.$emit('update-search-query',this.searchQuery);
			}
		},

		data:()=>({
			searchQuery:'',
			pagination:10,
			currentPage:1,
			sort:'NONE',
		})	
	}

</script>

<style>
	.datatable{
		width: 100%;
	}

	.pagination{
		float: right;
	}

	.search{
		right:0;
		top:0;
		position:absolute;
		padding-right:20px;
	}
</style>