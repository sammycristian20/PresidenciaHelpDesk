<template>
  <div class="language-menu">
      <button class="btn btn-default dropdown-toggle" type="button" id="language-list-dropdown"
        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" @click="getLanguageList()">
        {{ lang('add_new_language') }}
        <span class="caret"></span>
      </button>
      <ul class="dropdown-menu dropdown-menu-lg language-dropdown" aria-labelledby="language-list-dropdown">
        <li v-if="isLoading" class="nav-item mt-2">
          <relative-loader :size="30" />
        </li>
        <li v-for="lang in languageList" :key="lang.id" v-else class="nav-item">
          <a href="javascript:void(0)" @click="changeFlag(lang.locale)" class="nav-link text-dark">
            <img :src='getLocalFlag(lang.locale)'>&nbsp;{{lang.name}}
          </a>
        </li>
      </ul>
    </div>
</template>

<script>

import axios from 'axios';

export default {
  name: 'language-dropdown',

  data() {
    return {
      languageList: [],
      isLoading: false
    }
  },

  components: {
    'relative-loader': require('components/Extra/Loader'),
  },

  methods: {
    //getting language
    getLanguageList() {
      this.isLoading = true;
      axios.get('api/dependency/languages?meta=true')
        .then((resp) => {
          this.languageList = resp.data.data.languages.sort((a, b) => {
            if (a.name < b.name) {
              return -1;
            }
            if (b.name < a.name) {
              return 1;
            }
            return 0;
          });
        })
        .catch((err) => {
          this.languageList = [];
        })
        .finally(()=> {
          this.isLoading = false;
        })
    },

    //get flag image by language name
    getLocalFlag(x) {
      return this.basePath() + '/themes/default/common/images/flags/' + x + '.png'
    },

    //Emit event to change language/flag
    changeFlag(newLang) {
      let languageFlag = {};
      languageFlag['language'] = newLang;
      this.$emit('getflag', languageFlag);
    },
  }

};
</script>

<style lang="css" scoped>
.language-dropdown {
  min-height: 50px;
  max-height: 165px;
  overflow-y: auto;
}
.language-menu {
    margin-bottom: 0.5rem;
    margin-top: 0.5rem;
}
</style>
