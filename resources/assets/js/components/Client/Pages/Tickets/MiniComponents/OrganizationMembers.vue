<template>
	
		<div>
		
			<transition name="modal">
			 
			 <deactivate-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :layout="layout" 
			 	:deleteUrl="'/org/delete/user/'+deleteId">

			 </deactivate-modal>
			</transition>

			<div v-if="!loading" id="userlisttable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
				
					<div class="row">
						
						<div class="col-sm-6" v-bind:class="{float1: lang_locale == 'ar'}">
						
							<div class="dataTables_length" id="userlisttable_length">
						
								<select-box :perpage="page" v-on:count="handleCount" :layout="layout"/>
							</div>
						</div>
						
						<div class="col-sm-6">
						
							<div class="dataTables_filter pull-right" v-bind:class="{left: lang_locale == 'ar'}">
								<label id="th">{{ lang('search') }} : 

									<input class="form-control input-sm" type="text" v-model="search" name="search"  @keyup="refresh" :style="inputStyle">
								</label>
							</div>
						</div>
					</div>

					<div class="row">
					
						<div class="col-sm-12">
					
							<table class="table table-hover table-striped dataTable no-footer" id="">

								<thead>
								
									<tr role="row">
										
										<th v-for="thead in theads" id="th" :class="[{align1: lang_locale == 'ar'}]" rowspan="1" colspan="1" 
											
											@click="sort(thead.sort)">{{ lang(thead.title)}} <i v-if="thead.sort" :class="sortclass"></i>
										</th>
									</tr>
								</thead>
								
								<tbody>
								
									<tr v-for="user in filteredUsers" role="row" class="odd">
										
										<td v-if="user.first_name && user.last_name !='Undefined'" class="sorting_1" >
										
											<span :title="user.first_name +' '+ user.last_name">
										
												{{subString(user.first_name +' '+user.last_name )}}
											</span>
										</td>

										<td v-else class="sorting_1" :title="user.user_name">{{subString(user.user_name)}}</td>
									
										<td :title="user.email">{{subString(user.email !== 'Not available' ? user.email : '--')}}</td>
									
										<td :title="user.mobile">{{subString(user.mobile !== 'Not available' ? user.mobile : '--')}}</td>
									
										<td>
											<span v-if="user.active === 1" class="label label-success">{{lang('active')}}</span>
											<span v-if="user.active === 0" class="label label-danger">{{lang('inactive')}}</span>
										</td>
									
										<td>
											<span v-if="user.ban === 1" class="label label-success">{{lang('yes')}}</span>
											<span v-if="user.ban === 0" class="label label-danger">{{lang('no')}}</span>
										</td>
									
										<td :title="formattedTime(user.updated_at)">{{subString(formattedTime(user.updated_at))}}</td>
									
										<td>
											
											<router-link :to="{ name:'EditUser', params:{id:user.id} }">

												<button :class="{float1: lang_locale == 'ar'}" class="btn btn-primary btn-xs" title="Edit"> 
													<i class="fa fa-edit"> </i>
												</button>
											</router-link>
											<a v-if="auth.user_data.id !== user.id" :class="{left: lang_locale == 'ar'}">
												<button class="btn btn-danger btn-xs" @click="getDeleteId(user.id)" title="Deactivate" 
													:disabled="user.active==0">
													<i class="fas fa-trash"></i>
												</button>
											</a>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-12" :class="{rem: lang_locale == 'ar'}" style="text-align:right">
							<uib-pagination :total-items="Records" v-model="pagination" :max-size="maxSize" class="pagination" :boundary-links="true" :items-per-page="PerPage" ></uib-pagination>
						</div>
					</div>
				</div>

			<div v-if="loading">
			
				<loader :color="layout.portal.client_header_color" :animation-duration="4000" :size="60"/>
			</div>
		</div>
</template>
<script>
	
	import { mapGetters } from 'vuex'
	
	import axios from 'axios'

	import { getSubStringValue } from 'helpers/extraLogics';

	export default {
		
		name : 'org-members',

		description : 'Organization members list component',

		props : {

			members : { type : Array, default : ()=>{}},

			org_id : { type : String | Number, default : ''},

			layout : { type : Object, default : ()=>{}},

			auth : { type : Object, default : ()=>{}},
		},
		
		data() {
		
			return {
				
				loading : false,

				showModal : false,
				
				message:'',

				Members:this.members,
				
				OrgId:this.org_id,
				
				lang_locale : this.layout.language,

				records : this.members.length,
				
				page :10,
				
				pagination: { currentPage: 1 },
				
				maxSize:7,
				
				sortclass : "fa fa-sort",

				sortKey: ['user_name'],
				
				sortOrder: ['asc'],
				
				search:'',
				
				inputStyle: {
					
					borderColor : this.layout.portal.client_input_field_color
				},
				deleteId:'',

				theads : [ 
					{ sort : 'user_name', title : 'name'},
					{ sort : 'email', title : 'email'},
					{ sort : 'mobile', title : 'phone'},
					{ title : 'status'}, { title : 'ban'},
					{ sort : 'updated_at', title : 'last_activity'},
					{ title : 'action'},
				]
			}
		},

		computed: {
			
			usersSorted(){
			
				return _.orderBy(this.Members, this.sortKey, this.sortOrder);
			},
			
			filteredUsers(){

				return this.Members.filter((users)=>{
						
					return users.user_name.toLowerCase().match(this.search) || users.email.toLowerCase().match(this.search) || users.mobile.toLowerCase().match(this.search) || users.updated_at.toLowerCase().match(this.search) 
				 
				}).filter((row, index) => {
					
					let start = (this.pagination.currentPage-1)*this.page;
					
					let end = this.pagination.currentPage*this.page;
					
					if(index >= start && index < end) return true;
				}) 
			},

			PerPage() {
				
				return this.page?parseInt(this.page):25;
			},

			Records() {
				
				return this.records?parseInt(this.records):0;
			},

			...mapGetters(['formattedTime','formattedDate'])
		},

		methods:{

			handleCount(payload){
				
				this.pagination= { currentPage: 1 };
				
				this.page=payload;
			},
			
			sort(key) {
				
				if (key == this.sortKey) {
					
					this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
					
					this.sortclass = this.sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc';
					
					this.Members = this.usersSorted;

				} else {
					
					this.sortKey = key;
					
					this.sortOrder = 'asc';
					
					this.sortclass = this.sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc';
					
					this.Members=this.usersSorted;
				}
			},

			 refresh(){
					
				if(this.search==''){
				
					this.pagination= { currentPage: 1 };
					
					this.records =this.Members.length;
				} else {
					
					this.pagination= { currentPage: 1 };
					
					this.records =this.filteredUsers.length;
				}
			},
			
			getDeleteId(x){
				
				this.showModal = true;

				this.deleteId=x;
			},

			onClose(){

				this.showModal = false;

				this.getData()
			},

			getData(){

				this.$Progress.start();

				this.loading = true;

				axios.get('organizations/data/'+this.deleteId+'/'+this.OrgId).then(res=>{
						
						this.Members = res.data.data.orgmembers;
						
						this.loading=false;

						this.$Progress.finish();

					}).catch(error=>{

						this.loading = false

						this.$Progress.fail();
					});
			},

			subString(value){
				
				return getSubStringValue(value,10)
			}
		},

		components:{
		
			'deactivate-modal' : require('./DeactivateModal'),
		
			'select-box': require('./SelectBox'),

			'client-panel-loader' : require('components/Client/ClientPanelLayoutComponents/ReusableComponents/Loader.vue'),
		}
	};
</script>