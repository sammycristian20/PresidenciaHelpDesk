<template>
  <div>
    <div class="col-md-12 language-menu-dropdown">
      <language-dropdown v-on:getflag="assignNewLanguage"></language-dropdown>
    </div>
    <div class="col-md-12 add-language-box">
      <ul class="add-language-ul">
      <li class="add-language-li">
        <label class="label-word-break"><strong>{{formFieldData.labels_for_form_field[0].label}}</strong></label>
        <ul class="li-child">
          <li v-for="(label, labelIndex) in formFieldData.labels_for_form_field" :key="labelIndex">
            <div>
              <label style="float:left" :for="label.label + '_' + labelIndex">
                <img :src='getLocalFlag(label.language)'>&nbsp;
              </label>
              <div class="form-field-label">
                <input :name="label.label + '_' + labelIndex" type="text" v-model="label.label">
                <input placeholder="Description" :name="label.description + '_' + labelIndex" type="text" v-model="label.description">
              </div>
            </div>
          </li>
        </ul>
      </li>
      <span style="display: inline-flex;" v-if="formFieldData.options && formFieldData.options.length > 0 && canChangeLanguageForOptions">
        <li class="add-language-li" v-for="(option, optionIndex) in formFieldData.options" :key="optionIndex">
          <label class="label-word-break"><strong>{{option.labels[0].label}}</strong></label>
          <ul class="li-child">
            <li v-for="(label, labelIndex) in option.labels" :key="labelIndex">
              <div>
                <label style="float:left" :for="label.label + '_' + labelIndex">
                  <img :src='getLocalFlag(label.language)'>&nbsp;
                </label>
                <div class="form-field-label">
                  <input :name="label.label + '_' + labelIndex" type="text" v-model="label.label">
                </div>
              </div>
            </li>
          </ul>
        </li>
      </span>
      </ul>
    </div>
  </div>
</template>

<script>

import _ from 'lodash-core';

export default {

  name: 'FB-add-language',

  components: {
    'language-dropdown': require('./LanguageDropdown')
  },

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

  computed: { 
    canChangeLanguageForOptions () {
      const deptHelptopic = ['Department', 'Help Topic'];
      return !deptHelptopic.includes(this.formFieldData.title);
    }
  },

  methods: {

    // Add new languages
    assignNewLanguage(newLang) {
      const currentTimestamp = this.getCurrentTimestamp();
      newLang.form_identifier = currentTimestamp;
      newLang.id = currentTimestamp;

      // Add lanaguage to options
      if(this.formFieldData.options && this.canChangeLanguageForOptions) {
        this.formFieldData.options.forEach(element => {
          newLang.label = element.labels[0].label;
          if(!this.isLangPresentAlready(element.labels, newLang.language)) {
            element.labels.push(new Object(Object.assign({}, newLang)));
          }
        });
      }

      // Add language to label and description if it's not present already
      if(!this.isLangPresentAlready(this.formFieldData.labels_for_form_field, newLang.language)) {
        newLang.label = this.formFieldData.labels_for_form_field[0].label;
        newLang.description = this.formFieldData.labels_for_form_field[0].description;
        this.formFieldData.labels_for_form_field.push(new Object(Object.assign({}, newLang)));
      }
    },

    // Get flag on basis of country code
    getLocalFlag(x) {
      return this.basePath() + '/themes/default/common/images/flags/' + x + '.png'
    },

    // Check the laguage present already
    isLangPresentAlready(array, lang) {
      return _.findIndex(array, (element) => element.language === lang) > -1;
    },

    getCurrentTimestamp() {
      return new Date().getTime();
    },
  }
}
</script>

<style lang="css" scoped>
.form-field-label {
  overflow: hidden;
}
.form-field-label > input {
  width: 85%;
  border: 1px dashed #fff;
}
ul {
  list-style-type: none;
}
.label-word-break {
  word-break: break-word;
}
.li-child {
  padding-left: 1.5rem;
}
.language-menu-dropdown {
  padding-left: 0;
}
.add-language-box {
  max-height: 235px;
  overflow: auto;
  border: 1px solid #dedede;
  border-radius : 0.25rem;
}
.add-language-ul {
  display: inline-flex;
  padding-left: 0;
}
.add-language-li {
  margin: 1.5rem;
  width: 185px;
}
</style>