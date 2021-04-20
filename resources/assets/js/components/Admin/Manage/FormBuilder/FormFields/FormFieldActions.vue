<template>
  <div title="">
    <button class="btn btn-default" v-tooltip="lang('edit')" @click="onEditFormField(fieldData)">
      <i class="fas fa-pencil-alt" aria-hidden="true"></i>
    </button>
    <span class="dropdown" v-if="canAddChild()">
      <button class="btn btn-default" v-tooltip="lang('add_child')" data-toggle="dropdown"><i class="fas fa-plus" aria-hidden="true"></i></button>
      <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
        <li v-for="(item, index) in menuItems" :key="item.id" @click="onChildClick(index, selectedOption)" class="nav-item">
          <a class="nav-link text-dark" href="javascript:void(0);">{{ item.title }}</a>
        </li>
      </ul>
    </span>
    <button class="btn btn-danger" v-tooltip="lang('delete')" v-if="fieldData.is_deletable" @click="removeElementAt()">
      <i class="fas fa-times" aria-hidden="true"></i>
    </button>
  </div>
</template>

<script>
import { boolean } from 'helpers/extraLogics';
import { mapGetters } from 'vuex';

export default {
  props: {

    // Field data object
    fieldData: {
      type: Object,
      required: true
    },

    // Function to execute for editing field properties
    onEditFormField: {
      type: Function,
      required: true
    },

    // Function to add new node element
    onChildClick: {
      type: Function,
      required: false
    },

    // `true` if a node is not a root node
    selectedOption: {
      type: String | Object,
      required: false
    }
  },
  data: () => {
    return {
    }
  },

  computed: {
    ...mapGetters({menuItems:'getFormMenus',categoryType:'getFormCategoryType'})
  },

  methods: {
    // emit event to remove element from array, `reference_id` AND `reference_type` is used for form groups
    removeElementAt() {
      window.eventHub.$emit('onRemoveElement', this.fieldData, this.fieldData.reference_id, this.fieldData.reference_type);
    },

    canAddChild() {

      if(this.categoryType != 'ticket' && this.categoryType != 'user' && this.fieldData.label == 'Department') return false;

      return typeof this.selectedOption !== 'string' && boolean(this.selectedOption);
    }
  }
};
</script>

<style lang="css" scoped>
.fb-movable-button {
  cursor: move;
}
.dropdown-menu {
  left: -125px;
  top: -125px;
}
</style>