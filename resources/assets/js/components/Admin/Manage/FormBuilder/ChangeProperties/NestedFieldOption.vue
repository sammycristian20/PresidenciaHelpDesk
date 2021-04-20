<template>
  <div class="option-box">
    <span @dblclick="onDoubleClick()" v-show="!isEditMode">{{value}}</span>
    <input :ref="'editWorkplace_' + optionIndex" v-show="isEditMode" v-model.trim="editedValue" @keyup.enter="updateValue()">
    <i class="fa fa-times fa__fb" id="delete-option" aria-hidden="true" @click="isEditMode ? onCancelEdit() : onDelete()" :title="isEditMode ? lang('cancel') : lang('delete')"></i>
  </div>
</template>

<script>
export default {
  name: '',

  props: {
    /**
     * Options array index value
     * Used to update/delete options
     */
    optionIndex: {
      type: Number,
      required: true
    },

    /**
     * Label value
     */
    value: {
      type: String,
      required: true
    }
  },

  data: () => {
    return {
      isEditMode: false,
      editedValue: ''
    }
  },

  beforeMount() {
    this.editedValue = this.value;
  },

  methods: {

    onDoubleClick() {
      this.isEditMode = true;
      this.$nextTick(() => {
        this.$refs['editWorkplace_' + this.optionIndex].focus();
      });
    },

    onDelete() {
      this.$emit('on-delete', this.optionIndex)
    },

    onCancelEdit() {
      this.isEditMode = false;
    },

    updateValue() {
			this.isEditMode = false;
      if(this.editedValue === '') {
        this.editedValue = this.value;
      }
			this.$emit('on-update', { newValue: this.editedValue, index: this.optionIndex });
		}
  }
}
</script>

<style scoped>
.option-box {
	cursor: move;
	background-color: #dedede;
	padding: 0.2rem 0.5rem 0.2rem 0.5rem;
	border: 1px solid #dedede;
	margin: 0.5rem 0.5rem 0 0.1rem;
	border-radius: .4rem;
}
.fa__fb {
	color: darkgrey;
	cursor: pointer;
}
</style>