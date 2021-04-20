<style scoped>
.vddl-draggable {
  cursor: move;
}
.vddl-placeholder {
  display: none;
}

.dropbtn {
  background-color: #fff;
  color: #3498db;
  padding: 14px;
  font-size: 14px;
  cursor: pointer;
  border: 1px solid #ededed;
}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #fff;
  min-width: 200px;
  overflow: auto;
  box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
  z-index: 1;
}

.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

.dropdown a:hover {
  background-color: #ddd;
}

.show {
  display: block;
  max-height: 230px;
}
.col-xs-2 {
    width: 4.666667%;
}
.input-search {
  width: 100%;
  padding: 7px;
  border-radius: 3px;
  border: 0.1em solid rgba(0,0,0,.12);
  transition: .15s all ease-in-out;
  background: white;
}
</style>
<template>
  <div class="dropdown col-xs-2">
      <i class="fa fa-object-group dropbtn" title="Form groups" aria-hidden="true" @click="openFormGroupList()"></i>
    <vddl-list
      class="col-xs-4"
      :list="filteredList"
      :horizontal="true"
      :drop="handleDrop"
    >
      <div id="fg_dropdown" class="dropdown-content">
        <input type="text" class="input-search" placeholder="Search form group" v-model="searchString">
        <i v-if="isNotAvailable" style="padding-left: 3px;">Search query not available!</i>
        <vddl-draggable
          v-for="item in filteredList"
          :key="item.group_id"
          :draggable="item"
          effect-allowed="copy"
          data-toggle="tooltip"
        >
          <a>{{ item.title }}</a>
        </vddl-draggable>
        <vddl-placeholder></vddl-placeholder>
      </div>
    </vddl-list>
  </div>
</template>

<script>
export default {
  props: {
    /**
     * @type {Array}
     */
    groupDragOptions: { type: Array, default: () => [] }
  },

  data() {
    return {
      searchString: '',
      isNotAvailable: false
    };
  },

  computed: {

    filteredList() {
      let data = this.groupDragOptions.filter(post => {
        return post.title.toLowerCase().includes(this.searchString.trim().toLowerCase())
      })
      if(data.length == 0){        
        this.isNotAvailable = true;
        return this.groupDragOptions;
      }
      this.isNotAvailable = false;
      return data; 
    }
  },

  methods: {
    //disabled drop event
    handleDrop(data) {
      return false;
    },

    /* When the user clicks on the button,
        toggle between hiding and showing the dropdown content */

    openFormGroupList() {
      document.getElementById("fg_dropdown").classList.toggle("show");
    },

    // Close the dropdown if the user clicks outside of it
    documentClick(event) {
      if (!event.target.matches(".dropbtn")) {
        let dropdowns = document.getElementsByClassName("dropdown-content");
        let i;
        for (i = 0; i < dropdowns.length; i++) {
          let openDropdown = dropdowns[i];
          if (openDropdown.classList.contains("show") && !event.target.matches(".input-search")) {
            openDropdown.classList.remove("show");
          }
        }
      }
    }
  },

  created() {
    document.addEventListener("click", this.documentClick);
  },
  destroyed() {
    // important to clean up!!
    document.removeEventListener("click", this.documentClick);
  }
};
</script>