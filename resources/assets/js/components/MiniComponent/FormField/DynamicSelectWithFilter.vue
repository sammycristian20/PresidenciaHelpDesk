<template>
  <div class="row">
    <div class="dynamic-select-with-filter-container col-md-12">
      <dynamic-select
        :id="node.unique"
        :name="node.unique"
        :api-endpoint="apiEndpoint"
        :api-parameters="apiParameters"
        :multiple="multiple"
        :value="selectedAssets"
        :required="Boolean(node.required)"
        :label="node.label"
        :onChange="onChange"
        :isInlineForm="true"
        :actionBtn="{ text: 'filter', action: openFilterPopup }">
        <span slot="dsHelpText">
          <small><span v-tooltip="formattedFilterString" v-html="formattedFilterString"></span></small>
        </span>
      </dynamic-select>
    </div>

    <transition name="modal">
      <modal v-if="showFilterPopup" :showModal="showFilterPopup" :onClose="() => showFilterPopup = false"
			:containerStyle="{ width: '800px' }">
      	<div slot="title">
          <h4>{{ trans('filter') }}</h4>
        </div>
        <div slot="fields" class="ds-filter-modal">
          <loader v-if="isLoading" :animation-duration="4000" :size="60" />
          <filter-dropdowns v-if="!isLoading" :metaData="filterOptions" @onChangeFilter="onChangeFilter" />
        </div>
        <span slot="controls">
          <button class="btn btn-primary" @click="applyFilter()">{{ trans('apply') }}</button>
        </span>
      </modal>
    </transition>
  </div>
</template>

<script>
import Modal from 'components/Common/Modal'
import axios from 'axios'
import { MULTIPLE_PROPERTY_HELPER, getFormattedTextAndApiParamsForSelectedFilter } from 'helpers/extraLogics'
import { mapGetters } from 'vuex'

export default {

  name: 'dynamic-select-with-filter',

  props: {
    node: { type: Object, required: true },
    filterApiEndpoint: { type: String, required: true },
    multiple: { type: Boolean, default: false },
    onChange: { type: Function, required: true },
    selectedAssets: { type: Array, required: false},
    clearSelectedValue: { type: Function, required: false},
    changeSelectedAsset: { type: Function, required: false}
  },

  data: () => {
		return {
      showFilterPopup: false,
      isLoading: false,
      filterOptions: [],
      apiParameters: {},
      selectedFilters: {},
      formattedFilterString: ''
		}
  },

  computed: {
    apiEndpoint: function () {
      return MULTIPLE_PROPERTY_HELPER.convertStringOfPropertiesToObject(this.node.api_info).url;
    },

    ...mapGetters(['getReporter'])

  },

  watch: {

    getReporter(newValue,oldValue){

      if( newValue ){
        let usedByIds = []
        usedByIds.push( newValue );
        this.apiParameters = {
          "used_by_ids": usedByIds
        }
        this.$store.dispatch('setSelectedValues', [])
        this.clearSelectedValue();
      } else {
        this.apiParameters ={}
      }
    }
  },

  methods: {

    openFilterPopup() {
      this.showFilterPopup = true

      if (this.filterOptions.length === 0) {
        this.isLoading = true
        axios.get(this.filterApiEndpoint)
          .then((response) => {
            this.filterOptions = response.data.data
          })
          .catch((error) => {
            console.error(error)
          })
          .finally(() => {
            this.isLoading = false
          })
      }
    },

    applyFilter () {
      this.filterOptions.map((element) => element.value = this.selectedFilters[element.key])
      const result = getFormattedTextAndApiParamsForSelectedFilter(this.filterOptions)
      this.formattedFilterString = result.formatedText
      this.apiParameters = result.apiParams
      this.showFilterPopup = false
      this.changeSelectedAsset()
    },

    onChangeFilter (value) {
      this.selectedFilters = { ...this.selectedFilters, ...value }
    },
  },

  components: {
    'modal': Modal,
    'dynamic-select': require('components/MiniComponent/FormField/DynamicSelect'),
    'filter-dropdowns': require('components/Common/Filter/FilterDropdowns'),
    'loader': require('components/Extra/Loader'),
  }

};
</script>

<style>
  .dynamic-select-with-filter-container {
    margin-right: -32px;
    margin-bottom: 1rem;
  }
  .dynamic-select-with-filter-container > .form-group {
    padding-right: 0px;
    margin-bottom: 0px;
  }
  .ds-filter-modal {
    padding-left: 1em;
    padding-right: 1em;
  }
  .filter-btn {
     border-top-left-radius: 0px;
    border-bottom-left-radius: 0px;
    height: 36px;
  }
  .dynamic-select-with-filter-container .faveo-dynamic-select .vs__dropdown-toggle {
    border-top-right-radius: 0px;
    border-bottom-right-radius: 0px;
  }
</style>