<template>

    <div>

        <modal v-if="showModal" :showModal="showModal" :onClose="onClose">

            <div slot="title">

                <h4 class="modal-title">{{lang('description')}}</h4>
            </div>

            <div slot="fields">

            <div id="log_desc">

                <span v-html="description"></span>
            </div>
            </div>

        </modal>
    </div>
</template>

<script type="text/javascript">

    export default {

        name : 'task-description',

        description : 'Task description Modal component',

        props:{

            showModal:{type:Boolean,default:false},

            onClose:{type: Function, default : ()=>{}},

            description : { type : String, default : ''}
        },

        components:{'modal':require('components/Common/Modal.vue')}
    };
</script>

<style type="text/css" scoped>
 
  #log_desc{
    overflow: auto;
    word-break: break-all;
    min-height: 150px;
    max-height: 300px;
  }
</style>