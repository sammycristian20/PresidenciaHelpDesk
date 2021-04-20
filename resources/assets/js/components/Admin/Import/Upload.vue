<template>
    <div>
        <custom-loader v-if="loading" :duration="loadingSpeed" />
        <alert componentName="importerUpload" />
        <faveo-box :title="lang('importer_upload')" v-if="!uploaded">

            <div class="row">

                <div class="form-group col-sm-12">

                    <label >{{ lang('importer_import_file_label') }} <span class="text-red">*</span></label>
                    <input type="file" name="import_file" @change="fileSelected" >

                </div>

            </div>

            <div class="card-footer" slot="actions">
                <button type="button" @click= "submitCallback" class="btn btn-primary" >
                    <i class="fas fa-upload"></i>
                    {{lang('importer_upload_btn')}}
                </button>
            </div>

        </faveo-box>

        <user-import-mapper
                v-if="uploaded" :apiEndpoint="apiUrl"
                :updateParent="updateParent"
                :table-headings="tableHeadings"
                :button-text="buttonText"
                :import-id="importId"
                :expand-by-default="true"
        />

    </div>
</template>

<script>
    import FaveoBox from 'components/MiniComponent/FaveoBox';
    import axios from "axios";
    import {errorHandler, successHandler} from 'helpers/responseHandler';
    import UserImportMapper from "../../Extra/UserImportMapper";
    export default {

        name: "Upload",

        components: {
            UserImportMapper,
            'faveo-box' : FaveoBox,
            "alert" : require('components/MiniComponent/Alert'),
            "custom-loader": require("components/MiniComponent/Loader"),
        },

        data() {
            return {
                apiUrl:'importer/api/processing/',
                importId: null,
                importFile: null,
                componentRenderKey: Math.random(),
                loading:false,
                loadingSpeed: 4000,
                uploaded: false,
                tableHeadings: {
                    faveo : {
                        header: 'importer_system_atrributes',
                        description: 'importer_system_atrribute_description'
                    },
                    thirdParty: {
                        header: 'importer_xl_attributes',
                        description: 'importer_xl_attribute_description'
                    },
                    overwrite: {
                        header: 'importer_overwrite',
                        description: 'importer_overwrite_description'
                    }
                },
                buttonText: 'importer_import'
            }
        },

        methods: {
            onChange(value, name) {

                this[name] = value;

                if(value === null){

                    this[name] = '';
                }
            },

            fileSelected(event) {
                if(event.target.files[0].size < 25000000 ) //25 MB
                    this.importFile = event.target.files[0];
                else{
                    alert(lang('importer_max_upload_size'))
                }

            },

            updateParent() {
                setTimeout(() => {
                    this.redirect('/importer');
                },1000);
            },

            customErrors(err) {
                if (err.response.status === 412) {
                    this.setAlert(err.response.data.message.import_file)
                } else {
                    errorHandler(err, 'importerUpload')
                }
            },

            setAlert(msg) {
                errorHandler({
                    response: {
                        status: 400,
                        data: {
                            message: msg
                        }
                    }
                },'importerUpload');
            },

            submitCallback() {

                this.loading = true;

                let formDataObj = new FormData();
                formDataObj.append('import_file',this.importFile);

                axios.post('/importer/api/upload',formDataObj,{ headers: { 'Content-Type': 'multipart/form-data' } })
                    .then((res) => {
                        this.importId = res.data.data.import_id
                        this.apiUrl = "importer/api/processing?import_id=" + this.importId
                        successHandler(res, 'importerUpload');
                        this.uploaded = true;
                    })
                    .catch((err) => {
                        this.customErrors(err);
                        this.uploaded = false;
                    })
                    .finally(() => {
                        this.loading = false;
                    })
            },
        }
    }
</script>

<style scoped>

</style>