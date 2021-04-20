<template>
    <!-- if selected category is equals to label, its color becomes darker -->

	<a v-on:click="updateCurrentTab" v-bind:class="selectedCategory == label ? 'selected': 'unselected'">
        <i v-bind:class="classname"></i> <span>{{ label }}</span>
        <small class="label pull-right bg-green">{{ count }}</small>
    </a>
</template>

<script>
    import { mapGetters } from 'vuex'

	export default {
		props:['route','classname','label','count'],
        methods:{
            updateCurrentTab(){
                this.$store.dispatch('updateSelectedCategory', this.label)
            }
        },
        computed:{
          ...mapGetters(['selectedCategory'])
        },
	}
</script>

<style>
    
    a{
        color: #b8c7ce!important;
    }

    a:hover{
        background-color: black;
    }

    .selected{
        background-color: black;
        font-size: medium;
    }

</style>