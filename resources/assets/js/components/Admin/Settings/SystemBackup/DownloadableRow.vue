<template>
  <div id="custom_row__dr">
    <label>{{ data[column] }}</label>
    <a :href="downloadLink()" target="_blank">
      <i id="icon__dr" class="fa fa-download pull-right" aria-hidden="true" title="Download"></i>
    </a>
  </div>
</template>

<script>

export default {
  name: 'downloadable-row',

  props: {
    /**
     * Individual row object 
     */
    data: {
      type: Object,
      required: true
    },
    /**
     * Array index of the row element
     */
    index: {
      type: Number,
      required: true
    },
    /**
     * Column name
     */
    column: {
      type: String,
      required: true
    }
  },

  data: () => {
    return {
      baseUrl: window.axios.defaults.baseURL
    }
  },

  methods: {
    /**
     * Return href to download the backup file
     */
    downloadLink() {
      const type = this.column === 'filename' ? 'filesystem' : 'database';
      return this.baseUrl + '/api/download-backup/' + this.data.id + '/' + type;
    }
  }
}
</script>

<style lang="css" scoped>
#icon__dr {
  cursor: pointer;
  visibility: hidden;
  padding-top: 7px;
}
#custom_row__dr:hover #icon__dr {
  visibility: visible;
}
</style>