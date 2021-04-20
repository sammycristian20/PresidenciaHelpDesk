<template>
  <div class="main-div__formbuilder">

    <draggable-element v-bind="dragOptions" tag="div" class="item-container" :list="list" :value="value" @start="isDragInProgress = true" @end="isDragInProgress = false" @input="dragChangeEventEmitter" :empty-insert-threshold="230" :disabled="isDisableDraging">

      <transition-group type="transition" :name="!isDragInProgress ? 'flip-list' : null">
        <div class="item-group form-field-element" :title="lang('move')" v-for="field in originalValue" :key="field.groupid || field.id">

          <!-- text/number/date/email/file/textarea/htmltextarea -->
          <FBSimpleFields v-if="isSimpleFields(field) || field.title === 'Api'" :field-data="field" :on-edit-form-field="onShowSettingModal" :isChild="isChild"/>

          <!-- select -->
          <FBSelect v-if="field.title !== 'Api' && (field.type === 'api' || field.type === 'select' || field.type === 'select2' || field.type === 'multiselect')" :field-data="field" :isDisabled="isDisableSelectBox(field)" :on-edit-form-field="onShowSettingModal" :on-child-click="onChildClick" :isChild="isChild"/>


          <!-- checkbox -->
          <FBCheckbox v-if="field.type=='checkbox'" :field-data="field" :on-edit-form-field="onShowSettingModal" :on-child-click="onChildClick" :isChild="isChild"/>

          <!-- radio -->
          <FBRadio v-if="field.type=='radio'" :field-data="field" :on-edit-form-field="onShowSettingModal" :on-child-click="onChildClick" :isChild="isChild"/>

          <!-- Form group -->
          <form-group-field v-if="field.type=='group'" :field-data="field" :isChild="isChild"/>

          <!-- Custom form type -->
          <FBCustom v-if="field.type=='custom'" :field-data="field" :on-edit-form-field="onShowSettingModal" :isChild="isChild"/>

        </div>
      </transition-group>

    </draggable-element>

    <transition name="modal">
      <form-builder-setting-modal title="settings" v-if="showSettingModal" :onCloseSettingModal="onCloseSettingModal" :isShowSettingModal="showSettingModal" :field-data="selectedFormFieldObject" />
    </transition>

  </div>
</template>

<script>

import draggable from 'vuedraggable';
import { mapGetters } from 'vuex';

export default {
  name: 'draggable-form-field-item',

  components: {
    'FBSimpleFields': require('./FormFields/FBSimpleFields'),
    'FBSelect': require('./FormFields/FBSelect'),
    'FBCheckbox': require('./FormFields/FBCheckbox'),
    'FBRadio': require('./FormFields/FBRadio'),
    'form-group-field': require('./FormFields/FormGroupField'),
    'FBCustom': require('./FormFields/FBCustom'),
    'form-builder-setting-modal': require('./ChangeProperties/FormBuilderSettingModal'),
    'draggable-element': draggable
  },

  props: {

    // v-model binded value
    value: {
      type: Array,
      default: null
    },

    // list elements
    list: {
      type: Array,
      default: null
    },

    // `true` if a node is not a root node
    isChild: {
      type: Boolean,
      default: () => false
    }
  },

  data: function () {
    return {

      isDragInProgress: false,

      menuItems: null, // Possible form fields type

      selectedFormFieldObject: null,

      showSettingModal: false, // Falg to show/hide stting modal,

      // drag config options
      dragOptions: {
        animation: 200,
        group: 'faveo-form-builder',
        disabled: false,
        ghostClass: 'ghost'
      }
    }
  },

  beforeMount() {
    this.menuItems = this.$store.getters.getFormMenus;
  },

  created() {
    window.eventHub.$on('formFieldSettingsApplied', this.onCloseSettingModal);
  },

  computed: {

    originalValue() {
      return this.value ? this.value : this.list;
    },

    ...mapGetters({isDisableDraging: 'isDisableDraging'})
  },
  methods: {

    // Drag change event emitter, listener is in draggable plugin
    dragChangeEventEmitter(value) {
      this.$emit('input', value);
    },

    isSimpleFields(field) {
      const simpleFields = ['text', 'number', 'date', 'email', 'file', 'textarea', 'htmltextarea', 'taggable'];
      return simpleFields.indexOf(field.type) > -1;
    },

    // returns true, to disable dropdown if default field OR some spacial case
    isDisableSelectBox(field) {
      return field.default === 1 && field.title !=='Help Topic' && field.title !== 'Department';
    },

    // Add new node element for nested fields
    onChildClick(childElementIndex, selectedOption) {
      let clonedMenuItem = JSON.parse(JSON.stringify(this.menuItems));
      const currentTimestamp = this.getCurrentTimestamp();
      clonedMenuItem[childElementIndex].form_identifier = currentTimestamp;
      clonedMenuItem[childElementIndex].id = currentTimestamp;
      if(Array.isArray(selectedOption)) {
        for (let i = 0; i < selectedOption.length; i++) {
          selectedOption[i].nodes.push(clonedMenuItem[childElementIndex]);
        }
      } else {
        selectedOption.nodes.push(clonedMenuItem[childElementIndex]);
      }
    },


    onShowSettingModal(data) {
      this.selectedFormFieldObject = data;
      this.$store.commit('updateIsDisableDraging', true);
      this.showSettingModal = true;
    },

    onCloseSettingModal() {
      this.$store.commit('updateIsDisableDraging', false);
			this.showSettingModal = false;
    },

    getCurrentTimestamp() {
      return new Date().getTime();
    },
  
  },

};
</script>

<style scoped>
.item-container {
  background-color: #fff
}
.dropdown-menu {
  left: -125px;
  top: -125px;
}
.item {
  margin: 0.3rem;
  padding: 1rem;
  border: solid #dedddd 1px;
}
.form-field-element {
  cursor: move;
  margin-bottom: 0.1rem;
  padding: 1rem 0 0.5rem 0.5rem;
}
.form-field-label > input {
  padding: 5px;
  border: 1px dashed #fff;
  text-align: right;
}
.form-field-element:hover {
  box-shadow: 0 -1px 1px -1px rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 1px 3px 0 rgba(0,0,0,.12);
}
.item-sub {
  margin: 0 0 0 1rem;
}
.space-filler {
  flex: 1 1 auto;
}
.form-field-actions-btn {
  visibility: hidden;
  margin-right: 1rem;
}
.form-field:hover .form-field-actions-btn{
  visibility: visible;
}
.fb__icon {
  cursor: pointer;
  color: #545454;
}
</style>

<style lang="css">
.form-field:hover .form-field-actions-btn{
  visibility: visible;
}
</style>
