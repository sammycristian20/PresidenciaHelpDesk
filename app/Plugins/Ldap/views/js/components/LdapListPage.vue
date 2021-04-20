<template>
   <faveo-box :title="trans('ldap_list')">

      <alert componentName="ldap-list-page" />

			<loader :duration="4000" v-if="isLoading" />

      <div class="card-tools d-flex mt-1" slot="headerMenu">

         <a class="btn btn-tool" :href="basePath() + '/ldap/settings/create'" v-tooltip="trans('configure_new_ldap')">
         	<i class="fas fa-plus" aria-hidden="true"></i>
         </a>

         <span class="btn-tool" v-tooltip="trans('hide_default_login')" >
         	<input class="hide-checkbox"  type="checkbox" v-model="hideDefaultLogin" @change="updateHideDefaultLogin()">
         </span>

         <!-- <div class="input-group">
				<span class="input-group-addon">
					<input type="checkbox" v-model="hideDefaultLogin" @change="updateHideDefaultLogin()">
				</span>
				<input type="text" class="form-control" :value="trans('hide_default_login')" disabled="true">
			</div> -->
      </div>

      <div class="list-group">
         <div class="list-group-item" v-for="list in adList" :key="list.id">
            <div class="row">
               <div class="col-md-2"  v-tooltip="trans(list.schema)">
                  <div class="ldap-schema-img">
                     <img :src="list.image_url" :alt="list.schema" class="w-100">
                  </div>
               </div>
               <div class="col-md-2 description-block data-block" v-for="key in dataToDisplay" :key="key">
                  <h5 class="description-header">{{trans(key)}}</h5>
                  <span class="description-text">{{list[key] ? list[key] : '---'}}</span>
               </div>
               <div class="col-md-2 description-block action-block float-right">
                  <a class="btn btn-primary btn-sm" :href="basePath() + '/ldap/settings/' + list.id + '/edit'"><i class="fas fa-edit" aria-hidden="true"  v-tooltip="trans('edit')"></i></a>
                  <button class="btn btn-danger btn-sm" @click="deleteItem(list.id)"><i class="fas fa-trash" aria-hidden="true"  v-tooltip="trans('delete')"></i></button>
               </div>
            </div>
         </div>
      </div>

   </faveo-box>
</template>

<script>

import axios from 'axios';
import {
	successHandler,
	errorHandler
} from 'helpers/responseHandler';
import FaveoBox from 'components/MiniComponent/FaveoBox';

export default {

	name: 'ldap-list-page',

	data: function () {
		return {
			dataToDisplay: ['domain', 'port', 'encryption', 'username'],
			adList: [],
			hideDefaultLogin: false,
			isLoading: false
		}
	},

	beforeMount() {
		this.getLdapList();
	},

	methods: {

		getLdapList() {
			this.isLoading = true;
			axios.get('api/ldap/settings')
				.then(response => {
					this.adList = response.data.data.ldap_list;
					this.hideDefaultLogin = response.data.data.hide_default_login;
				})
				.catch(error => {
					errorHandler(error, 'ldap-list-page')
				})
				.finally(() => {
					this.isLoading = false;
				})
		},
		deleteItem(ldapId) {
			let isConfirmed = confirm('Are you sure you want to delete?');
			if (isConfirmed) {
				this.isLoading = true;
				axios.delete('api/ldap/settings/' + ldapId)
					.then(response => {
						successHandler(response, 'ldap-list-page');
						this.getLdapList();
					})
					.catch(error => {
						errorHandler(error, 'ldap-list-page');
					})
					.finally(() => {
						this.isLoading = false;
					})
			}
		},

		updateHideDefaultLogin() {
			this.isLoading = true;
			const params = {
				hide_default_login: this.hideDefaultLogin
			};
			axios.post('api/ldap/hide-default-login', params)
				.then(response => {
          successHandler(response, 'ldap-list-page');
				})
				.catch(error => {
					errorHandler(error, 'ldap-list-page');
				})
				.finally(() => {
					this.isLoading = false;
				})
		}
	},

	components: {
		'faveo-box': FaveoBox,
		'alert': require('components/MiniComponent/Alert'),
		'loader': require('components/MiniComponent/Loader')
	}


}
</script>

<style scoped>
.list-group-item {
    border: none;
    border-top: 2px solid #FAFAFA;
}
.list-group-item:first-child {
    border-top: none;
}
.list-group-item:nth-child(odd) {
    background: #FAFAFA;
}
.list-group-item:nth-child(even) {
    background: #FFFFFF;
}
.data-block {
    padding-top: 1.5rem;
}
.action-block {
    padding-top: 2.5rem;
}
.list-group-item:hover {
    background: #F5F5F5;
}
.list-group-item-heading {
    padding-top: .5rem;
}
.description-block>.description-text {
    text-transform: inherit;
}
.hide-checkbox{ height : 25px;}

.w-100 { width : 100% !important; }
</style>