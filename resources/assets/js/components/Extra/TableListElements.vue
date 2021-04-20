<template>
	<div v-if="data.listElementObj" class="element-list-box">
    <span v-if="listElements.length === 0">--</span>
    <template v-if="listElements.length > 0">
      <div v-for="element in listElements" :key="element.id">
        <a :href="url(element)" :target="urlTarget">{{element.name}}</a>
      </div>
      <div>
        <button class="btn btn-default btn-xs" v-if="displayShowAllButton" @click="readMore">{{lang('show_all')}}</button>
        <button class="btn btn-default btn-xs" v-if="!isReadMore" @click="readLess">{{lang('show_less')}}</button>
      </div>
    </template>
	</div>
</template>

<script type="text/javascript">

	export default {
		
		name: 'table-list-elements',

		description: 'ELements list view in datatable column',		
		
		props: {
			data : { type : Object, required : true }
    },

    data: () => {
      return {
        limit: 2, // Limit of data element to show
        isReadMore: true // used to toggle buttons `show_all` and `show_less`
      }
    },
    
    computed: {
      /** List down elements on the basis of limit */
      listElements: function() {
        return this.data[this.data.listElementObj.key].slice(0, this.limit);
      },

      /**
       * Check whether to show the show all button or not
       * Keeping maximum length as 2 here, show button if more than 2 elements present
       */
      displayShowAllButton: function() {
        return this.isReadMore && this.data[this.data.listElementObj.key].length > 2;
      },

      urlTarget() {
        return (this.data.listElementObj.redirectUrl) ? '_blank' : '';
      },
    },
		
		methods: {

      /**
       * onReadMore click operation
       */
      readMore() {
        this.isReadMore = false;
        this.limit = this.data[this.data.listElementObj.key].length;
      },

      /**
       * onReadLess click operation
       */
      readLess() {
        this.isReadMore = true;
        this.limit = 2;
      },

      url(element) {
        return (this.data.listElementObj.redirectUrl) ? this.data.listElementObj.redirectUrl  + element.id : 'javascript:;';
      }
		}
	};

</script>

<style type="text/css" scoped>
.element-list-box {
		max-width: 300px;
		max-height: 100px;
		overflow: auto;
	}
</style>