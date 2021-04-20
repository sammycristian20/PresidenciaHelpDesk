<template>
  <div class="main-div">
    <faveo-box box-class="card card-light" :title="lang('filter')">
      <div class="row">
        <dynamic-select
          v-for="item in componentMetaData"
          :key="item.name"
          :id="item.name"
          :name="item.name"
          :apiEndpoint="item.url"
          :classname="item.className"
          :elements="item.elements"
          :multiple="item.isMultiple"
          :label="lang(item.label)"
          :value="item.value"
          :onChange="onChange"
          :strlength="item.strlength"
          :clearable="item.value ? true : false">
        </dynamic-select>
      </div>

      <div class="card-footer" slot="actions">
        <button id="apply-btn" class="btn btn-primary" type="button" @click="onApply">
          <span class="fas fa-check"></span>&nbsp; {{ lang('apply')}}
        </button>
          <button id="apply-btn" class="btn btn-primary single-btn" type="button" @click="onReset">
            <span class="fas fa-undo"></span>&nbsp; {{ lang('reset')}}
          </button>
          <button id="apply-btn" class="btn btn-danger single-btn" type="button" @click="onCancel">
            <span class="fas fa-times"></span>&nbsp; {{ lang('cancel')}}
          </button>
      </div>
    </faveo-box>

  </div>
</template>

<script>

import FaveoBox from 'components/MiniComponent/FaveoBox';

    export default {
        name : "advance-filter",

        description : "",

        components: {
        'dynamic-select': require("components/MiniComponent/FormField/DynamicSelect"),
        'faveo-box': FaveoBox,
      },

        props:{
          metaData: { type:Array, required: true },
        },

        methods:{

        /**
         * The function which will be called whaen value of the field changes.
         * @param {value}
         * `value` will be the updated value of the field
         * @param {name}
         * `name` will be thw name of the state in the parent class    
         * @return {Void} 
         */
        onChange(value, name){
            this.selectedFilters[name] = value;
        },

        onCancel() {
          this.$emit('selectedFilters', 'closeEvent');
        },

        onApply() {
          this.$emit('selectedFilters',this.selectedFilters);
        },
        onReset() {
          this.selectedFilters = {};
          this.$emit('selectedFilters', 'resetEvent');
        }
      },

        data(){
            return{

                /**
                 * Values that has been selected
                 * @type {Null|String}
                 */
                selectedFilters: {},

                /**
                 * It closes the field dropdown once a value is selected.
                 * For single-select, it will be true. For multi-select it will be false
                 * @type {Boolean}
                 */
                close_on_select: !this.multiple,

                isShowFilter: false,

                componentMetaData: this.metaData,

            }
        },
};
</script>

<style scoped>
</style>

<docs>
  metaData should be an Array

  e.g;

  [
    { name: 'userType',
      url: '',
      elements: this.userFilterOptions, // The for local(hardcoded) values
      isMultiple: false,
      isPrepopulate: false,
      label: 'user_type',
      value: '',
      className: 'col-xs-4'
    },
    { name: 'departments',
      url: 'api/dependency/departments', // Data from api call 
      elements: [],
      isMultiple: true,
      isPrepopulate: false,
      label: 'departments',
      value: '',
      className: 'col-xs-4'
    }]
</docs>
