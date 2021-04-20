<template>
    <div class="fm-content d-flex flex-column">
        <disk-list v-bind:manager="manager"/>
<!--        <breadcrumb v-bind:manager="manager"/>-->
        <div class="fm-content-body">
            <table-view v-if="viewType === 'table'" v-bind:manager="manager" :key="randomKey"/>
            <grid-view v-else v-bind:manager="manager" :key="randomKey"/>
            <div style="height: 1px;" v-if="files.length" v-observe-visibility="handleScrollToBottom"></div>
        </div>
    </div>
</template>

<script>
// Components
import DiskList from './DiskList.vue';
import Breadcrumb from './Breadcrumb.vue';
import TableView from './TableView.vue';
import GridView from './GridView.vue';
import GET from "../../http/get";
import managerHelper from './mixins/manager';
import EventBus from "../../eventBus"

export default {
  name: 'Manager',
  mixins: [managerHelper],
  components: {
    DiskList,
    Breadcrumb,
    TableView,
    GridView,
  },
  props: {
    manager: { type: String, required: true },
  },

  data() {
    return {
      page: 1,
      lastPage: 1
    }
  },

  mounted() {
      EventBus.$on('refresh-file-list-fm', () => {
        this.fetch(true);
      })
    this.fetch(true);
  },

  destroyed() {
    EventBus.$off(['refresh-file-list-fm']);
  },

  computed: {
    /**
     * view type - grid or table
     * @returns {default.computed.viewType|(function())|string}
     */
    viewType() {
      return this.$store.state.fm[this.manager].viewType;
    },

    activeManager() {
      return this.$store.state.fm.activeManager;
    },

    randomKey() {
      let epochData = new Date().getTime();
      let random = Math.random() * ((100-10) + 10);
      return epochData * random;
    },

    loading() {
      return this.$store.state.fm.messages.loading;
    },
  },

  methods: {
    makeFilesArrayEmpty() {
      this.$store.commit(`fm/${this.manager}/resetFiles`);
      this.$store.dispatch('fm/resetStateMinimal');
    },

    fetch(makeEmpty = false) {
      if (makeEmpty) {
        this.makeFilesArrayEmpty();
        this.page = this.lastPage = 1;
      }

      let disk = this.selectedDisk;

      // let manager = this.activeManager;
      let path = null

      GET.content(disk, path, this.page).then((response) => {
        let data = response.data.data;
        if (data.length) {
          // this.fileList.push(...data);
          this.$store.commit(`fm/${this.manager}/setDirectoryContent`, data);
          this.lastPage = response.data.last_page;
        }
        // else {
        //   if (this.page < this.lastPage) {
        //     this.handleScrollToBottom(true);
        //   }
        // }
      });
    },

    handleScrollToBottom(isVisible) {
      if (!isVisible) { return }
      if (this.page >= this.lastPage) { return }
      this.page++;
      this.fetch();
    }
  }
};
</script>

<style lang="scss">
    .fm-content {
        height: 100%;
        padding-left: 1rem;

        .fm-content-body {
            overflow: auto;
        }
    }
</style>
