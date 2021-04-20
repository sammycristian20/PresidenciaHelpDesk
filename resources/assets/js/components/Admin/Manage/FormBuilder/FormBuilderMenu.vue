<template>

  <div class="card" ref="stickyBox" :style="{ width: headerWidth }" :class="{ sticky: isSticky }">

    <div class="card-body" id="drag__body">

      <span class="dropdown float-right form-group-list" v-if="formGroupList && formGroupList.length > 0 && getFormCategoryType !== 'user' && getFormCategoryType !== 'organisation'">
        <button class="btn btn-default dropdown-toggle" type="button" id="form-group-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">{{ lang('form_groups') }}
        </button>

        <draggable-element :list="formGroupList" tag="ul" :group="{ name: 'faveo-form-builder', pull: 'clone', put: false }" :clone="cloneElement" class="dropdown-menu dropdown-menu-right dropdown-menu-xl">
          <li v-for="item in formGroupList" v-bind:key="item.id" class="nav-item">
            <a style="cursor: move" href="javascript:void(0)" class="nav-link text-dark">{{ item.name }}</a>
          </li>
        </draggable-element>
      </span>

      <draggable-element class="form__menulsit" :list="getFormMenus" :group="{ name: 'faveo-form-builder', pull: 'clone', put: false }" :clone="cloneElement">
        <span v-for="element in getFormMenus" :key="element.id">
          <span v-tooltip="element.title" class="form__menu"><i :class="['fas', element.icon_class]" aria-hidden="true"></i></span>
        </span>
      </draggable-element>

    </div>

  </div>

</template>

<script>

import draggable from 'vuedraggable';
import { mapGetters } from 'vuex';

export default {

  components: {
    'draggable-element': draggable,
  },

  props: {
    // List of form groups
    formGroupList: {
      type: Array,
      required: true
    },
  },

  data() {
    return {
			isSticky: false, // flag to use for fixing form menu pallet on top
			headerWidth: '100%',
			stickyNavTop: 0,
			stickyOffsetCounter: 0,
			topBarWidth: 0,
      // formGroupSearchText: ''
    }
  },

  computed: {
    ...mapGetters(['getFormMenus','getFormCategoryType'])
  },

  created() {
    window.addEventListener('scroll', this.stickyNav);
  },

  mounted() {
    this.stickyNavTop = this.$refs.stickyBox.offsetTop;
  },

  // computed: {
  //   getFormGroupList: function() {
  //     if(!this.formGroupSearchText) {
  //       return this.formGroupList;
  //     }
  //     return this.formGroupList.filter((fg) => {
  //       return fg.name.toLowerCase().includes(this.formGroupSearchText.toLowerCase());
  //     })
  //   }
  // },

  methods: {

    /**
     * Clone element
     * Assign timestamp to `groupid` if field type is group
     * else assign timestamp to `id`
     * 
     * This will resolve the DOM update error in loop for type group
     */
    cloneElement(data) {
      let newData = JSON.parse(JSON.stringify(data));
      const currentTimestamp = this.getCurrentTimestamp();
      if (newData.type === 'group') {
        newData.groupid = currentTimestamp;
      } else {
        newData.id = currentTimestamp;
      }
      newData.form_identifier = currentTimestamp;
      return newData;
    },

    getCurrentTimestamp() {
      return new Date().getTime();
    },

    // Used for fixing/unfixing form menu top bar
    stickyNav() {
      let scrollTop = this.getWindowScrollTop();
			if (scrollTop > this.stickyNavTop) {
				this.isSticky = true;
				this.stickyOffsetCounter++;
				if (this.stickyOffsetCounter == 1) {
					this.topBarWidth = this.getStickyBoxOffset();
				}
				this.headerWidth = this.topBarWidth;
			} else {
				this.stickyOffsetCounter = 0;
				this.isSticky = false;
				this.headerWidth = '100%';
			}
    },
    
    getWindowScrollTop(){
      return $(window).scrollTop();
    },

    getStickyBoxOffset() {
      return this.$refs.stickyBox.offsetWidth + 'px';
    }
  },

  beforeDestroy() {
		window.removeEventListener('scroll', this.stickyNav);
	}
};
</script>

<style scoped>

.sticky {
	position: sticky;
	top: 50px;
	z-index: 999;
	-webkit-box-shadow: 0 2px 6px rgba(63, 63, 63, 0.1);
	box-shadow: 0 2px 6px rgba(63, 63, 63, 0.1);
}

.form__menulsit {
  padding: 1rem 0rem;
}

.form__menu {
  margin: 0.1rem;
  padding: 0.5rem 1rem;
  border: 1px solid #cfcfcf;
  cursor: move;
  border-radius: 0.25rem;
}

.form-group-list {
  padding-top: 0.5rem;
}

.dropdown-menu {
  max-height: 35vh;
  overflow-y: auto;
}

#drag__body {
  padding: 0.5rem;
}

.nav-item:hover { background-color: #e9ecef; }

</style>
