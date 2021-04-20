<template>
	
	<div class="card card-light card-outline">
				
		<template v-if="loading || !orgData">
				
			<div class="row">

				<loader :animation-duration="4000" :size="40"/>
			</div>
		</template>

		<template v-if="orgData && !loading">

			<div class="card-body box-profile">
				
				<div class="text-center">
						
					<faveo-image-element id="org_img" :source-url="orgImage" :classes="['profile-user-img img-fluid img-circle']" 
						alternative-text="Org Image"/>

				</div>

				<h3 class="profile-username text-center" v-tooltip="orgData.name">{{subString(orgData.name,20)}}</h3>

				<p class="text-muted text-center">{{trans('organization')}}</p>

				<a :href="basePath()+'/organizations/'+orgData.id+'/edit'" class="btn btn-primary btn-block" v-tooltip="trans('edit')">

					<i class="fas fa-edit"> </i> {{trans('edit')}}
				</a>

				<ul class="list-group list-group-unbordered mb-3">
						
					<li class="list-group-item">
							 
						<label>{{trans('domain')}}</label> 

						<a class="float-right"  v-tooltip="orgData.domain">{{subString(orgData.domain ? orgData.domain : '---',20)}}</a>
					</li>	

					<li class="list-group-item">
						
						<label>{{trans('address')}}</label>
							
						<a class="float-right"  v-tooltip="orgData.address">{{subString(orgData.address ? orgData.address : '---',30)}}</a>
					</li>

					<li class="list-group-item">
						
						<label>{{trans('phone')}}</label>
							
						<a class="float-right"  v-tooltip="orgData.phone">{{subString(orgData.phone ? orgData.phone : '---',20)}}</a>
					</li>

					<li class="list-group-item">
						
						<label>{{trans('description')}}</label>
							
						<a class="float-right"  v-tooltip="orgData.internal_notes">
							
							{{subString(orgData.internal_notes ? orgData.internal_notes : '---',30)}}
						</a>
					</li>

					<li class="list-group-item" v-for="value in orgData.custom_field_values">
						
						<label>{{value.label}}</label>
							
						<a class="float-right"  v-if="Array.isArray(value.value)" v-tooltip="value.value.toString()">

							&nbsp;{{value.value.toString()}}
						</a>

						<a v-else class="float-right" v-tooltip="value.value"> {{subString(value.value,30)}}</a>
					</li>
				</ul>

				<a href="javascript:;" class="btn btn-primary btn-block" @click="showModal = true">

					<i class="fas fa-plus"> </i> {{trans('add_manager')}}
				</a>
			</div>		
		</template>

		<transition name="modal">

			<org-manager-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :orgId="orgData.id"
			:managerList="managerList">

			</org-manager-modal>
		</transition>
	</div>
</template>

<script>
	
	import { getSubStringValue } from 'helpers/extraLogics';

	import { mapGetters } from 'vuex'

	export default{

		name : '',

		description : '',

		props : {

			orgData : { type : String|Number, default : ''},

			managerList : { type : Array, default : ()=>[] },
		},

		data(){

			return {

				showModal : false,

				loading : false,

				orgImage : '',
			}
		},

		beforeMount(){

			this.orgImage = this.orgData.logo ? this.orgData.logo : 
											this.basePath()+'/themes/default/common/images/org.png'; 
		},

		computed : {

			...mapGetters(['formattedTime','formattedDate'])
		},

		methods : {

			subString(name,length = 15){
			
				return getSubStringValue(name,length)
			},

			onClose(){

				this.showModal = false;

				this.$store.dispatch('unsetValidationError');
			},
		},

		components : {

			'org-manager-modal' : require('./MiniComponents/OrgManagerModal.vue'),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'faveo-image-element': require('components/Common/FaveoImageElement')
		}
	};
</script>

<style scoped>
	#org_img { height : 100px !important; }
</style>