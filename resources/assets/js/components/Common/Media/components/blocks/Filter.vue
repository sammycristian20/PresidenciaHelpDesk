<template>

	<div>
		<date-picker v-model="value2" type="month" placeholder="Select month"></date-picker>

	</div>

</template>

<script>




export default {

  name: 'Filter',

  data() {
    return {
    	value2: null
    };
  },

  components: {
  	"date-picker": DatePicker,
  },

  computed: {
    dateFilter() {
      	return this.$store.state.fm.settings.dateFilter;
    },

  //   typeFilter() {
		// return this.$store.state.fm.settings.typeFilter;
  //   }
  },
};
</script>

<style lang="css" scoped>
</style>
