<template>
    <div :class="['card', boxClass]">
      <div class="card-header">
        <h3 class="card-title" v-if="title">{{title}}</h3>

        <slot name="customActions"></slot> 
        
        <slot name="headerTooltip"></slot>

        <slot v-if="title" name="headerMenu"></slot>
      </div>

      <div class="card-body">
        <slot></slot>
      </div>

      <slot name="actions"></slot>

    </div>
</template>

<script>
  export default {
    name: 'faveo-box',

    description: 'Bootstrap box -- theme AdminLTE',

    props: {
      /**
       * Box title */
      title: {
        type: String,
        default: ''
      },
      /**
       * Box class
       * e.g. `box-primary`, `box-default`, etc
       */
      boxClass: {
        type: String,
        default: 'card-light'
      }
    },

    computed: {
      hasFooterSlot() {
        return !!this.$slots.actions;
      }
    }
  };
</script>