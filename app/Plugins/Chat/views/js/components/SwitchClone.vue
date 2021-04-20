<template>
    
    <div :class="classname">
         <switches
            v-model="enabled"
            theme="bootstrap"
            :color="enabled ? 'success':'danger'"
            :type-bold="bold">
        </switches>
    </div>

</template>

<script>
import Vue from 'vue';
import Switch from "components/MiniComponent/FormField/Switch";
export default {
    extends:Switch,
    props: {
        id : {
            required: true
        }
    },
    components: {
        switches: require("vue-switches")
    },

    beforeCreate() {
        Vue.delete(this.$options.props,'onChange')
    },

    methods: {
        onChange(val) {
            this.$emit("switchStatus", this.id)
        },

    }
}
</script>