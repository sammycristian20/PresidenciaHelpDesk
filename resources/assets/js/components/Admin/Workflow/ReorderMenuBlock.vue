<style scoped>
.handle {
  padding: 12px; background: #428bca; color: #fff; margin-right: 10px; display: inline-block; cursor: move;
  text-decoration: none; font-weight: bold; position: absolute; height: 45px;
}
.nodrag {
  min-height: 44px;
}
.tree-node {
  margin: 10px; border: 1px solid #ededed; color: #7c9eb2; clear: both; background-color: #fff;
}
.colp {
  margin-left: 40px; padding: 10px; padding-bottom: 5px;
}
.colp a {
  padding: 5px 5px; margin-top: -1px;
}
.fa.pull-right {
  margin-left: 9px;
}
</style>
<template>
  <div class="tree-node" ref="closemenu">
    <vddl-nodrag class="nodrag">
      <vddl-handle :handle-left="20" :handle-top="20" class="handle">
        <i class="fa fa-list glypic-li" aria-hidden="true"></i>
      </vddl-handle>
      <div class="contnt">
        <div  class="colp">
          <label class="col-open">{{item.name}}</label>                       
        </div>
      </div>
    </vddl-nodrag>
  </div>
</template>
<script>
export default {
  props: ["item","indexValue"],
  data() {
    return {
    };
  },
  components: {
  },
  methods: {
 
  },
  mounted() {
  }
};
</script>