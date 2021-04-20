<template>
    <div id="web-socket-settings__main">
        <alert componentName="web-socket-settings" />

        <faveo-box :title="lang('websocket_settings')">

            <loader v-if="isLoading"></loader>

            <section v-if="websocketsConfigData" id="web-socket-settings__body">
                <div class="row">
                    <text-field
                        v-for="(value, key) in websocketsConfigData"
                        :key="key"
                        :id="key"
                        :label="lang(key)"
                        :hint="lang(key + '_HINT')"
                        type="text"
                        :name="key"
                        classname="col-sm-6"
                        :value="websocketsConfigData[key]"
                        :onChange="onChange"
                        >
                    </text-field>
                </div>
            </section>

            <div slot="actions" class="card-footer">
                <button id="agent-submit" class="btn btn-primary" type="button" :disabled="isLoading" @click="onSubmit">
                    <span class="fas fa-save" id="submit-btn"></span>&nbsp; {{ lang('submit')}}
                </button>
            </div>

        </faveo-box>
    </div>
</template>

<script>
import axios from 'axios';
import FaveoBox from 'components/MiniComponent/FaveoBox';
import {errorHandler, successHandler} from "helpers/responseHandler";

export default {

    name: 'web-socket-settings',

    data: () => {
      return {
          websocketsConfigData: null,
          isLoading: true
      }
    },

    beforeMount() {
        axios.get('api/get-websockets-config')
            .then(response => {
                this.websocketsConfigData = response.data.data;
            })
            .catch(error => {
                errorHandler(error, 'web-socket-settings');
            })
            .finally(() => {
                this.isLoading = false;
            })
    },

    methods: {
        onSubmit() {
            this.isLoading = true;
            axios.post('api/update-websockets-config', this.websocketsConfigData )
                .then(response => {
                    successHandler(response, 'web-socket-settings');
                })
                .catch(error => {
                    errorHandler(error, 'web-socket-settings');
                })
                .finally(() => {
                    this.isLoading = false;
                })
        },

        onChange(value, name) {
            this.websocketsConfigData[name] = value;
        },

    },


    components: {
        'faveo-box': FaveoBox,
        'loader': require("components/MiniComponent/Loader"),
        'text-field': require("components/MiniComponent/FormField/TextField"),
    }
}
</script>

<style>

</style>