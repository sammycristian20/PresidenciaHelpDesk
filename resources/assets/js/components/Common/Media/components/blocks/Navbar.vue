<template>
    <div class="fm-navbar mb-3">
        <div class="row">
            <div class="col-sm-4">
                <div class="btn-group" role="group">

                  <button type="button" class="btn btn-primary"
                            v-on:click.stop="refreshAll()"
                            v-bind:title="lang.btn.refresh">
                        <i class="fas fa-sync-alt"/>
                    </button>
                    <button type="button" class="btn btn-primary"
                            disabled
                            v-if="uploading"
                            v-bind:title="lang.btn.upload">
                        <i class="fas fa-upload"/>
                    </button>
                    <button type="button" class="btn btn-primary"
                            v-else
                            v-on:click="showModal('Upload')"
                            v-bind:title="lang.btn.upload">
                        <i class="fas fa-upload"/>
                    </button>
                    <button type="button" class="btn btn-primary"
                            v-bind:disabled="!isAnyItemSelected"
                            v-on:click="showModal('Delete')"
                            v-bind:title="lang.btn.delete">
                        <i class="fas fa-trash-alt"/>
                    </button>

                    <button type="button" class="btn btn-primary"
                            v-bind:class="[viewType === 'table' ? 'active' : '']"
                            v-on:click="selectView('table')"
                            v-bind:title="lang.btn.table">
                        <i class="fas fa-th-list"/>
                    </button>
                    <button role="button" class="btn btn-primary"
                            v-bind:class="[viewType === 'grid' ? 'active' : '']"
                            v-on:click="selectView('grid')"
                            v-bind:title="lang.btn.grid">
                        <i class="fas fa-th"/>
                    </button>
<!--                    <button role="button" class="btn btn-primary"-->
<!--                            v-on:click.prevent="toggleFilterView"-->
<!--                            v-bind:title="lang.btn.filter">-->
<!--                        <i class="fas fa-filter"/>-->
<!--                    </button>-->
                </div> <!--btn-grp-->

            </div> <!--col-->
        </div>
    </div>
</template>

<script>
import translate from '../../mixins/translate';
import EventBus from '../../eventBus';

export default {
  mixins: [translate],

  data() {
    return {
      selectOptions: ['Images', 'Videos', 'Documents'],
    };
  },
  computed: {
    /**
     * Active manager name
     * @returns {default.computed.activeManager|(function())|string|activeManager}
     */
    activeManager() {
      return this.$store.state.fm.activeManager;
    },

    /**
     * Back button state
     * @returns {boolean}
     */
    backDisabled() {
      return !this.$store.state.fm[this.activeManager].historyPointer;
    },

    /**
     * Forward button state
     * @returns {boolean}
     */
    forwardDisabled() {
      return this.$store.state.fm[this.activeManager].historyPointer
          === this.$store.state.fm[this.activeManager].history.length - 1;
    },

    /**
     * Is any files or directories selected?
     * @returns {boolean}
     */
    isAnyItemSelected() {
      return this.$store.state.fm[this.activeManager].selected.files.length > 0
          || this.$store.state.fm[this.activeManager].selected.directories.length > 0;
    },

    /**
     * Manager view type - grid or table
     * @returns {default.computed.viewType|(function())|string}
     */
    viewType() {
      return this.$store.state.fm[this.activeManager].viewType;
    },

    /**
     * Upload files
     * @returns {boolean}
     */
    uploading() {
      return this.$store.state.fm.messages.actionProgress > 0;
    },

    /**
     * Clipboard - action type
     * @returns {null}
     */
    clipboardType() {
      return this.$store.state.fm.clipboard.type;
    },

    /**
     * Full screen status
     * @returns {default.computed.fullScreen|(function())|boolean|fullScreen|*|string}
     */
    fullScreen() {
      return this.$store.state.fm.fullScreen;
    },

    /**
     * Show or Hide hidden files
     * @returns {boolean}
     */
    hiddenFiles() {
      return this.$store.state.fm.settings.hiddenFiles;
    },
  },
  methods: {
    toggleFilterView() {
      this.$store.commit('fm/settings/showFilter');
    },

    emitRefreshEvent() {
      EventBus.$emit(`refresh-file-list-fm`);
    },

    /**
     * Refresh file manager
     */
    refreshAll() {
      this.emitRefreshEvent();
    },

    /**
     * History back
     */
    historyBack() {
      this.$store.dispatch(`fm/${this.activeManager}/historyBack`);
    },

    /**
     * History forward
     */
    historyForward() {
      this.$store.dispatch(`fm/${this.activeManager}/historyForward`);
    },

    /**
     * Copy
     * @param type
     */
    toClipboard(type) {
      this.$store.dispatch('fm/toClipboard', type);

      // show notification
      if (type === 'cut') {
        EventBus.$emit('addNotification', {
          status: 'success',
          message: this.lang.notifications.cutToClipboard,
        });
      } else if (type === 'copy') {
        EventBus.$emit('addNotification', {
          status: 'success',
          message: this.lang.notifications.copyToClipboard,
        });
      }
    },

    /**
     * Paste
     */
    paste() {
      this.$store.dispatch('fm/paste');
    },

    /**
     * Set Hide or Show hidden files
     */
    toggleHidden() {
      this.$store.commit('fm/settings/toggleHiddenFiles');
    },

    /**
     * Show modal window
     * @param modalName
     */
    showModal(modalName) {
      // show selected modal
      this.$store.commit('fm/modal/setModalState', {
        modalName,
        show: true,
      });
    },

    /**
     * Select view type
     * @param type
     */
    selectView(type) {
      if (this.viewType !== type) this.$store.commit(`fm/${this.activeManager}/setView`, type);
    },

    /**
     * Full screen toggle
     */
    screenToggle() {
      const fm = document.getElementsByClassName('fm')[0];

      if (!this.fullScreen) {
        if (fm.requestFullscreen) {
          fm.requestFullscreen();
        } else if (fm.mozRequestFullScreen) {
          fm.mozRequestFullScreen();
        } else if (fm.webkitRequestFullscreen) {
          fm.webkitRequestFullscreen();
        } else if (fm.msRequestFullscreen) {
          fm.msRequestFullscreen();
        }
      } else if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
      } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
      } else if (document.msExitFullscreen) {
        document.msExitFullscreen();
      }

      this.$store.commit('fm/screenToggle');
    },
  },
};
</script>

<style lang="css">
    .fm-navbar.btn-group {
        margin-right: 0.4rem;
    }
</style>
