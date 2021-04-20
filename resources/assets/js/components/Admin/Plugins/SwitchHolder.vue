<template>

    <div class="btn-group">

        <status-switch 
            :name="data.name"
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
        statusChange(name) {
            axios.post('plugin/status/'+name)
            .then((res) => {
                successHandler(res,'dataTableModal');
                setTimeout(()=>{
                    window.eventHub.$emit('refreshData');
                    window.eventHub.$emit('update-sidebar');
                },10);
            })
            .catch((err) => {
                errorHandler(err,'dataTableModal');
            })
        }
    }

}
</script>