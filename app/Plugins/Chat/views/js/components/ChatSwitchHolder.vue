<template>

    <div class="btn-group">

        <status-switch 
            :name="data.id"
            :value="data.status"
            :key="Math.random()"
            :onChange="onChange"
        />

    </div>

</template>

<script>

import Switch from "components/MiniComponent/FormField/Switch";
import {errorHandler,successHandler} from "helpers/responseHandler";

export default {
    components : {
        "status-switch":Switch
    },
    props:['data'],
    methods: {
        onChange(value,name) {
            this.statusChange(name);

        },

        statusChange(id) {
            axios.get('chat/api/status/'+id)
            .then((res) => {
                successHandler(res,'dataTableModal');
                window.eventHub.$emit('refreshData');
            })
            .catch((err) => {
                errorHandler(err,'dataTableModal');
            })
        }
    }

}
</script>