<template>
  <div class="row">
    <dynamic-select
      v-for="item in componentMetaData"
      :key="item.key"
      :id="item.key"
      :name="item.key"
      :apiEndpoint="item.apiEndpoint"
      :elements="item.elements"
      :multiple="item.multiple"
      :label="trans(item.label)"
      :value="item.value"
      :onChange="onChange"
      :classname="getClassname"
      :strlength="17"
    >
    </dynamic-select>
  </div>
</template>

<script>

  export default {
    name : 'filter-dropdown',

    components: {
      'dynamic-select': require('components/MiniComponent/FormField/DynamicSelect'),
    },

    props: {
      metaData: { type: Array, required: true },
    },

    data () {
      return {
        componentMetaData: this.metaData,
        selectedFilterObj: {}
      }
    },

    methods: {

      onChange (value, name) {
        this.selectedFilterObj[name] = value
        this.$emit('onChangeFilter', this.selectedFilterObj)
      },

    },

    computed: {

      getClassname: function () {
        const dropdownCount = this.componentMetaData.length
        let classname = 'col-md-12'
        if (dropdownCount === 2) {
          classname = 'col-md-6'
        } else if (dropdownCount > 2) {
          classname = 'col-md-4'
        }
        return classname;
      }
    },

};
</script>

<style scoped>
</style>