<template>
  <div class="row faveo-automator-rule">

    <div class="col-sm-2 rule-menu">
      <span><i class="fas fa-trash faveo-trash" @click="deleteRule()"></i></span>
      <span class="offset-md-2 max-width-max-content rule_btns">
        <a href="javascript:void(0)"
          :class="[{'faveo-active-border': category === 'ticket'}, 'btn', 'btn-default']"
          data-toggle="tooltip"
          data-placement="top"
          title="Ticket Field"
          @click="onCategoryChange('ticket')">
          <i class="fas fa-ticket-alt"></i>
        </a>

        <a href="javascript:void(0)"
          :class="[{'faveo-active-border': category === 'user'}, 'btn', 'btn-default']"
          data-toggle="tooltip"
          data-placement="top"
          title="User Field"
          @click="onCategoryChange('user')">
          <i class="fas fa-user"></i>
        </a>
      </span>
    </div>

    <dynamic-select
      classname="col-sm-4"
      :labelStyle="{display: 'none'}"
      :multiple="false"
      :id="'rule-' + rule._id"
      :name="'rule-' + rule._id"
      :elements="formFields[category]"
      optionLabel="label"
      :value="field"
      :onChange="onFieldChange"
      :strlength="25"
      :clearable="false">
    </dynamic-select>

    <template v-if="field !== null">

      <dynamic-select
        :key="field.id"
        classname="col-sm-2"
        :labelStyle="{display: 'none'}"
        :multiple="false"
        name="relation"
        :elements="relationList"
        optionLabel="name"
        :value="relation"
        :onChange="onRelationChange"
        :strlength="25"
        :clearable="false">
      </dynamic-select>

      <!--   automator form field should rerender whenever a field or category is changes else it might show values of old selection   -->
      <div class="col-sm-4" :key="JSON.stringify(field) + JSON.stringify(category)">
        <automator-form-field
          :key="rule._id"
          enforcerType="rule"
          :form-field="field"
          :enforcer-object="rule"
          :labelStyle="{display: 'none'}">
        </automator-form-field>
      </div>

    </template>

  </div>
</template>

<script>
import { Rule, getRelationList } from 'helpers/AutomatorUtils'

export default {

  name: 'faveo-automator-rule',

  props: {
    rule: { type: Rule, required: true },
    formFields: { type: Object, required: true },
    index: { type: Number, required: true },
  },

  data: () => {
    return {
      category: '',
      field: null,
      relation: null,
      relationList: []
    }
  },

  beforeMount () {
    this.category = this.rule.category;
    this.field = this.rule.field;
    this.relation = this.rule.relation;

    if (this.field) {
      this.relationList = getRelationList(this.field.type);
    }
  },

  methods: {

    onFieldChange (value) {
      this.field = value;
      this.refreshRelationList();
      this.$store.dispatch('onFieldChange', { key: 'rules', index: this.index, field: this.field });
    },

    refreshRelationList () {
      if (this.field) {
        this.relationList = getRelationList(this.field.type);
      } else {
        this.relationList = []
      }
    },

    onRelationChange (value) {
      this.relation = value;
      this.$store.dispatch('onRelationChange', { key: 'rules', index: this.index, relation: this.relation });
    },

    onCategoryChange (value) {
      this.field = null;
      this.category = value;

      this.$store.dispatch('onCategoryChange', { key: 'rules', index: this.index, value: new Rule (this.rule._id, this.category) });
    },

    deleteRule () {
      window.eventHub.$emit('deleteItem', 'rule', this.index, this.rule._id);
    }
  },

  components: {
    'dynamic-select': require('components/MiniComponent/FormField/DynamicSelect'),
    'automator-form-field': require('./AutomatorFormField'),
  }

}
</script>

<style scoped>

.rule-menu {
  line-height: 2.5;
}

.rule-menu > .faveo-trash {
  margin-right: 1rem;
}

.rule-menu > .active {
  background-color: lightblue;
}

.faveo-automator-rule {
  padding-top: 1.5rem;
}

.faveo-automator-rule:hover {
  box-shadow: 0 -1px 1px -1px rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 1px 3px 0 rgba(0,0,0,.12);
}

.faveo-active-border {
  color: #333 !important;
  background-color: #e6e6e6 !important;
  border: 2px solid #3c8dbc;
}
.rule_btns { position: relative; top: -2px;}
</style>