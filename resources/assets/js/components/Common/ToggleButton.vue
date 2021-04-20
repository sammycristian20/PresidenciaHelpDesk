<style scoped>
</style>
<template>
	 <input type="checkbox" name="my-checkbox" v-model="modelValue" checked :data-size="size" :data-on-color="onColor" :data-off-color="offColor" :data-on-text="onText" :data-off-text="offText">
</template>
<script>
export default {
  props: ["size", "onColor", "offColor", "onText", "offText"],
  data() {
    return {
      modelValue: ""
    };
  }
};
</script>