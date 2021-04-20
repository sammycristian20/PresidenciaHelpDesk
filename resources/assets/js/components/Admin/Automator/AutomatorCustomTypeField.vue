<template>
  <div style="padding-left: 3rem; padding-right: 3rem;">

    <dynamic-select v-if="field.unique === 'mail_agent'" :label="trans('agent')" name="users" apiEndpoint="/api/dependency/agents" :value="users" :onChange="onChange" rules="required" :required="true" :multiple="true"></dynamic-select>

    <text-field :label="trans('subject')" type="text" name="subject" :value="subject" :onChange="onChange" rules="required" :required="true"></text-field>

    <tiny-editor-with-validation name="body" :label="trans('body')" :value="body || ''" :onChange="onChange" rules="required" :required="true"></tiny-editor-with-validation>

  </div>
</template>

<script>

export default {
  name: 'automator-custom-type-field',

  props: {
    field: { type: Object, required: true },
    actionEmail: { type: Object, required: true },
    index: { type: Number, required: true },
  },

  data: () => {
    return {
      users: null,
      subject: '',
      body: ''
    }
  },

  beforeMount() {
    this.users = this.actionEmail.users;
    this.subject = this.actionEmail.subject;
    this.body = this.actionEmail.body;
  },

  methods: {
    onChange (value, name) {
      this.$store.dispatch('updateActionEmail', { index: this.index, key: name, value: value })
    }
  },

  components: {
    'dynamic-select': require('components/MiniComponent/FormField/DynamicSelect'),
    'text-field': require('components/MiniComponent/FormField/TextField'),
  }
}
</script>

<style>

</style>