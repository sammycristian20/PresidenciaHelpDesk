<template>
  <div>
    <div>
      <label>{{lang('pattern_regex')}}</label>
      <input type="text" class="form-input-box" v-model="formFieldData.pattern" :placeholder="lang('pattern_help_message')">
    </div>
    <div>
      <label>{{lang('validation_message')}}</label>
      <input type="text" class="form-input-box" v-if="!formFieldData.labels_for_validation[0]" :placeholder="lang('regex_with_validation_message')">
      <input type="text" class="form-input-box" v-else v-model="formFieldData.labels_for_validation[0].label">
    </div>
  </div>
</template>

<script>
export default {

  props: {

    // Form field data
    formFieldData: {
      type: Object,
      required: true
    }
  },
  
  data: () => {
    return {
    }
  },
}
</script>

<style lang="css" scoped>
.form-input-box {
	display: block;
  width: 100%;
	height: 34px;
	padding: 6px 12px;
	font-size: 14px;
	line-height: 1.42857143;
	color: #555;
	background-color: #fff;
	background-image: none;
	border: 1px solid #ccc;
	border-radius: 4px;
	-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
	box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
	-webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
	-o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
	transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
}
</style>


